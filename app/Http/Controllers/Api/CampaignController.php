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
use Contentful\Management\Client;
use Contentful\Management\Resource\Asset;
use Contentful\Management\Resource\Entry;
use Illuminate\Support\Facades\Storage;

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

        $campaigns = $query->paginate($length);

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
                $campaign->end_date = Carbon::now()->addDay(90);
                $campaign->availability = '90';
            }
            if ($request->campaign_end_date_type == 2) {
                $campaign->end_date = Carbon::now()->addDay(21);
                $campaign->availability = '21';
            }
            if ($request->campaign_end_date_type == 3) {
                $campaign->end_date = Carbon::now()->addDay($request->campaign_end_date_day);
                $campaign->day = $request->campaign_end_date_day;
                $campaign->availability = $request->campaign_end_date_day;
            }
            // if ($request->campaign_end_date_type == 2) {
            //     $campaign->end_date = $request->campaign_end_date;
            // }

            $campaign->status = 1;
            $campaign->save();

            if ($request->has('campaign_audiences') && count($request->campaign_audiences) > 0) {
                foreach ($request->campaign_audiences as $i => $audience) {
                    $audience = (object) $audience;

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

                    if (isset($audience->file) && $audience->file != '') {
                        $file_parts = explode(";base64,", $audience->file);
                        $file_type_aux = explode("@file/", $file_parts[0]);
                        $file_type = $file_type_aux[1];
                        $file_base64 = base64_decode($file_parts[1]);
                        $fileNameToStore = uniqid() . '_' . time() . '.xlsx';
                        $fileURL = "/assets/files/audience/" . $fileNameToStore;
                        Storage::disk('public_uploads')->put($fileURL, $file_base64);

                        $media = new Media;
                        $media->owner_id = $adc->id;
                        $media->type = "audience_file";
                        $media->name = $fileNameToStore;
                        $media->url = $fileURL;
                        $media->save();
                    }

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
            $adsPage->telegram = $request->ads_page_telegram;
            $adsPage->external_page = $request->ads_page_external_page;
            $adsPage->save();

            if ($request->has('ads_page_logo') && $request->ads_page_logo != '') {
                // $filename = uniqid();
                // $fileExt = $request->ads_page_logo->getClientOriginalExtension();
                // $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                // $request->ads_page_logo->move(public_path().'/assets/images/logo/', $fileNameToStore);

                $image_parts = explode(";base64,", $request->ads_page_logo);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                $fileURL = "/assets/images/logo/" . $fileNameToStore;
                Storage::disk('public_uploads')->put($fileURL, $image_base64);

                $media = new Media;
                $media->owner_id = $adsPage->id;
                $media->type = "ads_logo";
                $media->name = $fileNameToStore;
                $media->url = $fileURL;
                $media->save();
            }

            if ($request->has('ads_page_banner') && $request->ads_page_banner != '') {
                // $filename = uniqid();
                // $fileExt = $request->ads_page_banner->getClientOriginalExtension();
                // $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                // $request->ads_page_banner->move(public_path().'/assets/images/banner/', $fileNameToStore);

                $image_parts = explode(";base64,", $request->ads_page_banner);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                $fileURL = "/assets/images/banner/" . $fileNameToStore;
                Storage::disk('public_uploads')->put($fileURL, $image_base64);

                $media = new Media;
                $media->owner_id = $adsPage->id;
                $media->type = "ads_banner";
                $media->name = $fileNameToStore;
                $media->url = $fileURL;
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
                            $audience = Audience::where('fe_id', $id)->first();
                            if ($audience) {
                                $audience->ads_id = $newAds->id;
                                $audience->fe_id = null;
                                $audience->update();
                            }
                        }
                    }

                    if (isset($ads->image) && $ads->image != '') {
                        $image_parts = explode(";base64,", $ads->image);
                        $image_type_aux = explode("image/", $image_parts[0]);
                        $image_type = $image_type_aux[1];
                        $image_base64 = base64_decode($image_parts[1]);
                        $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                        $fileURL = "/assets/images/nft/" . $fileNameToStore;

                        Storage::disk('public_uploads')->put($fileURL, $image_base64);

                        // $filename = uniqid();
                        // $fileExt = $ads->image->getClientOriginalExtension();
                        // $fileNameToStore = $filename.'_'.time().'.'.$fileExt;
                        // $ads->image->move(public_path().'/assets/images/nft/', $fileNameToStore);

                        $media = new Media;
                        $media->owner_id = $newAds->id;
                        $media->type = "ads_nft";
                        $media->name = $fileNameToStore;
                        $media->url = $fileURL;
                        $media->save();
                    }
                }
            }

            return $campaign;
        });

        //contentful env
        $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        $newcampaign = Campaign::where('id', $campaign->id)->first();
        $total_budget = Audience::where('campaign_id', $campaign->id)->sum('price');

        //retrieve data from database
        $newadspage = AdsPage::where('campaign_id', $campaign->id)->first();

        $url_logo = Media::where('owner_id', $newadspage->id)->where('type', 'ads_logo')->first();
        $url_banner = Media::where('owner_id', $newadspage->id)->where('type', 'ads_banner')->first();

        $logo = new \Contentful\Core\File\RemoteUploadFile(
            $campaign->name . 'CollectionLogo',
            'JPEG,JPG,PNG',
            'http://127.0.0.1:8000' . $url_logo->url
        );

        $banner = new \Contentful\Core\File\RemoteUploadFile(
            $campaign->name . 'Collection Banner',
            'JPEG,JPG,PNG',
            'http://127.0.0.1:8000' . $url_banner->url
        );

        // Prepare uploadig image
        $asset_logo = new Asset();
        $asset_logo->setTitle('en-US', 'Collection Logo of ' . $campaign->name);
        $asset_logo->setFile('en-US', $logo);

        //process Image
        $environment->create($asset_logo);
        $asset_logo_id = $asset_logo->getId();
        $asset_logo = $environment->getAsset($asset_logo_id);
        $asset_logo->process('en-US');

        // Prepare uploadig image
        $asset_banner = new Asset();
        $asset_banner->setTitle('en-US', 'Collection Banner of ' . $campaign->name);
        $asset_banner->setFile('en-US', $banner);

        //process Image
        $environment->create($asset_banner);
        $asset_banner_id = $asset_banner->getId();
        $asset_banner = $environment->getAsset($asset_banner_id);
        $asset_banner->process('en-US');

        //add collection page to contentful
        $entry_ads_page = new Entry('adsPage');
        $entry_ads_page->setField('usersemail', 'en-US', auth('sanctum')->user()->email);
        $entry_ads_page->setField('campaignName', 'en-US', $campaign->name);
        $entry_ads_page->setField('availability', 'en-US', $newcampaign->availability);
        $entry_ads_page->setField('startDate', 'en-US', $newcampaign->start_date);
        $entry_ads_page->setField('totalBudget', 'en-US', Audience::where('campaign_id', $campaign->id)->sum('price'));
        $entry_ads_page->setField('collectionPageName', 'en-US', $newadspage->name);
        $entry_ads_page->setField('collectionPageText', 'en-US', $newadspage->description);
        $entry_ads_page->setField('collectionPageWebsite', 'en-US', $newadspage->website);
        $entry_ads_page->setField('collectionPageDiscord', 'en-US', $newadspage->discord);
        $entry_ads_page->setField('collectionPageMedium', 'en-US', $newadspage->medium);
        $entry_ads_page->setField('collectionPageTelegram', 'en-US', $newadspage->telegram);
        $entry_ads_page->setField('collectionPageLogo', 'en-US', $asset_logo->asLink());
        $entry_ads_page->setField('collectionPageBanner', 'en-US', $asset_banner->asLink());
        $environment->create($entry_ads_page);

        //publish ads page to contentful
        $entry_id = $entry_ads_page->getId();
        $entry_ads_page = $environment->getEntry($entry_id);
        $entry_ads_page->publish();

        //update Campaign Data
        $updatecampaign = Campaign::where('user_id', auth('sanctum')->user()->id)->orderBy('id', 'desc')->first();
        $updatecampaign->entry_id = $entry_id;
        $updatecampaign->save();

        //add ads to contentful
        $adv = Ads::where('campaign_id', $campaign->id)->get();

        foreach ($adv as $ad) {

            $audience = Audience::where('ads_id', $ad->id)->first();
            $detail_audience = DetailTarget::where('audience_id', $audience->id)->first();

            //upload image
            $url_image = Media::where('owner_id', $ad->id)->where('type', 'ads_nft')->orderby('id','desc')->first();

            $image = new \Contentful\Core\File\RemoteUploadFile(
                $campaign->name . 'Media',
                'JPEG,JPG,PNG',
                'http://backend.walletads.io' . $url_image->url
            );

            $asset_image = new Asset();
            $asset_image->setTitle('en-US', 'Collection Logo of ' . $campaign->name);
            $asset_image->setFile('en-US', $image);

            //process Image
            $environment->create($asset_image);
            $asset_image_id = $asset_image->getId();
            $asset_image = $environment->getAsset($asset_image_id);
            $asset_image->process('en-US');


            $entry_ads = new Entry('adsCreation');
            $entry_ads->setField('userEmail', 'en-US', auth('sanctum')->user()->email);
            $entry_ads->setField('adsCreation', 'en-US', $audience->name);
            $entry_ads->setField('campaignName', 'en-US', $campaign->name);
            $entry_ads->setField('campaignAvailability', 'en-US', $campaign->availability);
            $entry_ads->setField('campaignStartDate', 'en-US', $campaign->start_date);
            $entry_ads->setField('adsName', 'en-US', $ad->name);
            $entry_ads->setField('adsText', 'en-US', $ad->description);
            $entry_ads->setField('budget', 'en-US', $audience->price);
            $entry_ads->setField('pricePerAirdrop', 'en-US', $audience->price_airdrop);
            $entry_ads->setField('totalUser', 'en-US', $audience->total_user);
            $entry_ads->setField('adsImage', 'en-US', $asset_image->asLink());
            // $entry_ads->setField('cryptocurrenciesUsed', 'en-US', $detail_audience->cryptocurrency_used);
            $entry_ads->setField('accountAge', 'en-US', $detail_audience->account_age_year . ' years ' . $detail_audience->account_age_month . ' months ' . $detail_audience->account_age_day . ' days');
            $entry_ads->setField('availableCreditInWallet', 'en-US', $detail_audience->available_credit_wallet);
            $entry_ads->setField('tradingVolume', 'en-US', $detail_audience->trading_volume);
            $entry_ads->setField('airdropsReceived', 'en-US', $detail_audience->airdrops_received);
            $entry_ads->setField('amountOfTransaction', 'en-US', $detail_audience->amount_transaction . ' Within ' . $detail_audience->amount_transaction_day . ' days');
            $entry_ads->setField('nftPurchases', 'en-US', $detail_audience->nft_purchases);
            $environment->create($entry_ads);

            //publish ads to contentful
            $entry_id = $entry_ads->getId();
            $entry_ads = $environment->getEntry($entry_id);
            $entry_ads->publish();
        }

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

            $campaign->type = $request->campaign_end_date_type;
            if ($request->campaign_end_date_type == 1) {
                $campaign->end_date = Carbon::now()->addDay(90);
                $campaign->availability = '90';
            }
            if ($request->campaign_end_date_type == 2) {
                $campaign->end_date = Carbon::now()->addDay(21);
                $campaign->availability = '21';
            }
            if ($request->campaign_end_date_type == 3) {
                $campaign->end_date = Carbon::now()->addDay($request->campaign_end_date_day);
                $campaign->day = $request->campaign_end_date_day;
                $campaign->availability = $request->campaign_end_date_day;
            }

            $campaign->update();

            if ($request->has('campaign_audiences') && count($request->campaign_audiences) > 0) {
                foreach ($request->campaign_audiences as $audience) {
                    $audience = (object) $audience;

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

                        $file_parts = explode(";base64,", $audience->file);
                        $file_type_aux = explode("@file/", $file_parts[0]);
                        $file_type = $file_type_aux[1];
                        $file_base64 = base64_decode($file_parts[1]);
                        $fileNameToStore = uniqid() . '_' . time() . '.xlsx';
                        $fileURL = "/assets/files/audience/" . $fileNameToStore;
                        Storage::disk('public_uploads')->put($fileURL, $file_base64);

                        $media->name = $fileNameToStore;
                        $media->url = $fileURL;
                        $media->save();
                    }

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
            $adsPage->instagram = $request->ads_page_instagram;
            $adsPage->external_page = $request->ads_page_external_page;
            $adsPage->save();

            if ($request->has('ads_page_logo') && $request->ads_page_logo != '') {
                $media = Media::where('owner_id', $adsPage->id)->where('type', 'ads_logo')->first();
                if ($media) {
                    unlink(public_path() . $media->url);
                } else {
                    $media = new Media;
                    $media->owner_id = $adsPage->id;
                    $media->type = "ads_logo";
                }

                $image_parts = explode(";base64,", $request->ads_page_logo);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                $fileURL = "/assets/images/logo/" . $fileNameToStore;
                Storage::disk('public_uploads')->put($fileURL, $image_base64);

                $media->name = $fileNameToStore;
                $media->url = $fileURL;
                $media->save();
            }

            if ($request->has('ads_page_banner') && $request->ads_page_banner != '') {
                $media = Media::where('owner_id', $adsPage->id)->where('type', 'ads_banner')->first();
                if ($media) {
                    unlink(public_path() . $media->url);
                } else {
                    $media = new Media;
                    $media->owner_id = $adsPage->id;
                    $media->type = "ads_banner";
                }

                $image_parts = explode(";base64,", $request->ads_page_banner);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileNameToStore = uniqid() . '_' . time() . '.' . $image_type;
                $fileURL = "/assets/images/banner/" . $fileNameToStore;
                Storage::disk('public_uploads')->put($fileURL, $image_base64);

                $media->name = $fileNameToStore;
                $media->url = $fileURL;
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

                    if (isset($ads->image) && $ads->image != '') {
                        $media = Media::where('owner_id', $oldAds->id)->where('type', 'ads_nft')->first();
                        if ($media) {
                            unlink(public_path() . $media->url);
                        } else {
                            $media = new Media;
                            $media->owner_id = $oldAds->id;
                            $media->type = "ads_nft";
                        }

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

            return $campaign;
        });

        $data = Campaign::with('audiences', 'adsPage', 'ads')->find($campaign->id);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }
}
