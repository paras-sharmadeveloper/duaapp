<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;
use App\Models\{Vistors};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $visitorId;
    public function __construct($visitorsId)
    {
        $this->visitorId = $visitorsId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        try {
            Log::info('Dispatched');
            $visitor = Vistors::find($this->visitorId);
            $uuid = $visitor->booking_uniqueid;
            $tokenId = $visitor->booking_number;
            $userMobile = $visitor->phone;
            $duaType = $visitor->dua_type;
            $venueAddress = $visitor->venueSloting->venueAddress;
            $countryCode   = $visitor->country_code;
            $mobile =  'whatsapp:' . $countryCode . $userMobile;
            $message = $this->whatsAppConfirmationTemplate($venueAddress, $uuid, $tokenId, $userMobile, $duaType);

            $result = $this->sendWhatsAppMessage($mobile, $message);
            if ($result['data'] == 'success') {
                $visitor->update([
                    'msg_sent_status' => (!empty($result)) ?   $result['status'] : '',
                    'msg_sid' => (!empty($result)) ? $result['sid'] : '',
                    'msg_date' =>  Carbon::now(),

                ]);
                Log::info('true');
                return true;
            }
            // Log::info('false check Env'.env('TWILIO_ACCOUNT_SID'));
            return false;
        } catch (\Exception $e) {

            Log::info('ex' . $e->getMessage());
            //throw $th;
        }
    }

    private function sendWhatsAppMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        try {
            $messageInstance =  $twilio->messages->create(
                "$to",
                [
                    'from' => "whatsapp:" . env('TWILIO_PHONE_WHATSAPP'),
                    'body' => $message,
                    "statusCallback" => route('twillio.status.callback')
                ]
            );
            $messageSid = $messageInstance->sid; // Get MessageSid
            $messageSentStatus = $messageInstance->status; // Get MessageSentStatus
            return [
                'data' => 'success',
                'sid' => $messageSid,
                'status' => $messageSentStatus
            ];
        } catch (\Exception $e) {
            //throw $th;
            return [
                'data' => $e->getMessage(),
                'sid' => '',
                'status' => ''
            ];
        }
    }
    private function whatsAppConfirmationTemplate($venueAddress, $uuid, $tokenId, $userMobile, $duaType)
    {

        $venueDateEn = date("d M Y", strtotime($venueAddress->venue_date));
        $statusLink = route('booking.status', $uuid);
        $message  = <<<EOT
        Asalamualaikum,
        Please see below confirmation for your dua token.

        Your Dua Ghar : $venueAddress->city
        Your Dua Date : $venueDateEn
        Your Online Dua Token : $statusLink
        Your Token Number :  $tokenId
        Your Dua Type : $duaType
        Your registered mobile: $userMobile

        Please reach by 1pm to validate and print your token.

        Read and listen all books for free. Please visit KahayFaqeer.org
        EOT;
        return $message;
    }
}
