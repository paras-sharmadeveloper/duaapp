<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\{VenueAddress, Venue, WhatsApp, VenueSloting, Vistors};
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

        // $dataArr = [
        //     'customer_number' => $userPhoneNumber,
        //     'customer_response' => $Respond,
        //     'bot_reply' => "It is working",
        //     'data_sent_to_customer' => '',
        //     'last_reply_time' => date('Y-m-d H:i:s'),
        //     'steps' => 1
        // ];
        // WhatsApp::create($dataArr);
        // return 1; 

        $existingCustomer = WhatsApp::where(['customer_number' =>  $userPhoneNumber])->orderBy('created_at', 'desc')->first();
        $dataArr = [];
        $countryId = Venue::where(['iso' => 'PK'])->get()->first();
        if (empty($existingCustomer)) {
            $step = 1;
            $data = 'Qibla Syed Sarfraz Ahmad Shah';
            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => null,
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step
            ];
            WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && ($existingCustomer->steps == 1 ||  $Respond == 'Press 1')) { // send Cites here
            $step = $existingCustomer->steps + 1;
            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where(function ($query) use ($newDate) {
                    $query->where('venue_date', '>=', $newDate)  
                        ->orWhereDate('venue_date', '=', now()->format('Y-m-d'));   
                })
                ->where('venue_date', '>=', now()->format('Y-m-d'))
                ->take(3)
                ->get();
            $cityArr = [];
            $i = 1;
            foreach ($venuesListArr as $venue) {
                $cityArr[$i] = $i . ' '. $venue->city;
            
                $i++;
            }
            $data = implode("\n", $cityArr);
          
            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($cityArr),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step
            ];
            WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && $existingCustomer->steps == 2) { // send Dates here
            $step = $existingCustomer->steps + 1;
            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
            $getDate = $data_sent_to_customer[$Respond];

            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('venue_date', '>=', $getDate)
                ->orderBy('venue_date', 'ASC')
                ->take(3)
                ->get();

           

            $VenueDates = [];
            $i = 1;
            foreach ($venuesListArr as $venueDate) {
                $VenueDates[$i] = $i . ' ' . $venueDate->venue_date;
                $i++;
            }


            $data = implode(',', $VenueDates);
            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($VenueDates),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step
            ];
            WhatsApp::create($dataArr);
        }else if (!empty($existingCustomer) && $existingCustomer->steps == 3) { // send Slots  here
            $step = $existingCustomer->steps + 1;
            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
            $getDate = $data_sent_to_customer[$Respond];

            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('venue_date', '>=', $getDate)->first();

            $slots = VenueSloting::where(['venue_address_id' => $venuesListArr->id])
                ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                ->orderBy('slot_time', 'ASC')
                ->take(3)
                ->get();

            $slotArr = [];
            $i = 1;
            foreach ($slots->venueSloting as $slot) {
                $slotArr[$i] = $i . ' '. $slot->slot_time;

              //   $slotArr[$i] = $i . ' ' . $slot->slot_time;
                $i++;
            }


            $data = implode("\n", $slotArr); // Use "\n" for a new line

            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($slotArr),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step
            ];
            WhatsApp::create($dataArr);
        }else{
            $message = <<<EOT
                Your Dua Appointment Confirmed With {{1}} ✅

                    Event Date : {{2}}

                    Venue : {{3}}

                    {{4}}

                    Token #{{5}}

                    Your Mobile : {{6}}

                    Your Appointment Time : {{7}}

                    Appointment Duration : {{8}}

                    {{9}}
                    To view your token online please click below:

                    {{10}}

                    {{11}}
                
            EOT;
        }
    } 



    public function handleFallback()
    {
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


    private function WhatsAppbotMessages($data, $step)
    {
        $message = ''; 
        if ($step == 1) {
            $message = <<<EOT
            Welcome to the KahayFaqeer.org Dua Appointment WhatsApp Chatbot Scheduler.

            Please note that this dua appointment is valid only for visitors who can physically visit dua ghar in Pakistan. Dua requests via online or phone are not available at the moment. Only proceed if you are fully sure to visit dua ghar in person.
            
            To schedule a dua meeting with $data please enter 1
            EOT;
        } else if ($step == 2) {
            $message = <<<EOT
                Please enter the number for your city
                $data
            EOT;
        } else if ($step == 3) {

            $message = <<<EOT
            Please enter the number below on which date slot you want to schedule your dua meeting?

            $data   
        EOT;


           
        } else if ($step == 4) {

            $message = <<<EOT
            Please enter the number below on which time slot you want to schedule your dua meeting?

            $data 
            EOT;
        } else if ($step == '5') {

            $message = <<<EOT
            Your Dua Appointment Confirmed With {{1}} ✅

                Event Date : {{2}}

                Venue : {{3}}

                {{4}}

                Token #{{5}}

                Your Mobile : {{6}}

                Your Appointment Time : {{7}}

                Appointment Duration : {{8}}

                {{9}}
                To view your token online please click below:

                {{10}}

                {{11}}
            
        EOT;
        } else {
        }

        return $message;
    }
}
