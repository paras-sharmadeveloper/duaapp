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

    public function deleteRecordAfter30($existingCustomer){
        $createdAt = Carbon::parse($existingCustomer->created_at);
        $currentTime = Carbon::now();
        $differenceInMinutes = $createdAt->diffInMinutes($currentTime);

        if ($differenceInMinutes > 10) {
            $existingCustomer->delete();
        }
    }

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

        if(!empty($existingCustomer)){
            $this->deleteRecordAfter30($existingCustomer);
        }

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

            // Choose Language
            $step = 0;
            $data = '';
            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $options = ['1' , '2'];
            $data = [
               '1' => trim($whatsAppEmoji['1'] . ' English') ,
               '2' => trim($whatsAppEmoji['2'] . ' Urdu')
            ];

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($data),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];

            WhatsApp::create($dataArr);
        }else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && ($existingCustomer->steps == 0 )) {
              // Welcome  Message
                $step = $existingCustomer->steps + 1;
                $customer_response = $Respond;

                $lang = ($Respond == 1 ) ? 'eng' : 'urdu';
                $message = $this->WhatsAppbotMessages('', $step , $lang );
                $this->sendMessage($userPhoneNumber, $message);
                $options = ['1'];
                $dataArr = [
                    'lang' => $lang,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => json_encode($options),
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => implode(',', $options)
                ];
                WhatsApp::create($dataArr);
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && ($existingCustomer->steps == 1 )) {
             // Welcome  Dua Type
            $step = $existingCustomer->steps + 1;
            $customer_response = $Respond;
            $lang =  $existingCustomer->lang;
            $options = ['1','2'];

            $data = [
                '1' => trim($whatsAppEmoji['1'] . ' Dua') ,
                '2' => trim($whatsAppEmoji['2'] . ' Dum')
             ];


            $message = $this->WhatsAppbotMessages('', $step , $lang );
            $this->sendMessage($userPhoneNumber, $message);


            $dataArr = [
                'lang' => $lang,
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($data),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => $step,
                'response_options' => implode(',', $options)
            ];
            WhatsApp::create($dataArr);
    }else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && $existingCustomer->steps == 2 ) {
          //   City
            $step = $existingCustomer->steps + 1;
            $lang =  $existingCustomer->lang;


            $dua_option = ($Respond == 1) ? 'dua' : 'dum';
            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('venue_date', '>=', date('Y-m-d'))
                ->take(3)
                ->get();

            if(empty($venuesListArr)){
                $message = $this->WhatsAppbotMessages('', 9 , $lang);
                $this->sendMessage($userPhoneNumber, $message);
            }

            $cityArr = [];

            $distinctCities = $venuesListArr->pluck('city')->unique();



            $i = 1;
            foreach ($distinctCities as $key => $city) {

                if($lang == 'urdu'){
                    $cityName =  $this->cityArrWithUrdu($city);
                }else{
                    $cityName = $city;
                }


                $cityArr[$city] = trim($whatsAppEmoji[$i] . ' ' . $cityName);

                $options[$i] =  $i;
                $i++;
            }
            asort($cityArr);
            asort($options);


            if(empty($cityArr)){
                $data = ($lang =='eng') ?'Currently no venue or city available':  'فی الحال کوئی مقام یا شہر دستیاب نہیں ہے۔';
                $message = $this->WhatsAppbotMessages($data, 9 , $lang);
            }else{
                $data = implode("\n", $cityArr);
                $message = $this->WhatsAppbotMessages($data, $step,$lang);
            }
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
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept)  && $existingCustomer->steps == 3) {

              // Seat Booked Here
            $dua_option = $existingCustomer->dua_option;

            $step = $existingCustomer->steps + 1;
            $customer_response = $existingCustomer->customer_response;
            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);

            $cityAr = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
            $cityName = explode('-', $cityAr);
            $lang =  $existingCustomer->lang;
            //  $getDate = $data_sent_to_customer[$Respond];

            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('city',  $cityName[0])
                ->whereDate('venue_date',date('Y-m-d'))

                ->orderBy('venue_date', 'ASC')
                ->first();

            if (!empty($venuesListArr)) {

                $status = isAllowedTokenBooking($venuesListArr->venue_date, $venuesListArr->slot_appear_hours , $venuesListArr->timezone);
                if ($status['allowed']) {

                    $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $dua_option])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id'])->first();

                    if ($tokenIs) {


                        $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
                        // $slotId = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
                        $slotId = $tokenIs->id;
                        $duaType = $tokenIs->type;
                        $venueAddress = $tokenIs->venueAddress;
                        // $tokenId = $venueSlots->token_id;
                        $tokenId = str_pad($tokenIs->token_id, 2, '0', STR_PAD_LEFT);
                        $cleanedNumber = str_replace('whatsapp:', '', $userPhoneNumber);
                        $venue = $venueAddress->venue;
                        $result = $this->formatWhatsAppNumber($cleanedNumber);
                        $userMobile = $result['mobileNumber'];
                        $timestamp = strtotime($tokenIs->slot_time);
                        $slotTime = date('h:i A', $timestamp) . '(' . $venueAddress->timezone . ')';
                        $uuid = Str::uuid()->toString();

                        $venueDate = date("d M Y h:i A", strtotime($venueAddress->venue_date));
                        Vistors::create([
                            'is_whatsapp' => 'yes',
                            'slot_id' => $slotId,
                            'meeting_type' => 'on-site',
                            'booking_uniqueid' =>  $uuid,
                            'booking_number' => $tokenId,
                            'country_code' => '+' . $countryCode,
                            'phone' => $cleanNumber,
                            'source' => 'WhatsApp',
                            'dua_type' => $dua_option,
                            'lang' => ($lang == 'eng') ? 'en' : 'ur'
                        ]);
                        $duaBy = 'Qibla Syed Sarfraz Ahmad Shah';

                        $appointmentDuration = $venueAddress->slot_duration . ' minute 1 Question';

                        $statusLink = route('booking.status', $uuid);
                        $pdfLink = '';

                        $message = <<<EOT
                            Your Dua Appointment Confirmed With $duaBy ✅

                            Event Date : $venueDate

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

                }else{
                    $data = ($lang =='eng') ? $status['message'] :  $status['message_ur'];
                    $message = $this->WhatsAppbotMessages($data, 9 , $lang);
                    $this->sendMessage($userPhoneNumber, $message);
                }




            } else {
                $data = ($lang =='eng') ? 'There is no venue for the Selected Date.' : 'There is no venue for the Selected Date.';
                $message = $this->WhatsAppbotMessages($data, 9 , $lang);
                $this->sendMessage($userPhoneNumber, $message);



            }



        } else{
            $optionss = $existingCustomer->data_sent_to_customer;

            if(empty($optionss)){
                $data = $whatsAppEmoji[1];
            }else{
                $data = json_decode($optionss , true);
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
                Welcome to the KahayFaqeer.org Dua Appointment Scheduler.

                Please note online or phone dua meeting is not possible at this time.

                To schedule a dua meeting with Qibla Syed Sarfraz Ahmad Shah Sahab please enter 1

                EOT;

            }else{

                $message = <<<EOT
                و KahayFaqeer.org دعا اپائنٹمنٹ شیڈولر میں خوش آمدید۔

                براہ مہربانی نوٹ کریں کہ اس وقت آن لائن یا فون پر دعا ممکن نہیں ہے۔

                قبلہ سید سرفراز احمد شاہ صاحب سے دعا ملاقات کا وقت طے کرنے کے لیے براہ مہربانی 1 درج کریں۔
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

    private function cityArrWithUrdu($city){
        if($city == 'Lahore'){
            $name = 'لاہور';
        }else  if($city == 'Islamabad'){
            $name = 'اسلام آباد';
        }else  if($city == 'Karachi'){
            $name = 'کراچی';
        }
        return $name;
    }
}
