<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use App\Models\{VenueAddress, Venue, WhatsApp, VenueSloting, Vistors};
use Carbon\Carbon;

class TwillioIVRHandleController extends Controller
{
    protected $statementUrl;
    protected $cityUrl;
    protected $numbersUrl;
    protected $country;

    public function __construct()
    {
        $this->statementUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/statements/';
        $this->cityUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/city/';
        $this->numbersUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/numbers/';
        $this->country = Venue::where(['iso' => 'PK'])->get()->first();
        
    }

    public function handleIncomingCall()
    {
        $fromCountry = request('FromCountry'); 
        $customer = request('From'); 

        $dataArr = [
            'customer_number' => $customer,
            'customer_response' => null,
            'bot_reply' =>  null,
            'data_sent_to_customer' => 'twillio',
            'last_reply_time' => date('Y-m-d H:i:s'),
            'steps' => 1
        ];
        WhatsApp::create($dataArr);
        // if($fromCountry  == 'PK'){

        // }
        $response = new VoiceResponse();
        // STEP 1: Welcome Message
        $response->play($this->statementUrl . 'statement_welcome_message.wav');

        $response->play($this->statementUrl . 'statement_bookmeeting.wav');
        $response->play($this->numbersUrl . 'number_01.wav');
        $response->play($this->statementUrl . 'statement_press.wav');



        // Prompt user to press any key to proceed
        $gather = $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.bookmeeting'),
        ]);


        // Set the response content type to XML
        header("Content-type: text/xml");

        // Laravel specific: return a response with the TwiML content
        return response($response, 200)->header('Content-Type', 'text/xml');
    }


    private function getexistingCustomer($userPhoneNumber){
        return WhatsApp::where(['customer_number' =>  $userPhoneNumber])->orderBy('created_at', 'desc')->first();
    }



    public function handleBookMeeting()
    {
        $response = new VoiceResponse();
        $userInput = request('Digits');
        $customer = request('From'); 
        $venuesListArr = VenueAddress::where('venue_id', $this->country->id)
            ->where('venue_date', '>=', date('Y-m-d'))
            ->take(3)
            ->get();
        $i = 1;
        $cityArr = [];
        foreach ($venuesListArr as $venue) {

            $cityArr[$i] = strtolower($venue->city);

            $i++;
        }

        foreach (array_unique($cityArr) as $k => $city) {

            if ($k <= 9) {
                $number = '0' . $k;
            } else {
                $number = $k;
            }

            $response->play($this->cityUrl . 'city_' . $city . '.wav');
            $response->play($this->statementUrl . 'statement_kay_liye.wav');
            $response->play($this->numbersUrl . 'number_' . $number . '.wav');
            $response->play($this->statementUrl . 'statement_press.wav');
        }
        $gather = $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.dates'),
        ]);

        $exsitingCustomer = $this->getexistingCustomer($customer);
        if($exsitingCustomer){
            $dataArr = [
                'customer_number' => request('From'),
                'customer_response' => $userInput ,
                'bot_reply' =>  json_encode(array_unique($cityArr)),
                'data_sent_to_customer' => 'twillio',
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => 2
            ];
        WhatsApp::where(['id' =>  $exsitingCustomer->id ])->update($dataArr);
        }
        

       
        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    public function handleDates()
    {
        $userInput = request('Digits');
        $response = new VoiceResponse();
        $storedCityArr =  request()->session()->get('cityArr');

        $customer = request('From'); 

        $exsitingCustomer = $this->getexistingCustomer($customer);
        if($exsitingCustomer){
            $asdas = json_decode($exsitingCustomer->bot_reply , true); 
            $cityName = $asdas[$userInput];

            $venuesListArr = VenueAddress::where('venue_id', $this->country->id)
            ->where('city',  $cityName)
            ->where('venue_date', '>=', date('Y-m-d'))
            ->orderBy('venue_date', 'ASC')
            ->take(3)
            ->get();

            $VenueDates = [];
            $VenueDatesAadd = [];

            $i = 1;
            foreach ($venuesListArr as $venueDate) {
                $currentDate = Carbon::parse($venueDate->venue_date);
                $VenueDates[$i] = $currentDate->format('j M Y');
                $VenueDatesAadd[$i] = $venueDate->id;
                $i++;
            }

            foreach ($VenueDates as $k => $date) {
                $response->say('Press ' . $k . ' for ' . $date . '');
            }
            $gather = $response->gather([
                'numDigits' => 1,
                'action' => route('ivr.time'),
            ]); 
           
            
                $dataArr = [
                    'customer_number' => request('From'),
                    'customer_response' => $userInput ,
                    'bot_reply' =>  json_encode(array_unique($VenueDatesAadd)),
                    'data_sent_to_customer' => 'twillio',
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => 3
                ];
            WhatsApp::where(['id' =>  $exsitingCustomer->id ])->update($dataArr);
           return response($response, 200)->header('Content-Type', 'text/xml');
        }

        // $storedCityArr = session('cityArr');
      

        

        

       
 
    }


    public function handleSlots()
    {

        $userInput = request('Digits');
        $response = new VoiceResponse();

        $customer = request('From'); 
        $exsitingCustomer = $this->getexistingCustomer($customer);


        if($exsitingCustomer){
            $asdas = json_decode($exsitingCustomer->bot_reply , true); 
            $storedCityArr = $asdas[$userInput];

     
            $venueAddreId = $storedCityArr[$userInput];
            $slots = VenueSloting::where(['venue_address_id' => $venueAddreId])
                ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                ->orderBy('slot_time', 'ASC')
                ->take(3)
                ->get();

            $slotArr = [];
            $i = 1;
            foreach ($slots as $slot) {
                $timestamp = strtotime($slot->slot_time);
                $slotTime = date('h:i A', $timestamp);
                $response->say('Press ' . $i . ' to book slot ' . $slotTime . '');
                // $slotArr[$slot->id] =  $slotTime;
                // $options[] = $i;
                $i++;
            }

            return response($response, 200)->header('Content-Type', 'text/xml');
     }

        // $gather = $response->gather([
        //     'numDigits' => 1,
        //     'action' => route('ivr.dates'),
        // ]);

    }

     
}
