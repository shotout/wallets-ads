<?php

namespace App\Jobs;

use App\Models\Campaign;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign)
    {
        $campaign;
    }   
    
        //
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
            $entry_id = Campaign::find($this->campaign->id)->entry_id;

            $client = New Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
            $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

            $entry = $environment->getEntry($entry_id);
            $entry->setField('paymentMethod', 'en-US', 'Crypto');
            $entry->update();          

    }
}
