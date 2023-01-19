<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use App\Jobs\SendResetEmail;
use Illuminate\Http\Request;
use App\Jobs\SendNotifRegister;
use Contentful\Management\Client;
use Contentful\Core\Api\Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Contentful\Management\Resource\Entry;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:100',
            'tax_id' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'street' => 'required|string|max:500',
            'post_code' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'required|string|max:100',
            'password' => 'required|confirmed|min:8|max:100',
            'country' => 'required|string|max:100',
        ]);

        $email = $request->email;

        $user = new User;
        $user->company_name = $request->company_name;
        $user->tax_id = $request->tax_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->street = $request->street;
        $user->post_code = $request->post_code;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->remember_token = Str::random(16);

        if ($user->save()) {

            SendNotifRegister::dispatch($user)->onQueue('apiCampaign');

            //contentful env    
            $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
            $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

            $newuser = User::where('email', $email)->first();

            //add user to contentful
            $entry = new Entry('users');
            $entry->setField('companyName', 'en-US', $newuser->company_name);
            $entry->setField('taxId', 'en-US', $newuser->tax_id);
            $entry->setField('firstName', 'en-US', $newuser->first_name);
            $entry->setField('lastName', 'en-US', $newuser->last_name);
            $entry->setField('street', 'en-US', $newuser->street);
            $entry->setField('postCode', 'en-US', $newuser->post_code);
            $entry->setField('city', 'en-US', $newuser->city);
            $entry->setField('email', 'en-US', $newuser->email);
            $entry->setField('phone', 'en-US', $newuser->phone);
            $entry->setField('country', 'en-US', $newuser->country);
            $entry->setField('accountCreatedTime', 'en-US', $newuser->created_at);
            $environment->create($entry);

            //publish user to contentful
            $entry_id = $entry->getId();            

            //update user with contentful id
            $updateuser = User::where('email', $email)->first();
            $updateuser->entry_id = $entry_id;
            $updateuser->save();        

            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 201);           
        }
            
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|min:8|max:100',
        ]);

        $credentials = $request->only('email', 'password');



        if (auth()->attempt($credentials)) {
            $user = User::with('photo','payment')->where('email', $request->email)->first();

            if($user->status == 0){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Our team is currently still checking your data. You will be informed by email as soon as your account is activated.'
                ], 401);
            }
    
            if (!$user->email_verified_at) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Account not verified, please check your email.'
                ], 403);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'data' => $user
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'email or password incorrect',
        ], 401);
    }

    public function verify($token)
    {
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'token expired',
            ], 401);

            return redirect()->to(env('FE_URL'));
        }

        $user->email_verified_at = now();
        $user->status = 1;        
        $user->update();

        // return response()->json([
        //     'status' => 'success',
        //     'data' => $user
        // ], 200);
        return redirect()->to(env('FE_URL').'/login?verify=true');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
        ]);

        $user = User::where('email', $request->email)->first();
    
        if ($user) {
            $user->remember_token = Str::random(16);
            $user->update();

            SendResetEmail::dispatch($user)->onQueue('apiCampaign');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'link reset password will send if email exist',
        ], 200);
    }

    public function checkToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:100',
        ]);

        $user = User::where('remember_token', $request->token)->first();
    
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'token expired',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:100',
            'password' => 'required|confirmed|min:8|max:100',
        ]);

        $user = User::where('remember_token', $request->token)->first();
    
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'token expired',
            ], 401);
        }

        $user->password = bcrypt($request->password);
        $user->remember_token = null;
        $user->update();

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|max:100',
            'new_password' => 'required|max:100',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = User::where('email', $request->email)->first();

            $user->password = bcrypt($request->new_password);
            $user->update();

            return response()->json([
                'status' => 'password changed',
                'data' => $user,
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'email or password incorrect',
        ], 401);
    }
}


