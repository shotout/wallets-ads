<?php

namespace App\Jobs;

use App\Models\Campaign;
use Carbon\Carbon;
use Contentful\Management\Client;
use Contentful\Management\Resource\Entry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCryptoPaymet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */

   

    public function __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }   
    
        //
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
            $id = $this->campaign_id;
            $entry_id = Campaign::find($id)->entry_id;

            $campaign = Campaign::find($id);

            $client = New Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
            $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

            $entry = $environment->getEntry($entry_id);
            $entry->setField('paymentMethod', 'en-US', 'Cryptocurrencies');
            $entry->update();
            $entry->publish();  
            
    }
}
