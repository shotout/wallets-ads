<?php

namespace App\Handler;

use App\Jobs\SendConfirmEmail;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;
use App\Models\Blacklisted;
use App\Models\Campaign;
use App\Models\Invoice;
use App\Models\StripePayment;
use App\Models\User;
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

        if ($request->type === 'checkout.session.completed' && $request->data['object']['payment_status'] === 'paid') {

            $paymentid = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
            $campaign = Campaign::where('id', $paymentid->campaign_id)->first();

            $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
            $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

            $updatepayment = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
            $updatepayment->status = '1';
            $updatepayment->save();

            $entry = $environment->getEntry($campaign->entry_id);
            $entry->setField('paymentStatus', 'en-US', true);
            $entry->update();
            $entry->publish();

            $updatestatus = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
            $updatestatus->status = '1';
            $updatestatus->save();

            return response()->json(['success' => true], 200);
        }

        if ($data['sys']['contentType']['sys']['id'] == 'users') {
            //retrieve data from contentful
            $entry_id = $data['sys']['id'];

            //retrieve user from database
            $user = User::where('entry_id', $entry_id)->first();
            SendConfirmEmail::dispatch($user, 'register')->onQueue('apiCampaign');
        }


        if ($data['sys']['type'] == 'Entry') {

            if ($data['sys']['contentType']['sys']['id'] == 'adsPage' && isset($data['fields']['invoiceFile'])) {

                logger($data);
                //get data campaign
                $entry_id = $data['sys']['id'];
                $campaign = Campaign::where('entry_id', $entry_id)->first();

                if ($data['fields']['paymentStatus']['en-US'] == false) { {
                        $payment_status = '0';
                    }
                } else {
                    $payment_status = '1';
                }

                $invoice_file = $data['fields']['invoiceFile']['en-US']['sys']['id'];
                        
                $response = Http::get('https://cdn.contentful.com/spaces/m6gjbuid69la/environments/master/assets/'.$invoice_file.'?access_token='.env('CONTENTFUL_DELIVERY_TOKEN'));            
                
                logger($response->json());
                        
                $invoice_link = 'Https:'.$response['fields']['file']['url'];
                $invoice_name = time().$response['fields']['file']['fileName'];
                
                $file = file_get_contents($invoice_link);
                Storage::disk('public')->put('invoices/'.$invoice_name, $file);

                $invoice_url ='/storage/invoices/'.$invoice_name;
                

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

                
            }
            
        }
    }
}
