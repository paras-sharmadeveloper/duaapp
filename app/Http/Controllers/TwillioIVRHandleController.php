<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use App\Models\{VenueAddress, Venue, TwillioIvrResponse, VenueSloting, Vistors, Country};

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TwillioIVRHandleController extends Controller
{
    protected $statementUrl;
    protected $cityUrl;
    protected $numbersUrl;
    protected $country;
    protected $monthsIvr;
    protected $yearsIvr;

    public function __construct()
    {
        $this->statementUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/statements/';
        $this->cityUrl      = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/city/';
        $this->numbersUrl   = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/numbers/';
        $this->monthsIvr    = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/months/';
        $this->yearsIvr     = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/years/';
        $this->country      = Venue::where(['iso' => 'PK'])->get()->first();
    }

    public function handleIncomingCall(Request $request)
    {

        $response = new VoiceResponse();


        $fromCountry = $request->input('FromCountry');
        $customer = $request->input('From');
        $userInput = $request->input('Digits');
       
        // STEP 1: Welcome Message
        $response->play($this->statementUrl . 'statement_welcome_message.wav');

        $response->play($this->statementUrl . 'statement_bookmeeting.wav');
        $response->play($this->numbersUrl . 'number_01.wav');
        $response->play($this->statementUrl . 'statement_press.wav');

        // Prompt user to press any key to proceed

        $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.pickcity'),
            'timeout' => 10, // Set the timeout to 10 seconds
        ]);
        $options = ["1" => 1];

        TwillioIvrResponse::create([
            'mobile' => $customer,
            'response_digit' => $request->input('Digits', 0),
            'attempts' => 1,
            'route_action' => 'ivr.start',
            'customer_options' => json_encode($options)

        ]);
 
        return response($response, 200)->header('Content-Type', 'text/xml');
    }


    public function StartFlow(Request $request){

        $response = new VoiceResponse(); 
        $userInput = $request->input('Digits');

        $existingData = $this->getexistingCustomer($request->input('From'));
        if (!empty($existingData)) {
            $customer_option = json_decode($existingData->customer_options, true);
            if (array_key_exists($userInput,  $customer_option)) {
                $response->redirect(route('ivr.pickcity'));
            }else{

                $response->play($this->statementUrl . 'wrong_number_input.wav');
                $response->redirect(route('ivr.welcome'));
                $attempts  = $existingData->attempts + 1; 
                $existingData->update(['attempts' =>  $attempts]);
            }
        }
        return response($response, 200)->header('Content-Type', 'text/xml');

    }

    public function handleCity(Request $request)
    {
        $response = new VoiceResponse();
        $userInput = $request->input('Digits');
        $existingData = $this->getexistingCustomer($request->input('From'));

        if (!empty($existingData)) {
            $customer_option = json_decode($existingData->customer_options, true);

            if (array_key_exists($userInput, $customer_option)) {

                $response->play($this->statementUrl . 'statement_select_city.wav');
                $query = $this->getDataFromVenue();
                $venuesListArr = $query->get();
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
                $response->gather([
                    'numDigits' => 1,
                    'action' => route('ivr.dates'),
                    'timeout' => 10
                ]);
                $this->SaveLog($request, array_unique($cityArr), 'ivr.dates');
                return response($response, 200)->header('Content-Type', 'text/xml');
            } else {
                $response->play($this->statementUrl . 'wrong_number_input.wav');

                foreach($customer_option as $nu =>  $options){
                    if ($nu <= 9) {
                        $number = '0' . $nu;
                    } else { 
                        $number = $nu;
                    }
                     
                    $response->play($this->cityUrl . 'city_' . $options . '.wav');
                    $response->play($this->statementUrl . 'statement_kay_liye.wav');
                    $response->play($this->numbersUrl . 'number_' . $number . '.wav');
                    $response->play($this->statementUrl . 'statement_press.wav');
                } 
                $attempts  = $existingData->attempts + 1; 
                $existingData->update(['attempts' =>  $attempts]); 
                return response($response, 200)->header('Content-Type', 'text/xml');
            }

            
        }  

    }


    public function handleDates(Request $request)
    {
        $response = new VoiceResponse();

        $userInput = $request->input('Digits');

        $existingData = $this->getexistingCustomer($request->input('From'));

        if (!empty($existingData)) {
            $customer_option = json_decode($existingData->customer_options, true);
            if (array_key_exists($userInput,  $customer_option)) {

                $cityName = $customer_option[$userInput];

                $query = $this->getDataFromVenue();
                $venuesListArr =   $query->where('city',  $cityName)->orderBy('venue_date', 'ASC')->take(3)->get();

                $VenueDates = [];
                $VenueDatesAadd = [];

                $i = 1;
                foreach ($venuesListArr as $venueDate) {
                    $columnToShow = $venueDate->combinationData->columns_to_show;
                    $venueStartTime = Carbon::parse($venueDate->venue_date . ' ' . $venueDate->slot_starts_at_morning);

                    if ($venueStartTime <=  Carbon::now() && $columnToShow >= $i) {
                        $VenueDates[$i] = $venueDate->venue_date;
                        $VenueDatesAadd[$i] = $venueDate->id;
                        // $VenueDates[$venueDate->id] = trim($whatsAppEmoji[$i]. ' ' .$venueDate->venue_date);

                        $i++;
                    } else if ($columnToShow >= $i && $venueDate->venue_date > Carbon::now()->format('Y-m-d')) {
                        $VenueDates[$i] = $venueDate->venue_date;
                        $VenueDatesAadd[$i] = $venueDate->id;
                        $i++;
                    }
                }

                foreach ($VenueDates as $k => $date) {

                    $datesArr = explode('-', $date);
                    $year = $datesArr[0];
                    $month = $datesArr[1];
                    $day = $datesArr[2];
                    if ($k <= 9) {
                        $number = '0' . $k;
                    } else {
                        $number = $k;
                    }
                    $response->play($this->statementUrl . 'statement_agar_aap.wav');
                    $response->play($this->numbersUrl . 'number_' . $day . '.wav');
                    $response->play($this->monthsIvr . 'Month_' . $month . '.wav');
                    $response->play($this->yearsIvr . 'Year_' . $year . '.wav');
                    $response->play($this->statementUrl . 'statement_ko_dua_karwana.wav');
                    $response->play($this->statementUrl . 'statement_baraye_meharbani.wav');
                    $response->play($this->numbersUrl . 'number_' . $number . '.wav');
                    $response->play($this->statementUrl . 'statement_press.wav');
                }

                $response->gather([
                    'numDigits' => 1,
                    'action' => route('ivr.time'),
                    'timeout' => 10
                ]);
                $this->SaveLog($request, $VenueDatesAadd, 'ivr.time');
            } else {
                $response->play($this->statementUrl . 'wrong_number_input.wav');
                $attempts  = $existingData->attempts + 1; 
                $existingData->update(['attempts' =>  $attempts]); 
                $response->redirect(route('ivr.dates'));
            }
            return response($response, 200)->header('Content-Type', 'text/xml');
        }
 
      
        
    }


    public function handleSlots(Request $request)
    {


        $response = new VoiceResponse();
        $userInput = $request->input('Digits');
        $existingData = $this->getexistingCustomer($request->input('From'));

        if (!empty($existingData)) {
            $customer_option = json_decode($existingData->customer_options, true);
            if (array_key_exists($userInput,  $customer_option)) { 
                $venueAddreId =   $customer_option[$userInput]; 

                $venueAddress = VenueAddress::find($venueAddreId);
                $countryTimeZone = $venueAddress->timezone;
                $countryCode = $this->findCountryByPhoneNumber($request->input('From'));
    
                $cleanNumber = str_replace($countryCode, '', $request->input('From'));
    
                $visitors = Vistors::where('phone', $cleanNumber)->first();
    
                if ($visitors) {
                    $recordAge = $visitors->created_at->diffInDays(now());
                    $rejoin = $venueAddress->rejoin_venue_after;
                    if ($rejoin > 0 && $recordAge <= $rejoin) {
                        if ($recordAge == 0) {
                            $response->play($this->statementUrl . 'DuaMeeting_booked_already_for_today.mp3');
                            $response->play($this->statementUrl . 'ApKiDuaMeetingBookNaheKiJasakti_BarayeMeharbaniap.mp3');
                        }
                        if ($rejoin <= 9) {
                            $day = '0' .  $rejoin;
                        } else {
                            $day = $$rejoin;
                        }
                        $response->play($this->numbersUrl . 'number_' . $day . '.wav');
                        $response->play($this->statementUrl . 'din.mp3');
                        $response->play($this->statementUrl . 'BaadKoshshKarien.mp3');
                        return response($response, 200)->header('Content-Type', 'text/xml');
                    }
                }
    
           
                    $response->play($this->statementUrl . 'statement_select_time.wav');
    
                    $slots = VenueSloting::where(['venue_address_id' => $venueAddreId])
                        ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                        ->orderBy('slot_time', 'ASC')
                        ->take(3)
                        ->get();
    
                    $slotArr = [];
                    $options = [];
                    $i = 1;
                    foreach ($slots as $slot) {
                        // $timestamp = strtotime($slot->slot_time);
                        $timeArr = explode(':', $slot->slot_time);
                        $hours = $timeArr[0];
                        $minutes = $timeArr[1];
                        $seconds = $timeArr[2];
    
                        $hours = intval($timeArr[0]);
                        $ampm = '';
                        if ($hours >= 0 && $hours < 12) {
                            $ampm = "AM";
                        } else {
                            $ampm = "PM";
                        }
    
                        if ($i <= 9) {
                            $number = '0' . $i;
                        } else {
                            $number = $i;
                        }
    
                        if ($hours <= 9) {
                            $hourNew = '0' . $hours;
                        } else {
                            $hourNew = $hours;
                        }
                        $response->play($this->statementUrl . 'statement_agar_aap.wav');
                        if ($ampm == 'AM') {
                            $response->play($this->statementUrl . 'statement_morning.wav');
                        } else {
                            $response->play($this->statementUrl . 'statement_afternoon.wav');
                        }
                        $response->play($this->numbersUrl . 'number_' .  $hourNew . '.wav');
                        $response->play($this->statementUrl . 'statement_bajkay.wav');
                        if ($minutes != '00') {
                            // $response->play($this->statementUrl . 'statement_aur.wav');  
                            $response->play($this->numbersUrl . 'number_' . $minutes . '.wav');
                            $response->play($this->statementUrl . 'statement_minute.wav');
                        }
                        $response->play($this->statementUrl . 'statement_ko_dua_karwana.wav');
                        $response->play($this->statementUrl . 'statement_baraye_meharbani.wav');
    
                        // $response->play($this->statementUrl . 'statement_kay_liye.wav');
                        $response->play($this->numbersUrl . 'number_' . $number . '.wav');
                        $response->play($this->statementUrl . 'statement_press.wav');
    
    
                        //   $response->say('Press ' . $i . ' to book slot ' . $slotTime . ' ');
                        // $slotArr[$slot->id] =  $slotTime;
                        $options[$i] = $slot->id;
                        $i++;
                    }
                   $response->gather([
                        'numDigits' => 1,
                        'action' => route('ivr.makebooking'),
                    ]); 
                    $this->SaveLog($request, $options, 'ivr.makebooking');
                 

            }else{
                $response->play($this->statementUrl . 'wrong_number_input.wav');
                $attempts  = $existingData->attempts + 1; 
                $existingData->update(['attempts' =>  $attempts]); 
                $response->redirect(route('ivr.time')); 
            }
        }
 
 
        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    public function MakeBooking(Request $request)
    {

        $userInput = $request->input('Digits');
        $customer = $request->input('From'); 
        $response = new VoiceResponse(); 
        $existingData = $this->getexistingCustomer($request->input('From'));


        if (!empty($existingData)) {
            $customer_option = json_decode($existingData->customer_options, true);
            if (array_key_exists($userInput,  $customer_option)) { 
                $slotId = $customer_option[$userInput];
                $venueSlots = VenueSloting::find($slotId);
                $venueAddress = $venueSlots->venueAddress;
                // $tokenId = $venueSlots->token_id; 
                $tokenId = str_pad($venueSlots->token_id, 2, '0', STR_PAD_LEFT);
                $countryCode = $this->findCountryByPhoneNumber($customer);

                $cleanNumber = str_replace($countryCode, '', $customer);

                $uuid = Str::uuid()->toString();
                $booking = Vistors::create([
                    'is_whatsapp' => 'yes',
                    'slot_id' => $slotId,
                    'meeting_type' => 'on-site',
                    'booking_uniqueid' =>  $uuid,
                    'booking_number' => $tokenId,
                    'country_code' => $countryCode,
                    'phone' => $cleanNumber,
                    'source' => 'Phone'
                ]);

                if ($booking) {
                    TwillioIvrResponse::where(['mobile' => $customer])->delete();
                }
 


                for ($i = 1; $i <= 2; $i++) {

                    $response->play($this->statementUrl . 'statement_your_token_date.wav');
                    $datesArr = explode('-', $venueAddress->venue_date);
                    $year = $datesArr[0];
                    $month = $datesArr[1];
                    $day = $datesArr[2];

                    $response->play($this->numbersUrl . 'number_' . $day . '.wav');
                    $response->play($this->monthsIvr . 'Month_' . $month . '.wav');
                    $response->play($this->yearsIvr . 'Year_' . $year . '.wav');


                    // $response->say($currentDate->format('j M Y')); 
                    $response->play($this->statementUrl . 'statement_your_dua_time.wav');

                    // $response->say($venueSlots->slot_time); 
                    $chunksTime = explode(':', $venueSlots->slot_time);

                    $hours = $chunksTime[0];
                    $minutes = $chunksTime[1];
                    $seconds = $chunksTime[2];

                    $ampm = '';
                    if ($hours >= 0 && $hours < 12) {
                        $ampm = "AM";
                    } else {
                        $ampm = "PM";
                    }

                    if ($hours <= 9) {
                        $hourNew = '0' . $hours;
                    } else {
                        $hourNew = $hours;
                    }



                    $response->play($this->numbersUrl . 'number_' .  $hourNew . '.wav');
                    $response->play($this->statementUrl . 'statement_bajkay.wav');
                    if ($minutes != '00') {
                        // $response->play($this->statementUrl . 'statement_aur.wav');  
                        $response->play($this->numbersUrl . 'number_' . $minutes . '.wav');
                        $response->play($this->statementUrl . 'statement_minute.wav');
                    }
                    $response->play($this->statementUrl . 'statement_your_token_number.wav');

                    if ($venueSlots->token_id <= 9) {
                        $number = '0' . $venueSlots->token_id;
                    } else {
                        $number = $venueSlots->token_id;
                    }


                    $response->play($this->numbersUrl . 'number_' . $number . '.wav');
                }
                $response->play($this->statementUrl . 'statement_15_min_before.wav');
                $response->play($this->statementUrl . 'statement_goodbye.wav');

            }else{

                $response->play($this->statementUrl . 'wrong_number_input.wav');
                $attempts  = $existingData->attempts + 1; 
                $existingData->update(['attempts' =>  $attempts]); 
                $response->redirect(route('ivr.time'));  
                
            }
            return response($response, 200)->header('Content-Type', 'text/xml');
        }


       
    }

    function findCountryByPhoneNumber($phoneNumber)
    {
        $countries = Country::all(); // Assuming you have a Country model

        foreach ($countries as $country) {
            $countryCodeLength = strlen($country->phonecode);
            if (substr($phoneNumber, 0, $countryCodeLength) === $country->phonecode) {
                return $country->phonecode;
            }
        }

        // If no matching country code is found
        return "Unknown";
    }

    function findKeyByValueInArray($array, $key)
    {
        if ($key > 0) {
            $key = $key - 1;
        }
        $arrayKeys = array_keys($array);
        return ($arrayKeys[$key]) ? $arrayKeys[$key] : null;
    }


    public function handleRepeat($route)
    {
        $response = new VoiceResponse();
        $response->redirect(route($route));
        return response($response)->header('Content-Type', 'text/xml');
    }





    public function handleTimeout(Request $request)
    {
        $response = new VoiceResponse();
        $response->say("Sorry, we didn't receive any input. Goodbye!");

        return response($response)->header('Content-Type', 'text/xml');
    }

    private function getexistingCustomer($userPhoneNumber)
    {
        return TwillioIvrResponse::where(['mobile' =>  $userPhoneNumber])->orderBy('id', 'desc')->first();
    }

    public function getDataFromVenue()
    {
        $venuesListArr = VenueAddress::where('venue_id', $this->country->id)->where('venue_date', '>=', date('Y-m-d'));
        return  $venuesListArr;
    }

    public function SaveLog($request, $options, $from)
    {

        TwillioIvrResponse::create([
            'mobile' => $request->input('From'),
            'response_digit' => $request->input('Digits'),
            'attempts' => 1,
            'route_action' => $from,
            'customer_options' => json_encode($options)

        ]);
    }
}
