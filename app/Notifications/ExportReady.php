<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
class ExportReady extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $filename; 
    public function __construct($filename)
    {
        //
        $this->filename = $filename;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    { 
        // $storagePath = __DIR__.'/storage/app/';  
        // $storagePath = storage_path('/storage/exports/');
        // $storagePath = storage_path('/storage/app/public/');
        // $path = $this->UplaodToS3($this->filename,$storagePath.$this->filename); 
        
        

        $compleatePath =  'https://'.env('AWS_BUCKET_GENERAL').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com/'.$this->filename; 

        // unlink($storagePath.$this->filename); 
        return (new MailMessage)
                    ->line('Your Export is Ready to Download. Click Below to Download')
                    // ->action($this->filename,url('/storage/app/'.$this->filename))
                    ->action($this->filename,$compleatePath )
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }


    public function UplaodToS3($fileName,$file){
         $path = Storage::disk('s3_general')->put($fileName, file_get_contents($file));
         $path = Storage::disk('s3_general')->url($path); 
         return $path; 
    }

     
}
