<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendConfirmEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function show()
    {
        $user = User::with('photo')->find(auth('sanctum')->user()->id);

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not found',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:100',
            'tax_id' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'street' => 'required|string|max:500',
            'post_code' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,'.auth('sanctum')->user()->id,
            'phone' => 'required|string|max:100',
            'password' => 'nullable|confirmed|min:8|max:100',
            'photo' => 'nullable|image|max:1024|image|mimes:jpeg,png,jpg',
        ]);

        $user = User::find(auth('sanctum')->user()->id);
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not found',
            ]);
        }

        $user->company_name = $request->company_name;
        $user->tax_id = $request->tax_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->street = $request->street;
        $user->post_code = $request->post_code;
        $user->city = $request->city;
        $user->phone = $request->phone;
        $user->email = $request->email;
        if ($request->has('password') && $request->password != '') {
            $user->password = bcrypt($request->password);
        }
        $user->update();

        if ($request->hasFile('photo')) {
            $media = Media::where('owner_id', $user->id)->where('type', 'user_photo')->first();
            if ($media) {
                unlink(public_path().$media->url);
            } else {
                $media = new Media;
                $media->owner_id = auth('sanctum')->user()->id;
                $media->type = "user_photo";
            }

            $filename = uniqid();
            $fileExt = $request->photo->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
            $request->photo->move(public_path().'/assets/images/user/', $fileNameToStore);

            $media->name = $fileNameToStore;
            $media->url = "/assets/images/user/$fileNameToStore";
            $media->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]); 
    }
}