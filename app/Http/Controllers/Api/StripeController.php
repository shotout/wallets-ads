<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\StripeClient;

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

            //charge customer
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
}
