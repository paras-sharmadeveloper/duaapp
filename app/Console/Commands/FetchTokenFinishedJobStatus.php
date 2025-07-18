<?php

namespace App\Console\Commands;

use App\Jobs\WhatsappforTempUsers;
use App\Models\JobStatus;
use App\Models\VisitorTemp;
use Illuminate\Console\Command;

class FetchTokenFinishedJobStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-token-finished-job-status';

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

        $jobSta = JobStatus::whereDate('created_at',date('Y-m-d'))->where(['status' => 'token_finished','entry_created' => 'Token_finished'])->get()->toArray();
        // echo "<pre>"; print_r($visitors); die;
        foreach ($jobSta as $jobStatus) {
            $userInputs = json_decode($jobStatus['user_inputs'], true);
            $inputs = $userInputs['inputs'];
            $countryCode = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
            $mobile = $inputs['mobile'];
            $completeNumber =  $countryCode . $mobile;
            $temp =  VisitorTemp::create(['user_inputs' => $jobStatus['user_inputs']]);
            JobStatus::find($jobStatus['id'])->update(['entry_created' => 'Yes']);
            WhatsappforTempUsers::dispatch($temp->id,  $completeNumber)->onQueue('whatsapp-temp-users');
        }
        // Output any information if needed
        $this->info('FetchToken Finished Job Status fetched successfully.');
    }
}
