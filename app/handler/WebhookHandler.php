<?php 

namespace App\Handler;

use Spatie\WebhookClient\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle( DeliveryClient $client)
    {
        logger('webhook received');
        
    }
}