<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Log;
class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $countryCode;
    protected $phone;
    protected $message;

    public function __construct($countryCode, $phone, $message)
    {
        $this->countryCode = $countryCode;
        $this->phone = $phone;
        $this->message = $message;
    }

    public function handle()
    {

        try {
            $twilioClient = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $twilioClient->messages->create(
                "whatsapp:+".$this->phone,
                [
                  'from' => 'whatsapp:'.env('TWILIO_PHONE_WHATSAPP'),
                  'body' => $this->message
                ]
              );
            // Vistors::find($this->visitorId)->update(['sms_sent_at' => date('y-m-d H:i:s')]);
            // Your job's code here
        } catch (\Exception $e) {
            Log::error('Job failed SendMessage: ' . $e->getMessage());
            // You can also dispatch a notification or take other actions here
        }
    }
}
