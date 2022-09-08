<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Media;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'method' => 'required|string',
        ]);

        if ($request->has('method') && $request->method != '') {
            $payment = Payment::where('user_id', auth('sanctum')->user()->id)->first();

            if (!$payment) {
                $payment = new Payment;
            }

            $payment->user_id = auth('sanctum')->user()->id;
            $payment->method = $request->method;

            if ($request->method == 'card') {
                $request->validate([
                    'email' => 'required|email|max:100',
                    'card_number' => 'required',
                    'card_expired' => 'required',
                    'card_cvc' => 'required',
                    'card_name' => 'required',
                    'country' => 'required',
                    'post_code' => 'required',
                ]);

                $payment->type = 1;
                $payment->email = $request->email;
                $payment->card_number = $request->card_number;
                $payment->card_expired = $request->card_expired;
                $payment->card_cvc = $request->card_cvc;
                $payment->card_name = $request->card_name;
                $payment->country = $request->country;
                $payment->post_code = $request->post_code;
            }

            if ($request->method == 'crypto') {
                $request->validate([
                    'wallet_address' => 'required',
                ]);

                $payment->type = 2;
                $payment->wallet_address = $request->wallet_address;
            }

            $payment->save();

            return response()->json([
                'status' => 'success',
                'data' => $payment
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'method' => 'required|string',
        ]);

        if ($request->has('method') && $request->method != '') {
            $payment = Payment::where('id', $id)
                ->where('user_id', auth('sanctum')->user()->id)
                ->first();

            if (!$payment) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'data not found'
                ], 404); 
            }

            $payment->user_id = auth('sanctum')->user()->id;
            $payment->method = $request->method;

            if ($request->method == 'card') {
                $request->validate([
                    'email' => 'required|email|max:100',
                    'card_number' => 'required',
                    'card_expired' => 'required',
                    'card_cvc' => 'required',
                    'card_name' => 'required',
                    'country' => 'required',
                    'post_code' => 'required',
                ]);

                $payment->type = 1;
                $payment->email = $request->email;
                $payment->card_number = $request->card_number;
                $payment->card_expired = $request->card_expired;
                $payment->card_cvc = $request->card_cvc;
                $payment->card_name = $request->card_name;
                $payment->country = $request->country;
                $payment->post_code = $request->post_code;
            }

            if ($request->method == 'crypto') {
                $request->validate([
                    'wallet_address' => 'required',
                ]);

                $payment->type = 2;
                $payment->wallet_address = $request->wallet_address;
            }

            $payment->update();

            return response()->json([
                'status' => 'success',
                'data' => $payment
            ], 200);
        }
    }
}