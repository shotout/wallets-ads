<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendConfirmEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $flag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $flag)
    {
        $this->user = $user;
        $this->flag = $flag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->flag === 'register') {
            $this->user->email_message = 'Account Activation';
            $this->user->name = $this->user->first_name ." ".$this->user->last_name;

            Mail::send('email.confirm', ['user' => $this->user, 'flag' => $this->flag], function($message) {
                $message->to($this->user->email, $this->user->name)->subject($this->user->email_message);
                $message->from(env('MAIL_FROM_ADDRESS'));
            });
        }
        if ($this->flag === 'login') {
            $this->user->email_message = 'NFT Daily Sign In';
            Mail::send('email.confirm', ['user' => $this->user, 'flag' => $this->flag], function($message) {
                $message->to($this->user->email, $this->user->name)->subject($this->user->email_message);
                $message->from(env('MAIL_FROM_ADDRESS'));
            });
        }
        if ($this->flag === 'unregister') {
            $this->user->email_message = 'NFT Daily Account Deletion';
            Mail::send('email.confirm', ['user' => $this->user, 'flag' => $this->flag], function($message) {
                $message->to($this->user->email, $this->user->name)->subject($this->user->email_message);
                $message->from(env('MAIL_FROM_ADDRESS'));
            });
        }
    }
}
