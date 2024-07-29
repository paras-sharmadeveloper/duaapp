<?php

namespace App\Console\Commands;

use App\Jobs\WhatsappforTempUsers;
use App\Models\VisitorTemp;
use Illuminate\Console\Command;

class RetryVisitorTempFailedMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:retry-visitor-temp-failed-message';

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
        $jobSta = VisitorTemp::whereDate('created_at', date('Y-m-d'))
        // ->where('msg_sent_status', 'failed')
        // ->limit(2)
        ->get()
        ->toArray();        // echo "<pre>"; print_r($visitors); die;
        foreach ($jobSta as $jobStatus) {
            $userInputs = json_decode($jobStatus['user_inputs'], true);
            $inputs = $userInputs['inputs'];
            $countryCode = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
            $mobile = $inputs['mobile'];
            $completeNumber =  $countryCode . $mobile;
           //  $temp =  VisitorTemp::where(['id' => $jobStatus['id']])->update(['user_inputs' => $jobStatus['user_inputs']]);
            // JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Yes']);
            $message = 'Due to high traffic all tokens have been issued for today. Kindly please try next week at 08:00 AM sharply. Thank you';
            WhatsappforTempUsers::dispatch($jobStatus['id'],  $completeNumber,$message)->onQueue('whatsapp-temp-users');
        }
        // Output any information if needed
        $this->info('FetchToken Finished Job Status fetched successfully.');
    }

}
