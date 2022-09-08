<?php 

namespace App\Handler;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle( )
    {
        $data = $this->webhookCall->payload;

        logger($data['sys']['contentType']['sys']['id']);
    }
}