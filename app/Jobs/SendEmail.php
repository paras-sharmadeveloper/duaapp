<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\BookingConfirmationEmail;
use Illuminate\Support\Facades\Mail;  
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;
    protected $recipient;
    public function __construct($recipient, $data)
    {
        $this->data = $data;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    { 
        try {
           Mail::to($this->recipient)->send(new BookingConfirmationEmail($this->data)); 
  
        } catch (\Exception $e) {
            Log::error('Job failed: ' . $e->getMessage());
         
        } 
    }
}
