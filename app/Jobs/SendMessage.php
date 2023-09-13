<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;
 
class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $message;
    protected $is_whatsapp;
    protected $messageSendTo;

    // recipient means to message sent to person number

    public function __construct($recipient, $message,$is_whatsapp)
    {
        $this->message = $message;
        $this->messageSendTo = $recipient;
        $this->is_whatsapp = $is_whatsapp; 
    }

    /**
     * Execute the job.
     */

     public function handle()
     {
         // Send via Twilio SMS
         $twilioClient = new TwilioClient(
             config('services.twilio.sid'), 
             config('services.twilio.token')
         );
         if($this->is_whatsapp == 'yes'){
            
            $twilioClient->messages->create(
                "+".$this->messageSendTo, 
                [
                    'from' => config('services.twilio.phone'), 
                    'body' => $this->message
                ]
            );

            // $twilioClient->messages->create(
            //     "whatsapp:" . "+".$this->messageSendTo, 
            //     [
            //         'from' => "whatsapp:" . config('services.twilio.whatsapp'), 
            //         'body' => $this->message
            //     ]
            // );
         }else{
            $twilioClient->messages->create(
                "+".$this->messageSendTo, 
                [
                    'from' => config('services.twilio.phone'), 
                    'body' => $this->message
                ]
            );
         }
 
         // Send via Twilio WhatsApp
        
 
       
     }
   
}
