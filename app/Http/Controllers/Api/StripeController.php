<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Payment;
use App\Models\StripePayment;
use App\Models\User_payment;
use App\Models\Voucher;
use Exception;
use Illuminate\Http\Request;
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
            $update_payment->payment_data = $request->payment_data;
            $update_payment->save(); 

            return response()->json(['message' => 'Payment Data Updated'], 200);
        } 
        catch (Exception $e) {
            return response()->json([
                'status' => 'Payment Data Update Failed'
            ], 500);
        }
    }
}
