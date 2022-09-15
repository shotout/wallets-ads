<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StripePayment;
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
            $stripe = new StripeClient(env('STRIPE_LIVE_API_KEY'));

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
            $charge = new StripeClient(env('STRIPE_LIVE_API_KEY'));

            $charge->charges->create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'source' => $result->id,
                'description' => $request->description,
            ]);

            return response()->json( ['message' => 'Payment Success'], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'Payment Failed'
            ], 500);
        }
    }


    public function intent(Request $request)
    {
        try{

            Stripe::setApiKey(env('STRIPE_TEST_API_KEY'));

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types'  => ['card'],
                'line_items'            => [[
                    'price_data' => [
                        'currency'      => 'usd',
                        'unit_amount'   => $request->total_budget,
                        'product_data'  => [
                            'name'  => $request->campaign_name,
                        ]],
                        'quantity'      => 1,]],                        
                'mode' => 'payment',
                'client_reference_id' => 'INV_001',
                'customer_email' => auth('sanctum')->user()->email,
                'success_url' => "https://dashboard.walletads.io/create-campaign/?id=".$request->campaign_id."&status=success",
                'cancel_url' =>  "https://dashboard.walletads.io/create-campaign/?id=".$request->campaign_id."&status=fail"
                ]);
                
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
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'Intent Failed'
            ], 500);
        }   
    }
}
