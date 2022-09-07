<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendResetEmail implements ShouldQueue
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
        $this->user->email_message = 'Reset Password';
        $this->user->name = $this->user->first_name ." ".$this->user->last_name;

        Mail::send('email.reset', ['user' => $this->user], function($message) {
            $message->to($this->user->email, $this->user->name)->subject($this->user->email_message);
            $message->from(env('MAIL_FROM_ADDRESS'));
        });
    }
}
