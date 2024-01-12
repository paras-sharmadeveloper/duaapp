<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use App\Models\{VenueAddress, Venue, WhatsApp, VenueSloting, Vistors, Country};

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
        $this->cityUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/city/';
        $this->numbersUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/numbers/';
        $this->monthsIvr = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/months/';
        $this->yearsIvr = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/years/';
        $this->country = Venue::where(['iso' => 'PK'])->get()->first();
    }

    public function handleIncomingCall(Request $request)
    {
        $fromCountry = $request->input('FromCountry');
        $customer = $request->input('From');
        $userInput = $request->input('Digits');
        $response = new VoiceResponse();
        // STEP 1: Welcome Message
        $response->play($this->statementUrl . 'statement_welcome_message.wav');

        $response->play($this->statementUrl . 'statement_bookmeeting.wav');
        $response->play($this->numbersUrl . 'number_01.wav');
        $response->play($this->statementUrl . 'statement_press.wav');

        // Prompt user to press any key to proceed

        $gather = $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.pickcity'),
            'timeout' => 10, // Set the timeout to 10 seconds
        ]);

        $response->redirect(route('ivr.welcome'));




        // $response->redirect(route('ivr.welcome'));

        // Set the response content type to XML
        header("Content-type: text/xml");

        // Laravel specific: return a response with the TwiML content
        return response($response, 200)->header('Content-Type', 'text/xml');
    }





    public function handleCity(Request $request)
    {
        $response = new VoiceResponse();
        $customer = request('From');
        $response->play($this->statementUrl . 'statement_select_city.wav');

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
            'timeout' => 10
        ]);
        $response->redirect(route('ivr.pickcity'));



        if ($request->input('Digits') != null) {

            $dataArr = [
                'customer_number' => request('From'),
                'customer_response' => $request->input('Digits'),
                'bot_reply' =>  json_encode(array_unique($cityArr)),
                'data_sent_to_customer' => 'twillio-city ',
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => 2
            ];
            Log::info('Received Digits city: ' . $request->input('Digits'));
            WhatsApp::create($dataArr);
        }
        header("Content-type: text/xml");

        return response($response, 200)->header('Content-Type', 'text/xml');
    }

    public function handleDates(Request $request)
    {
        $response = new VoiceResponse();

        $customer = request('From');

        $exsitingCustomer = $this->getexistingCustomer($customer);
        if ($exsitingCustomer) {

            $CityyName = json_decode($exsitingCustomer->bot_reply, true);

            $cityName = isset($CityyName[$request->input('Digits')]) ? $CityyName[$request->input('Digits')] : '';

            if (empty($cityName)) {
                $response->say('You have entered Wront input. Please choose the Right Input ');
                $response->redirect(route('ivr.dates'));
            } else {


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

                    // $currentDate = Carbon::parse($venueDate->venue_date);
                    // $VenueDates[$i] = $venueDate->venue_date;
                    // // $VenueDates[$i] = $currentDate->format('j M Y');
                    // $VenueDatesAadd[$i] = $venueDate->id;
                    // $i++;
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
                    $response->play($this->numbersUrl . 'number_' . $day . '.wav');
                    $response->play($this->monthsIvr . 'Month_' . $month . '.wav');
                    $response->play($this->yearsIvr . 'Year_' . $year . '.wav');
                    $response->play($this->statementUrl . 'statement_kay_liye.wav');
                    $response->play($this->numbersUrl . 'number_' . $number . '.wav');
                    $response->play($this->statementUrl . 'statement_press.wav');
                }

                if ($request->input('Digits') !== null) {
                    $gather = $response->gather([
                        'numDigits' => 1,
                        'action' => route('ivr.time'),
                        'timeout' => 10
                    ]);
                } else {
                    $response->redirect(route('ivr.dates'));
                }



                Log::info('Received Digits handles dates: ' . $request->input('Digits'));

                $dataArr = [
                    'customer_number' => $customer,
                    'customer_response' => $request->input('Digits'),
                    'bot_reply' =>  json_encode($VenueDatesAadd),
                    'data_sent_to_customer' => 'twillio dates',
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => 3
                ];
                WhatsApp::create($dataArr);
            }



            return response($response, 200)->header('Content-Type', 'text/xml');
        }
    }


    public function handleSlots(Request $request)
    {


        $response = new VoiceResponse();

        $customer = $request->input('From');
        $userInput = $request->input('Digits');
        $exsitingCustomer = $this->getexistingCustomer($customer);
        $isVisiable = false;
        $step = $exsitingCustomer->steps + 1;
        $bot_reply = json_decode($exsitingCustomer->bot_reply, true);
        $venueAddreId = isset($bot_reply[$request->input('Digits')]) ? $bot_reply[$request->input('Digits')] : '';

        if (empty($venueAddressId)) {
            $response->say('You have entered Wront inputs. Please choose the Right Input ');
            $response->redirect(route('ivr.dates'));
        } else {

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
                        $text = 'Today';
                    } else {
                        $text = 'Before ' . $recordAge . ' Day';
                    }
                    if ($venueAddress->rejoin_venue_after == 1) {
                        $day = 'day';
                    } else {
                        $day = 'days';
                    }

                    $data = 'You already Booked a seat ' . $text . '  You can Rejoin only After ' . $venueAddress->rejoin_venue_after . ' ' . $day;
                    $response->say($data);

                    return false;
                }
            }

            if ($exsitingCustomer) {
                $response->play($this->statementUrl . 'statement_select_time.wav');

                $userInput = $exsitingCustomer->customer_response;
                $bot_reply = json_decode($exsitingCustomer->bot_reply, true);
                $venueAddreId = isset($bot_reply[$request->input('Digits')]) ? $bot_reply[$request->input('Digits')] : '';
                // $venueAddreId = $asdas[$request->input('Digits')];
                if (empty($venueAddressId)) {
                    $response->say('You have entered Wront inputs. Please choose the Right Input ');
                    $response->redirect(route('ivr.dates'));
                }

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
                    $response->play($this->numbersUrl . 'number_' .  $hourNew . '.wav');
                    $response->play($this->statementUrl . 'statement_bajkay.wav');
                    if ($minutes != '00') {
                        // $response->play($this->statementUrl . 'statement_aur.wav');  
                        $response->play($this->numbersUrl . 'number_' . $minutes . '.wav');
                        $response->play($this->statementUrl . 'statement_minute.wav');
                    }

                    $response->play($this->statementUrl . 'statement_kay_liye.wav');
                    $response->play($this->numbersUrl . 'number_' . $number . '.wav');
                    $response->play($this->statementUrl . 'statement_press.wav');


                    //   $response->say('Press ' . $i . ' to book slot ' . $slotTime . ' ');
                    // $slotArr[$slot->id] =  $slotTime;
                    $options[$i] = $slot->id;
                    $i++;
                }

                $dataArr = [
                    'customer_number' => $customer,
                    'customer_response' => $request->input('Digits'),
                    'bot_reply' =>  json_encode($options),
                    'data_sent_to_customer' => 'twillio slots',
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => 4
                ];
                Log::info('Received Digits handles slots: ' . $request->input('Digits'));
                WhatsApp::create($dataArr);


                $gather = $response->gather([
                    'numDigits' => 1,
                    'action' => route('ivr.makebooking'),
                ]);
            }
        } 
        return response($response, 200)->header('Content-Type', 'text/xml'); 

    }

    public function MakeBooking(Request $request)
    {

        $userInput = $request->input('Digits');


        $response = new VoiceResponse();

        $customer = $request->input('From');
        $exsitingCustomer = $this->getexistingCustomer($customer);


        if ($exsitingCustomer) {
            $userInput = $exsitingCustomer->customer_response;
            $lastSent = json_decode($exsitingCustomer->bot_reply, true);
            $slotId = (isset($lastSent[$request->input('Digits')])) ? $lastSent[$request->input('Digits')] : '';

            if (empty($slotId)) {
                $response->say('You have entered Wront inputs. Please choose the Right Input ');
                $response->redirect(route('ivr.time'));
              
            }else{
                $venueSlots = VenueSloting::find($slotId);
                $venueAddress = $venueSlots->venueAddress;
                // $tokenId = $venueSlots->token_id; 
                $tokenId = str_pad($venueSlots->token_id, 2, '0', STR_PAD_LEFT);
                $countryCode = $this->findCountryByPhoneNumber($customer);
    
                $cleanNumber = str_replace($countryCode, '', $customer);
    
                $uuid = Str::uuid()->toString();
                Vistors::create([
                    'is_whatsapp' => 'yes',
                    'slot_id' => $slotId,
                    'meeting_type' => 'on-site',
                    'booking_uniqueid' =>  $uuid,
                    'booking_number' => $tokenId,
                    'country_code' => $countryCode,
                    'phone' => $cleanNumber,
                    'source' => 'Phone'
                ]);
    
                Log::info('Make booking Digits: ' . $request->input('Digits'));
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
                $response->play($this->statementUrl . 'statement_15_min_before.wav');
                $response->play($this->statementUrl . 'statement_goodbye.wav');

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
        return WhatsApp::where(['customer_number' =>  $userPhoneNumber])->orderBy('created_at', 'desc')->first();
    }
}
