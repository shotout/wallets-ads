<?php 

namespace App\Handler;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle( )
    {
        $test = 0;
        $data = $this->webhookCall->payload;
        logger('webhook received');
        logger($data);
    }
}