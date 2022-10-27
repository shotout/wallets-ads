<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Media;
use App\Models\Voucher;
use App\Models\Audience;
use App\Models\Campaign;
use App\Models\Blacklisted;
use App\Models\UserVoucher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendConfirmEmail;
use Contentful\Management\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\DeleteSnoozeRecord;
use App\Models\StripePayment;
use Carbon\Carbon;
use Contentful\Management\Resource\Entry;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function show()
    {
        $user = User::with('photo', 'payment')->find(auth('sanctum')->user()->id);

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
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
            'country' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . auth('sanctum')->user()->id,
            'phone' => 'required|string|max:100',
            'password' => 'nullable|confirmed|min:8|max:100',
            'photo' => 'nullable|image|max:1024|image|mimes:jpeg,png,jpg',
        ]);

        $user = User::find(auth('sanctum')->user()->id);
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not found',
            ], 404);
        }

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
        if ($request->has('password') && $request->password != '') {
            $user->password = bcrypt($request->password);
        }
        $user->update();

        //update contentfull data
        $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        $updateuser = User::find(auth('sanctum')->user()->id);

        $entry = $environment->getEntry($updateuser->entry_id);
        $entry->setField('companyName', 'en-US', $updateuser->company_name);
        $entry->setField('taxId', 'en-US', $updateuser->tax_id);
        $entry->setField('firstName', 'en-US', $updateuser->first_name);
        $entry->setField('lastName', 'en-US', $updateuser->last_name);
        $entry->setField('street', 'en-US', $updateuser->street);
        $entry->setField('postCode', 'en-US', $updateuser->post_code);
        $entry->setField('city', 'en-US', $updateuser->city);
        $entry->setField('country', 'en-US', $updateuser->country);
        $entry->setField('email', 'en-US', $updateuser->email);
        $entry->setField('phone', 'en-US', $updateuser->phone);
        $entry->update();


        if ($request->hasFile('photo')) {
            $media = Media::where('owner_id', $user->id)->where('type', 'user_photo')->first();
            if ($media) {
                unlink(public_path() . $media->url);
            } else {
                $media = new Media;
                $media->owner_id = auth('sanctum')->user()->id;
                $media->type = "user_photo";
            }

            $filename = uniqid();
            $fileExt = $request->photo->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
            $request->photo->move(public_path() . '/assets/images/user/', $fileNameToStore);

            $media->name = $fileNameToStore;
            $media->url = "/assets/images/user/$fileNameToStore";
            $media->save();
        }

        $user = User::with('photo')->find($user->id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'flag' => 'required|string',
            'wallet_address' => 'required|string',
            'token' => 'required|string',
        ]);

        $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        $snoozeend = Carbon::now()->addDays(30)->format('d-m-Y');

        $decode = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $request->token)[1]))));

        if ($decode->wallet_address != $request->wallet_address) {
            return response()->json([
                'status' => 'failed',
                'message' => 'invalid Wallet Address',
            ], 400);
        }

        if ($request->flag == 'snooze') {
            $request->validate([
                'snooze_ads' => 'required|integer',
            ]);

            $bl = Blacklisted::where('walletaddress', $request->wallet_address)->where('is_subscribe', 2)->first();

            if (!$bl) {
                $bl = new Blacklisted;
                $bl->walletaddress = $request->wallet_address;
                $bl->is_subscribe =  $request->is_subscribe;
            }


            $bl->is_subscribe = $request->is_subscribe;
            $bl->snooze_ads = $request->snooze_ads;
            $bl->campaign_id = $request->id;
            $bl->save();
        }

        if ($request->flag == 'subscribe') {
            $request->validate([
                'is_subscribe' => 'required|integer',
            ]);

            if ($request->is_subscribe == 1) {
                $bl = Blacklisted::where('walletaddress', $request->wallet_address)->where('is_subscribe', 1)->first();
            }

            if ($request->is_subscribe == 0) {
                $bl = Blacklisted::where('walletaddress', $request->wallet_address)->where('is_subscribe', 0)->where('campaign_id', $request->id)->first();
            }


            if (!$bl) {
                $bl = new Blacklisted;
                $bl->walletaddress = $request->wallet_address;
            }

            $bl->campaign_id = $request->id;
            $bl->is_subscribe = $request->is_subscribe;
            $bl->save();
        }

        $blacklisted = Blacklisted::where('walletaddress', $request->wallet_address)->where('campaign_id', $request->id)->where('is_subscribe', $request->is_subscribe)->first();

        if ($blacklisted->is_subscribe == 1) {

            $check = Blacklisted::where('walletaddress', $request->wallet_address)->wherein('is_subscribe', [0, 2])->get();

            foreach ($check as $c) {
                $delete_entry = $environment->getEntry($c->entry_id);
                $delete_entry->unpublish();
                $delete_entry->delete();

                $c->delete();
            }

            $subscribe = new Entry('subscribedWallets');
            $subscribe->setField('walletAddress', 'en-US', $blacklisted->walletaddress);
            $subscribe->setField('termsAccepted', 'en-US', true);
            $subscribe->setField('campaignid2', 'en-US', $blacklisted->campaign_id);
            $environment->create($subscribe);

            $entry_id = $subscribe->getId();
            $entry_subscribe = $environment->getEntry($entry_id);
            $entry_subscribe->publish();

            $blacklisted->entry_id = $entry_id;
            $blacklisted->save();
        }

        if ($blacklisted->is_subscribe == 0) {

            if (!$blacklisted->entry_id) {
                $newblacklisted = new Entry('blacklistedWalletAddress');
                $newblacklisted->setField('walletAddress', 'en-US', $blacklisted->walletaddress);
                $newblacklisted->setField('status', 'en-US', 'unsubscribed');
                $newblacklisted->setField('terms', 'en-US', true);
                $newblacklisted->setField('campaignId', 'en-US', $blacklisted->campaign_id);
                $environment->create($newblacklisted);

                $entry_id = $newblacklisted->getId();
                $entry_blacklisted = $environment->getEntry($entry_id);
                $entry_blacklisted->publish();

                $blacklisted->entry_id = $entry_id;
                $blacklisted->save();
            }
        }


        if ($blacklisted->is_subscribe == 2) {

            $snoozed = Blacklisted::where('walletaddress', $request->wallet_address)->where('is_subscribe', 2)->first();

            if($snoozed->entry_id){
                $entry_snoozed = $environment->getEntry($snoozed->entry_id);
                $entry_snoozed->setField('snoozeEnd', 'en-US', $snoozeend);
                $entry_snoozed->setField('campaignId', 'en-US', $snoozed->campaign_id);
                $entry_snoozed->update();
                $entry_snoozed->publish();
            }

            if (!$snoozed->entry_id) {
                $snooze = new Entry('snoozeWalletAddress');
                $snooze->setField('walletAddress', 'en-US', $blacklisted->walletaddress);
                $snooze->setField('status', 'en-US', 'snoozed');
                $snooze->setField('terms', 'en-US', true);
                $snooze->setField('campaignId', 'en-US', $blacklisted->campaign_id);
                $snooze->setField('snoozeEnd', 'en-US', $snoozeend);
                $environment->create($snooze);

                $entry_id = $snooze->getId();
                $entry_blacklisted = $environment->getEntry($entry_id);
                $entry_blacklisted->publish();

                $snoozed->entry_id = $entry_id;
                $snoozed->save();

                $id = $snoozed->id;

                DeleteSnoozeRecord::dispatch($id)->delay(now()->addDays(30));
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $bl
        ], 200);
    }

    public function voucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'budget' => 'required',
        ]);

        // is code valid -------
        $voucher = Voucher::where('code', $request->code)->where('status', 2)->first();

        if (!$voucher) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Your entered promo code is not known.',
            ], 404);
        }
        // --------------
        if ($voucher->code == 'COUPONMASTER22') {
            return response()->json([
                'status' => 'success',
                'message' => 'Your entered master promo code is valid.',
            ], 200);
        }
        // // is min budget ----
        // $campaign = Campaign::find($request->campaign_id);

        // if (!$campaign) {
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => 'Your campaign is not known.',
        //     ], 404);
        // }

        // $userBudget = Audience::where('campaign_id', $campaign->id)->sum('price');

        if ($request->budget < $voucher->min_budget) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Minimum amount not reached.',
            ], 400);
        }
        // -------------

        //if alreadyhascampaign pay with cc

        $alreadyhascampaign = Campaign::where('user_id', auth('sanctum')->user()->id)->where('payment_method', 'Card')->first();
        $paymentcheck = StripePayment::where('email', auth('sanctum')->user()->email)->where('status', 1)->first();

        if ($alreadyhascampaign && $paymentcheck) {
            return response()->json([
                'status' => 'failed',
                'message' =>  'This promo code is only for new users.',
            ], 400);
        }

        //if alreadyhascampaign pay with crypto
        $campaigncrypto = Campaign::where('user_id', auth('sanctum')->user()->id)->where('payment_method', 'Cryptocurrencies')->first();
        if ($campaigncrypto) {
            return response()->json([
                'status' => 'failed',
                'message' =>  'This promo code is only for new users.',
            ], 400);
        }
        // // is new user -----
        //     $userVoucher = UserVoucher::where('user_id', auth('sanctum')->user()->id)
        //         ->where('type', 1)
        //         ->first();

        //     if ($userVoucher) {
        //         return response()->json([
        //             'status' => 'failed',
        //             'message' => 'This promo code is only for new users.',
        //         ], 400);
        //     }


        // -----------

        $uv = new UserVoucher;
        $uv->user_id = auth('sanctum')->user()->id;
        $uv->voucher_id = $voucher->id;
        // $uv->campaign_id = $campaign->id;
        $uv->type = 1;
        $uv->status = 1;
        $uv->save();

        $data = (object) array(
            'code' => $voucher->code,
            'amount' => $voucher->value
        );

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}
