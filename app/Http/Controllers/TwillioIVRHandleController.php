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
        $existingData = $this->getexistingCustomer($request->input('From'));



        $response->say('Welcome to Kahay Faqeer. Please Choose Your Preferred Language. Press 1 for English and Press 2 for Urdu');
        $existingData = $this->getexistingCustomer($request->input('From'));
        $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.dua.option'),
            'timeout' => 10, // Set the timeout to 10 seconds
        ]);
        $options = ['1' => 'en', '2' => 'ur'];
 
        TwillioIvrResponse::create([
            'mobile' => $request->input('From'),
            'response_digit' => $request->input('Digits',0),
            'attempts' => 1,
            'lang' => '',
            'route_action' => 'ivr.dua.option',
            'customer_options' => json_encode($options)

        ]);
        return response($response, 200)->header('Content-Type', 'text/xml'); 
         
    }


 
    public function handleDuaOption(Request $request)
    {

        $response = new VoiceResponse();
        $existingData = $this->getexistingCustomer($request->input('From'));
        $userInput = $request->input('Digits');
        $customer_option = json_decode($existingData->customer_options, true);
        $dua_option = ''; 
        


        if (!empty($existingData)) {
            if (array_key_exists($userInput,  $customer_option)) {
                $lang = $customer_option[$userInput]; 
            }
        }else{
            $lang = 'en'; 
        }

        


        if ($lang == 'en') {
            $response->say('Please Select Type of Dua. Press 1 for Dua and Press 2 for Dum');
        }else {
            $language = 'ur-PK';
            $response->say('Please Select Type of Dua. Press 1 for Dua and Press 2 for Dum');
        }
        $options = ['1' => 'dua', '2' => 'dum'];
       
        $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.pickcity'),
            'timeout' => 20, // Set the timeout to 10 seconds
        ]);
        return response($response, 200)->header('Content-Type', 'text/xml');
    }
    public function handleCity(Request $request)
    {
        $response = new VoiceResponse();
        $userInput = $request->input('Digits');
        $existingData = $this->getexistingCustomer($request->input('From'));
        $lang = $existingData->lang;
        $customer_option = json_decode($existingData->customer_options, true);
        if (!empty($existingData)) {

            $response->play($this->statementUrl . $lang . '/statement_select_city.wav');
            $query = $this->getDataFromVenue();
            $venuesListArr = $query->get();
            $distinctCities = $venuesListArr->pluck('city')->unique();

            
            $i = 1;
            $cityArr = [];
            foreach ($distinctCities as $key => $city) {
                
                $cityArr[$i] = strtolower($city);

                // $cityArr[$i] =  strtolower($city); 

                $i++;
            }
            ksort($cityArr);
            foreach ($cityArr as $k => $city) {
                 

                if ($k <= 9) {
                    $number = '000' . $k;
                } else if ($k <= 99) { 
                    $number = '00'.$k;
                } else if ($k <= 999) { 
                    $number = '0'.$k;
                }


                $response->play($this->cityUrl . 'city_' . $city . '.wav');
                if($lang =='en'){
                    $response->play($this->statementUrl . $lang . '/statement_agar_aap_eng.wav');  
                    $response->play($this->statementUrl . $lang . '/statement_press_eng.wav');   
                    $response->play($this->numbersUrl . $number . '.wav');
                   
                }else{
                    $response->play($this->statementUrl . $lang . '/statement_kay_liye.wav');
                    $response->play($this->numbersUrl . $number . '.wav');
                    $response->play($this->statementUrl . $lang . '/statement_press.wav');
                }
               
               
              
            }
            $dua_option = '';
            if (array_key_exists($userInput,  $customer_option)) {
                $dua_option = $customer_option[$userInput];
            }
            TwillioIvrResponse::create([
                'mobile' => $request->input('From'),
                'response_digit' => $request->input('Digits'),
                'attempts' => 1,
                'lang' => $lang,
                'dua_option' => $dua_option,
                'route_action' => 'ivr.makebooking',
                'customer_options' => json_encode($cityArr)
    
            ]);
         

            // $this->SaveLog($request, $cityArr, 'ivr.dates');
        } else {

            $response->play($this->statementUrl .'wrong_number_input.wav');
            $response =  $this->handleWelcomeInputs($response, $request, false);
            $attempts  = $existingData->attempts + 1;
            $existingData->update(['attempts' =>  $attempts]);
        } 
        $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.makebooking'),
            'timeout' => 10
        ]);
        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    public function MakeBooking(Request $request)
    {

        $userInput = $request->input('Digits');
        $customer = $request->input('From');
        $response = new VoiceResponse();
        $existingData = $this->getexistingCustomer($request->input('From'));
        $dua_option = $existingData->dua_option;
        $customer_option = json_decode($existingData->customer_options, true);
        $countryId = Venue::where(['iso' => 'PK'])->get()->first();
        $lang  =  $existingData->lang; 
        if (!empty($existingData)) {
            if (array_key_exists($userInput,  $customer_option)) {
                $CityName = $customer_option[$userInput];
                $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                    ->where('city',  $CityName)
                    ->where('venue_date', '=', date('Y-m-d'))
                    ->orderBy('venue_date', 'ASC')
                    ->first();

                if (!empty($venuesListArr)) {

                    $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                        ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                        ->where(['type' => $dua_option])
                        ->orderBy('id', 'ASC')
                        ->select(['venue_address_id', 'token_id', 'id'])->first();
                        Log::info("Token:",json_encode($tokenIs)); 



                    $slotId = $tokenIs->id;

                    $venueAddress = $tokenIs->venueAddress;
                    $tokenId = str_pad($tokenIs->token_id, 4, '0', STR_PAD_LEFT);


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

                    // $response->play($this->cityUrl . 'city_' . $city . '.wav');

                    for ($i = 1; $i <= 2; $i++) {

                        $response->play($this->statementUrl . $lang . '/statement_your_token_date.wav');
                        $datesArr = explode('-', $venueAddress->venue_date);
                        $year = $datesArr[0];
                        $month = $datesArr[1];
                        $day = $datesArr[2];

                        if ($day <= 9) {
                            $number = '000' . $day;
                        } else if ($day <= 99) { 
                            $number = '00'.$day;
                        } else if ($day <= 999) { 
                            $number = '0'.$day;
                        }

                        $response->play($this->numbersUrl. $number . '.wav');
                        $response->play($this->monthsIvr. 'Month_' . $month . '.wav');
                        $response->play($this->yearsIvr . 'Year_' . $year . '.wav'); 
 
                        $response->play($this->statementUrl.$lang . '/statement_your_token_number.wav');
 

                        if ($tokenId <= 9) {
                            $number = '000' . $tokenId;
                        } else if ($tokenId <= 99) { 
                            $number = '00'.$tokenId;
                        } else if ($tokenId <= 999) { 
                            $number = '0'.$tokenId;
                        }


                        $response->play($this->numbersUrl . $number . '.wav');
                    }
                    $response->play($this->statementUrl.$lang . '/statement_15_min_before.wav');
                    $response->play($this->statementUrl .$lang . '/statement_goodbye.wav');
                }
            }
        } else {

            $response->play($this->statementUrl .$lang . '/wrong_number_input.wav');
            $attempts  = $existingData->attempts + 1;
            $existingData->update(['attempts' =>  $attempts]);
            $response->redirect(route('ivr.time'));
        }

        return response($response, 200)->header('Content-Type', 'text/xml');
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
        return "";
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
