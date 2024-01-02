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



    public function handleBookMeeting()
    {
        $response = new VoiceResponse();

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
        request()->session()->put('cityArr', array_unique($cityArr));

       
        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    public function handleDates()
    {
        $userInput = request('Digits');
        $response = new VoiceResponse();
        $storedCityArr =  request()->session()->get('cityArr');

        // $storedCityArr = session('cityArr');
        $cityName = $storedCityArr[$userInput];

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
        session(['datesArr' => $VenueDatesAadd]);
        return response($response, 200)->header('Content-Type', 'text/xml');

        

       
 
    }


    public function handleSlots()
    {

        $userInput = request('Digits');
        $response = new VoiceResponse();

        $storedCityArr = session('datesArr');
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

        // $gather = $response->gather([
        //     'numDigits' => 1,
        //     'action' => route('ivr.dates'),
        // ]);

    }

     
}
