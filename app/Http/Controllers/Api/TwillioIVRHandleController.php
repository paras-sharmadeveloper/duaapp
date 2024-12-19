<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use App\Models\{VenueAddress, Venue, TwillioIvrResponse, VenueSloting, Vistors, Country , Reason};

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
    protected $voice;

    public function __construct()
    {
        $this->statementUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/statements/';
        $this->cityUrl      = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/city/';
        $this->numbersUrl   = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/numbers/';
        $this->monthsIvr    = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/months/';
        $this->yearsIvr     = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/years/';
        $this->country      = Venue::where(['iso' => 'PK'])->get()->first();
        $this->voice = 'Polly.Stephen-Neural';
    }

    public function handleIncomingCall(Request $request)
    {

        return false;  // This chanel no more needed
        $response = new VoiceResponse();
        $existingData = $this->getexistingCustomer($request->input('From'));

        $response->play($this->statementUrl . 'ur/welcome_for_eng_press1_for_urdu_press2.wav');
        // $response->play($this->statementUrl . 'en/welcome_for_eng_press1_for_urdu_press2.wav');
        $isWrongInput = $request->input('wrong_input',false);
        $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.dua.option'),
            'timeout' => 20,
        ]);
        $options = ['1' => 'en', '2' => 'ur'];
        if(!empty($existingData)){
            $this->FlushEntries($request->input('From'));
        }
        if(!$isWrongInput){

            TwillioIvrResponse::create([
                'mobile' => $request->input('From'),
                'response_digit' => $request->input('Digits',0),
                'attempts' => 1,
                'lang' => '',
                'route_action' => 'ivr.dua.option',
                'customer_options' => json_encode($options)

            ]);

        }


        return response($response, 200)->header('Content-Type', 'text/xml');

    }

    public function handleDuaOption(Request $request)
    {

        $response = new VoiceResponse();
        $isWrongInput = $request->input('wrong_input',false);
        $existingData = $this->getexistingCustomer($request->input('From'));
        $userInput = $request->input('Digits');
        $customer_option = json_decode($existingData->customer_options, true);
        $dua_option = '';

        $lang = $existingData->lang;

        if (!empty($existingData)) {
            $existingData->update(['logs' => json_encode($request->all())]);

            if (array_key_exists($userInput,  $customer_option)) {
                $lang = $customer_option[$userInput];
            }else if(!empty($userInput)){
                $existingData->update(['logs' => json_encode($request->all())]);
                $isWrongInput = true;

                $response->play($this->statementUrl .$lang . '/wrong_input.wav');



                // $response->say("You have Entered Wrong Input Please choose the Right Input",['voice' => $this->voice]);
                $attempts  = $existingData->attempts + 1;
                $existingData->update(['attempts' =>  $attempts]);

                $redirectUrl = route('ivr.welcome', ['wrong_input' => true, 'redirect_to' => 'step1']);
                $response->redirect($redirectUrl);
            }

            $options = ['1' => 'dua', '2' => 'dum'];

            $response->play($this->statementUrl .$lang . '/if_dua_press1_if_dum_press2.wav');

                $response->gather([
                    'numDigits' => 1,
                    'action' => route('ivr.pickcity'),
                    'timeout' => 20,
                ]);

            if(!$isWrongInput ){

                TwillioIvrResponse::create([
                    'mobile' => $request->input('From'),
                    'response_digit' => $request->input('Digits',0),
                    'attempts' => 1,
                    'lang' => $lang,
                    'route_action' => 'ivr.pickcity',
                    'customer_options' => json_encode($options)

                ]);


            }
        }

        return response($response, 200)->header('Content-Type', 'text/xml');
    }
    public function handleCity(Request $request)
    {
        $response = new VoiceResponse();
        $isWrongInput = $request->input('wrong_input',false);
        $userInput = $request->input('Digits');
        $existingData = $this->getexistingCustomer($request->input('From'));
        $lang = $existingData->lang;
        $customer_option = json_decode($existingData->customer_options, true);

        $dua_option = '';
            if (array_key_exists($userInput,  $customer_option)) {
                $dua_option = $customer_option[$userInput];
            }else if(!empty($userInput)){
                // $existingData->update(['logs' => json_encode($request->all())]);
                $isWrongInput = true;
                $response->play($this->statementUrl .$lang . '/wrong_input.wav');
                // $response->say("You have Entered Wrong Input Please choose the Right Input",['voice' => $this->voice]);
                $attempts  = $existingData->attempts + 1;
                $existingData->update(['attempts' =>  $attempts]);

                $redirectUrl = route('ivr.dua.option', ['wrong_input' => true, 'to' => 'dua_option']);
                $response->redirect($redirectUrl);


            }




            $query = $this->getDataFromVenue();
            $venuesListArr = $query->get();
            $distinctCities = $venuesListArr->pluck('city')->unique();


            if($dua_option=='dua' && !empty($venuesListArr->reject_dua_id)){
                $reason  = Reason::find($venuesListArr->reject_dua_id);
                $response->play($reason->reason_ivr_path);
                return response($response, 200)->header('Content-Type', 'text/xml');

          }
          if($dua_option=='dum' && !empty($venuesListArr->reject_dum_id)){
            $reason  = Reason::find($venuesListArr->reject_dum_id);
            $response->play($reason->reason_ivr_path);
            return response($response, 200)->header('Content-Type', 'text/xml');
          }



            $i = 1;
            $cityArr = [];
            foreach ($distinctCities as $key => $city) {
                $cityArr[$i] = strtolower($city);
                $i++;
            }
            ksort($cityArr);

            if(!empty($cityArr)){
                $response->play($this->statementUrl . $lang . '/statement_select_city.wav');
                foreach ($cityArr as $k => $city) {


                    if ($k <= 9) {
                        $number = '000' . $k;
                    } else if ($k <= 99) {
                        $number = '00'.$k;
                    } else if ($k <= 999) {
                        $number = '0'.$k;
                    }else if ($k <= 2000) {
                        $number = $k;
                    }



                    if($lang =='en'){
                        $response->play($this->statementUrl . $lang . '/statement_agar_aap.wav');
                        $response->play($this->cityUrl . 'city_' . $city . '.wav');
                        $response->play($this->statementUrl . $lang . '/statement_press.wav');
                        $response->say($k,['voice' => $this->voice]);
                    }else{
                        $response->play($this->cityUrl . 'city_' . $city . '.wav');
                        $response->play($this->statementUrl . $lang . '/statement_kay_liye.wav');
                        $response->play($this->numbersUrl . $number . '.wav');
                        $response->play($this->statementUrl . $lang . '/statement_press.wav');
                    }


                }
            }else{
                $response->play($this->statementUrl . $lang . '/cant_book_dua_meeting.wav');
                $response->play($this->statementUrl . $lang . '/please_try_again_later.wav');
                $response->play($this->statementUrl . $lang . '/statement_goodbye.wav');

            }

            $response->gather([
                'numDigits' => 1,
                'action' => route('ivr.makebooking'),
                'timeout' => 10
            ]);

        if (!$isWrongInput) {

            TwillioIvrResponse::create([
                'mobile' => $request->input('From'),
                'response_digit' => $request->input('Digits'),
                'attempts' => 1,
                'lang' => $lang,
                'dua_option' => $dua_option,
                'route_action' => 'ivr.makebooking',
                'customer_options' => json_encode($cityArr)

            ]);


        }

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

        $calledCountry = $request->input('CallerCountry');

        $country = Country::where('iso','LIKE',$calledCountry)->first();




        if (!empty($existingData)) {
            if (array_key_exists($userInput,  $customer_option)) {
                $CityName = $customer_option[$userInput];
                $today = getCurrentContryTimezone($countryId->id);
                $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                    ->where('city',  $CityName)
                    ->whereDate('venue_date',$today)
                    ->orderBy('venue_date', 'ASC')
                    ->first();

                    if($venuesListArr->status == 'inactive'){
                        $response->play($this->statementUrl.$lang . '/cant_book_dua_meeting.wav');
                        return response($response, 200)->header('Content-Type', 'text/xml');
                    }

                if (!empty($venuesListArr)) {

                    $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $venuesListArr->timezone);
                    $venue_available_country =  json_decode($venuesListArr->venue_available_country);
                    $userCountry = VenueAvilableInCountry($venue_available_country,$country->id);

                    if(!$userCountry['allowed']){
                        $response->play($this->statementUrl.$lang . '/cant_book_dua_meeting.wav');
                        return response($response, 200)->header('Content-Type', 'text/xml');
                    }

                    // $status = isAllowedTokenBooking($venuesListArr->venue_date, $venuesListArr->slot_appear_hours , $venuesListArr->timezone);
                    if ($status['allowed']) {

                        $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                        ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                        ->where(['type' => $dua_option])
                        ->orderBy('id', 'ASC')
                        ->select(['venue_address_id', 'token_id', 'id'])->first();

                    $slotId = $tokenIs->id;
                    $duaType = $tokenIs->type;
                    $venueAddress = $tokenIs->venueAddress;
                    // $tokenId = str_pad($tokenIs->token_id, 4, '0', STR_PAD_LEFT);
                    $tokenId = $tokenIs->token_id;
                    $countryCode = $this->findCountryByPhoneNumber($customer);
                    $cleanNumber = str_replace($countryCode, '', $customer);
                    $uuid = Str::uuid()->toString();

                        // Rejoin

                    $rejoin = $venueAddress->rejoin_venue_after;
                    $rejoinStatus = userAllowedRejoin($cleanNumber, $rejoin);
                    if(!$rejoinStatus['allowed']){
                        $response->play($this->statementUrl.$lang . '/cant_book_dua_meeting.wav');
                        return response($response, 200)->header('Content-Type', 'text/xml');

                    }


                    $booking = Vistors::create([
                        'is_whatsapp' => 'yes',
                        'slot_id' => $slotId,
                        'meeting_type' => 'on-site',
                        'booking_uniqueid' =>  $uuid,
                        'booking_number' => $tokenId,
                        'country_code' => '+'.$country->phonecode,
                        'phone' => $cleanNumber,
                        'source' => 'Phone',
                        'dua_type' => $dua_option,
                        'lang' => $lang

                    ]);

                    if ($booking) {
                        TwillioIvrResponse::where(['mobile' => $customer])->delete();
                    }

                    for ($i = 1; $i <= 2; $i++) {

                        $response->play($this->statementUrl . $lang . '/statement_your_token_date.wav');

                        $correctVenueDate = date('Y-m-d', strtotime($venueAddress->venue_date));

                        $datesArr = explode('-', $correctVenueDate);
                        $year = $datesArr[0];
                        $month = $datesArr[1];
                        $day = $datesArr[2];

                         if ($day <= 99) {
                            $myday = '00'.$day;
                        } else if ($day <= 999) {
                            $myday = '0'.$day;
                        }else if ($day <= 2000) {
                            $myday = $day;
                        }




                        if($lang  == 'en'){
                            $response->say($day,['voice' => $this->voice]);
                            $response->say($month,['voice' => $this->voice]);
                            $response->say($year,['voice' => $this->voice]);
                        }else{
                            // Log::info("Myday" . $myday);
                            // Log::info("Url" . $this->numbersUrl. $myday .'.wav');
                            // $response->say($myday,['voice' => $this->voice]);
                             $response->play($this->numbersUrl. $myday .'.wav');
                             $response->play($this->monthsIvr. 'month_' . $month . '.wav');
                             $response->play($this->yearsIvr .  $year . '.wav');
                        }


                        $response->play($this->statementUrl.$lang . '/statement_your_token_number.wav');

                        if ($tokenId <= 9) {
                            $tokenNumber = '000' . $tokenId;
                        } else if ($tokenId <= 99) {
                            $tokenNumber = '00'.$tokenId;
                        } else if ($tokenId <= 999) {
                            $tokenNumber = '0'.$tokenId;
                        }else if ($tokenId <= 2000) {
                            $tokenNumber = $tokenId;
                        }

                        if($lang  == 'en'){
                            $response->say($tokenId,['voice' => $this->voice]);

                        }else{

                            $response->play($this->numbersUrl . $tokenNumber . '.wav');
                        }

                    }
                    $response->play($this->statementUrl.$lang . '/statement_15_min_before.wav');
                    $response->play($this->statementUrl .$lang . '/statement_goodbye.wav');

                    }else{
                        $response->play($this->statementUrl.$lang . '/cant_book_dua_meeting.wav');
                    }

                }else{
                    // No venue
                    $response->play($this->statementUrl.$lang . '/cant_book_dua_meeting.wav');

                }
            }else {

                $response->play($this->statementUrl .$lang . '/wrong_input.wav');
                // $response->say("You have Entered Wrong Input Please choose the Right Input",['voice' => $this->voice]);
                $attempts  = $existingData->attempts + 1;
                $existingData->update(['attempts' =>  $attempts]);
                $redirectUrl = route('ivr.pickcity', ['wrong_input' => true, 'to' => 'dua_option']);
                $response->redirect($redirectUrl);
            }
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

    private function FlushEntries($phoneNUmber)
    {
        TwillioIvrResponse::where(['mobile' => $phoneNUmber])->delete();
    }
}
