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
use App\Models\Vistors;
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $tries = 3; // Number of retry attempts
    public $retryAfter = 60; // Delay between retries (in seconds)

    protected $data;
    protected $recipient;
    protected $visitorId;
    public function __construct($recipient, $data,$id)
    {
        $this->data = $data;
        $this->recipient = $recipient;
        $this->visitorId = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
           Mail::to($this->recipient)->send(new BookingConfirmationEmail($this->data));
           Vistors::find($this->visitorId)->update(['email_sent_at' => date('y-m-d H:i:s')]);



        } catch (\Exception $e) {
            Log::error('Job failed: ' . $e->getMessage());

        }
    }

}
