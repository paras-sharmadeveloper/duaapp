<?php

namespace App\Mail;
 
use Illuminate\Bus\Queueable; 
use Illuminate\Mail\Mailable; 
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SendGrid;
use SendGrid\Mail\Mail;    

class UserOtp extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $dynamicData;

    public function __construct($bookingData)
    {
        $this->dynamicData = $bookingData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'KahayFaqeer verification code. For your security, do not share this code.',
        );
    }

    public function build()
    {
        return $this->view('email-template.user-otp')
                    ->subject('KahayFaqeer verification code. For your security, do not share this code.')
                    ->with(['bookingData' => $this->dynamicData]);
    }

    public function sendUsingSendGrid($recipient)
    {

        $email = new Mail();  
        $email->setFrom($recipient, 'Kahey Faqeer');
        $email->setSubject($this->dynamicData['subject']);
        $email->addTo($recipient, $this->dynamicData['name']); 

        // $email->addContent("text/plain",  $this->view('email-template.test')->with(['dynamicData' => $this->dynamicData]));
        // $email->addContent(
        //     "text/html", $this->view('email-template.test')->with(['dynamicData' => $this->dynamicData])
        // );
        $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
         
    }

    
}
