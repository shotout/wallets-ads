<?php 

namespace App\Handler;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle( )
    {
        $data = 0;
        $data = $this->webhookCall->payload;
        logger('webhook received ok');
        logger($data);
    }
}