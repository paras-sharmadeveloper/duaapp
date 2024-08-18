<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Twilio\Rest\Client;
use App\Models\{VisitorTempEntry};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class SendInstantWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $mobile;
    public $message;
    public $id;

    public function __construct($id, $mobile,$message)
    {
      $this->mobile =  "whatsapp:".$mobile;
      $this->message =  $message;
      $this->id =  $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {
            //code...

            $result = $this->sendWhatsAppMessage($this->mobile, $this->message);
            if ($result['data'] == 'success') {
                 VisitorTempEntry::find($this->id)->where([
                    'msg_sid' => $result['sid'],
                    'msg_sent_status' =>  $result['status'],
                    'msg_date' => date('Y-m-d H:i:s')
                ]);

                Log::info('true');
            }

        } catch (\Throwable $th) {
            //throw $th;
        }



    }

    private function sendWhatsAppMessage($to, $data)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        $message = <<<EOT
        General Announcement and Notification: Please Read Carefully:
        $data
        EOT;



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
}
