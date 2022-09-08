<?php 

namespace App\Handler;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;

class WebhookHandler implements ProcessWebhookJob 
{
       

    public function handle( )
    {
        logger('webhook received');
        
    }
}