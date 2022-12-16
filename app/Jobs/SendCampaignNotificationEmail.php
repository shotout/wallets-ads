<?php

namespace App\Jobs;

use App\Models\Ads;
use App\Models\AdsPage;
use App\Models\Audience;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendCampaignNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $campaign;
    protected $user;
    protected $collection;
    protected $invoice;
    protected $ads;
    protected $amount;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
        $this->user = User::find($campaign->user_id);
        $this->collection = AdsPage::where('campaign_id', $campaign->id)->first();
        $this->invoice = Invoice::where('campaign_id', $campaign->id)->first();
        $this->ads = DB::table('ads')->join('audiences', 'ads.id', '=', 'audiences.ads_id')->where('ads.campaign_id', $campaign->id)->get(['ads.*', 'audiences.name as audience_name']);
        $this->amount = Audience::where('campaign_id', $campaign->id)->sum('price');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->email_message = 'New Campaign Notification';
        $this->campaign->date = date('m/d/Y', strtotime($this->campaign->start_date));
        $this->campaign->amount = $this->amount;

        $email = array("edo@stebasia.com","jannik@kuningan.de","andre@admiral.studio");

        $ignore = array("test@mail.com","tyo@stebasia.com","jannik@stebasia.com","zul@stebasia.com","edo@stebasia.com");

        if (in_array($this->user->email, $ignore) == false) {
            foreach ($email as $item) {
                Mail::send('email.newcampaign', ['user' => $this->user, 'campaign' => $this->campaign, 'adspage' => $this->collection, 'invoice' => $this->invoice, 'ads' => $this->ads], function ($message) use ($item) {
                    $message->to($item, $item)->subject($this->user->email_message);
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                });
            }
        }
    }
}
