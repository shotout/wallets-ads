<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $invoice;
    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->user = User::find($invoice->user_id);
    }
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->email_message = 'Your invoice from walletads';

        $id_invoice = $this->invoice->id;
        
        $invoice = Invoice::where('id', $id_invoice)->first();
        $user_name = User::where('id', $invoice->user_id )->first();
       
        $this->user->name = $user_name->first_name ." ".$user_name->last_name;
        $this->user->email = $user_name->email;
        $this->user->company = $user_name->company_name;
        $this->user->budget = number_format( $this->invoice->amount , 0 , '.' , ',' );


        $invoice->date = date('m/d/Y', strtotime($invoice->invoice_date));
        

        Mail::send('email.invoice', ['user' => $this->user, 'invoice' => $invoice], function($message) {
            $message->to($this->user->email, $this->user->name)->subject($this->user->email_message);
            $message->bcc('jannik@kuningan.de', 'Jannik');
            $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'))->attach(env('APP_URL').$this->invoice->invoice_url);
        });
    }
}
