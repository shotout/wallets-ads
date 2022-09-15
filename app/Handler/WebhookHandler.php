<?php 

namespace App\Handler;

use App\Jobs\SendConfirmEmail;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;
use App\Models\Blacklisted;
use App\Models\User;
use Contentful\Management\Client;
use Illuminate\Http\Request;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle(Request $request)
    {
        $data = $this->webhookCall->payload;
        logger($data);

        if ($request->type === 'checkout.session.completed') {
            
            $client = New Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
            $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

            $entry = $environment->getEntry('29qhIihgtgWXQMZLOAzWNy');
            $entry->setField('paymentStatus', 'en-US', false);
            $entry->update();
            $entry->publish();     
        }
        
    }
}