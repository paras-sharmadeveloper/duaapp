<?php

namespace App\Jobs;

use App\Models\VisitorTemp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsappforTempUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $phone;

    public $data;
    public function __construct($id , $mobile,$data='')
    {
        $this->id = $id;
        $this->phone = $mobile;
        $this->data = $data;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $mobile =  'whatsapp:' . $this->phone;

            if(!empty($this->data)){
                $data = $this->data;
            }else{
                $data = 'Due to high traffic all tokens have been issued for today. Kindly please try next Monday at 08:00 AM sharp. Thank you';
            }


            $message = <<<EOT
            Please see the below warning message:
            $data
            EOT;


            $result = $this->sendWhatsAppMessage($mobile, $message);
            if ($result['data'] == 'success') {
                VisitorTemp::find($this->id)->update([
                    'msg_sent_status' => (!empty($result)) ?   $result['status'] : '',
                    'msg_sid' => (!empty($result)) ? $result['sid'] : '',
                    'msg_date' =>  Carbon::now(),

                ]);
                Log::info('true');
            }
            // Log::info('false check Env'.env('TWILIO_ACCOUNT_SID'));

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
                    "statusCallback" => route('twillio.status.callback.temp')
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
