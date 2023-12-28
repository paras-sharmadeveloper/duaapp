<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\{VenueAddress,Venue};
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Carbon;
class WhatsAppController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $body = $request->all();
        $today = Carbon::now();
        $NextDate = $today->addDay();
        $newDate = $NextDate->format('Y-m-d');

        // Extract necessary information from the incoming request
        $userPhoneNumber = $body['From'];
        $Respond = $body['Body'];
        Notification::create([ 'message' => json_encode($body)]);
 
        $countryId = Venue::where(['iso' => 'PK'])->get()->first();
       $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
        ->where(function ($query) use ($newDate) {
        $query->where('venue_date', '>=', $newDate) // Use '>=' instead of '>'
            ->orWhereDate('venue_date', '=', now()->format('Y-m-d')); // Use now() instead of date()
        })
        ->where('venue_date', '>=', now()->format('Y-m-d'))
        ->take(3)
        ->get();
        $cityArr = []; 
        foreach($venuesListArr as $venue){
            $cityArr[] = $venue->city; 
        }


         $currentStep = $this->getCurrentStep($userPhoneNumber);


        switch ($currentStep) {
            case 1: 
                $data = 'Qibla Syed Sarfraz Ahmad Shah'; 
                $this->sendMessage($userPhoneNumber, $this->WhatsAppbotMessages($data,1));
                $this->setCurrentStep($userPhoneNumber, 2,$selectedOpt = ''); 
                $this->setCurrentStep($userPhoneNumber, 2);
                break;
            case 2: 
                    $cityName = ''; 
                    $i =1 ; 
                    foreach($cityArr as $city){
                        $cityName+= $i. ' ' . $city; 
                        $i++;
                    }
                $data = 'Please enter the number for your city
                '.$cityName; 
                $this->sendMessage($userPhoneNumber, $this->WhatsAppbotMessages($data,2)); 
                
                $this->setCurrentStep($userPhoneNumber, 3);
                break;
            case 3:
                // Assume the date/time is provided in the incoming message
                $data = 'Qibla Syed Sarfraz Ahmad Shah'; 
                $this->sendMessage($userPhoneNumber, $this->WhatsAppbotMessages($data,3));
                $this->setCurrentStep($userPhoneNumber, 4);
                break;
            case 3:
                    // Assume the date/time is provided in the incoming message
                $data = 'Qibla Syed Sarfraz Ahmad Shah'; 
                $this->sendMessage($userPhoneNumber, $this->WhatsAppbotMessages($data,4));
                $this->setCurrentStep($userPhoneNumber, 5);
                break;
            default:
                $responseMessage = "Invalid selection. Please enter a valid option.";
                break;
        }

     
        
    }

    private function getCurrentStep($userPhoneNumber)
    {   
        return session()->get("current_step_$userPhoneNumber", 1);
    }


    private function setCurrentStep($userPhoneNumber, $step,$selectedOpt = '')
    { 
        session()->put([
            "current_step_$userPhoneNumber" =>  $step,
            "selected_option_$userPhoneNumber" => $selectedOpt,
           
        ]);
        // session()->put("current_step_$userPhoneNumber", $step);
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


    private function WhatsAppbotMessages($data,$step){
        if($step == '1'){

            $message = <<<EOT
            Welcome to the KahayFaqeer.org Dua Appointment WhatsApp Chatbot Scheduler.

            Please note that this dua appointment is valid only for visitors who can physically visit dua ghar in Pakistan. Dua requests via online or phone are not available at the moment. Only proceed if you are fully sure to visit dua ghar in person.
            
            To schedule a dua meeting with $data please enter 1
            EOT;

        }else if($step == '2'){
            $message = <<<EOT
                Please enter the number for your city
                $data
            EOT; 
        } else if($step == '3'){

        } else if($step == '4'){

        } else if($step == '5'){

        }else{

           

        }
        
      return $message;
    }
}
