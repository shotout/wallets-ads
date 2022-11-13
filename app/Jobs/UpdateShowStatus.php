<?php

namespace App\Jobs;

use Contentful\Management\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateShowStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $entry_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($entry_id)
    {
        $this->entry_id = $entry_id;
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

        $entry = $environment->getEntry($this->entry_id);
        $entry->setField('showOnReportDashboard', 'en-US', false);
        $entry->update();
        $entry->publish();
    }
}
