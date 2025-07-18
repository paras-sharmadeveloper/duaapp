<?php

namespace App\Events;
 
use Illuminate\Broadcasting\InteractsWithSockets; 
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class SendNotifications implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
  
    public function __construct($message)
    {
        $this->message = $message;
    }
  
    public function broadcastOn()
    {
        return ['my-channel-local'];
    }
  
    public function broadcastAs()
    {
        return 'my-event-local';
    }  
}

