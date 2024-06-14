<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FetchVisitorsWithNullMsgSid;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\FetchVisitorsWithNullMsgSid::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --queue=default,high,low,face-recognition,create-slots,whatsapp-notification,create-future-dates')
        ->everyMinute()
        ->withoutOverlapping();
        $schedule->command(FetchVisitorsWithNullMsgSid::class)->everyThirtyMinutes();

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
