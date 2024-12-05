<?php

namespace App\Jobs;

use App\Models\WhatsappNotificationLogs;
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

            $messageInstance = $twilioClient->messages->create(
                "whatsapp:+".$this->countryCode.$this->phone,
                [
                  'from' => 'whatsapp:'.env('TWILIO_PHONE_WHATSAPP'),
                  'body' => $this->message,
                  "statusCallback" => route('twillio.status.callback').'?event=whatsapp_event'
                ]
              );

              $messageSid = $messageInstance->sid; // Get MessageSid
               $messageSentStatus = $messageInstance->status; // Get MessageSentStatus
              WhatsappNotificationLogs::create([
                    'venue_date' => date('Y-m-d H:i:s'),
                    'dua_type' => 'Notification',
                    'whatsAppMessage' => $this->message,
                    'mobile' => $this->countryCode.$this->phone,
                    'msg_sid' => $messageSid,
                    'msg_sent_status' => $messageSentStatus,
                    'msg_date' => date('Y-m-d H:i:s'),
                ]);
            // Your job's code here
        } catch (\Exception $e) {
            Log::error('Job failed SendMessage: ' . $e->getMessage());
            // You can also dispatch a notification or take other actions here
        }
    }
}
