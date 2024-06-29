<?php

namespace App\Console\Commands;

use App\Jobs\WhatsAppConfirmation;
use App\Models\JobStatus;
use App\Models\Vistors;
use App\Models\WorkingLady;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
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

        $jobStats = JobStatus::whereDate('created_at',date('Y-m-d'))->where(['entry_created' => 'Pending'])->get( ['id'])->toArray();
        // echo "<pre>"; print_r($visitors); die;
        foreach ($jobStats as $jobStatus) {
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
            $jobStatus->update(['entry_created' => 'Yes']);

        }
        // Output any information if needed
        $this->info('Visitors with NULL msg_sid fetched successfully.');
    }
}
