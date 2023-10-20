<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Log;
use App\Models\Vistors;
class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $tries = 3; // Number of retry attempts
    public $retryAfter = 60; // Delay between retries (in seconds)

    protected $message;
    protected $is_whatsapp;
    protected $messageSendTo;
    protected $visitorId; 
    // recipient means to message sent to person number

    public function __construct($recipient, $message,$is_whatsapp,$id)
    {
        $this->message = $message;
        $this->messageSendTo = $recipient;
        $this->is_whatsapp = $is_whatsapp; 
        $this->visitorId = $id;  
    }   
 
    /**
     * Execute the job.
     */

     public function handle()
     {
        

        try {
            $twilioClient = new TwilioClient(
                config('services.twilio.sid'), 
                config('services.twilio.token')
            );

        

            if($this->is_whatsapp == 'yes'){

               
                $twilioClient->messages->create(
                    "whatsapp:+".$this->messageSendTo,  // User's phone number
                    [
                      'from' => 'whatsapp:'.env('TWILIO_PHONE_WHATSAPP'),
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
                    "whatsapp:+".$this->messageSendTo,  // User's phone number
                    [
                      'from' => 'whatsapp:'.env('TWILIO_PHONE_WHATSAPP'),
                      'body' => $this->message
                    ]
                  );
            //    $twilioClient->messages->create(
            //        "whatsapp:+".$this->messageSendTo, 
            //        [
            //            'from' => config('services.twilio.phone'), 
            //            'body' => $this->message
            //        ]
            //    );
            }
            Vistors::find($this->visitorId)->update(['sms_sent_at' => date('y-m-d H:i:s')]); 
            // Your job's code here
        } catch (\Exception $e) {
            Log::error('Job failed SendMessage: ' . $e->getMessage());
            // You can also dispatch a notification or take other actions here
        } 
        
 
       
     }
   
}
