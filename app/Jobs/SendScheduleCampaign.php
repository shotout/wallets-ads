<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendScheduleCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $campaign;
    protected $user;
    protected $total_budget;
    protected $total_sendout;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaign,$total_budget,$total_sendout)
    {
        $this->campaign = $campaign;
        $this->user = User::find($campaign->user_id);
        $this->total_budget = $total_budget;
        $this->total_sendout = $total_sendout;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->email_message = 'Your campaign has been scheduled';
        $this->user->name = $this->user->first_name.' '.$this->user->last_name;

        Mail::send('email.scheduled', ['user' => $this->user, 'campaign' => $this->campaign, 'budget'=>$this->total_budget, 'sendout' => $this->total_sendout], function($message) {
            $message->to($this->user->email, $this->user->name)->subject($this->user->email_message);
            $message->from(env('MAIL_FROM_ADDRESS'));
        });
    }
}
