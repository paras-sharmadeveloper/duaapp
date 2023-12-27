<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $body = $request->all();

        // Extract necessary information from the incoming request
        $from = $body['From'];
        $message = $body['Body'];
        

        // Implement your chatbot logic here, including database interactions
        // Example: Fetch user data from the database based on the incoming message

        // Respond to the user
        $name = 'Qibla Syed Sarfraz Ahmad Shah'; 
        return $this->sendMessage($from, $this->WhatsAppbotMessages($name));
    }

    public function handleFallback(){
        return "ok"; 
    }

    private function sendMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
       
        try {
            $twilio->messages->create(
                "whatsapp:$to",
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


    private function WhatsAppbotMessages($name){
        $message = <<<EOT
        Welcome to the KahayFaqeer.org Dua Appointment WhatsApp Chatbot Scheduler.

        Please note that this dua appointment is valid only for visitors who can physically visit dua ghar in Pakistan. Dua requests via online or phone are not available at the moment. Only proceed if you are fully sure to visit dua ghar in person.
        
        To schedule a dua meeting with $name please enter 1
        EOT;
      return $message;
    }
}
