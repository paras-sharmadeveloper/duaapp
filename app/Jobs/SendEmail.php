<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SendGrid;
use SendGrid\Mail\Mail;
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $message;
    protected $recipient;
    public function __construct($recipient, $message)
    {
        $this->message = $message;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    { 
         $email = new Mail();
         $email->setFrom("kahayfaqeer.org@gmail.com", "DUA APP");
         $email->setSubject("Your Booking has been confirmed");
         $email->addTo($this->recipient);
         $email->addContent("text/plain", $this->message);
 
         $sendgrid = new SendGrid(config('services.sendgrid.key'));
         $sendgrid->send($email);
    }
}
