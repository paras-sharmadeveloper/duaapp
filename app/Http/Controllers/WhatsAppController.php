<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $body = $request->all();

        // Extract necessary information from the incoming request
        $from = $body['From'];
        $Respond = $body['Body'];
        Notification::create([ 'message' => json_encode($body)]);

        if($Respond == 'Press 1'){
            $data =  '1️⃣ Lahore , 2️⃣ Islamabad'; 
            return $this->sendMessage($from, $this->WhatsAppbotMessages($data,$Respond));
        }else{
            $data ="Qibla Syed Sarfraz Ahmad Shah";
            return $this->sendMessage($from, $this->WhatsAppbotMessages($data,$Respond));
        }

     
        
    }

    public function handleFallback(){
        return "ok"; 
    }

    private function sendMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
       
        try {
            $twilio->messages->create(
                "$to",
                [
                    'from' => "whatsapp:" . env('TWILIO_PHONE_WHATSAPP'),
                    'body' => $message,
                ]
            );
            return response()->json(['data' => 'success']); 
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['error' => $e->getMessage()]); 
        }

        
    }


    private function WhatsAppbotMessages($data,$Respond){
        if($Respond == 'Press 1'){
            $message = <<<EOT
                Please enter the number for your city
                $data
            EOT; 
        }else{

            $message = <<<EOT
            Welcome to the KahayFaqeer.org Dua Appointment WhatsApp Chatbot Scheduler.

            Please note that this dua appointment is valid only for visitors who can physically visit dua ghar in Pakistan. Dua requests via online or phone are not available at the moment. Only proceed if you are fully sure to visit dua ghar in person.
            
            To schedule a dua meeting with $data please enter 1
            EOT;

        }
        
      return $message;
    }
}
