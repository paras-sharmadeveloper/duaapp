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
            // WhatsAppConfirmation::dispatch($visitor['id'])->onQueue('whatsapp-notification-resend')->onConnection('database');
            WhatsAppConfirmation::dispatch($visitor['id'])->onQueue('whatsapp-notification-resend');

        }
        // Output any information if needed
        $this->info('Visitors with NULL msg_sid fetched successfully.');
    }
}
// sudo nano /etc/supervisor/conf.d/app-kahayfaqeer-horizon.conf

// [program:app-kahayfaqeer-horizon]
// process_name=%(program_name)s_%(process_num)02d
// command=php /home/kahayfaqeer/public_html/Token_App/artisan horizon
// autostart=true
// autorestart=true
// user=kahayfaqeer
// redirect_stderr=true
// stdout_logfile=/home/kahayfaqeer/public_html/Token_App/horizon.log
// stopwaitsecs=3600
// sudo supervisorctl start appkahayfaqeerhorizon
// sudo supervisorctl status appkahayfaqeerhorizon

// sudo chown -R www-data:www-data /home/kahayfaqeer/public_html/Token_App/storage
// sudo chmod -R 777 //home/kahayfaqeer/public_html/Token_App/storage

// php /home/kahayfaqeer/public_html/Token_App/artisan schedule:run >> /home/kahayfaqeer/public_html/cron-ta.log
