<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

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
        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_AUTH_TOKEN');
        $twilioWhatsAppNumber = env('TWILIO_WHATSAPP_NUMBER');

        $twilio = new Client($twilioSid, $twilioToken);

        $phoneNumber = $this->countryCode . $this->phone;

        // Send WhatsApp message
        $twilio->messages->create(
            'whatsapp:' . $phoneNumber,
            [
                'from' => 'whatsapp:' . $twilioWhatsAppNumber,
                'body' => $this->message
            ]
        );
    }
}
