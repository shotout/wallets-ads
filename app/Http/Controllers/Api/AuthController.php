<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendConfirmEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validation
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|confirmed|min:8|max:100',
        ]);

        // add new user
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->remember_token = Str::random(16);

        if ($user->save()) {
            // sending email verification
            SendConfirmEmail::dispatch($user, 'register')->onQueue('apiCampaign');

            // generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // retun response
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'data' => $user
            ]);           
        }
    }

    public function login(Request $request)
    {
        // validation
        $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|min:8|max:100',
        ]);

        // find email
        $user = User::where('email', $request->email)->first();

        // check if email has register
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'email account not registered',
            ]);
        }

        // check if email has verify
        if (!$user->email_verified_at) {
            return response()->json([
                'status' => 'failed',
                'message' => 'email account not verified',
            ]);
        }

        // generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // retun response
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'data' => $user
        ]);
    }

    public function verify($token)
    {
        // check email token and update 
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'token expired',
            ]);
        }

        $user->email_verified_at = now();
        $user->remember_token = null;
        $user->update();

        // generate token api
        $token = $user->createToken('auth_token')->plainTextToken;

        // retun response
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'data' => $user
        ]);
    }
}
