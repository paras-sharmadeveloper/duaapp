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

        $visitors = Vistors::whereDate('created_at',date('Y-m-d'))->whereNull('msg_sid')->get( ['id'])->toArray();


        // echo "<pre>"; print_r($visitors); die;
        foreach ($visitors as $visitor) {
            echo $visitor['id'];
            // WhatsAppConfirmation::dispatch($visitor['id'])->onQueue('whatsapp-notification-resend')->onConnection('database');
            WhatsAppConfirmation::dispatch($visitor['id'])->onQueue('whatsapp-notification');

        }
        // Output any information if needed
        $this->info('Visitors with NULL msg_sid fetched successfully.1');
    }
}

