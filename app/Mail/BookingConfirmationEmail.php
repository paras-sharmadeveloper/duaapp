<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Mail\HtmlContent;

class BookingConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $dynamicData;

    public function __construct($dynamicData)
    {
        $this->dynamicData = $dynamicData;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))->view('email-template.booking-confirmed')
        ->with(['dynamicData' => $this->dynamicData])
        ->subject($this->dynamicData['subject'])
        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    }
     
    public function sendUsingSendGrid($recipient)
    {

        $email = new Mail();  
        $email->setFrom($recipient, 'Kahey Faqeer');
        $email->setSubject($this->dynamicData['subject']);
        $email->addTo($recipient, $this->dynamicData['first_name']); 

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

     
