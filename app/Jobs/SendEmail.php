<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\BookingConfirmationEmail;
use Illuminate\Support\Facades\Mail;  

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;
    protected $recipient;
    public function __construct($recipient, $data)
    {
        $this->data = $data;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    { 
        try {
           Mail::to($this->recipient)->send(new BookingConfirmationEmail($this->data)); 
  
        } catch (\Throwable $th) {
            // echo '<pre>'; print_r($th->getMessage()); die;  
         
        }
        //  $email = new Mail();
        //  $email->setFrom("kahayfaqeer.org@gmail.com", "DUA APP");
        //  $email->setSubject("Your Booking has been confirmed");
        //  $email->addTo($this->recipient);
        //  $email->addContent("text/plain", $this->message);
 
        //  $sendgrid = new SendGrid(config('services.sendgrid.key'));
        //  $sendgrid->send($email);
    }
}
