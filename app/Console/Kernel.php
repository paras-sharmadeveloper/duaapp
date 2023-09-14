<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('queue:work --queue=default --sleep=3 --tries=3')->everyMinute();

        // Schedule a task for a 'high-priority' queue
        $schedule->command('queue:work --queue=send-email --sleep=3 --tries=3')->everyMinute();
    
        // Schedule a task for a 'low-priority' queue
        $schedule->command('queue:work --queue=send-message --sleep=3 --tries=3')->everyMinute();
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
