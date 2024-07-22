<?php

namespace App\Console\Commands;

use App\Jobs\WhatsAppConfirmation;
use App\Jobs\WhatsappforTempUsers;
use App\Models\JobStatus;
use App\Models\{Vistors,VisitorTemp,VenueSloting};
use App\Models\WorkingLady;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
class FetchPendingJobStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-pending-job-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $jobStats = JobStatus::whereDate('created_at',date('Y-m-d'))->where(['entry_created' => 'Pending'])->get()->toArray();

        // echo "<pre>"; print_r($jobStats); die;

        foreach ($jobStats as $jobStatus) {

            try {
                $userInputs = json_decode($jobStatus['user_inputs'], true);

                // $userInputs = $jobStatus['user_inputs'];
                $inputs = $userInputs['inputs'];
                $tokenIs = VenueSloting::where('venue_address_id',$inputs['venueId'])
                ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                ->where(['type' => $inputs['dua_type']])
                ->orderBy('id', 'ASC')
                ->select(['venue_address_id', 'token_id', 'id'])->first();

                if(empty($tokenIs)){
                    return response()->json([
                        'errors' => [
                            'status' => false,
                            'refresh' => true,
                            'message' => 'All Token is issued for the day. please try after some days.',
                            'message_ur' => 'تمام ٹوکن دن کے لیے جاری کیے جاتے ہیں۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                        ]
                    ], 455);
                }

                $tokenId = $tokenIs->token_id;
                $slotId = $tokenIs->id;

                $existingVisitor = Vistors::where('phone', $inputs['mobile'])
                           ->whereDate('created_at', now()->toDateString())
                           ->first();

                if(!$existingVisitor){
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
                    $visitor->phone = $inputs['mobile'];
                    $visitor->is_whatsapp = 'yes';
                    $visitor->booking_uniqueid = $uuid;
                    $visitor->booking_number = $tokenId;//
                    $visitor->user_ip = (isset($inputs['user_ip'])) ? $inputs['user_ip'] : null;
                    $visitor->recognized_code = (!empty($result)) ?  $result['recognized_code'] : null;
                    $visitor->meeting_type = 'on-site';
                    $visitor->source = 'Website';
                    $workingLady = WorkingLady::where('qr_id', $inputs['QrCodeId'])->where('is_active', 'active')->count();
                    $visitor->token_status = 'vaild';

                    $visitor->save();

                    $bookingId = $visitor->id;
                    // WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification')->onConnection('database');
                    WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');
                    JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Yes']);
                }else{
                    $message = "This Token already taken by someone please try again for another token there is limited tokens in system.";
                    JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Duplicate']);
                }










                if ($workingLady == 0 && !empty($inputs['working_lady_id'])) {
                    return response()->json([
                        'errors' => [
                            'status' => false,
                            'message' => 'This Qr is not valid or not active',
                            'message_ur' => 'یہ Qr درست نہیں ہے یا فعال نہیں ہے۔',
                        ]
                    ], 422);
                }



            } catch (QueryException $e) {
                Log::error('Booking error' . $e);

                $errorCode = $e->errorInfo[1];
                $message = "";
                if ($errorCode === 1062) {

                    $message = "This Token already taken by someone please try again for another token there is limited tokens in system.";

                    JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Duplicate']);
                }else{
                    JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Error']);
                    // use Illuminate\Support\Facades\Log;
                }
                $userInputs = json_decode($jobStatus['user_inputs'], true);
                $inputs = $userInputs['inputs'];
                $countryCode = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
                $mobile = $inputs['mobile'];

                $completeNumber =   $countryCode . $mobile;

                $temp =  VisitorTemp::create(['user_inputs' => $jobStatus['user_inputs']]);
                JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Yes']);

                WhatsappforTempUsers::dispatch($temp->id,  $completeNumber,$message)->onQueue('whatsapp-temp-users');
                //throw $th;
            }



        }
        // echo "<pre>"; print_r($jobStats); die;
        // Output any information if needed
        $this->info('Fetch Pending Job Status fetched successfully 111.');
    }
}
