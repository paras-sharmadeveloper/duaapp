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
        $this->sendMessage($from, "Hello! Your message was: $message");
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
}
