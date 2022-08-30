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
            ->with('audiences','adsPage','ads')
            ->orderBy($column, $dir);

        if ($request->has('status') && $request->input('status') != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->input('search') != '') {
            $query->where(function($q) use($request) {
                $q->where('field1', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('field2', 'like', '%' . $request->input('search') . '%');
            });
        }

        $campaigns = $query->paginate($length);

        // $counter = (object) array(
        //     "airdrop" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_airdrop'),
        //     "click" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_click'),
        //     "mint" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_mint'),
        // );

        return response()->json([
            'status' => 'success',
            'data' => $campaigns
        ]);    
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
                $campaign->end_date = Carbon::now()->addDay(7);
            } else if ($request->campaign_end_date_type == 2) {
                $campaign->end_date = $request->campaign_end_date;
            }
            $campaign->status = 1;
            $campaign->save();

            if ($request->has('campaign_audiences') && count($request->campaign_audiences) > 0) {
                foreach ($request->campaign_audiences as $i => $audience) {
                    $audience = (object) $audience;

                    $adc = new Audience;
                    $adc->campaign_id = $campaign->id;
                    $adc->fe_id = $audience->fe_id;
                    $adc->name = "Audience ".$i+1;
                    $adc->price = $audience->price;
                    $adc->save();

                    $optimizeTarget = new OptimizeTarget;
                    $optimizeTarget->audience_id = $adc->id;
                    $optimizeTarget->price = $audience->optimized_targeting_price;
                    $optimizeTarget->description = $audience->optimized_targeting_description;
                    $optimizeTarget->save();

                    $balanceTarget = new BalanceTarget;
                    $balanceTarget->audience_id = $adc->id;
                    $balanceTarget->price = $audience->balanced_targeting_price;
                    $balanceTarget->description = $audience->balanced_targeting_description;
                    $balanceTarget->cryptocurrency_used = $audience->balanced_targeting_cryptocurrency;
                    $balanceTarget->account_age_year = $audience->balanced_targeting_year;
                    $balanceTarget->account_age_month = $audience->balanced_targeting_month;
                    $balanceTarget->account_age_day = $audience->balanced_targeting_day;
                    $balanceTarget->airdrops_received = $audience->balanced_targeting_airdrops;
                    $balanceTarget->wallet_type = $audience->balanced_targeting_wallet;
                    $balanceTarget->location = $audience->balanced_targeting_location;
                    $balanceTarget->save();

                    $detailTarget = new DetailTarget;
                    $detailTarget->audience_id = $adc->id;
                    $detailTarget->price = $audience->detailed_targeting_price;
                    $detailTarget->description = $audience->detailed_targeting_description;
                    $detailTarget->amount_transaction = $audience->detailed_targeting_amount_transaction;
                    $detailTarget->trading_volume = $audience->detailed_targeting_trading_volume;
                    $detailTarget->available_credit_wallet = $audience->detailed_targeting_available_credit_wallet;
                    $detailTarget->nft_purchases = $audience->detailed_targeting_nft_purchases;
                    $detailTarget->save();
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
            $adsPage->external_page = $request->ads_page_external_page;
            $adsPage->save();

            if ($request->hasFile('ads_page_logo')) {
                $filename = uniqid();
                $fileExt = $request->ads_page_logo->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                $request->ads_page_logo->move(public_path().'/assets/images/logo/', $fileNameToStore);

                $media = new Media;
                $media->owner_id = $adsPage->id;
                $media->type = "ads_logo";
                $media->name = $fileNameToStore;
                $media->url = "/assets/images/logo/$fileNameToStore";
                $media->save();
            }

            if ($request->hasFile('ads_page_banner')) {
                $filename = uniqid();
                $fileExt = $request->ads_page_banner->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                $request->ads_page_banner->move(public_path().'/assets/images/banner/', $fileNameToStore);

                $media = new Media;
                $media->owner_id = $adsPage->id;
                $media->type = "ads_banner";
                $media->name = $fileNameToStore;
                $media->url = "/assets/images/banner/$fileNameToStore";
                $media->save();
            }

            if ($request->has('campaign_ads') && count($request->campaign_ads) > 0) {
                foreach ($request->campaign_ads as $ads) {
                    $ads = (object) $ads;

                    $newAds = new Ads;
                    $newAds->campaign_id = $campaign->id;
                    $newAds->name = $ads->name;
                    $newAds->description = $ads->description;
                    $newAds->save();

                    if (count($ads->fe_id) > 0) {
                        foreach ($ads->fe_id as $id) {
                            $audience = Audience::where('fe_id', $id)->first();
                            $audience->ads_id = $newAds->id;
                            $audience->fe_id = null;
                            $audience->update();
                        }
                    }

                    if (isset($ads->image)) {
                        $filename = uniqid();
                        $fileExt = $ads->image->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                        $ads->image->move(public_path().'/assets/images/nft/', $fileNameToStore);
        
                        $media = new Media;
                        $media->owner_id = $newAds->id;
                        $media->type = "ads_nft";
                        $media->name = $fileNameToStore;
                        $media->url = "/assets/images/nft/$fileNameToStore";
                        $media->save();
                    }
                }
            }

            return $campaign;
        });

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ]); 
    }

    public function show($id)
    {
        $campaign = Campaign::where('user_id', auth('sanctum')->user()->id)
            ->where('id', $id)
            ->with('audiences','adsPage','ads')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ]);  
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
                ->with('audiences','adsPage','ads')
                ->first();
            $campaign->user_id = auth('sanctum')->user()->id;
            $campaign->name = $request->campaign_name;
            $campaign->start_date = $request->campaign_start_date;
            $campaign->type = $request->campaign_end_date_type;
            if ($request->campaign_end_date_type == 1) {
                $campaign->end_date = Carbon::now()->addDay(7);
            } else if ($request->campaign_end_date_type == 2) {
                $campaign->end_date = $request->campaign_end_date;
            }
            $campaign->save();

            if ($request->has('campaign_audiences') && count($request->campaign_audiences) > 0) {
                foreach ($request->campaign_audiences as $audience) {
                    $audience = (object) $audience;

                    if (isset($audience->fe_id) && $audience->fe_id != '') {
                        $adc = new Audience;
                        $adc->fe_id = $audience->fe_id;
                    } else {
                        $adc = Audience::find($audience->id);
                    }
                    
                    $adc->campaign_id = $campaign->id;
                    $adc->price = $audience->price;
                    $adc->save();

                    if (isset($audience->fe_id) && $audience->fe_id != '') {
                        $optimizeTarget = new OptimizeTarget;
                        $optimizeTarget->audience_id = $adc->id;
                    } else {
                        $optimizeTarget = OptimizeTarget::where('audience_id', $adc->id)->first();
                    }
                    $optimizeTarget->price = $audience->optimized_targeting_price;
                    $optimizeTarget->description = $audience->optimized_targeting_description;
                    $optimizeTarget->save();

                    if (isset($audience->fe_id) && $audience->fe_id != '') {
                        $balanceTarget = new BalanceTarget;
                        $balanceTarget->audience_id = $adc->id;
                    } else {
                        $balanceTarget = BalanceTarget::where('audience_id', $adc->id)->first();
                    }
                    $balanceTarget->price = $audience->balanced_targeting_price;
                    $balanceTarget->description = $audience->balanced_targeting_description;
                    $balanceTarget->cryptocurrency_used = $audience->balanced_targeting_cryptocurrency;
                    $balanceTarget->account_age_year = $audience->balanced_targeting_year;
                    $balanceTarget->account_age_month = $audience->balanced_targeting_month;
                    $balanceTarget->account_age_day = $audience->balanced_targeting_day;
                    $balanceTarget->airdrops_received = $audience->balanced_targeting_airdrops;
                    $balanceTarget->wallet_type = $audience->balanced_targeting_wallet;
                    $balanceTarget->location = $audience->balanced_targeting_location;
                    $balanceTarget->save();

                    if (isset($audience->fe_id) && $audience->fe_id != '') {
                        $detailTarget = new DetailTarget;
                        $detailTarget->audience_id = $adc->id;
                    } else {
                        $detailTarget = DetailTarget::where('audience_id', $adc->id)->first();
                    }
                    $detailTarget->price = $audience->detailed_targeting_price;
                    $detailTarget->description = $audience->detailed_targeting_description;
                    $detailTarget->amount_transaction = $audience->detailed_targeting_amount_transaction;
                    $detailTarget->trading_volume = $audience->detailed_targeting_trading_volume;
                    $detailTarget->available_credit_wallet = $audience->detailed_targeting_available_credit_wallet;
                    $detailTarget->nft_purchases = $audience->detailed_targeting_nft_purchases;
                    $detailTarget->save();
                }
            }

            $adsPage = AdsPage::where('campaign_id', $campaign->id)->first();
            $adsPage->name = $request->ads_page_name;
            $adsPage->description = $request->ads_page_description;
            $adsPage->website = $request->ads_page_website;
            $adsPage->discord = $request->ads_page_discord;
            $adsPage->twitter = $request->ads_page_twitter;
            $adsPage->instagram = $request->ads_page_instagram;
            $adsPage->medium = $request->ads_page_medium;
            $adsPage->facebook = $request->ads_page_facebook;
            $adsPage->external_page = $request->ads_page_external_page;
            $adsPage->save();

            if ($request->hasFile('ads_page_logo')) {
                $media = Media::where('owner_id', $adsPage->id)->where('type', 'ads_logo')->first();
                if ($media) {
                    unlink(public_path().$media->url);
                }

                $filename = uniqid();
                $fileExt = $request->ads_page_logo->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                $request->ads_page_logo->move(public_path().'/assets/images/logo/', $fileNameToStore);

                $media->name = $fileNameToStore;
                $media->url = "/assets/images/logo/$fileNameToStore";
                $media->save();
            }

            if ($request->hasFile('ads_page_banner')) {
                $media = Media::where('owner_id', $adsPage->id)->where('type', 'ads_banner')->first();
                if ($media) {
                    unlink(public_path().$media->url);
                }

                $filename = uniqid();
                $fileExt = $request->ads_page_banner->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                $request->ads_page_banner->move(public_path().'/assets/images/banner/', $fileNameToStore);

                $media->name = $fileNameToStore;
                $media->url = "/assets/images/banner/$fileNameToStore";
                $media->save();
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
                            $audience->ads_id = $oldAds->id;
                            $audience->update();
                        }
                    }

                    if (isset($ads->fe_id) && count($ads->fe_id) > 0) {
                        foreach ($ads->fe_id as $fe_id) {
                            $audience = Audience::where('fe_id', $fe_id)->first();
                            $audience->ads_id = $oldAds->id;
                            $audience->fe_id = null;
                            $audience->update();
                        }
                    }

                    if (isset($ads->image)) {
                        $media = Media::where('owner_id', $oldAds->id)->where('type', 'ads_nft')->first();
                        if ($media) {
                            unlink(public_path().$media->url);
                        }

                        $filename = uniqid();
                        $fileExt = $ads->image->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                        $ads->image->move(public_path().'/assets/images/nft/', $fileNameToStore);
            
                        $media->name = $fileNameToStore;
                        $media->url = "/assets/images/nft/$fileNameToStore";
                        $media->save();
                    }
                }
            }

            return $campaign;
        });

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ]); 
    }
}
