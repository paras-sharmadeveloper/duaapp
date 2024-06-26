<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{ Vistors,JobStatus, WorkingLady};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Jobs\{WhatsAppConfirmation};
class CreateVisitorEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobId;
    public function __construct($jobid)
    {
        $this->jobId = $jobid;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $jobStatus = JobStatus::where(['job_id' =>  $this->jobId ])->get()->first();
        if (!empty($jobStatus) && $jobStatus['status'] == 'completed' ) {
            $result = $jobStatus['result'];
            // $result = json_decode($jobStatus['result']);
            if ($result['status']) {

                try {
                    $userInputs = json_decode($jobStatus['user_inputs'], true);
                    // $userInputs = $jobStatus['user_inputs'];
                    $inputs = $userInputs['inputs'];
                    $uuid = Str::uuid()->toString();
                    // Create a new Visitor record
                    $visitor = new Vistors();
                    $visitor->slot_id = $inputs['slotId'];
                    $visitor->dua_type = $inputs['dua_type'];
                    $visitor->working_lady_id = $inputs['working_lady_id'];
                    $visitor->dua_type = $inputs['duaType'];
                    $visitor->user_timezone = $inputs['timezone'];
                    $visitor->lang = $inputs['lang'];
                    $visitor->country_code = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
                    // $visitor->country_code = (strpos($inputs['country_code'],'+')) ? $inputs['country_code'] : '+'.$inputs['country_code'];
                    $visitor->phone = $inputs['mobile'];
                    $visitor->is_whatsapp = 'yes';
                    $visitor->booking_uniqueid = $uuid;
                    $visitor->booking_number = $inputs['tokenId']; //
                    $visitor->user_ip = (isset($inputs['user_ip'])) ? $inputs['user_ip'] : null;
                    $visitor->recognized_code = (!empty($result)) ?  $result['recognized_code'] : null;
                    $visitor->meeting_type = 'on-site';
                    $visitor->source = 'Website';
                    $workingLady = WorkingLady::where('qr_id', $inputs['QrCodeId'])->where('is_active', 'active')->count();


                    if ($workingLady == 0 && !empty($inputs['working_lady_id'])) {
                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'message' => 'This Qr is not valid or not active',
                                'message_ur' => 'یہ Qr درست نہیں ہے یا فعال نہیں ہے۔',
                            ]
                        ], 422);
                    }

                    $visitor->token_status = 'vaild';
                    $visitor->save();
                    $bookingId = $visitor->id;
                    // WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification')->onConnection('database');
                    WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');

                    return response()->json([
                        'message' => 'Booking submitted successfully',
                        "status" => true,
                        'bookingId' => $uuid,
                        'redirect_url' => route('booking.status', [$uuid])
                    ], 200);
                } catch (QueryException $e) {
                    Log::error('Booking error' . $e);

                    $errorCode = $e->errorInfo[1];

                    if ($errorCode === 1062) {

                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'refresh' => true,
                                'message' => trans('messages.slot_id'),
                                'message_ur' => 'یہ ٹوکن اس سیکنڈ میں کسی اور نے بک کروایا ہے۔ ٹوکن دوبارہ بک کرنے کے لیے براہ کرم اپنے براؤزر کو ریفریش کریں۔ ایک ہی وقت میں سینکڑوں دوسرے لوگ بھی ٹوکن بک کرنے کی کوشش کر رہے ہیں۔ دوسرا بک کرنے کے لیے براہ کرم اپنی اسکرین ریفریش کریں۔'
                            ]
                        ], 455);



                    } else {
                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'refresh' => false,
                                'message' => $e->getMessage(),
                            ]
                        ], 455);
                    }

                    // WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification-send-er')->onConnection('database');

                } catch (\Exception $e) {
                    // Log any other exceptions
                    Log::error('Exception: ' . $e->getMessage());

                    return response()->json([
                        'errors' => [
                            'status' => false,
                            'message' =>  $e->getMessage(),
                        ]
                    ], 455);

                }
            } else {
                return response()->json([
                    'errors' => [
                        'status' => false,
                        'message' => 'You already book Token with us.Please try after few days',
                        'message_ur' => 'آپ پہلے سے ہی ہمارے ساتھ ٹوکن بک کر چکے ہیں۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                    ]
                ], 455);
            }
        }else if($jobStatus['status'] == 'error'){
            return response()->json([
                'errors' => [
                    'status' => false,
                    'refresh' => true,
                    'message' => 'There Might be some issue at backend please try after some time.',
                    'message_ur' => 'بیک اینڈ پر کچھ مسئلہ ہو سکتا ہے براہ کرم کچھ دیر بعد کوشش کریں۔',
                ]
            ], 455);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Job not Completed',
            ], 422);
        }
    }
}
