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

        $existingCustomer = WhatsApp::where(['customer_number' =>  $userPhoneNumber])->first();
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
        } else if (!empty($existingCustomer) && !empty($existingCustomer) == 1) {
            $step = 2;
            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where(function ($query) use ($newDate) {
                    $query->where('venue_date', '>=', $newDate) // Use '>=' instead of '>'
                        ->orWhereDate('venue_date', '=', now()->format('Y-m-d')); // Use now() instead of date()
                })
                ->where('venue_date', '>=', now()->format('Y-m-d'))
                ->take(3)
                ->get();
            $cityArr = [];
            $i = 1;
            foreach ($venuesListArr as $venue) {
                $cityArr[$i] = $i . ' ' . $venue->city;
                $i++;
            }
            $data = implode(',', $cityArr);
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
        } else if (!empty($existingCustomer) && !empty($existingCustomer) == 1) {
            $step = 3;
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
                $slotArr[$i] = $i . ' ' . $slot->slot_time;
                $i++;
            }


            $data = implode(',', $slotArr);
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
        } else if ($step == '3') {
        } else if ($step == '4') {
        } else if ($step == '5') {
        } else {
        }

        return $message;
    }
}
