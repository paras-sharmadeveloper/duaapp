<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

use App\Notifications\ExportReady;
class ExportNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $user;
    public $fileName;
    public $to; 
    public function __construct($user,$filename,$to=[])
    {
        $this->user = $user;
        $this->fileName = $filename;
        $this->to = $to; 
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Notification::route('mail', $this->to)->notify(new ExportReady($this->fileName));
       
    }
}
