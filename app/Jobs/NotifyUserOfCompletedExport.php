<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\ExportReady;
use Illuminate\Support\Facades\Notification;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $user;
    public $fileName;
    public $to; 
    public function __construct($filename,$to=[])
    {
        $this->fileName = $filename;
        $this->to = $to; 
    }

    public function handle()
    {
        Notification::route('mail',$this->to)->notify(new ExportReady($this->fileName));
       
    }
}
