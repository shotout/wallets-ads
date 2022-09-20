<?php 

namespace App\Handler;

use App\Jobs\SendConfirmEmail;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;
use App\Models\Blacklisted;
use App\Models\Campaign;
use App\Models\StripePayment;
use App\Models\User;
use Contentful\Management\Client;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle(Request $request)
    {
        $data = $this->webhookCall->payload;
        logger($data);
       
        if ($request->type === 'checkout.session.completed' && $request->data['object']['payment_status'] === 'paid') {

            $paymentid = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
            $campaign = Campaign::where('id', $paymentid->campaign_id)->first();
            
            $client = New Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
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
       
        
    }
}