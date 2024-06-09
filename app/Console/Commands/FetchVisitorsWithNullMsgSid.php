<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vistors;
use Carbon\Carbon;
use App\Jobs\WhatsAppConfirmation;
class FetchVisitorsWithNullMsgSid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-visitors-with-null-msg-sid';

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
        // Get the current date
        $today = Carbon::now()->toDateString();

        // Fetch visitors with NULL msg_sid for today

        $visitors = Vistors::whereDate('created_at',date('Y-m-d'))->whereNull('msg_sid')->get( ['id'])->toArray();


        // $visitors = Vistors::whereDate('created_at', date('Y-m-d'))
        //                     ->whereNull('msg_sid')
        //                     ->get(['id']);

        // Do something with the fetched visitors, like sending notifications
        echo "<pre>"; print_r($visitors); die;
        foreach ($visitors as $visitor) {
            WhatsAppConfirmation::dispatch($visitor['id'])->onQueue('whatsapp-notification')->onConnection('database');

        }

        // Output any information if needed
        $this->info('Visitors with NULL msg_sid fetched successfully.');
    }
}
