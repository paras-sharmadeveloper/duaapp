<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Notification, Country};
use App\Models\{VenueAddress, Venue, WhatsApp, VenueSloting, Vistors, Reason};
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    private $countryId;
    public function __construct($var = null)
    {
        $this->countryId = Venue::where(['iso' => 'PK'])->get()->first();
    }


    public function handleWebhook(Request $request)
    {
        $body = $request->all();
        $today = Carbon::now();
        $userPhoneNumber = $body['From'];
        $waId = $body['WaId'];
        $Respond = $body['Body'];
        $existingCustomer = WhatsApp::where(['customer_number' =>  $userPhoneNumber])->whereDate('created_at', $today)->orderBy('created_at', 'desc')->first();

        $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
            if (empty($data_sent_to_customer)) {
                Log::info("data_sent_to_customer Log if one message delivered");
                return false;
            }

        if (empty($existingCustomer)) {
            $msh= 'Dua / Dum tokens can ONLY be booked on official website https://kahayfaqeer.org/dua via mobile on Monday 08:00 AM on first come first serve basis.

            دعا / دم ٹوکن صرف آفیشل ویب سائیٹ سے بزریعہ موبائل سوموار صبح ۸ بجے لئیے جاسکتے ہیں پہلے آئیے پہلے پائیے کی بنیاد پر۔
            https://kahayfaqeer.org/dua';
            $message = $this->WhatsAppbotMessages($msh, 9,'');
            $this->sendMessage($userPhoneNumber, $message);

            $dataArr = [
                'lang' => 'en',
                'dua_option' => [],
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode([]),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => 0,
                'response_options' => null
            ];
            WhatsApp::create($dataArr);
        }else{
            return false;
        }





        return false;





















        Log::info('landed' . $this->countryId->id);
        $body = $request->all();
        $today = Carbon::now();
        // $NextDate = $today->addDay();
        // $newDate = $NextDate->format('Y-m-d');
        // Extract necessary information from the incoming request
        $userPhoneNumber = $body['From'];
        $waId = $body['WaId'];
        $Respond = $body['Body'];
        $responseString = strval($Respond);

        $countryCode = $this->findCountryByPhoneNumber($waId);
        $cleanNumber = str_replace($countryCode, '', $waId);

        $existingCustomer = WhatsApp::where(['customer_number' =>  $userPhoneNumber])->whereDate('created_at', $today)->orderBy('created_at', 'desc')->first();

        $venue = VenueAddress::where('venue_id', $this->countryId->id)
            //  ->where('city',  $cityName[0])
            ->whereDate('venue_date', $today)
            ->orderBy('venue_date', 'ASC')
            ->first();
        $options = [];
        $responseAccept = [];


        if (empty($existingCustomer) && !empty($venue)) {



            Log::info('Test' . $this->countryId->id);


            $options = ['1', '2'];


            $dataEn = 'Dua Ghar *' . $venue->city . '*. If you willing to book dua/dum for city then please enter number below.

            Please enter your type of dua?
            *1* Dua
            *2* Dum';

            $dataUr = 'دعا گھر *' . $venue->city . '* کے ساتھ کھلے ہیں۔ اگر آپ شہر کے لیے دعا/دم بک کرنا چاہتے ہیں تو براہ کرم نیچے نمبر درج کریں،

            براہ کرم اپنی دعا کی قسم درج کریں؟
            *1* دعا
            *2* دم';

            $message = $this->WhatsAppbotMessagesNew($dataEn, $dataUr);
            $this->sendMessage($userPhoneNumber, $message);

            $data = [
                '1' => '*1* Dua',
                '2' => '*2* Dum',
            ];

            $dataArr = [
                'customer_number' => $userPhoneNumber,
                'customer_response' => $Respond,
                'bot_reply' =>  $message,
                'data_sent_to_customer' => json_encode($data),
                'last_reply_time' => date('Y-m-d H:i:s'),
                'steps' => 0,
                'response_options' => implode(',', $options)
            ];

            WhatsApp::create($dataArr);
        }


        if (!empty($existingCustomer) && !empty($venue)) {

            $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
            if (empty($data_sent_to_customer)) {
                Log::info("data_sent_to_customer Log if one message delivered");
                return false;
            }

            $status = TokenBookingAllowed($venue->venue_date, $venue->venue_date_end,  $venue->timezone);


            $venue_available_country =  json_decode($venue->venue_available_country);
            $country = Country::where('phonecode', str_replace('+', '', $countryCode))->first();
            $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);
            $rejoin = $venue->rejoin_venue_after;
            $rejoinStatus = userAllowedRejoin($cleanNumber, $rejoin);


            // Log::info("status".$venue->status);
            // Log::info("userCountry".$userCountry['allowed']);
            // Log::info("Rejoin".$rejoinStatus['allowed']);
            if (!empty($venue) &&  $venue->status == 'inactive') {

                $message = $this->WhatsAppNewWarning('For some reason currently this venue not accepting bookings. Please try after some time. Thank You', 'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ');
                $this->sendMessage($userPhoneNumber, $message);

                $dataArr = [
                    'lang' => 'en',
                    'dua_option' => [],
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => json_encode([]),
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => 0,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                // $this->FlushEntries($userPhoneNumber);
                return false;
            } elseif (!$userCountry['allowed']) {


                $message = $this->WhatsAppNewWarning($userCountry['message'], $userCountry['message_ur']);

                $this->sendMessage($userPhoneNumber, $message);

                $dataArr = [
                    'lang' => 'en',
                    'dua_option' => json_encode([]),
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $message,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => json_encode([]),
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => 0,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                // $this->FlushEntries($userPhoneNumber);
                return false;
            } else if (!$rejoinStatus['allowed']) {
                $message = $this->WhatsAppNewWarning($rejoinStatus['message'], $rejoinStatus['message_ur']);
                $this->sendMessage($userPhoneNumber, $message);
                $dataArr = [
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => json_encode([]),
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => 0,
                    'response_options' => implode(',', $options)
                ];

                WhatsApp::create($dataArr);
                return false;
            } elseif ($status['allowed']) {
                $dua_option = '';
                if ($responseString == 1) {
                    $dua_option = 'dua';
                } else
                if ($responseString == 2) {
                    $dua_option = 'dum';
                } else {
                    $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
                    // $data = implode('\n',$data_sent_to_customer);

                    $data = implode("\n", $data_sent_to_customer);
                    $message = <<<EOT
                        Please see the below warning message:
                        $data
                        براہ کرم ذیل میں انتباہی پیغام دیکھیں:
                        *1* دعا
                        *2* دم
                        EOT;
                    $this->sendMessage($userPhoneNumber, $message);
                    return false;
                }

                if ($dua_option == 'dua' && !empty($venue->reject_dua_id)) {
                    $reason  = Reason::find($venue->reject_dua_id);

                    $message = $this->WhatsAppNewWarning($reason->reason_english, $reason->reason_urdu);

                    $this->sendMessage($userPhoneNumber, $message);

                    //  $message = $this->WhatsAppbotMessages($data, 9 , $lang);

                    $dataArr = [
                        'lang' => 'en',
                        'dua_option' => $dua_option,
                        'customer_number' => $userPhoneNumber,
                        'customer_response' => $Respond,
                        'bot_reply' =>  $message,
                        'data_sent_to_customer' =>  json_encode([]),
                        'last_reply_time' => date('Y-m-d H:i:s'),
                        'steps' => 1,
                        'response_options' => null
                    ];
                    WhatsApp::create($dataArr);
                    // $this->FlushEntries($userPhoneNumber);

                    return false;
                }
                if ($dua_option == 'dum' && !empty($venue->reject_dum_id)) {
                    $reason  = Reason::find($venue->reject_dua_id);

                    $message = $this->WhatsAppNewWarning($reason->reason_english, $reason->reason_urdu);

                    $this->sendMessage($userPhoneNumber, $message);

                    $dataArr = [
                        'lang' => 'en',
                        'dua_option' => $dua_option,
                        'customer_number' => $userPhoneNumber,
                        'customer_response' => $Respond,
                        'bot_reply' =>  $message,
                        'data_sent_to_customer' =>  json_encode([]),
                        'last_reply_time' => date('Y-m-d H:i:s'),
                        'steps' => 1,
                        'response_options' => null
                    ];
                    WhatsApp::create($dataArr);
                    // $this->FlushEntries($userPhoneNumber);

                    return false;
                }

                Log::info('allowed dua_option' . $dua_option);

                $tokenIs = VenueSloting::where('venue_address_id', $venue->id)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $dua_option])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id', 'type'])->first();

                if ($tokenIs) {


                    $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
                    // $slotId = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
                    $slotId = $tokenIs->id;
                    $duaType = $tokenIs->type;

                    $venueAddress = $tokenIs->venueAddress;


                    $rejoin = $venueAddress->rejoin_venue_after;
                    // Rejoin Stat
                    $tokenId = str_pad($tokenIs->token_id, 2, '0', STR_PAD_LEFT);
                    $tokenType = $tokenIs->type;
                    $cleanedNumber = str_replace('whatsapp:', '', $userPhoneNumber);
                    $venue = $venueAddress->venue;
                    $result = $this->formatWhatsAppNumber($cleanedNumber);
                    $userMobile = $result['mobileNumber'];
                    $timestamp = strtotime($tokenIs->slot_time);
                    $slotTime = date('h:i A', $timestamp) . '(' . $venueAddress->timezone . ')';
                    $uuid = Str::uuid()->toString();

                    $token  = $tokenId . ' (' . ucwords($tokenType) . ')';

                    $venueDateEn = date("d M Y", strtotime($venueAddress->venue_date));
                    $venueDateUr =  date("d m Y", strtotime($venueAddress->venue_date));


                    try {
                        // Attempt to create the visitor
                        $visitor = Vistors::create([
                            'is_whatsapp' => 'yes',
                            'slot_id' => $slotId,
                            'meeting_type' => 'on-site',
                            'booking_uniqueid' =>  $uuid,
                            'booking_number' => $tokenId,
                            'country_code' => '+' . $countryCode,
                            'phone' => $cleanNumber,
                            'source' => 'WhatsApp',
                            'dua_type' => $dua_option,
                            'lang' => 'en'
                        ]);
                        $duaby = 'Qibla Syed Sarfraz Ahmad Shah';
                        $duabyUr = 'قبلہ سید سرفراز احمد شاہ';

                        $appointmentDuration = $venueAddress->slot_duration . ' minute 1 Question';

                        // $statusNote =($lang == 'eng') ? $venueAddress->status_page_note : $venueAddress->status_page_note_ur;
                        //  $venueAdrress = ($lang == 'en') ? $venueAddress->address : $venueAddress->address_ur;

                        $bookId = base64_encode($visitor->id);
                        $statusLink = route('booking.status', $uuid);

                        $pdfLink = '';
                        // $duaby ='';


                        $pdfLinkEn = 'Subscribe to Syed Sarfraz Ahmad Shah Official YouTube Channel  https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1';
                        $pdfLinkUr = 'سید سرفراز احمد شاہ آفیشل یوٹیوب چینل کو سبسکرائب کریں https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1';


                        $message = <<<EOT

                        آپ کی دعا قبلہ سید سرفراز احمد شاہ سے تصدیق شدہ ✅

                        واقعہ کی تاریخ : $venueDateUr
                        مقام: $venueAddress->city

                        $venueAddress->address_ur

                        ٹوکن # $token

                        کا موبائل : $userMobile

                        ملاقات کا دورانیہ :  $appointmentDuration

                        ٹوکن URL:
                        $statusLink

                        Your Dua Appointment Confirmed With Qibla Syed Sarfraz Ahmad Shah ✅

                        Event Date : $venueDateEn

                        Venue : $venueAddress->city

                        $venueAddress->address

                        Token # $token

                        Your Mobile : $userMobile

                        Appointment Duration : $appointmentDuration

                        Token URL:
                        $statusLink
                        EOT;

                        //     $message = <<<EOT

                        // پ کی دعا سے ملاقات کی تصدیق  $duabyUr ✅ سے ہوئی۔

                        // واقعہ کی تاریخ : $venueDateUr

                        // مقام: $venueAddress->city

                        // $venueAddress->address_ur

                        // ٹوکن #$token

                        // آپ کا موبائل : $userMobile

                        // ملاقات کا دورانیہ : $appointmentDuration

                        // $venueAddress->status_page_note_ur
                        // اپنا ٹوکن آن لائن دیکھنے کے لیے براہ کرم نیچے کلک کریں:

                        // $statusLink

                        // $pdfLinkUr

                        // Your Dua Appointment Confirmed With $duaby ✅

                        // Event Date : $venueDateEn

                        // Venue : $venueAddress->city

                        // $venueAddress->address

                        // Token #$token

                        // Your Mobile : $userMobile

                        // Appointment Duration : $appointmentDuration

                        // $venueAddress->status_page_note
                        // To view your token online please click below:

                        // $statusLink

                        // $pdfLinkEn
                        // EOT;


                        $this->sendMessage($userPhoneNumber, $message);
                        $dataArr = [
                            'customer_number' => $userPhoneNumber,
                            'customer_response' => $Respond,
                            'bot_reply' =>  'Slot Booked',
                            'data_sent_to_customer' => 'Slot Booked',
                            'last_reply_time' => date('Y-m-d H:i:s'),
                            'steps' => 1
                        ];
                        WhatsApp::create($dataArr);
                        // $this->FlushEntries($userPhoneNumber);
                        return false;
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Catch any database exceptions
                        $errorCode = $e->errorInfo[1];

                        // Check if the error is due to a unique constraint violation on 'slot_id'
                        if ($errorCode === 1062) { // MySQL error code for duplicate entry
                            // Produce a custom error message for slot_id not being unique
                            // $errorMessage = trans('messages.slot_id');
                            $errorMessageEnglish = trans('messages.slot_id', [], 'en');
                            $errorMessageUrdu = trans('messages.slot_id', [], 'ur');
                            $message = $this->WhatsAppNewWarning($errorMessageEnglish, $errorMessageUrdu);
                            $this->sendMessage($userPhoneNumber, $message);
                            $dataArr = [
                                'lang' => 'en',
                                'dua_option' => $dua_option,
                                'customer_number' => $userPhoneNumber,
                                'customer_response' => $Respond,
                                'bot_reply' =>  $message,
                                'data_sent_to_customer' =>  json_encode([]),
                                'last_reply_time' => date('Y-m-d H:i:s'),
                                'steps' => 1,
                                'response_options' => null
                            ];
                            WhatsApp::create($dataArr);
                            // $this->FlushEntries($userPhoneNumber);

                            return false;
                            // Now you can handle this error message accordingly, perhaps by returning it or using it in your response.
                        } else {
                            // Handle other types of database exceptions if needed
                            // You can log the exception or return a generic error message
                            $errorMessage = 'An error occurred while processing your request.';
                        }
                    }
                } else {

                    $errorMessageEnglish = trans('messages.all_token_booked', [], 'en');
                    $errorMessageUrdu = trans('messages.all_token_booked', [], 'ur');
                    $message = $this->WhatsAppNewWarning($errorMessageEnglish, $errorMessageUrdu);
                    $this->sendMessage($userPhoneNumber, $message);
                    $dataArr = [
                        'lang' => 'en',
                        'dua_option' => $dua_option,
                        'customer_number' => $userPhoneNumber,
                        'customer_response' => $Respond,
                        'bot_reply' =>  $message,
                        'data_sent_to_customer' =>  json_encode([]),
                        'last_reply_time' => date('Y-m-d H:i:s'),
                        'steps' => 1,
                        'response_options' => null
                    ];
                    WhatsApp::create($dataArr);
                    // $this->FlushEntries($userPhoneNumber);

                    return false;
                }
            }
        }
    }



    public function deleteRecordAfter30($existingCustomer)
    {
        $createdAt = Carbon::parse($existingCustomer->created_at);
        $currentTime = Carbon::now();
        $differenceInMinutes = $createdAt->diffInMinutes($currentTime);

        if ($differenceInMinutes > 10) {
            $existingCustomer->delete();
        }
    }

    public function handleWebhook1(Request $request)
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

        if (!empty($existingCustomer)) {
            $this->deleteRecordAfter30($existingCustomer);
        }

        // $user = Vistors::where('phone', $cleanNumber)->first();




        $dataArr = [];
        $countryId = Venue::where(['iso' => 'PK'])->get()->first();



        // https://emojipedia.org/keycap-digit-five
        // $whatsAppEmoji = [
        //     '1' => '1️⃣',
        //     '2' => '2️⃣',
        //     '3' => '3️⃣',
        //     '4' => '4️⃣',
        //     '5' => '5️⃣'
        // ];

        $whatsAppEmoji = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5'
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
            $this->FlushEntries($userPhoneNumber);

            return false;
        }

        if (empty($existingCustomer)) {

            // Choose Language
            $step = 0;
            $data = '';
            $message = $this->WhatsAppbotMessages($data, $step);
            $this->sendMessage($userPhoneNumber, $message);

            $options = ['1', '2'];
            $data = [
                '1' => trim($whatsAppEmoji['1'] . ' English'),
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
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && ($existingCustomer->steps == 0)) {
            // Welcome  Message
            $step = $existingCustomer->steps + 1;
            $customer_response = $Respond;

            $lang = ($Respond == 1) ? 'eng' : 'urdu';
            $message = $this->WhatsAppbotMessages('', $step, $lang);
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
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && ($existingCustomer->steps == 1)) {
            // Welcome  Dua Type
            $step = $existingCustomer->steps + 1;
            $customer_response = $Respond;
            $lang =  $existingCustomer->lang;
            $options = ['1', '2'];

            $data = [
                '1' => trim($whatsAppEmoji['1'] . ' Dua'),
                '2' => trim($whatsAppEmoji['2'] . ' Dum')
            ];


            $message = $this->WhatsAppbotMessages('', $step, $lang);
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
        } else if (!empty($existingCustomer) && in_array($responseString, $responseAccept) && $existingCustomer->steps == 2) {
            //   City
            $step = $existingCustomer->steps + 1;
            $lang =  $existingCustomer->lang;
            $dua_option = ($Respond == 1) ? 'dua' : 'dum';
            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('venue_date', '>=', date('Y-m-d'))
                ->take(3)
                ->get();

            if (empty($venuesListArr)) {
                $message = $this->WhatsAppbotMessages('', 9, $lang);
                $this->sendMessage($userPhoneNumber, $message);
            }

            if ($dua_option == 'dua' && !empty($venuesListArr->reject_dua_id)) {
                $reason  = Reason::find($venuesListArr->reject_dua_id);
                $data = ($lang == 'eng') ?  $reason->reason_english :  $reason->reason_urdu;
                $message = $this->WhatsAppbotMessages($data, 9, $lang);

                $dataArr = [
                    'lang' => $lang,
                    'dua_option' => $dua_option,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' =>  $data,
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                $this->FlushEntries($userPhoneNumber);

                return false;
            }
            if ($dua_option == 'dum' && !empty($venuesListArr->reject_dum_id)) {
                $reason  = Reason::find($venuesListArr->reject_dua_id);
                $data = ($lang == 'eng') ?  $reason->reason_english :  $reason->reason_urdu;
                $message = $this->WhatsAppbotMessages($data, 9, $lang);
                $dataArr = [
                    'lang' => $lang,
                    'dua_option' => $dua_option,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' =>  $data,
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                $this->FlushEntries($userPhoneNumber);

                return false;
            }

            $cityArr = [];

            $distinctCities = $venuesListArr->pluck('city')->unique();



            $i = 1;
            foreach ($distinctCities as $key => $city) {

                if ($lang == 'urdu') {
                    $cityName =  $this->cityArrWithUrdu($city);
                } else {
                    $cityName = $city;
                }


                $cityArr[$city] = trim($whatsAppEmoji[$i] . ' ' . $cityName);

                $options[$i] =  $i;
                $i++;
            }
            asort($cityArr);
            asort($options);


            if (empty($cityArr)) {
                $data = ($lang == 'eng') ? 'No dua/dum appointment is available at this time. Please try again later.' :  'اس وقت کوئی دعا/دم ملاقات دستیاب نہیں ہے۔ براہ کرم کچھ دیر بعد کوشش کریں.';
                $message = $this->WhatsAppbotMessages($data, 9, $lang);
            } else {
                $data = implode("\n", $cityArr);
                $message = $this->WhatsAppbotMessages($data, $step, $lang);
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

            $today = getCurrentContryTimezone($countryId->id);

            $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                ->where('city',  $cityName[0])
                ->whereDate('venue_date', $today)
                ->orderBy('venue_date', 'ASC')
                ->first();

            $rejoin = $venuesListArr->rejoin_venue_after;

            $waId = $request->input('WaId');
            $countryCode = $this->findCountryByPhoneNumber($waId);
            $cleanNumber = str_replace($countryCode, '', $waId);

            $rejoinStatus = userAllowedRejoin($cleanNumber, $rejoin);

            if (!$rejoinStatus['allowed']) {


                $data = ($lang == 'eng') ? $rejoinStatus['message'] : $rejoinStatus['message_ur'];

                //   $data = ($lang =='eng') ? 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You':  'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ';
                $message = $this->WhatsAppbotMessages($data, 9, $lang);
                $this->sendMessage($userPhoneNumber, $message);

                $dataArr = [
                    'lang' => $lang,
                    'dua_option' => $dua_option,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => $message,
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                $this->FlushEntries($userPhoneNumber);
                return false;
            }



            if (!empty($venuesListArr) &&  $venuesListArr->status == 'inactive') {

                $data = ($lang == 'eng') ? 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You' :  'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ';
                $message = $this->WhatsAppbotMessages($data, 9, $lang);
                $this->sendMessage($userPhoneNumber, $message);

                $dataArr = [
                    'lang' => $lang,
                    'dua_option' => $dua_option,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => $message,
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                $this->FlushEntries($userPhoneNumber);
                return false;
            }

            if (!empty($venuesListArr)) {

                $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $venuesListArr->timezone);

                $venue_available_country =  json_decode($venuesListArr->venue_available_country);
                $waId = $request->input('WaId');

                $countryCode = $this->findCountryByPhoneNumber($waId);
                $cleanNumber = str_replace($countryCode, '', $waId);

                $country = Country::where('phonecode', str_replace('+', '', $countryCode))->first();


                $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);

                if (!$userCountry['allowed']) {

                    $data = ($lang == 'eng') ? $userCountry['message'] :  $userCountry['message_ur'];
                    $message = $this->WhatsAppbotMessages($data, 9, $lang);
                    $this->sendMessage($userPhoneNumber, $message);

                    $dataArr = [
                        'lang' => $lang,
                        'dua_option' => $dua_option,
                        'customer_number' => $userPhoneNumber,
                        'customer_response' => $Respond,
                        'bot_reply' =>  $message,
                        'data_sent_to_customer' => $message,
                        'last_reply_time' => date('Y-m-d H:i:s'),
                        'steps' => $step,
                        'response_options' => null
                    ];
                    WhatsApp::create($dataArr);
                    $this->FlushEntries($userPhoneNumber);
                    return false;
                }

                //  $status = isAllowedTokenBooking($venuesListArr->venue_date, $venuesListArr->slot_appear_hours , $venuesListArr->timezone);
                if ($status['allowed']) {

                    $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                        ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                        ->where(['type' => $dua_option])
                        ->orderBy('id', 'ASC')
                        ->select(['venue_address_id', 'token_id', 'id', 'type'])->first();

                    if ($tokenIs) {


                        $data_sent_to_customer = json_decode($existingCustomer->data_sent_to_customer, true);
                        // $slotId = $this->findKeyByValueInArray($data_sent_to_customer, $Respond);
                        $slotId = $tokenIs->id;
                        $duaType = $tokenIs->type;

                        $venueAddress = $tokenIs->venueAddress;


                        $rejoin = $venueAddress->rejoin_venue_after;
                        // Rejoin Stat


                        // $rejoinStatus = userAllowedRejoin($cleanNumber, $rejoin);
                        // if(!$rejoinStatus['allowed']){
                        //     $data = ($lang =='eng') ? $rejoinStatus['message'] :  $rejoinStatus['message_ur'];
                        //     $message = $this->WhatsAppbotMessages($data, 9 , $lang);
                        //     $this->sendMessage($userPhoneNumber, $message);

                        //     $dataArr = [
                        //         'lang' => $lang,
                        //         'dua_option' => $dua_option,
                        //         'customer_number' => $userPhoneNumber,
                        //         'customer_response' => $Respond,
                        //         'bot_reply' =>  $message,
                        //         'data_sent_to_customer' => $message,
                        //         'last_reply_time' => date('Y-m-d H:i:s'),
                        //         'steps' => $step,
                        //         'response_options' => null
                        //     ];
                        //     WhatsApp::create($dataArr);
                        //     return false;
                        // }


                        // $tokenId = $venueSlots->token_id;
                        $tokenId = str_pad($tokenIs->token_id, 2, '0', STR_PAD_LEFT);
                        $tokenType = $tokenIs->type;
                        $cleanedNumber = str_replace('whatsapp:', '', $userPhoneNumber);
                        $venue = $venueAddress->venue;
                        $result = $this->formatWhatsAppNumber($cleanedNumber);
                        $userMobile = $result['mobileNumber'];
                        $timestamp = strtotime($tokenIs->slot_time);
                        $slotTime = date('h:i A', $timestamp) . '(' . $venueAddress->timezone . ')';
                        $uuid = Str::uuid()->toString();

                        $token  = $tokenId . ' (' . ucwords($tokenType) . ')';

                        $venueDate = ($lang == 'eng') ? date("d M Y", strtotime($venueAddress->venue_date)) : date("d m Y", strtotime($venueAddress->venue_date));
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

                        // $statusNote = ($lang == 'eng') ? $venueAddress->status_page_note : $venueAddress->status_page_note_ur;
                        $statusNote  = '';
                        $venueAdrress = ($lang == 'en') ? $venueAddress->address : $venueAddress->address_ur;

                        $statusLink = route('booking.status', $uuid);

                        $pdfLink = '';
                        $duaby = '';

                        if ($lang == 'eng') {
                            $pdfLink = 'Subscribe to Syed Sarfraz Ahmad Shah Official YouTube Channel  https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1';


                            $message = <<<EOT

                        Your Dua Appointment Confirmed $duaby ✅

                        Event Date : $venueDate

                        Venue : $venueAddress->city

                        $venueAddress->address

                        Token #$token

                        Your Mobile : $userMobile

                        Appointment Duration : $appointmentDuration

                        $statusNote

                        To view your token online please click below:

                        $statusLink

                        $pdfLink

                        EOT;
                        } else {
                            $pdfLink = 'سید سرفراز احمد شاہ آفیشل یوٹیوب چینل کو سبسکرائب کریں https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1';

                            $message = <<<EOT

                        آپ کی دعا ملاقات کی تصدیق ہوگئ ہے سید سرفراز احمد شاہ صاحب کے ساتھ $duaby ✅

                        تاریخ : $venueDate

                        دعا گھر : $venueAddress->city

                        $venueAddress->address_ur

                        ٹوکن #$tokenId

                        آپ کا موبائل : $userMobile

                        ملاقات کا دورانیہ : $appointmentDuration

                        $statusNote

                        اپنا ٹوکن آن لائن دیکھنے کے لیے براہ کرم نیچے کلک کریں:

                        $statusLink

                        $pdfLink

                        EOT;
                        }




                        // $message = <<<EOT
                        //     Your Dua Appointment Confirmed With $duaBy ✅

                        //     Event Date : $venueDate

                        //     Venue : $venueAddress->city

                        //     $venueAddress->address

                        //     Token #$tokenId

                        //     Your Mobile : $userMobile

                        //     Your Appointment Time : $slotTime

                        //     Appointment Duration : $appointmentDuration

                        //     $venueAddress->status_page_note
                        //     To view your token online please click below:

                        //     $statusLink

                        //     $pdfLink

                        //     EOT;
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

                        // $this->sendMessage($userPhoneNumber, $subScription);
                    } else {

                        $data = ($lang == 'eng') ? 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.' : 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔';
                        $message = $this->WhatsAppbotMessages($data, 9, $lang);
                        $this->sendMessage($userPhoneNumber, $message);
                    }
                } else {
                    $data = ($lang == 'eng') ? $status['message'] :  $status['message_ur'];
                    $message = $this->WhatsAppbotMessages($data, 9, $lang);
                    $this->sendMessage($userPhoneNumber, $message);
                }
            } else {

                $data = ($lang == 'eng') ?  'There is no Dua / Dum token booking available for today. Please try again later.' :  'آج کے لیے کوئی دعا/دم ٹوکن بکنگ دستیاب نہیں ہے۔ براہ کرم کچھ دیر بعد کوشش کریں.';
                $message = $this->WhatsAppbotMessages($data, 9, $lang);
                $this->sendMessage($userPhoneNumber, $message);

                $dataArr = [
                    'lang' => $lang,
                    'dua_option' => $dua_option,
                    'customer_number' => $userPhoneNumber,
                    'customer_response' => $Respond,
                    'bot_reply' =>  $message,
                    'data_sent_to_customer' => $message,
                    'last_reply_time' => date('Y-m-d H:i:s'),
                    'steps' => $step,
                    'response_options' => null
                ];
                WhatsApp::create($dataArr);
                $this->FlushEntries($userPhoneNumber);
                return false;

                // $data = ($lang =='eng') ? 'There is no venue for the Selected Date.' : 'There is no venue for the Selected Date.';
                // $message = $this->WhatsAppbotMessages($data, 9 , $lang);
                // $this->sendMessage($userPhoneNumber, $message);



            }
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


    private function WhatsAppbotMessagesNew($dataEn, $dataUr)
    {

        $message = <<<EOT
            KahayFaqeer.org دعا اپائنٹمنٹ شیڈولر میں خوش آمدید۔
            $dataUr
            Welcome to the KahayFaqeer.org Dua Appointment Scheduler.
            $dataEn
            EOT;


        return $message;
    }

    private function WhatsAppNewWarning($dataEn, $dataUr)
    {
        $message = <<<EOT
        Please see the below warning message:
        $dataEn
        براہ کرم ذیل میں انتباہی پیغام دیکھیں:
        $dataUr
        EOT;
        return $message;
    }


    private function WhatsAppbotMessages($data, $step, $lang = '')
    {
        $message = '';

        if ($step == 0 && $lang == '') {

            $message = <<<EOT
            Please enter your language?
            1 English
            2 Urdu
            EOT;
        }
        if ($step == 1) {

            if ($lang == 'eng') {

                $message = <<<EOT
                Welcome to the KahayFaqeer.org Dua Appointment Scheduler.

                Please note online or phone dua meeting is not possible at this time.

                To schedule a dua meeting with Qibla Syed Sarfraz Ahmad Shah Sahab please enter 1

                EOT;
            } else {

                $message = <<<EOT
                و KahayFaqeer.org دعا اپائنٹمنٹ شیڈولر میں خوش آمدید۔

                براہ مہربانی نوٹ کریں کہ اس وقت آن لائن یا فون پر دعا ممکن نہیں ہے۔

                قبلہ سید سرفراز احمد شاہ صاحب سے دعا ملاقات کا وقت طے کرنے کے لیے براہ مہربانی 1 درج کریں۔
                EOT;
            }
        } else if ($step == 2) {
            if ($lang == 'eng') {
                $message = <<<EOT
                Please enter your type of dua?
                1 Dua
                2 Dum
                EOT;
            } else {
                $message = <<<EOT
                براہ کرم اپنی دعا کی قسم منتخب کریں؟
                1 دعا
                2 دم
                EOT;
            }
        } else if ($step == 3) {
            if ($lang == 'eng') {
                $message = <<<EOT
                Please enter the number for your city
                $data
                EOT;
            } else {
                $message = <<<EOT
                براہ کرم اپنے شہر کا نمبر درج کریں۔
                $data
                EOT;
            }
        } else if ($step == 9) {
            if ($lang == 'eng') {
                $message = <<<EOT
                Please see the below warning message:
                $data
                EOT;
            } else {
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

    private function cityArrWithUrdu($city)
    {
        if ($city == 'Lahore') {
            $name = 'لاہور';
        } else  if ($city == 'Islamabad') {
            $name = 'اسلام آباد';
        } else  if ($city == 'Karachi') {
            $name = 'کراچی';
        }
        return $name;
    }
}
