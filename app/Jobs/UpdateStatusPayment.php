<?php

namespace App\Jobs;

use Contentful\Management\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateStatusPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $campaign;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        $entry = $environment->getEntry($this->Campaign->entry_id);
        $entry->setField('paymentStatus', 'en-US', true);
        $entry->update();
        $entry->publish();
    }
}
