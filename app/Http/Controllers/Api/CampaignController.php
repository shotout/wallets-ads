<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Ads;
use App\Models\Media;
use App\Models\AdsPage;
use App\Models\Audience;
use App\Models\Campaign;
use App\Models\DetailTarget;
use Illuminate\Http\Request;
use App\Models\BalanceTarget;
use App\Models\OptimizeTarget;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\SendCampaignNotificationEmail;
use App\Jobs\SendInvoiceEmail;
use App\Jobs\SendNotifRegister;
use App\Jobs\SendResetEmail;
use App\Jobs\SendScheduleCampaign;
use App\Jobs\UpdateCryptoPaymet;
use App\Jobs\UpdateShowStatus;
use App\Jobs\UploadCampaignToContentful;
use App\Models\Invoice;
use App\Models\User;
use Contentful\Management\Client;
use Contentful\Management\Resource\Asset;
use Contentful\Management\Resource\Entry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use JsonMapper\LaravelPackage\JsonMapper;
use GrahamCampbell\Markdown\Facades\Markdown;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('length') && $request->input('length') != '') {
            $length = $request->input('length');
        } else {
            $length = 10;
        }

        if ($request->has('column') && $request->input('column') != '') {
            $column = $request->input('column');
        } else {
            $column = 'id';
        }

        if ($request->has('dir') && $request->input('dir') != '') {
            $dir = $request->input('dir');
        } else {
            $dir = 'desc';
        }

        $query = Campaign::where('user_id', auth('sanctum')->user()->id)
            // ->with('audiences','adsPage','ads')
            ->orderBy($column, $dir);

        if ($request->has('status') && $request->input('status') != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->input('search') != '') {
            $query->where(function ($q) use ($request) {
                $q->where('field1', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('field2', 'like', '%' . $request->input('search') . '%');
            });
        }

        $campaigns = $query->where('is_show', '1')->paginate($length);

        // $counter = (object) array(
        //     "airdrop" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_airdrop'),
        //     "click" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_click'),
        //     "mint" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_mint'),
        // );

        return response()->json([
            'status' => 'success',
            'data' => $campaigns
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:200',
            'campaign_start_date' => 'required',
            'campaign_end_date_type' => 'required',
        ]);

        $campaign = DB::transaction(function () use ($request) {

            $campaign = new Campaign;
            $campaign->user_id = auth('sanctum')->user()->id;
            $campaign->name = $request->campaign_name;
            $campaign->start_date = $request->campaign_start_date;

            $campaign->type = $request->campaign_end_date_type;
            if ($request->campaign_end_date_type == 1) {
                $campaign->end_date = Carbon::parse($request->campaign_start_date)->addDay(90);
                $campaign->availability = '90';
            }
            if ($request->campaign_end_date_type == 2) {
                $campaign->end_date = Carbon::parse($request->campaign_start_date)->addDay(21);
                $campaign->availability = '21';
            }
            if ($request->campaign_end_date_type == 3) {
                $campaign->end_date = Carbon::parse($request->campaign_start_date)->addDay($request->campaign_end_date_day);
                $campaign->day = $request->campaign_end_date_day;
                $campaign->availability = $request->campaign_end_date_day;
            }
            // if ($request->campaign_end_date_type == 2) {
            //     $campaign->end_date = $request->campaign_end_date;
            // }
            $campaign->payment_method = 'Card';
            $campaign->status = 1;
            $campaign->is_show = 1;
            $wallets[] = $request->wallet_address;
            $campaign->sample_address = json_encode($wallets);
            $campaign->save();

            if ($request->has('campaign_audiences') && count($request->campaign_audiences) > 0) {
                foreach ($request->campaign_audiences as $i => $audience) {
                    $audience = (object) $audience;

                    if (isset($audience->price) && isset($audience->price_airdrop) && isset($audience->total_user)) {

                        $adc = new Audience;
                        $adc->campaign_id = $campaign->id;
                        if (isset($audience->fe_id)) {
                            $adc->fe_id = $audience->fe_id;
                        }
                        $adc->name = "Audience " . $i + 1;
                        if (isset($audience->price)) {
                            $adc->price = $audience->price;
                        }
                        if (isset($audience->price_airdrop)) {
                            $adc->price_airdrop = $audience->price_airdrop;
                        }
                        if (isset($audience->total_user)) {
                            $adc->total_user = $audience->total_user;
                        }
                        if (isset($audience->selected_fe_id)) {
                            $adc->selected_fe_id = $audience->selected_fe_id;
                        }
                        $adc->save();

                        // $optimizeTarget = new OptimizeTarget;
                        // $optimizeTarget->audience_id = $adc->id;
                        // $optimizeTarget->price = $audience->optimized_targeting_price;
                        // $optimizeTarget->description = $audience->optimized_targeting_description;
                        // $optimizeTarget->save();

                        // $balanceTarget = new BalanceTarget;
                        // $balanceTarget->audience_id = $adc->id;
                        // $balanceTarget->price = $audience->balanced_targeting_price;
                        // $balanceTarget->description = $audience->balanced_targeting_description;
                        // $balanceTarget->cryptocurrency_used = $audience->balanced_targeting_cryptocurrency;
                        // $balanceTarget->account_age_year = $audience->balanced_targeting_year;
                        // $balanceTarget->account_age_month = $audience->balanced_targeting_month;
                        // $balanceTarget->account_age_day = $audience->balanced_targeting_day;
                        // $balanceTarget->airdrops_received = $audience->balanced_targeting_airdrops;

                        // if (isset($audience->balanced_targeting_wallet) && $audience->balanced_targeting_wallet != '') {
                        //     $balanceTarget->wallet_type = $audience->balanced_targeting_wallet;
                        // }
                        // if (isset($audience->balanced_targeting_location) && $audience->balanced_targeting_location != '') {
                        //     $balanceTarget->location = $audience->balanced_targeting_location;
                        // }

                        // $balanceTarget->save();

                        $detailTarget = new DetailTarget;
                        $detailTarget->audience_id = $adc->id;
                        $detailTarget->campaign_id = $campaign->id;
                        // $detailTarget->price = $audience->detailed_targeting_price;
                        // $detailTarget->description = $audience->detailed_targeting_description;
                        if (isset($audience->detailed_targeting_cryptocurrency)) {
                            $detailTarget->cryptocurrency_used = $audience->detailed_targeting_cryptocurrency;
                        }
                        if (isset($audience->detailed_targeting_year)) {
                            $detailTarget->account_age_year = $audience->detailed_targeting_year;
                        }
                        if (isset($audience->detailed_targeting_month)) {
                            $detailTarget->account_age_month = $audience->detailed_targeting_month;
                        }
                        if (isset($audience->detailed_targeting_day)) {
                            $detailTarget->account_age_day = $audience->detailed_targeting_day;
                        }

                        if (isset($audience->detailed_targeting_available_credit_wallet)) {
                            $detailTarget->available_credit_wallet = $audience->detailed_targeting_available_credit_wallet;
                        }
                        if (isset($audience->detailed_targeting_trading_volume)) {
                            $detailTarget->trading_volume = $audience->detailed_targeting_trading_volume;
                        }
                        if (isset($audience->detailed_targeting_airdrops)) {
                            $detailTarget->airdrops_received = $audience->detailed_targeting_airdrops;
                        }

                        if (isset($audience->detailed_targeting_amount_transaction)) {
                            $detailTarget->amount_transaction = $audience->detailed_targeting_amount_transaction;
                        }
                        if (isset($audience->detailed_targeting_amount_transaction_day)) {
                            $detailTarget->amount_transaction_day = $audience->detailed_targeting_amount_transaction_day;
                        }
                        if (isset($audience->detailed_targeting_nft_purchases)) {
                            $detailTarget->nft_purchases = $audience->detailed_targeting_nft_purchases;
                        }
                        $detailTarget->save();

                        if (isset($audience->file) && $audience->file != '') {
                            $filename = uniqid();
                            $originalname = $audience->file->getClientOriginalName();
                            $fileExt = $audience->file->getClientOriginalExtension();
                            $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                            $audience->file->move(public_path() . '/assets/files/audience/', $fileNameToStore);

                            // $file_parts = explode(";base64,", $audience->file);
                            // $file_type_aux = explode("@file/", $file_parts[0]);
                            // $file_type = $file_type_aux[1];
                            // $file_base64 = base64_decode($file_parts[1]);
                            // $fileNameToStore = uniqid() . '_' . time() . '.xlsx';
                            // $fileURL = "/assets/files/audience/" . $fileNameToStore;
                            // Storage::disk('public_uploads')->put($fileURL, $file_base64);

                            $media = new Media;
                            $media->owner_id = $adc->id;
                            $media->type = "audience_file";
                            $media->name = $fileNameToStore;
                            $media->original_name = $originalname;
                            $media->url = '/assets/files/audience/' . $fileNameToStore;
                            $media->save();
                        }
                    }
                }
            }

            $adsPage = new AdsPage;
            $adsPage->campaign_id = $campaign->id;
            $adsPage->name = $request->ads_page_name;
            $adsPage->description = $request->ads_page_description;
            $adsPage->website = $request->ads_page_website;
            $adsPage->discord = $request->ads_page_discord;
            $adsPage->twitter = $request->ads_page_twitter;
            $adsPage->instagram = $request->ads_page_instagram;
            $adsPage->medium = $request->ads_page_medium;
            $adsPage->facebook = $request->ads_page_facebook;
            $adsPage->telegram = $request->ads_page_telegram;
            $adsPage->external_page = $request->ads_page_external_page;
            $adsPage->token_name = $request->ads_page_token_name;
            $adsPage->token_symbol = $request->ads_page_token_symbol;
            $adsPage->save();

            if ($request->has('ads_page_logo_url') && $request->ads_page_logo_url != '') {
                $media = Media::where('url', $request->ads_page_logo_url)->first();
                if ($media) {
                    $media->owner_id = $adsPage->id;
                    $media->save();
                }
            } elseif ($request->has('ads_page_logo') && $request->ads_page_logo != '') {
                $filename = uniqid();
                $fileExt = $request->ads_page_logo->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                $request->ads_page_logo->move(public_path() . '/assets/images/logo/', $fileNameToStore);

                // $image_parts = explode(";base64,", $request->ads_page_logo);
                // $image_type_aux = explode("image/", $image_parts[0]);
                // $image_type = $image_type_aux[1];
                // $image_base64 = base64_decode($image_parts[1]);
                // $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                // $fileURL = "/assets/images/logo/" . $fileNameToStore;
                // Storage::disk('public_uploads')->put($fileURL, $image_base64);

                $media = new Media;
                $media->owner_id = $adsPage->id;
                $media->type = "ads_logo";
                $media->name = $fileNameToStore;
                $media->url = '/assets/images/logo/' . $fileNameToStore;
                $media->save();
            }

            if ($request->has('ads_page_banner_url') && $request->ads_page_banner_url != '') {
                $media = Media::where('url', $request->ads_page_banner_url)->first();
                if ($media) {
                    $media->owner_id = $adsPage->id;
                    $media->save();
                }
            } elseif ($request->has('ads_page_banner') && $request->ads_page_banner != '') {
                $filename = uniqid();
                $fileExt = $request->ads_page_banner->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                $request->ads_page_banner->move(public_path() . '/assets/images/banner/', $fileNameToStore);

                // $image_parts = explode(";base64,", $request->ads_page_banner);
                // $image_type_aux = explode("image/", $image_parts[0]);
                // $image_type = $image_type_aux[1];
                // $image_base64 = base64_decode($image_parts[1]);
                // $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                // $fileURL = "/assets/images/banner/" . $fileNameToStore;
                // Storage::disk('public_uploads')->put($fileURL, $image_base64);

                $media = new Media;
                $media->owner_id = $adsPage->id;
                $media->type = "ads_banner";
                $media->name = $fileNameToStore;
                $media->url = '/assets/images/banner/' . $fileNameToStore;
                $media->save();
            }

            if ($request->has('campaign_ads') && count($request->campaign_ads) > 0) {
                foreach ($request->campaign_ads as $ads) {
                    $ads = (object) $ads;

                    $newAds = new Ads;
                    $newAds->campaign_id = $campaign->id;
                    if (isset($ads->name)) {
                        $newAds->name = $ads->name;
                    }
                    if (isset($ads->description)) {
                        $newAds->description = $ads->description;
                    }
                    $newAds->save();

                    if (count($ads->fe_id) > 0) {
                        foreach ($ads->fe_id as $id) {
                            $audience = Audience::where('campaign_id', $campaign->id)
                                ->where('fe_id', $id)
                                ->first();

                            if ($audience) {
                                $audience->ads_id = $newAds->id;
                                // $audience->fe_id = null;
                                $audience->update();
                            }
                        }
                    }

                    if (isset($ads->image_url) && $ads->image_url != '') {
                        $media = Media::where('url', $ads->image_url)->first();
                        if ($media) {
                            $media->owner_id = $newAds->id;
                            $media->save();
                        }
                    } elseif (isset($ads->image) && $ads->image != '') {
                        // $image_parts = explode(";base64,", $ads->image);
                        // $image_type_aux = explode("image/", $image_parts[0]);
                        // $image_type = $image_type_aux[1];
                        // $image_base64 = base64_decode($image_parts[1]);
                        // $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                        // $fileURL = "/assets/images/nft/" . $fileNameToStore;
                        // Storage::disk('public_uploads')->put($fileURL, $image_base64);

                        $filename = uniqid();
                        $fileExt = $ads->image->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                        $ads->image->move(public_path() . '/assets/images/nft/', $fileNameToStore);

                        $media = new Media;
                        $media->owner_id = $newAds->id;
                        $media->type = "ads_nft";
                        $media->name = $fileNameToStore;
                        $media->url = '/assets/images/nft/' . $fileNameToStore;
                        $media->save();
                    }
                }
            }

            return $campaign;
        });

        //start upload campaign to contenful
        UploadCampaignToContentful::dispatch($campaign)->delay(Carbon::now()->addSeconds(60));

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ], 201);
    }

    public function show($id)
    {
        $campaign = Campaign::where('user_id', auth('sanctum')->user()->id)
            ->where('id', $id)
            ->with('audiences', 'adsPage', 'ads')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:200',
            'campaign_start_date' => 'required',
            'campaign_end_date_type' => 'required',
        ]);

        $campaign = DB::transaction(function () use ($request, $id) {

            $campaign = Campaign::where('user_id', auth('sanctum')->user()->id)
                ->where('id', $id)
                ->with('audiences', 'adsPage', 'ads')
                ->first();

            if (!$campaign) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'data not found'
                ], 404);
            }
         

            $campaign->user_id = auth('sanctum')->user()->id;
            $campaign->name = $request->campaign_name;
            $campaign->start_date = $request->campaign_start_date;

            if (isset($request->status)) {
                $campaign->status = $request->status;
            }

            $campaign->type = $request->campaign_end_date_type;
            if ($request->campaign_end_date_type == 1) {
                $campaign->end_date = Carbon::parse($request->campaign_start_date)->addDay(90);
                $campaign->availability = '90';
            }
            if ($request->campaign_end_date_type == 2) {
                $campaign->end_date = Carbon::parse($request->campaign_start_date)->addDay(21);
                $campaign->availability = '21';
            }
            if ($request->campaign_end_date_type == 3) {
                $campaign->end_date = Carbon::parse($request->campaign_start_date)->addDay($request->campaign_end_date_day);
                $campaign->day = $request->campaign_end_date_day;
                $campaign->availability = $request->campaign_end_date_day;
            }

            $campaign->is_show = 1;
            $wallets[] = $request->wallet_address;
            $campaign->sample_address = json_encode($wallets);
            $campaign->update();

            if ($request->has('campaign_audiences') && count($request->campaign_audiences) > 0) {
                Audience::where('campaign_id', $campaign->id)->delete();

                foreach ($request->campaign_audiences as $audience) {
                    $audience = (object) $audience;

                    if (isset($audience->price) && isset($audience->price_airdrop) && isset($audience->total_user)) {

                        if (isset($audience->fe_id) && $audience->fe_id != '') {
                            $adc = new Audience;
                            if (isset($audience->fe_id)) {
                                $adc->fe_id = $audience->fe_id;
                            }

                            $counter = Audience::where('campaign_id', $campaign->id)->count();
                            $adc->name = "Audience " . $counter + 1;
                        } else {
                            $adc = Audience::find($audience->id);
                        }

                        $adc->campaign_id = $campaign->id;
                        if (isset($audience->price)) {
                            $adc->price = $audience->price;
                        }
                        if (isset($audience->price_airdrop)) {
                            $adc->price_airdrop = $audience->price_airdrop;
                        }
                        if (isset($audience->total_user)) {
                            $adc->total_user = $audience->total_user;
                        }
                        $adc->save();

                        if (isset($audience->fe_id) && $audience->fe_id != '') {
                            $detailTarget = new DetailTarget;
                            $detailTarget->audience_id = $adc->id;
                            $detailTarget->campaign_id = $campaign->id;
                        } else {
                            $detailTarget = DetailTarget::where('audience_id', $adc->id)->first();
                        }
                        if (isset($audience->detailed_targeting_cryptocurrency)) {
                            $detailTarget->cryptocurrency_used = $audience->detailed_targeting_cryptocurrency;
                        }
                        if (isset($audience->detailed_targeting_year)) {
                            $detailTarget->account_age_year = $audience->detailed_targeting_year;
                        }
                        if (isset($audience->detailed_targeting_month)) {
                            $detailTarget->account_age_month = $audience->detailed_targeting_month;
                        }
                        if (isset($audience->detailed_targeting_day)) {
                            $detailTarget->account_age_day = $audience->detailed_targeting_day;
                        }

                        if (isset($audience->detailed_targeting_available_credit_wallet)) {
                            $detailTarget->available_credit_wallet = $audience->detailed_targeting_available_credit_wallet;
                        }
                        if (isset($audience->detailed_targeting_trading_volume)) {
                            $detailTarget->trading_volume = $audience->detailed_targeting_trading_volume;
                        }
                        if (isset($audience->detailed_targeting_airdrops)) {
                            $detailTarget->airdrops_received = $audience->detailed_targeting_airdrops;
                        }

                        if (isset($audience->detailed_targeting_amount_transaction)) {
                            $detailTarget->amount_transaction = $audience->detailed_targeting_amount_transaction;
                        }
                        if (isset($audience->detailed_targeting_amount_transaction_day)) {
                            $detailTarget->amount_transaction_day = $audience->detailed_targeting_amount_transaction_day;
                        }
                        if (isset($audience->detailed_targeting_nft_purchases)) {
                            $detailTarget->nft_purchases = $audience->detailed_targeting_nft_purchases;
                        }
                        $detailTarget->save();

                        if (isset($audience->file) && $audience->file != '') {
                            $media = Media::where('owner_id', $adc->id)
                                ->where('type', 'audience_file')
                                ->first();

                            if ($media) {
                                unlink(public_path() . $media->url);
                            } else {
                                $media = new Media;
                                $media->owner_id = $adc->id;
                                $media->type = "audience_file";
                            }

                            $filename = uniqid();
                            $fileExt = $audience->file->getClientOriginalExtension();
                            $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                            $audience->file->move(public_path() . '/assets/files/audience/', $fileNameToStore);

                            $media->name = $fileNameToStore;
                            $media->url = '/assets/files/audience/' . $fileNameToStore;
                            $media->save();
                        }
                    }
                }
            }

            $adsPage = AdsPage::where('campaign_id', $campaign->id)->first();
            $adsPage->name = $request->ads_page_name;
            $adsPage->description = $request->ads_page_description;
            if($request->has('ads_page_website') && $request->ads_page_website != ''){
                $adsPage->website = $request->ads_page_website;
            }
            if($request->has('ads_page_discord') && $request->ads_page_discord != ''){
                $adsPage->discord = $request->ads_page_discord;
            }
            if($request->has('ads_page_twitter') && $request->ads_page_twitter != ''){
                $adsPage->twitter = $request->ads_page_twitter;
            }
            if($request->has('ads_page_instagram') && $request->ads_page_instagram != ''){
                $adsPage->instagram = $request->ads_page_instagram;
            }
            if($request->has('ads_page_medium') && $request->ads_page_medium != ''){
                $adsPage->medium = $request->ads_page_medium;
            }
            if($request->has('ads_page_facebook') && $request->ads_page_facebook != ''){
                $adsPage->facebook = $request->ads_page_facebook;
            }
            if($request->has('ads_page_external_page') && $request->ads_page_external_page != ''){
                $adsPage->external_page = $request->ads_page_external_page;
            }
            if($request->has('ads_page_token_name') && $request->ads_page_token_name != ''){
                $adsPage->token_name = $request->ads_page_token_name;
            }
            if($request->has('ads_page_token_symbol') && $request->ads_page_token_symbol != ''){
                $adsPage->token_symbol = $request->ads_page_token_symbol;
            }
            
            $adsPage->save();

            if ($request->has('ads_page_logo_url') && $request->ads_page_logo_url != '') {
                $media = Media::where('owner_id', $adsPage->id)
                    ->where('type', 'ads_logo')
                    ->first();

                if ($media) {
                    // unlink(public_path() . $media->url);
                } else {
                    $media = Media::where('url', $request->ads_page_logo_url)->first();
                    if ($media) {
                        $media->owner_id = $adsPage->id;
                        $media->save();
                    }
                }
            } elseif ($request->has('ads_page_logo') && $request->ads_page_logo != '') {
                $media = Media::where('owner_id', $adsPage->id)->where('type', 'ads_logo')->first();
                if ($media) {
                    // unlink(public_path() . $media->url);
                } else {
                    $media = new Media;
                    $media->owner_id = $adsPage->id;
                    $media->type = "ads_logo";
                }
                if (gettype($request->ads_page_logo) != 'string') {
                    $filename = uniqid();
                    $fileExt = $request->ads_page_logo->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                    $request->ads_page_logo->move(public_path() . '/assets/images/logo/', $fileNameToStore);

                    $media->name = $fileNameToStore;
                    $media->url = '/assets/images/logo/' . $fileNameToStore;
                    $media->save();
                }
            }

            if ($request->has('ads_page_banner_url') && $request->ads_page_banner_url != '') {
                $media = Media::where('owner_id', $adsPage->id)
                    ->where('type', 'ads_banner')
                    ->first();

                if ($media) {
                    // unlink(public_path() . $media->url);
                } else {
                    $media = Media::where('url', $request->ads_page_banner_url)->first();
                    if ($media) {
                        $media->owner_id = $adsPage->id;
                        $media->save();
                    }
                }
            } elseif ($request->has('ads_page_banner') && $request->ads_page_banner != '') {
                $media = Media::where('owner_id', $adsPage->id)->where('type', 'ads_banner')->first();
                if ($media) {
                    // unlink(public_path() . $media->url);
                } else {
                    $media = new Media;
                    $media->owner_id = $adsPage->id;
                    $media->type = "ads_banner";
                }

                if (gettype($request->ads_page_banner) != 'string') {
                    $filename = uniqid();
                    $fileExt = $request->ads_page_banner->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                    $request->ads_page_banner->move(public_path() . '/assets/images/banner/', $fileNameToStore);

                    $media->name = $fileNameToStore;
                    $media->url = '/assets/images/banner/' . $fileNameToStore;
                    $media->save();
                }
            }

            if ($request->has('campaign_ads') && count($request->campaign_ads) > 0) {
                foreach ($request->campaign_ads as $ads) {
                    $ads = (object) $ads;

                    if (isset($ads->id)) {
                        $oldAds = Ads::find($ads->id);
                    } else {
                        $oldAds = new Ads;
                        $oldAds->campaign_id = $campaign->id;
                    }

                    $oldAds->name = $ads->name;
                    $oldAds->description = $ads->description;
                    $oldAds->save();

                    if (isset($ads->audience_id) && count($ads->audience_id) > 0) {
                        foreach ($ads->audience_id as $adc_id) {
                            $audience = Audience::find($adc_id);

                            if ($audience) {
                                $audience->ads_id = $oldAds->id;
                                $audience->update();
                            }
                        }
                    }

                    if (isset($ads->fe_id) && count($ads->fe_id) > 0) {
                        foreach ($ads->fe_id as $fe_id) {
                            $audience = Audience::where('campaign_id', $campaign->id)
                                ->where('fe_id', $fe_id)
                                ->first();

                            if ($audience) {
                                $audience->ads_id = $oldAds->id;
                                $audience->fe_id = null;
                                $audience->update();
                            }
                        }
                    }

                    if (isset($ads->image_url) && $ads->image_url != '') {
                        $media = Media::where('owner_id', $oldAds->id)
                            ->where('type', 'ads_nft')
                            ->first();

                        if ($media) {
                            unlink(public_path() . $media->url);
                        } else {
                            $media = Media::where('url', $ads->image_url)->first();
                            if ($media) {
                                $media->owner_id = $oldAds->id;
                                $media->save();
                            }
                        }
                    } elseif (isset($ads->image) && $ads->image != '') {
                        $media = Media::where('owner_id', $oldAds->id)->where('type', 'ads_nft')->first();
                        if ($media) {
                            // unlink(public_path() . $media->url);
                        } else {
                            $media = new Media;
                            $media->owner_id = $oldAds->id;
                            $media->type = "ads_nft";
                        }
                        if (gettype($ads->image) != 'string') {
                            $filename = uniqid();
                            $fileExt = $ads->image->getClientOriginalExtension();
                            $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                            $ads->image->move(public_path() . '/assets/images/nft/', $fileNameToStore);

                            $media->name = $fileNameToStore;
                            $media->url = "/assets/images/nft/$fileNameToStore";
                            $media->save();
                        }
                    }
                }
            }

            return $campaign;
        });

        $data = Campaign::with('audiences', 'adsPage', 'ads')->find($campaign->id);

        $campaign = Campaign::find($campaign->id);
        UploadCampaignToContentful::dispatch($campaign)->delay(Carbon::now()->addSeconds(60));

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function singleUpload(Request $request)
    {
        $request->validate([
            'upload' => 'required',
            'type' => 'required',
        ]);

        if ($request->hasFile('upload')) {
            if ($request->has('type') && $request->type != '') {
                $filename = uniqid();
                $fileExt = $request->upload->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $fileExt;
                $request->upload->move(public_path() . '/assets/images/' . $request->type . '/', $fileNameToStore);

                $media = new Media;
                $media->type = "ads_" . $request->type;
                $media->name = $fileNameToStore;
                $media->url = '/assets/images/' . $request->type . '/' . $fileNameToStore;
                $media->save();

                return response()->json([
                    'status' => 'success',
                    'data' => $media->url
                ], 201);
            }
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Bad request'
        ], 400);
    }

    public function paymethod(Request $request)
    {
        $campaign_id = $request->campaign_id;
        if ($campaign_id) {

            $campaign = Campaign::find($campaign_id);
            $campaign->payment_method = 'Cryptocurrencies';

            if (isset($request->promo)) {
                $campaign->promo_code = $request->promo;
                $promo_code = $request->promo;
            }

            $campaign->save();

            UpdateCryptoPaymet::dispatch($campaign_id);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment method updated successfully'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong'
        ], 400);
    }


    public function invoices()
    {
        $invoices = Invoice::where('user_id', auth('sanctum')->user()->id)->get();

        $adtext = ads::where('campaign_id', '471')->get()->toArray();
        $adtext = json_decode($adtext[0]['description'], true);
        $adtext[0]['adtext'];
        $i = 1;
        foreach ($adtext as $key => $value) {
            $multiple[] = $value['adtext'];
        }

        foreach ($multiple as $key => $value) {
            $test = '|||Ad text ' . $i . ':';
            $ad_text[] = $test;
            $ad_text[] = Markdown::convertToHtml($multiple[$key]);
            $i++;
        }


        $tes = nl2br(' hello, "\n" 
        this is a test "\n" thanks, "\n" test');

        return response()->json([
            'status' => 'success',
            'data' => $invoices,
            'ad_text' => $ad_text,
            'tes' => $tes
        ], 200);
    }


    public function cancelStripe(Request $request)
    {
        $campaign_id = $request->campaign_id;

        if ($campaign_id) {
            $campaign = Campaign::find($campaign_id);
            $campaign->is_show = '0';
            $campaign->save();

            $entry_id = $campaign->entry_id;

            UpdateShowStatus::dispatch($entry_id)->delay(now()->addSeconds(70));

            return response()->json([
                'status' => 'success'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong'
        ], 400);
    }
}
