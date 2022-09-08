<?php 

namespace App\Handler;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Contentful\Delivery\Client as DeliveryClient;
use App\Models\Blacklisted;

class WebhookHandler extends ProcessWebhookJob 
{
       

    public function handle( )
    {
        $data = $this->webhookCall->payload;

        if($data['sys']['type'] == 'Entry')
            {
                if($data['sys']['contentType']['sys']['id'] == 'blacklistedWalletAddress')
                {
                    //retrieve data from contentful
                    $entry_id = $data['sys']['id'];
                    $walletaddress = $data['fields']['walletAddress']['en-US'];
                    
                    //insert into database
                    $blacklist = new Blacklisted;
                    $blacklist->entry_id = $entry_id;
                    $blacklist->walletaddress = $walletaddress;
                    $blacklist->save();

                }

            }
        
    }
}