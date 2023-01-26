<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Payment;
use App\Models\StripePayment;
use App\Models\User;
use App\Models\User_payment;
use App\Models\Voucher;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;
use Stripe\SetupIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_TEST_API_KEY'));

            //get token from request
            $result = $stripe->tokens->create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            //charge customer payment
            $charge = new StripeClient(env('STRIPE_TEST_API_KEY'));

            $charge->charges->create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'source' => $result->id,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Payment Success'], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'Payment Failed'
            ], 500);
        }
    }


    public function intent(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_TEST_API_KEY'));

            if (isset($request->promo)) {
                $coupon = Voucher::where('code', $request->promo)->first();

                $campaign = Campaign::where('id', $request->campaign_id)->first();

                $campaign->promo_code = $request->promo;
                $campaign->save();

                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types'  => ['card'],
                    'line_items'            => [[
                        'price_data' => [
                            'currency'      => 'usd',
                            'unit_amount'   => $request->total_budget,
                            'product_data'  => [
                                'name'  => $request->campaign_name,
                            ]
                        ],
                        'quantity'      => 1,
                    ]],
                    'mode' => 'payment',
                    'discounts' => [[
                        'coupon' => $coupon->coupon_id,
                    ]],
                    'client_reference_id' => 'INV_001',
                    'customer_email' => auth('sanctum')->user()->email,
                    'success_url' => "https://wallet-ads-frontend.vercel.app/create-campaign/?id=" . $request->campaign_id . "&status=success",
                    'cancel_url' =>  "https://wallet-ads-frontend.vercel.app/create-campaign/?id=" . $request->campaign_id . "&status=fail"
                ]);
            } else {


                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types'  => ['card'],
                    'line_items'            => [[
                        'price_data' => [
                            'currency'      => 'usd',
                            'unit_amount'   => $request->total_budget,
                            'product_data'  => [
                                'name'  => $request->campaign_name,
                            ]
                        ],
                        'quantity'      => 1,
                    ]],
                    'mode' => 'payment',
                    'client_reference_id' => 'INV_001',
                    'customer_email' => auth('sanctum')->user()->email,
                    'success_url' => "https://wallet-ads-frontend.vercel.app/create-campaign/?id=" . $request->campaign_id . "&status=success",
                    'cancel_url' =>  "https://wallet-ads-frontend.vercel.app/create-campaign/?id=" . $request->campaign_id . "&status=fail"
                ]);
            }

            $newpayment = new StripePayment();
            $newpayment->stripe_id = $session->id;
            $newpayment->campaign_id = $request->campaign_id;
            $newpayment->invoice = 'INV_001';
            $newpayment->amount = $request->total_budget;
            $newpayment->email = auth('sanctum')->user()->email;
            $newpayment->name =  $request->campaign_name;
            $newpayment->status = 0;
            $newpayment->save();

            return response()->json($session, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'Intent Failed'
            ], 500);
        }
    }


    public function savepayment(Request $request)
    {
        try {
            $save_payment = new User_payment();
            $save_payment->user_id = auth('sanctum')->user()->id;
            $save_payment->payment_data = $request->payment_data;
            $save_payment->save();

            return response()->json(['message' => 'Payment Data Saved'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'Payment Data Save Failed'
            ], 500);
        }
    }

    public function updatepayment(Request $request)
    {
        try {
            $update_payment = User_payment::where('user_id', auth('sanctum')->user()->id)->first();

            $update_payment->payment_method = $request->payment_data;

            if($request->payment_data == '2'){
                $update_payment->payment_data = '';
            }
            $update_payment->save();

            return response()->json(['message' => 'Payment Data Updated'], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Payment Data Update Failed'
            ], 500);
        }
    }

    public function getpayment()
    {
        try {
            $get_payment = User_payment::where('user_id', auth('sanctum')->user()->id)->first();

            return response()->json($get_payment, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Payment Data Get Failed'
            ], 500);
        }
    }

    public function customer_id()
    {
        try {
            $customer = Stripe::setApiKey(env('STRIPE_TEST_API_KEY'));

            $deleteuser = User::where('id', auth('sanctum')->user()->id)->first();

            if($deleteuser->customer_id != null && $deleteuser->customer_id != ''){
                $deleteuser = \Stripe\Customer::delete($deleteuser->customer_id);
            } 

            $customer = \Stripe\Customer::create([
                'email' => auth('sanctum')->user()->email
            ]);

            $customer = $customer->id;

            $user = User::where('id', auth('sanctum')->user()->id)->first();
            $user->customer_id = $customer;
            $user->save();

            $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_API_KEY'));

            $stripe->setupIntents->create(
                ['payment_method_types' => ['card'], 'customer' => $user->customer_id]
            );


            $stripe = $stripe->setupIntents->all();

            return response()->json([$stripe], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Customer ID Creation Failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function payment_method(Request $request)
    {
        try {
            $user = User::where('id', auth('sanctum')->user()->id)->first();

            $stripe = new \Stripe\StripeClient(env('STRIPE_TEST_API_KEY'));


            $stripe = $stripe->customers->allPaymentMethods(
                $user->customer_id,
                ['type' => 'card']
            );

            $data[] = [$stripe->data[0]['card']['brand'], $stripe->data[0]['card']['last4'], $stripe->data[0]['card']['exp_month'], $stripe->data[0]['card']['exp_year']];

            $new = User_payment::where('user_id', auth('sanctum')->user()->id)->first();

            if ($new) {
                $new->payment_method = 1;
                $new->payment_data = $data;
                $new->save();
            } else {
                $new = new User_payment();
                $new->user_id = auth('sanctum')->user()->id;
                $new->payment_method = 1;
                $new->payment_data = $data;
                $new->save();
            }

            return response()->json([$data], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Payment Method Creation Failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function delete_payment()
    {
        $delete = User_payment::where('user_id', auth('sanctum')->user()->id)->first();

        if($delete){
            $delete->payment_method = 0;
            $delete->payment_data = '';
            $delete->save();
        }

        return response()->json(['message' => 'Payment Method Deleted'], 200);
    }
}
