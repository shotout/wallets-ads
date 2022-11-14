<?php

namespace App\Handler;

use App\Jobs\SendCampaignNotificationEmail;
use App\Jobs\SendConfirmEmail;
use App\Jobs\SendInvoiceEmail;
use App\Jobs\SendScheduleCampaign;
use App\Jobs\UpdateStatusPayment;
use App\Models\Ads;
use App\Models\Audience;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;
use App\Models\Blacklisted;
use App\Models\Campaign;
use App\Models\Invoice;
use App\Models\StripePayment;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Contentful\Management\Client;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class WebhookHandler extends ProcessWebhookJob
{


    public function handle(Request $request)
    {
        $data = $this->webhookCall->payload;
        logger($data);

        $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        if (isset($data['type'])) {
            if ($data['type'] === 'checkout.session.completed' && $data['data']['object']['payment_status'] === 'paid') {

                $paymentid = StripePayment::where('stripe_id', $data['data']['object']['id'])->first();
                $campaign = Campaign::where('id', $paymentid->campaign_id)->first();

                $updatepayment = StripePayment::where('stripe_id', $data['data']['object']['id'])->first();
                $updatepayment->status = '1';
                $updatepayment->save();

                UpdateStatusPayment::dispatch($campaign)->delay(now()->addSeconds(90));
                $updatestatus = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
                $updatestatus->status = '1';
                $updatestatus->save();

                return response()->json(['success' => true], 200);
            }
        }


        if (isset($data['sys']['type'])) {
            if ($data['sys']['type'] == 'Entry') {

                if ($data['sys']['contentType']['sys']['id'] == 'users') {
                    //retrieve data from contentful
                    $entry_id = $data['sys']['id'];

                    //retrieve user from database
                    $user = User::where('entry_id', $entry_id)->first();
                    SendConfirmEmail::dispatch($user, 'register')->delay(Carbon::now()->addseconds(10));
                }

                if ($data['sys']['contentType']['sys']['id'] == 'adsPage') {

                    if (isset($data['fields']['invoiceFile'])) {
                        logger($data);
                        //get data campaign
                        $entry_id = $data['sys']['id'];
                        $campaign = Campaign::where('entry_id', $entry_id)->first();
                        $invoice = Invoice::where('campaign_id', $campaign->id)->first();



                        if ($data['fields']['paymentStatus']['en-US'] == false) { {
                                $payment_status = '0';
                            }
                        } else {
                            $payment_status = '1';
                        }

                        $invoice_file = $data['fields']['invoiceFile']['en-US']['sys']['id'];

                        $response = Http::get('https://cdn.contentful.com/spaces/m6gjbuid69la/environments/master/assets/' . $invoice_file . '?access_token=' . env('CONTENTFUL_DELIVERY_TOKEN'));

                        logger($response->json());

                        $invoice_link = 'Https:' . $response['fields']['file']['url'];
                        $invoice_name = $response['fields']['file']['fileName'];

                        $file = file_get_contents($invoice_link);
                        Storage::disk('public')->put('invoices/' . $invoice_name, $file);

                        $invoice_url = '/storage/invoices/' . $invoice_name;

                        if (empty($invoice)) {

                            //save invoice data 
                            $newinvoice = new Invoice();
                            $newinvoice->campaign_id = $campaign->id;
                            $newinvoice->user_id = $campaign->user_id;
                            $newinvoice->invoice_number = $data['fields']['invoiceNumber']['en-US'];
                            $newinvoice->invoice_date = $data['fields']['invoiceDate']['en-US'];
                            $newinvoice->campaign_name = $data['fields']['campaignName']['en-US'];
                            $newinvoice->amount = $data['fields']['totalBudget']['en-US'];
                            $newinvoice->payment_method = $data['fields']['paymentMethod']['en-US'];
                            $newinvoice->payment_status = $payment_status;
                            $newinvoice->invoice_url = $invoice_url;
                            $newinvoice->save();

                            //send invoice email                
                            $invoice = $newinvoice;
                        }

                        if ($invoice) {

                            //update invoice data                     
                            $invoice->campaign_id = $campaign->id;
                            $invoice->user_id = $campaign->user_id;
                            $invoice->invoice_number = $data['fields']['invoiceNumber']['en-US'];
                            $invoice->invoice_date = $data['fields']['invoiceDate']['en-US'];
                            $invoice->campaign_name = $data['fields']['campaignName']['en-US'];
                            $invoice->amount = $data['fields']['totalBudget']['en-US'];
                            $invoice->payment_method = $data['fields']['paymentMethod']['en-US'];
                            $invoice->payment_status = $payment_status;
                            $invoice->invoice_url = $invoice_url;
                            $invoice->update();
                        }


                        if ($data['fields']['sendInvoiceEmail']['en-US'] == true) {

                            SendInvoiceEmail::dispatch($invoice)->delay(Carbon::now()->addseconds(10));
                        }
                    }


                    if ($data['fields']['scheduledCampaign']['en-US'] == true) {

                        $entry_id = $data['sys']['id'];
                        $campaign = Campaign::where('entry_id', $entry_id)->first();

                        $total_budget = $data['fields']['totalBudget']['en-US'];
                        $total_sendout = Audience::where('campaign_id', $campaign->id)->sum('total_user');

                        if ($campaign->is_scheduled == 0) {
                            $campaign->is_scheduled = 1;
                            $campaign->save();

                            SendScheduleCampaign::dispatch($campaign, $total_budget, $total_sendout)->delay(Carbon::now()->addseconds(10));
                        }
                    }


                    if (isset($data['fields']['statusCampaign']['en-US'])) {
                        $entry_id = $data['sys']['id'];
                        $campaign = Campaign::where('entry_id', $entry_id)->first();

                        if ($data['fields']['statusCampaign']['en-US'] == 'In Review') {
                            $campaign->status = 1;
                        }
                        if ($data['fields']['statusCampaign']['en-US'] == 'Running') {
                            $campaign->status = 2;
                        }
                        if ($data['fields']['statusCampaign']['en-US'] == 'Finished') {
                            $campaign->status = 3;
                        }
                        $campaign->save();
                    }

                    if (isset($data['fields']['showOnReportDashboard']['en-US'])) {
                        $entry_id = $data['sys']['id'];
                        $campaign = Campaign::where('entry_id', $entry_id)->first();

                        if ($data['fields']['showOnReportDashboard']['en-US'] == false) {
                            $campaign->is_show = 0;
                        }
                        if ($data['fields']['showOnReportDashboard']['en-US'] == true) {
                            $campaign->is_show = 1;
                        }

                        $campaign->save();
                    }

                    if (isset($data['fields']['campaignAirdrops']['en-US'])) {
                        $entry_id = $data['sys']['id'];
                        $campaign = Campaign::where('entry_id', $entry_id)->first();
                    }
                }


                if ($data['sys']['contentType']['sys']['id'] == 'promoCodes') {

                    $entry_id = $data['sys']['id'];

                    $promo = Voucher::where('entry_id', $entry_id)->first();

                    if ($promo) {
                        $promo->code = $data['fields']['promoCode']['en-US'];
                        $promo->coupon_id = $data['fields']['stripeCouponId']['en-US'];
                        $promo->type = 1;
                        $promo->value = $data['fields']['couponAmount']['en-US'];
                        $promo->min_budget = $data['fields']['minimalSpent']['en-US'];
                        $promo->update();
                    } else {
                        $newcoupon = new Voucher();
                        $newcoupon->entry_id = $entry_id;
                        $newcoupon->code = $data['fields']['promoCode']['en-US'];
                        $newcoupon->coupon_id = $data['fields']['stripeCouponId']['en-US'];
                        $newcoupon->type = 1;
                        $newcoupon->value = $data['fields']['couponAmount']['en-US'];
                        $newcoupon->min_budget = $data['fields']['minimalSpent']['en-US'];
                        $newcoupon->save();
                    }
                }


                if ($data['sys']['contentType']['sys']['id'] == 'adsCreation') {

                    $entry_id = $data['sys']['id'];
                    $audience = Audience::where('entry_id', $entry_id)->first();

                    if ($audience && isset($data['fields']['adsAirdrops']['en-US'])) {
                        $audience->count_airdrop = $data['fields']['adsAirdrops']['en-US'];
                        $audience->save();

                        $ads_audience = Audience::where('ads_id', $audience->ads_id)->sum('count_airdrop');
                        $ads = Ads::where('id', $audience->ads_id)->first();
                        $ads->count_airdrop = $ads_audience;
                        $ads->save();

                        $count_airdrop = Audience::where('campaign_id', $audience->campaign_id)->sum('count_airdrop');

                        $campaign = Campaign::where('id', $audience->campaign_id)->first();
                        $campaign->count_airdrop = $count_airdrop;
                        $campaign->save();
                    }

                    if ($audience && isset($data['fields']['adsLinkClicks']['en-US'])) {
                        $audience->count_click = $data['fields']['adsLinkClicks']['en-US'];
                        $audience->save();

                        $ads_audience = Audience::where('ads_id', $audience->ads_id)->sum('count_click');
                        $ads = Ads::where('id', $audience->ads_id)->first();
                        $ads->count_click = $ads_audience;
                        $ads->save();

                        $count_clicks = Audience::where('campaign_id', $audience->campaign_id)->sum('count_click');

                        $campaign = Campaign::where('id', $audience->campaign_id)->first();
                        $campaign->count_click = $count_clicks;
                        $campaign->save();
                    }

                    if ($audience && isset($data['fields']['adsMints']['en-US'])) {
                        $audience->count_mint = $data['fields']['adsMints']['en-US'];
                        $audience->save();

                        $ads_audience = Audience::where('ads_id', $audience->ads_id)->sum('count_mint');
                        $ads = Ads::where('id', $audience->ads_id)->first();
                        $ads->count_mint = $ads_audience;
                        $ads->save();

                        $count_mints = Audience::where('campaign_id', $audience->campaign_id)->sum('count_mint');

                        $campaign = Campaign::where('id', $audience->campaign_id)->first();
                        $campaign->count_mint = $count_mints;
                        $campaign->save();
                    }

                    if ($audience && isset($data['fields']['adsImpressions']['en-US'])) {
                        $audience->count_impression = $data['fields']['adsImpressions']['en-US'];
                        $audience->save();

                        $ads_audience = Audience::where('ads_id', $audience->ads_id)->sum('count_impression');
                        $ads = Ads::where('id', $audience->ads_id)->first();
                        $ads->count_impression = $ads_audience;
                        $ads->save();

                        $count_impression = Audience::where('campaign_id', $audience->campaign_id)->sum('count_impression');

                        $campaign = Campaign::where('id', $audience->campaign_id)->first();
                        $campaign->count_impression = $count_impression;
                        $campaign->save();
                    }

                    if ($audience && isset($data['fields']['adsViews']['en-US'])) {
                        $audience->count_view = $data['fields']['adsViews']['en-US'];
                        $audience->save();

                        $ads_audience = Audience::where('ads_id', $audience->ads_id)->sum('count_view');
                        $ads = Ads::where('id', $audience->ads_id)->first();
                        $ads->count_view = $ads_audience;
                        $ads->save();

                        $count_view = Audience::where('campaign_id', $audience->campaign_id)->sum('count_view');

                        $campaign = Campaign::where('id', $audience->campaign_id)->first();
                        $campaign->count_view = $count_view;
                        $campaign->save();
                    }


                }
            }


            if ($data['sys']['type'] == 'DeletedEntry') {

                if ($data['sys']['contentType']['sys']['id'] == 'adsPage') {


                    $entry_id = $data['sys']['id'];
                    $campaign = Campaign::where('entry_id', $entry_id)->first();

                    if ($campaign) {

                        $audiences = Audience::where('campaign_id', $campaign->id)->get();

                        foreach ($audiences as $audience) {

                            $entry_audience = $environment->getEntry($audience->entry_id);
                            $entry_audience->unpublish();
                            $entry_audience->delete();

                            $audience->delete();
                        }
                    }

                    $campaign->delete();
                }

                
                if ($data['sys']['contentType']['sys']['id'] == 'adsCreation') {


                    $entry_id = $data['sys']['id'];
                    $campaign = Audience::where('entry_id', $entry_id)->first();

                    $campaign->delete();
                }

                if ($data['sys']['contentType']['sys']['id'] == 'users') {

                    $entry_id = $data['sys']['id'];
                    $user = User::where('entry_id', $entry_id)->first();

                    if ($user) {
                        $user->delete();
                    }
                }

                if ($data['sys']['contentType']['sys']['id'] == 'promoCodes') {

                    $coupon = Voucher::where('entry_id', $data['sys']['id'])->first();

                    if ($coupon) {
                        $coupon->delete();
                    }
                }

                if ($data['sys']['contentType']['sys']['id'] == 'blacklistedWalletAddress' || $data['sys']['contentType']['sys']['id'] == 'subscribedWallets') {

                    $wallet = Blacklisted::where('entry_id', $data['sys']['id'])->first();

                    if ($wallet) {
                        $wallet->delete();
                    }
                }
            }
        }
    }
}
