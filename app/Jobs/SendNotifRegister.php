<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendNotifRegister implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $list = array(
            "walletads@admiral.studio",
            "jannik@kuningan.de"
        );

        $this->user->email_message = 'New User Notification';
        $this->user->name = $this->user->first_name ." ".$this->user->last_name;

        foreach ($list as $item) {
            Mail::send('email.notif', ['user' => $this->user], function($message) use ($item) {
                $message->to($item, $item)->subject($this->user->email_message);
                $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
            });
        }
    }
}
