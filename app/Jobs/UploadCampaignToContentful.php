<?php

namespace App\Jobs;

use App\Models\Ads;
use App\Models\AdsPage;
use App\Models\Audience;
use App\Models\Campaign;
use App\Models\DetailTarget;
use App\Models\Media;
use App\Models\User;
use App\Models\Voucher;
use Contentful\Management\Client;
use Contentful\Management\Resource\Asset;
use Contentful\Management\Resource\Entry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadCampaignToContentful implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    /**
     * Create a new job instance.
     *
     * 
     * @return void
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $campaign = Campaign::find($this->campaign->id);

        //contentful env
        $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        $user = User::find($campaign->user_id);
        $voucher = Voucher::where('code', $campaign->promo_code)->first();

        //retrieve data from database
        $newadspage = AdsPage::where('campaign_id', $campaign->id)->first();

        //sample_wallet
        $sample[] = $campaign->sample_address;
        foreach ($sample as $key => $value) {
            $samples[] = $value;
        }

        $samples_address = json_decode($samples[0], true);

        $url_logo = Media::where('owner_id', $newadspage->id)->where('type', 'ads_logo')->first();
        // $url_banner = Media::where('owner_id', $newadspage->id)->where('type', 'ads_banner')->first();
        $url_logo2 = env("APP_URL") . $url_logo->url;

        $logo = new \Contentful\Core\File\RemoteUploadFile(
            $campaign->name . $url_logo->name,
            'JPEG,JPG,PNG',
            $url_logo2
        );

        // $banner = new \Contentful\Core\File\RemoteUploadFile(
        //     $campaign->name . 'Collection Banner',
        //     'JPEG,JPG,PNG',
        //     env("APP_URL") . $url_banner->url
        // );

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
        // $asset_banner = new Asset();
        // $asset_banner->setTitle('en-US', 'Collection Banner of ' . $campaign->name);
        // $asset_banner->setFile('en-US', $banner);

        //process Image
        // $environment->create($asset_banner);
        // $asset_banner_id = $asset_banner->getId();
        // $asset_banner = $environment->getAsset($asset_banner_id);
        // $asset_banner->process('en-US');


        $budget = Audience::where('campaign_id', $campaign->id)->distinct()->get('fe_id');
        $total_budget = 0;

        foreach ($budget as $key => $value) {
            $price = Audience::where('campaign_id', $campaign->id)->where('fe_id', $value->fe_id)->first('price');
            $total_budget = $total_budget + (int) $price->price;
        }

        $total_budget = (string)$total_budget;
        //add collection page to contentful
        $entry_ads_page = new Entry('adsPage');
        $entry_ads_page->setField('usersemail', 'en-US', $user->email);
        $entry_ads_page->setField('campaignName', 'en-US', $campaign->name);
        $entry_ads_page->setField('availability', 'en-US', $campaign->availability);
        $entry_ads_page->setField('startDate', 'en-US', $campaign->start_date);
        $entry_ads_page->setField('totalBudget', 'en-US', $total_budget);

        if ($campaign->promo_code != NULL and $campaign->promo_code != '') {
            $entry_ads_page->setField('promoCode', 'en-US', $campaign->promo_code);
            $entry_ads_page->setField('promoAmount', 'en-US', $voucher->value);
        }

        $entry_ads_page->setField('paymentMethod', 'en-US', $campaign->payment_method);
        $entry_ads_page->setField('paymentStatus', 'en-US', false);
        $entry_ads_page->setField('collectionPageName', 'en-US', $newadspage->name);
        $entry_ads_page->setField('collectionPageText', 'en-US', $newadspage->description);
        $entry_ads_page->setField('collectionPageWebsite', 'en-US', $newadspage->website);
        $entry_ads_page->setField('collectionPageDiscord', 'en-US', $newadspage->discord);
        $entry_ads_page->setField('collectionPageMedium', 'en-US', $newadspage->medium);
        $entry_ads_page->setField('collectionPageTelegram', 'en-US', $newadspage->telegram);
        $entry_ads_page->setField('collectionPageLogo', 'en-US', $asset_logo->asLink());
        // $entry_ads_page->setField('collectionPageBanner', 'en-US', $asset_banner->asLink());
        $entry_ads_page->setField('collectionPageTokenTrackerName', 'en-US', $newadspage->token_name);
        $entry_ads_page->setField('collectionPageTokenTrackerSymbol', 'en-US', $newadspage->token_symbol);
        $entry_ads_page->setField('campaignSampleWalletAddresses', 'en-US', $samples_address);
        $environment->create($entry_ads_page);

        //publish ads page to contentful
        $entry_id = $entry_ads_page->getId();
        $entry_ads_page = $environment->getEntry($entry_id);
        $entry_ads_page->publish();

        //update Campaign Data
        $updatecampaign = Campaign::where('id', $campaign->id)->orderBy('id', 'desc')->first();
        $updatecampaign->entry_id = $entry_id;
        $updatecampaign->save();

        //add ads to contentful
        $adv = Ads::where('campaign_id', $campaign->id)->orderBy('id', 'desc')->get();



        foreach ($adv as $ad) {

            $audience = Audience::where('ads_id', $ad->id)->orderBy('id', 'desc')->get();


            foreach ($audience as $aud) {

                //  $detail_audience = DetailTarget::where('audience_id', $aud->id)->first();

                //upload image
                $url_image = Media::where('owner_id', $ad->id)->where('type', 'ads_nft')->orderby('id', 'desc')->first();
                $url_file  = Media::where('owner_id', $aud->id)->where('type', 'audience_file')->first();

                $image = new \Contentful\Core\File\RemoteUploadFile(
                    $campaign->name . 'Media',
                    'JPEG,JPG,PNG,GIF',
                    env("APP_URL") . $url_image->url
                );


                $asset_image = new Asset();
                $asset_image->setTitle('en-US', 'Media ' . $aud->name . ' ' . $campaign->name);
                $asset_image->setFile('en-US', $image);

                //process Image
                $environment->create($asset_image);
                $asset_image_id = $asset_image->getId();
                $asset_image = $environment->getAsset($asset_image_id);
                $asset_image->process('en-US');


                // $checkaudience = Audience::where('campaign_id', $campaign->id)->where('fe_id', $aud->fe_id)->where('id', '!=', $aud->id)->first();

                if ($aud->price_airdrop == "0.039") {
                    $package = "Optimize Targeting";
                } else {
                    $package = "Upload Own Audience Targeting";


                    if ($url_file != NULL and $url_file != '') {
                        $file = new \Contentful\Core\File\RemoteUploadFile(
                            $url_file->original_name,
                            'xlsx/xls/csv',
                            env("APP_URL") . $url_file->url
                        );

                        $asset_file = new Asset();
                        $asset_file->setTitle('en-US', 'Audience file of ' . $campaign->name);
                        $asset_file->setFile('en-US', $file);

                        //process file
                        $environment->create($asset_file);
                        $asset_file_id = $asset_file->getId();
                        $asset_file = $environment->getAsset($asset_file_id);
                        $asset_file->process('en-US');
                    }
                }



                $adtext1 = ads::where('id', $ad->id)->get()->toArray();
                $adtext1 = json_decode($adtext1[0]['description'], true);
                $adtext1[0]['adtext'];
                $i = 1;

                foreach ($adtext1 as $key => $value) {
                    $multiple[] = '|||Ad text' . $i . ': </br>' . $value['adtext'] . '</br></br></br>';
                    $i++;
                }

                $ad_text = implode(" ", $multiple);

                $checkaudienceupload = Audience::where('campaign_id', $campaign->id)->where('fe_id', $aud->fe_id)->where('id', '!=', $aud->id)->first();

                $entry_ads = new Entry('adsCreation');
                $entry_ads->setField('userEmail', 'en-US', $user->email);
                $entry_ads->setField('adsCreation', 'en-US', $campaign->name . ' - ' . $aud->name . ' - ' . $ad->name);
                $entry_ads->setField('campaignName', 'en-US', $campaign->name);
                $entry_ads->setField('campaignAvailability', 'en-US', $campaign->availability);
                $entry_ads->setField('campaignStartDate', 'en-US', $campaign->start_date);
                $entry_ads->setField('adsName', 'en-US', $ad->name);
                // $entry_ads->setField('advertiseText', 'en-US', $ad_text);
                $entry_ads->setField('adtext1', 'en-US', $ad_text);
                $entry_ads->setField('budget', 'en-US', $aud->price);
                if ($aud->price_airdrop == "0.019") {
                    $entry_ads->setField('audienceFile', 'en-US', $asset_file->asLink());
                }

                $entry_ads->setField('targetingOption', 'en-US', $package);
                $entry_ads->setField('pricePerAirdrop', 'en-US', $aud->price_airdrop);
                $entry_ads->setField('totalUser', 'en-US', $aud->total_user);
                $entry_ads->setField('adsImage', 'en-US', $asset_image->asLink());
                // $entry_ads->setField('cryptocurrenciesUsed', 'en-US', $detail_audience->cryptocurrency_used);
                //  $entry_ads->setField('accountAge', 'en-US', $detail_audience->account_age_year . ' years ' . $detail_audience->account_age_month . ' months ' . $detail_audience->account_age_day . ' days');
                //  $entry_ads->setField('availableCreditInWallet', 'en-US', $detail_audience->available_credit_wallet);
                //  $entry_ads->setField('tradingVolume', 'en-US', $detail_audience->trading_volume);
                //  $entry_ads->setField('airdropsReceived', 'en-US', $detail_audience->airdrops_received);
                //  $entry_ads->setField('amountOfTransaction', 'en-US', $detail_audience->amount_transaction . ' Within ' . $detail_audience->amount_transaction_day . ' days');
                //  $entry_ads->setField('nftPurchases', 'en-US', $detail_audience->nft_purchases);
                $environment->create($entry_ads);

                //publish ads to contentful
                $entry_id = $entry_ads->getId();
                $entry_ads = $environment->getEntry($entry_id);
                $entry_ads->publish();

                //update ads data
                $aud->entry_id = $entry_id;
                $aud->update();
                $multiple = [];
            }
        }

        SendCampaignNotificationEmail::dispatch($campaign);
    }
}
