<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Notification, Country};
use App\Models\{VenueAddress, Venue, WhatsApp, VenueSloting, Vistors};
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        $waId = $body['WaId'];
        $Respond = $body['Body'];
        $responseString = strval($Respond);
        $countryCode = $this->findCountryByPhoneNumber($waId);
        $cleanNumber = str_replace($countryCode, '', $waId);
        $existingCustomer = WhatsApp::where(['customer_number' =>  $userPhoneNumber])->orderBy('created_at', 'desc')->first();
        // $user = Vistors::Where('phone', $validatedData['mobile'])->first();
        $dataArr = [];
        $countryId = Venue::where(['iso' => 'PK'])->get()->first();
        // https://emojipedia.org/keycap-digit-five
        $whatsAppEmoji = [
            '1' => '1️⃣',
            '2' => '2️⃣',
            '3' => '3️⃣',
            '4' => '4️⃣',
            '5' => '5️⃣'
        ];

        $options = [];
        $responseAccept = [];
        if (!empty($existingCustomer)) {
            $responseAccept =    explode(',', $existingCustomer->response_options);
        }

        if (!empty($existingCustomer) && $existingCustomer->data_sent_to_customer == 'Slot Booked') {
            $message = <<<EOT
                You already booked your slot with us. Thank You.
                EOT;

            $this->sendMessage($userPhoneNumber, $message);

            return false;
        }

        if (empty($existingCustomer)) {
            $step = 0;
            $data = '';
            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $options = ['1' , '2'];

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($options),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];
            WhatsApp::create($dataArr);
        }else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && ($existingCustomer->steps == 0 )) {

                $step = $existingCustomer->steps + 1;
                $customer_response = $Respond;

                $lang = ($Respond == 1 ) ? 'eng' : 'urdu';
                $message = $this->WhatsAppbotMessages('', $step , $lang );
                $this->sendMessage($userPhoneNumber, $message);
                $options = ['1','2'];
                $dataArr = [
                    'lang' => $lang,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => null,
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => implode(',', $options)
                ];
                WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && ($existingCustomer->steps == 1 )) {

            $step = $existingCustomer->steps + 1;
            $customer_response = $Respond;
            $lang =  $existingCustomer->lang;
            $options = ['1','2'];
            $message = $this->WhatsAppbotMessages('', $step , $lang );
            $this->sendMessage($userPhoneNumber, $message);

            $options = ['1'];
            $dataArr = [
                'lang' => $lang,
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => null,
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];
            WhatsApp::create($dataArr);
    }else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && $existingCustomer->steps == 2 ) {

            $step = $existingCustomer->steps + 1;
            $lang =  $existingCustomer->lang;

            $data = ($lang =='eng') ?'Currently no venue or city available':  'فی الحال کوئی مقام یا شہر دستیاب نہیں ہے۔'  ;
            $dua_option = ($existingCustomer->customer_response == 1) ? 'dua' : 'dum';
            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('venue_date', '>=', date('Y-m-d'))
                 ->take(3)
                ->get();

            if(empty($venuesListArr)){
                $message = $this->WhatsAppbotMessages($data, 9 , $lang);
                $this->sendMessage($userPhoneNumber, $message);
            }

            $cityArr = [];

            $distinctCities = $venuesListArr->pluck('city')->unique();



            $i = 1;
            foreach ($distinctCities as $key => $city) {

                $cityArr[$city] = trim($whatsAppEmoji[$i] . ' ' . $city);

                $options[$i] =  $i;
                $i++;
            }
            asort($cityArr);
            asort($options);

            $data = implode("\n", $cityArr);

            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'lang' => $lang,
                'dua_option' => $dua_option,
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($cityArr),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];
            WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept)  && $existingCustomer->steps == 2) {
            // send Dates here

            $dua_option = $existingCustomer->dua_option;

            $step = $existingCustomer->steps + 1;
            $customer_response = $existingCustomer->customer_response;
            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);

            $cityAr = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
            $cityName = explode('-', $cityAr);

            //  $getDate = $data_sent_to_customer[$Respond];

            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('city',  $cityName[0])
                ->where('venue_date', '=', date('Y-m-d'))
                ->orderBy('venue_date', 'ASC')
                ->first();

            if (!empty($venuesListArr)) {

                $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $dua_option])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id'])->first();

                if ($tokenIs) {


                    $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
                    // $slotId = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
                    $slotId = $tokenIs->slot_id;
                    $venueSlots = VenueSloting::find($slotId);
                    $venueAddress = $venueSlots->venueAddress;
                    // $tokenId = $venueSlots->token_id;
                    $tokenId = str_pad($tokenIs->token_id, 2, '0', STR_PAD_LEFT);
                    $cleanedNumber = str_replace('whatsapp:', '', $userPhoneNumber);
                    $venue = $venueAddress->venue;
                    $result = $this->formatWhatsAppNumber($cleanedNumber);
                    $userMobile = $result['mobileNumber'];
                    $timestamp = strtotime($venueSlots->slot_time);
                    $slotTime = date('h:i A', $timestamp) . '(' . $venueAddress->timezone . ')';
                    $uuid = Str::uuid()->toString();
                    Vistors::create([
                        'is_whatsapp' => 'yes',
                        'slot_id' => $slotId,
                        'meeting_type' => 'on-site',
                        'booking_uniqueid' =>  $uuid,
                        'booking_number' => $tokenId,
                        'country_code' => '+' . $countryCode,
                        'phone' => $cleanNumber,
                        'source' => 'WhatsApp'
                    ]);
                    $duaBy = 'Qibla Syed Sarfraz Ahmad Shah';

                    $appointmentDuration = $venueAddress->slot_duration . ' minute 1 Question';

                    $statusLink = route('booking.status', $uuid);
                    $pdfLink = '';

                    $message = <<<EOT
                        Your Dua Appointment Confirmed With $duaBy ✅

                        Event Date : $venueAddress->venue_date

                        Venue : $venueAddress->city

                        $venueAddress->address

                        Token #$tokenId

                        Your Mobile : $userMobile

                        Your Appointment Time : $slotTime

                        Appointment Duration : $appointmentDuration

                        $venueAddress->status_page_note
                        To view your token online please click below:

                        $statusLink

                        $pdfLink

                        EOT;
                    $this->sendMessage($userPhoneNumber, $message);
                    $dataArr = [
                        'customer_number' => $userPhoneNumber,
                        'customer_response' => $Respond,
                        'bot_reply' =>  $message,
                        'data_sent_to_customer' => 'Slot Booked',
                        'last_reply_time' => date('Y-m-d H:i:s'),
                        'steps' => 5
                    ];
                    WhatsApp::create($dataArr);
                    $this->FlushEntries($userPhoneNumber);

                    $youtubeLink = "https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1";
                    $whatspp = "https://whatsapp.com/channel/0029Va9FvbdGE56jAmX0fo2w";
                    $fb = "https://www.facebook.com/profile.php?id=100090074346701";
                    $spotify = "https://open.spotify.com/show/2d2PAVfeqIkZaWaJgrgnif?si=56e4fd97930f4b0a";
                    $applePodcase = "https://podcasts.apple.com/us/podcast/syed-sarfraz-ahmed-shah/id1698147381";
                    $kf = "https://kahayfaqeer.org";
                    $kfvideo = "https://videos.kahayfaqeer.org";
                    $subScription = <<<EOT
                        Please follow Qibla Syed Sarfraz Ahmad Shah lectures as follows:

                        Subscribe to Syed Sarfraz Ahmad Shah Official YouTube Channel ▶️  $youtubeLink

                        Follow Syed Sarfraz Ahmad Shah (Official) channel on WhatsApp ▶️ $whatspp

                        Follow Syed Sarfraz Ahmad Shah (Official) channel on Facebook ▶️ $fb

                        Read or listen all Kahay Faqeer Series books for free ▶️$kf

                        Watch Syed Sarfraz Ahmad Shah video library of over 2000+ videos ▶️ $kfvideo

                        Listen Syed Sarfraz Ahmad Shah on Spotify ▶️ $spotify

                        Listen Syed Sarfraz Ahmad Shah on Apple Podcast ▶️  $applePodcase

                        EOT;

                    $this->sendMessage($userPhoneNumber, $subScription);
                } else {

                    $data = implode("\n", []);
                    $message = $this->WhatsAppbotMessages($data, $step);
                    $this->sendMessage($userPhoneNumber, $message);
                }
            } else {
                return response()->json([
                    'status' =>  false,
                    'message' => "There is no venue for the Selected Date."
                ]);
            }


            $VenueDates = [];
            $i = 1;
            foreach ($venuesListArr as $venueDate) {
                $columnToShow = $venueDate->combinationData->columns_to_show;
                // $venueDate = $venueDate->venue_date;
                Log::info('Received venue_date: ' . $venueDate->venue_date);
                Log::info('Received slot_starts_at_morning: ' . $venueDate->slot_starts_at_morning);
                $venueStartTime = Carbon::parse($venueDate->venue_date . ' ' . $venueDate->slot_starts_at_morning);

                if ($venueStartTime <=  Carbon::now() && $columnToShow >= $i) {
                    $VenueDates[$venueDate->id] = trim($whatsAppEmoji[$i] . ' ' . $venueDate->venue_date);
                    $options[] = $i;
                    $i++;
                }
                // else{
                //     $VenueDates[$venueDate->id] = trim($whatsAppEmoji[$i]. ' El' .$venueDate->venue_date);
                // }
                else if ($columnToShow >= $i && $venueDate->venue_date > Carbon::now()->format('Y-m-d')) {
                    $VenueDates[$venueDate->id] = trim($whatsAppEmoji[$i] . ' ' . $venueDate->venue_date);
                    $options[] = $i;
                    $i++;
                }
            }
            $data = implode("\n", $VenueDates);
            $message = $this->WhatsAppbotMessages($data, $step);


            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($VenueDates),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];
            WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept)  && $existingCustomer->steps == 3) {
            // send Slots  here

            $isVisiable = false;

            $step = $existingCustomer->steps + 1;
            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
            $venueAddreId = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
            $venueAddress = VenueAddress::find($venueAddreId);
            $countryTimeZone = $venueAddress->timezone;


            // User Already There

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
                    $message = $this->WhatsAppbotMessages($data, 9);
                    $this->sendMessage($userPhoneNumber, $message);
                    $this->FlushEntries($userPhoneNumber);
                    return false;
                }
            }


            $mytime = Carbon::now()->tz($countryTimeZone);
            $eventDate = Carbon::parse($venueAddress->venue_date . ' ' . $venueAddress->slot_starts_at_morning, $countryTimeZone);
            $hoursRemaining = $eventDate->diffInHours($mytime);
            $slotsAppearAfter = intval($venueAddress->slot_appear_hours);
            $isTimeOver = false;
            if ($slotsAppearAfter == 0) {
                $isVisiable = true;
            } else if ($hoursRemaining <= $slotsAppearAfter) {
                $isVisiable = true;
            }

            if ($mytime->greaterThanOrEqualTo($eventDate)) {
                $isTimeOver = true;
            }


            $countryTimeZone = $venueAddress->timezone;
            $mytime = Carbon::now()->tz($countryTimeZone);
            $slotTime =  $mytime->format('H:i:s');
            // $getDate = $data_sent_to_customer[$Respond];

            if ($isVisiable == true && $isTimeOver == false) {

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
                    $slotArr[$slot->id] = $whatsAppEmoji[$i] . ' ' . $slotTime;
                    $options[] = $i;
                    $i++;
                }

                if (!empty($slotArr)) {
                    $data = implode("\n", $slotArr);
                } else {
                    $data = "Currently No Slots";
                    $step = 9; // warning messaghe will issue
                }
            } else if ($isTimeOver == true) {
                $step = 9; // warning messaghe will issue
                $data =  'Dua Meeting is already Started for Today. No Token will be issued for Today Please try again on some other day. Thank You.';
                $this->FlushEntries($userPhoneNumber);
            } else {
                $step = 9; // warning messaghe will issue
                $data =  'Dua meeting tokens will be available only ' . $slotsAppearAfter . '  hours before the dua starts. Please try again later ';
                $this->FlushEntries($userPhoneNumber);
            }


            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);
            if ($step == 9) {
                return false;
            }

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($slotArr),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];
            WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept)  && $existingCustomer->steps == 4) {


            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
            $slotId = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);

            $venueSlots = VenueSloting::find($slotId);
            $venueAddress = $venueSlots->venueAddress;
            // $tokenId = $venueSlots->token_id;
            $tokenId = str_pad($venueSlots->token_id, 2, '0', STR_PAD_LEFT);
            $cleanedNumber = str_replace('whatsapp:', '', $userPhoneNumber);
            $venue = $venueAddress->venue;
            $result = $this->formatWhatsAppNumber($cleanedNumber);
            $userMobile = $result['mobileNumber'];

            $timestamp = strtotime($venueSlots->slot_time);
            $slotTime = date('h:i A', $timestamp) . '(' . $venueAddress->timezone . ')';
            $uuid = Str::uuid()->toString();
            Vistors::create([
                'is_whatsapp' => 'yes',
                'slot_id' => $slotId,
                'meeting_type' => 'on-site',
                'booking_uniqueid' =>  $uuid,
                'booking_number' => $tokenId,
                'country_code' => '+' . $countryCode,
                'phone' => $cleanNumber,
                'source' => 'WhatsApp'
            ]);
            $duaBy = 'Qibla Syed Sarfraz Ahmad Shah';

            $appointmentDuration = $venueAddress->slot_duration . ' minute 1 Question';

            $statusLink = route('booking.status', $uuid);
            $pdfLink = '';

            $message = <<<EOT
            Your Dua Appointment Confirmed With $duaBy ✅

            Event Date : $venueAddress->venue_date

            Venue : $venueAddress->city

            $venueAddress->address

            Token #$tokenId

            Your Mobile : $userMobile

            Your Appointment Time : $slotTime

            Appointment Duration : $appointmentDuration

            $venueAddress->status_page_note
            To view your token online please click below:

            $statusLink

            $pdfLink

            EOT;
            $this->sendMessage($userPhoneNumber, $message);
            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => 'Slot Booked',
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => 5
            ];
            WhatsApp::create($dataArr);
            $this->FlushEntries($userPhoneNumber);

            $youtubeLink = "https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1";
            $whatspp = "https://whatsapp.com/channel/0029Va9FvbdGE56jAmX0fo2w";
            $fb = "https://www.facebook.com/profile.php?id=100090074346701";
            $spotify = "https://open.spotify.com/show/2d2PAVfeqIkZaWaJgrgnif?si=56e4fd97930f4b0a";
            $applePodcase = "https://podcasts.apple.com/us/podcast/syed-sarfraz-ahmed-shah/id1698147381";
            $kf = "https://kahayfaqeer.org";
            $kfvideo = "https://videos.kahayfaqeer.org";
            $subScription = <<<EOT
            Please follow Qibla Syed Sarfraz Ahmad Shah lectures as follows:

            Subscribe to Syed Sarfraz Ahmad Shah Official YouTube Channel ▶️  $youtubeLink

            Follow Syed Sarfraz Ahmad Shah (Official) channel on WhatsApp ▶️ $whatspp

            Follow Syed Sarfraz Ahmad Shah (Official) channel on Facebook ▶️ $fb

            Read or listen all Kahay Faqeer Series books for free ▶️$kf

            Watch Syed Sarfraz Ahmad Shah video library of over 2000+ videos ▶️ $kfvideo

            Listen Syed Sarfraz Ahmad Shah on Spotify ▶️ $spotify

            Listen Syed Sarfraz Ahmad Shah on Apple Podcast ▶️  $applePodcase

            EOT;

            $this->sendMessage($userPhoneNumber, $subScription);
        } else {
            $optionss = $existingCustomer->data_sent_to_customer;

            if (empty($optionss)) {
                $data = $whatsAppEmoji[1];
            } else {
                $data = json_decode($optionss, true);
                $data = implode("\n", $data);
            }


            $message = <<<EOT

            Please press the correct number as below
            $data

            EOT;
            $this->sendMessage($userPhoneNumber, $message);
        }
    }

    private function WhatsAppbotMessages($data, $step,$lang='')
    {
        $message = '';

        if ($step == 0 && $lang == '') {

            $message = <<<EOT
            Please select your language?
            1 English
            2 Urdu
            EOT;
        }
        if ($step == 1) {

            if($lang =='eng'){

                $message = <<<EOT
                Welcome to the KahayFaqeer.org Dua Appointment WhatsApp Chatbot Scheduler.

                Please note that this dua appointment is valid only for visitors who can physically visit dua ghar in Pakistan. Dua requests via online or phone are not available at the moment. Only proceed if you are fully sure to visit dua ghar in person.

                To schedule a dua meeting with $data please enter 1
                EOT;

            }else{

                $message = <<<EOT
                KahayFaqeer.org دعا اپائنٹمنٹ شیڈولر میں خوش آمدید۔

                براہ کرم نوٹ کریں کہ اس وقت آن لائن یا فون کے ذریعے دعا دستیاب نہیں ہے۔

                قبلہ سید سرفراز احمد شاہ صاحب کے ساتھ دعا ملاقات کا شیڈول کرنے کے لیے براہ کرم 1 درج کریں۔
                EOT;
            }


        }
        else if ($step == 2) {
            if($lang =='eng'){
                $message = <<<EOT
                Please select your type of dua?
                1 Dua
                2 Dum
                EOT;
            }else{
                $message = <<<EOT
                براہ کرم اپنی دعا کی قسم منتخب کریں؟
                1 دعا
                2 دم
                EOT;
            }

        } else if ($step == 3) {
            if($lang =='eng'){
                $message = <<<EOT
                Please enter the number for your city
                $data
                EOT;
            }else{
                $message = <<<EOT
                براہ کرم اپنے شہر کا نمبر درج کریں۔
                $data
                EOT;
            }

        } else if ($step == 9) {
            if($lang =='eng'){
                $message = <<<EOT
                Please see the below warning message:
                $data
                EOT;
            }else{
                $message = <<<EOT
                براہ کرم ذیل میں انتباہی پیغام دیکھیں:
                $data
                EOT;
            }
        }

        return $message;
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

    private function FlushEntries($phoneNUmber)
    {
        WhatsApp::where(['customer_number' => $phoneNUmber])->delete();
    }



    function formatWhatsAppNumber($whatsappNumber)
    {
        // Remove any non-numeric characters
        $whatsappNumber = preg_replace('/[^\d]/', '', $whatsappNumber);

        // Check if the number starts with a plus sign
        if (substr($whatsappNumber, 0, 1) === '+') {
            $countryCode = substr($whatsappNumber, 0, 3);
            $mobileNumber = substr($whatsappNumber, 3);
        } else {
            // If the number doesn't start with a plus sign, assume the country code is missing
            $countryCode = '';
            $mobileNumber = $whatsappNumber;
        }

        return [
            'countryCode' => $countryCode,
            'mobileNumber' => $mobileNumber,
        ];
    }

    function findKeyByValueInArray($array, $key)
    {
        if ($key > 0) {
            $key = $key - 1;
        }
        $arrayKeys = array_keys($array);
        return ($arrayKeys[$key]) ? $arrayKeys[$key] : null;
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
}
