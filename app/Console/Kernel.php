<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\{FetchVisitorsWithNullMsgSid,FetchPendingJobStatus,FetchTokenFinishedJobStatus};

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\FetchVisitorsWithNullMsgSid::class,
        Commands\FetchPendingJobStatus::class,
        Commands\FetchTokenFinishedJobStatus::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->exec('/usr/bin/php8.2 artisan queue:work --queue=default,high,low,face-recognition,create-slots,whatsapp-notification,create-future-dates,whatsapp-notification-resend')
        //  ->everyTwoMinutes()
        //  ->withoutOverlapping();

        // $schedule->command('queue:work --queue=default,high,low,face-recognition,create-slots,whatsapp-notification,whatsapp-notification-resend,create-future-dates')
        // ->everyTwoMinutes()
        // ->withoutOverlapping()
        // ->sendOutputTo('storage/logs/scheduler.log');
        $schedule->command(FetchVisitorsWithNullMsgSid::class)->everyFiveMinutes();
        $schedule->command(FetchPendingJobStatus::class)->everyFiveMinutes();
        $schedule->command(FetchTokenFinishedJobStatus::class)->everyFiveMinutes();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}


