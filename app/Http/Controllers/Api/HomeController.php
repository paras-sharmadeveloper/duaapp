<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, Country, User, Notification, Timezone, Ipinformation, VenueStateCity, VisitorTempEntry, WhatsappNotificationLogs};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{WhatsAppConfirmation , SendInstantWhatsapp};
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Reason;
use Twilio\Rest\Client;
use App\Models\WorkingLady;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    use OtpTrait;
    public function __construct()
    {
        //  $this->middleware('auth');
    }

    //

    public function WhatsAppNotifications(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'whatsAppMessage' => 'required',
                'user_mobile.*' => 'required'
            ]);

            $userMobile = $request->input('user_mobile');
            $token_template = $request->input('token_template');
            $dataMessage = '';
            $userId = [];
            foreach ($userMobile as $id => $phone) {
                $userId[] = $id;
            }

            if (isset($token_template)) {
            }

            $visitors = Vistors::whereIn('id', $userId)->get(['id', 'booking_uniqueid', 'dua_type', 'created_at', 'phone', 'country_code', 'booking_number', 'slot_id']);
            $messhhhs = [];
            foreach ($visitors as $visitor) {

                $vennueAdd = $visitor->slot->venueAddress;

                $currentMessage  = $request->input('whatsAppMessage');
                $mobile          =  $visitor->country_code .  $visitor->phone;
                $currentMessage0 = str_replace('{token_url}', route('booking.status', [$visitor->booking_uniqueid]), $currentMessage);
                $currentMessage1 = str_replace('{dua_type}', $visitor->dua_type, $currentMessage0);
                $currentMessage2 = str_replace('{date}', date('d M Y', strtotime($visitor->created_at)), $currentMessage1);
                $currentMessage3 = str_replace('{mobile}', $mobile, $currentMessage2);
                $currentMessage4 = str_replace('{city}', $vennueAdd->city, $currentMessage3);
                $finalMessage    = str_replace('{token_number}', $visitor->booking_number, $currentMessage4);

                if (isset($token_template)) {
                    $message = <<<EOT
            $finalMessage
            EOT;
                } else {
                    $message = <<<EOT
                General Announcement and Notification: Please Read Carefully:
                $finalMessage
                EOT;
                }


                $response = $this->sendMessage($mobile, $message);

                WhatsappNotificationLogs::create([
                    'venue_date' => $request->input('pick_venue_date'),
                    'dua_type' => $request->input('dua_type'),
                    'whatsAppMessage' => $message,
                    'mobile' => $mobile,
                    'msg_sid' => $response['sid']
                ]);
            }
            return response()->json(['success' => true]);
        }
        $logs = WhatsappNotificationLogs::orderBy('id', 'desc')->get();

        return view('whatsappNotifications.index', compact('logs'));

        // return view('whatsappNotifications.index');
    }


    public function StatusLcdScreen(Request $request)
    {
        $today = date('Y-m-d');
        $venueAddress = VenueAddress::where('venue_date', 'LIKE', "%{$today}%")->get();
        $distinctCities = $venueAddress->pluck('city')->unique();
        $getDates = $venueAddress->whereIn('city', $distinctCities)->pluck('id', 'city');

        if ($request->ajax()) {

            $city = $request->input('city');
            $venueAddress = VenueAddress::where(['city' => $city])->where('venue_date', 'LIKE', "%{$today}%")->select('id', 'venue_date')->get();;
            return response()->json($venueAddress);
        }
        return view('frontend.lcd-status', compact('venueAddress', 'distinctCities', 'getDates'));
    }

    public function bookingAdmin($id)
    {
        $slots = VenueSloting::where(['venue_address_id' => $id])
            ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
            ->orderBy('slot_time', 'ASC')
            ->get();
        $countries = Country::all();
        $venueAddress = VenueAddress::find($id);
        return view('admin-booking', compact('id', 'slots', 'countries', 'venueAddress'));
    }

    public function index(Request $request, $locale = '')
    {
        if (!isMobileDevice($request)) {
            return abort('403');
        }
        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }

        // You can store the chosen locale in the user's preferences if needed

        $therapistRole = Role::where('name', 'therapist')->first();
        $VenueList = Venue::all();
        $countryList = Country::all();
        $therapists = $therapistRole->users;
        $timezones = Country::with('timezones')->get();

        $reasons = Reason::where(['type' => 'announcement'])->first();
        return view('frontend.bookseat', compact('VenueList', 'countryList', 'therapists', 'timezones', 'locale', 'reasons'));
    }


    public function deleteVisitorShow()
    {
        $visitors = Vistors::whereNotNull(['recognized_code'])->get();
        return view('workingLady.visitorsdelete', compact('visitors'));
    }

    public function deleteVisitor(Request $request, $id)
    {

        $is = deleteObject($id);
        if ($is) {
            Vistors::where(['recognized_code' => $id])->delete();
            return redirect()->back()->with(['success' => "Object Cleared"]);
        } else {
            return redirect()->back()->with(['error' => "Failed to delete object"]);
        }
    }



    // public function BookingSubmit(Request $request)
    // {
    //     $start = microtime(true);
    //     $from = $request->input('from', 'null');
    //     $vaildation = [];


    //     if ($from == 'admin' && $request->input('dua_type') == 'dua') {

    //         $vaildation =  [
    //             'mobile' => 'required|string|digits:10|max:10',
    //             'user_question' => 'nullable|string',
    //             'country_code' => 'required',
    //         ];
    //     } else {

    //         $vaildation = [
    //             'mobile' => 'required|string|digits:10|max:10',
    //             'user_question' => 'nullable|string',
    //             'country_code' => 'required'
    //         ];
    //     }
    //     $messages = [];


    //     // $validator = Validator::make($request->all(), $validation, $messages);

    //     $validatedData = $request->validate($vaildation, $messages);
    //     // if ($validator->fails()) {
    //     //     return response()->json(['errors' => $validator->errors()], 422);
    //     // }
    //     $tokenStatus = $this->FinalBookingCheck($request);

    //     // echo "<pre>"; print_r($tokenStatus); die;
    //     if ($tokenStatus['status']) {
    //         $slotId = $tokenStatus['slot_id'];
    //         $tokenId = $tokenStatus['tokenId'];
    //         $venueAddress  = $tokenStatus['venuesListArr'];
    //     } else {
    //         return response()->json([
    //             'errors' =>  $tokenStatus
    //         ], 422);
    //     }

    //     try {
    //         $isUsers = [];
    //         $recognizedCode = null;

    //         $tokenId = str_pad($tokenId, 2, '0', STR_PAD_LEFT);
    //         $source = "Website";
    //         $rejoin = $venueAddress->rejoin_venue_after;
    //         $rejoin = $venueAddress->rejoin_venue_after;
    //         $rejoinStatus = userAllowedRejoin($validatedData['mobile'], $rejoin);
    //         if (!$rejoinStatus['allowed'] &&  $from != 'admin') {
    //             $source = "Website";
    //             $message = ($request->input('lang') == 'en') ? $rejoinStatus['message'] : $rejoinStatus['message_ur'];
    //             return response()->json(['message' => $message, "status" => false], 406);
    //         } else if (!$rejoinStatus['allowed'] && $from == 'admin') {
    //             $source = "Website";
    //             return redirect()->back()->withErrors(['error' => 'You already booked a seat before']);
    //         }

    //         $captured_user_image = $request->input('captured_user_image');
    //         if (!empty($captured_user_image)) {
    //             $myImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_user_image));
    //             // $isUsers = $this->IsRegistredAlready($myImage, $rejoin);
    //             // if (!empty($isUsers) && $isUsers['status'] == false) {
    //             //     $end = microtime(true);
    //             //     $totalTime = $end - $start;
    //             //     $recognizedCode = $isUsers['recognized_code'];
    //             //     return response()->json(['message' => $isUsers['message'],  'totalTime' => $totalTime, 'isUser' => $isUsers, "status" => false], 406);
    //             // }
    //         }
    //         $uuid = Str::uuid()->toString();
    //         $countryCode = $request->input('country_code');
    //         $country = Country::where(['phonecode' => $countryCode])->first();
    //         $venue_available_country =  json_decode($venueAddress->venue_available_country);
    //         $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);
    //         if (!$userCountry['allowed']) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $userCountry['message'],
    //                 'message_ur' => $userCountry['message_ur'],
    //             ]);
    //         }

    //         $bookingNumber =  $tokenId;
    //         // $mobile = $countryCode . $validatedData['mobile'];
    //         $booking = new Vistors;

    //         $booking->country_code = '+' . $countryCode;
    //         $booking->phone = $validatedData['mobile'];
    //         $booking->user_question =  $request->input('user_question', null);
    //         $booking->slot_id =  $slotId;
    //         $booking->is_whatsapp = $request->has('is_whatsapp') ? 'yes' : 'no';
    //         $booking->booking_uniqueid = $uuid;
    //         $booking->user_ip =   $request->ip();
    //         $booking->recognized_code = (!empty($isUsers)) ?  $isUsers['recognized_code'] : $recognizedCode ;
    //         $booking->booking_number = $bookingNumber;
    //         $booking->meeting_type = $venueAddress->type;
    //         $booking->user_timezone = $request->input('timezone', null);
    //         $booking->source = $source;
    //         $booking->dua_type = $request->input('dua_type');
    //         $booking->lang = $request->input('lang', 'en');
    //         $booking->working_lady_id = $request->input('working_lady_id', 0);

    //         $workingLady = WorkingLady::where('qr_id', $request->input('QrCodeId'))->where('is_active', 'active')->count();


    //         if ($workingLady == 0 && !empty($request->input('working_lady_id'))) {
    //             return response()->json([
    //                 'errors' => [
    //                     'status' => false,
    //                     'message' => 'This Qr is not valid or not active',
    //                     'message_ur' => 'یہ Qr درست نہیں ہے یا فعال نہیں ہے۔',
    //                 ]
    //             ], 422);
    //         }

    //         $booking->token_status = 'vaild';
    //         // Save the booking record
    //         $booking->save();
    //         $bookingId = $booking->id;

    //         // WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification')->onConnection('database');
    //         WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');

    //         if ($from == 'admin') {
    //             return  redirect()->route('booking.status', $uuid);
    //             // booking.status
    //             // return redirect()->back()->with('success', 'Booking created successfully');
    //         } else {

    //             $end = microtime(true);
    //             $totalTime = $end - $start;

    //             // WhatsAppConfirmation::dispatch($booking->id)->onConnection('database')->onQueue('whatsapp-send');
    //             return response()->json([
    //                 'message' => 'Booking submitted successfully',
    //                 "totalTime" => $totalTime,
    //                 "status" => true, 'bookingId' => $uuid,
    //                 'redirect_url' => route('booking.status', [$uuid]) . '?time=' . $totalTime
    //             ], 200);
    //         }
    //     } catch (QueryException $e) {
    //         Log::error('Booking error' . $e);

    //         $errorCode = $e->errorInfo[1];

    //         if ($errorCode === 1062) { // Error code for Duplicate Entry
    //             return response()->json([
    //                 'status' => false,
    //                 'refresh' => true,
    //                 'message' => trans('messages.slot_id'),
    //             ], 422);
    //         } else {
    //             // Handle other types of database errors
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $e->getMessage(),
    //             ], 500);
    //         }

    //         // WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification-send-er')->onConnection('database');

    //     } catch (\Exception $e) {
    //         // Log any other exceptions
    //         Log::error('Exception: ' . $e->getMessage());

    //         // Return a generic error response
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }



    public function checkTokens(){

    }
    public function BookingSubmitManual(Request $request)
    {


        $start = microtime(true);
        $from = $request->input('from', 'null');
        $vaildation = [];
        $vaildation = [
            'mobile' => 'required|string|digits:10|max:10',
            'user_question' => 'nullable|string',
            'country_code' => 'required'
        ];

        $messages = [];
        $validatedData = $request->validate($vaildation, $messages);
        $tokenStatus = $this->FinalBookingCheck($request);
        // echo "<pre>"; print_r($tokenStatus); die;
        if ($tokenStatus['status']) {
            // $slotId = $tokenStatus['slot_id'];
            // $tokenId = $tokenStatus['tokenId'];
            $venueAddress  = $tokenStatus['venuesListArr'];
        } else {
            return response()->json([
                'errors' =>  $tokenStatus
            ], 422);
        }

        $query = Vistors::whereDate('created_at', date('Y-m-d'));
        $DuaCount = $query->where(['dua_type' =>$request->input('duaType')])->count();

        $count =  VenueSloting::where(['venue_address_id' => $venueAddress->id,'type' => $request->input('duaType') ])->count();



        if($count == $DuaCount && $request->input('duaType') == 'dua'){

            return response()->json([
                'errors' =>  [
                    'status' =>  false,
                    'message' => 'All Tokens Dua Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                    'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                ]
            ], 422);

        }
        if($count == $DuaCount && $request->input('duaType') == 'dum'){

            return response()->json([
                'errors' =>  [
                    'status' =>  false,
                    'message' => 'All Tokens Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                    'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                ]
            ], 422);


        }
        if($count == $DuaCount && $request->input('duaType') =='working_lady_dua'){

            return response()->json([
                'errors' => [
                    'status' =>  false,
                    'message' => 'All Tokens Working Lady Dua  Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                    'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                ]
            ], 422);

        }
        if($DuaCount == $count && $request->input('duaType') =='working_lady_dum'){

            return response()->json([
                'errors' => [
                    'status' =>  false,
                    'message' => 'All Tokens Working Lady Dum  Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                    'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                ]
            ], 422);
        }


        $isPerson = VisitorTempEntry::where(['phone' => $validatedData['mobile']])->whereDate('created_at',date('Y-m-d'))->count();
            if( $isPerson > 0){

                return response()->json([
                    'errors' =>  [
                        'status' =>  false,
                        'message' => "You have already submitted your entry for token booking earlier today. Our system is processing all entries at this time. If system approve your token then it will send token details to your WhatsApp. Kindly don't make further new entries and wait for the next 2 hours.",
                        'message_ur' => "آپ نے آج پہلے ہی ٹوکن بکنگ کے لیے اپنا اندراج جمع کرایا ہے۔ ہمارا سسٹم اس وقت تمام اندراجات پر کارروائی کر رہا ہے۔ اگر سسٹم آپ کے ٹوکن کو منظور کرتا ہے تو یہ آپ کے واٹس ایپ پر ٹوکن کی تفصیلات بھیجے گا۔ برائے مہربانی مزید نئی اندراجات نہ کریں اور اگلے 2 گھنٹے انتظار کریں۔",
                    ]
                ], 422);


            }


        try {
            $isUsers = [];
            $recognizedCode = null;

            // $tokenId = str_pad($tokenId, 2, '0', STR_PAD_LEFT);
            $source = "Website";
            $rejoin = $venueAddress->rejoin_venue_after;
            $rejoin = $venueAddress->rejoin_venue_after;
            $rejoinStatus = userAllowedRejoin($validatedData['mobile'], $rejoin);
            if (!$rejoinStatus['allowed'] &&  $from != 'admin') {
                $source = "Website";
                $message = ($request->input('lang') == 'en') ? $rejoinStatus['message'] : $rejoinStatus['message_ur'];
                return response()->json(['message' => $message, "status" => false], 406);
            } else if (!$rejoinStatus['allowed'] && $from == 'admin') {
                $source = "Website";
                return redirect()->back()->withErrors(['error' => 'You already booked a seat before']);
            }

            $captured_user_image = $request->input('captured_user_image');
            if (!empty($captured_user_image)) {
                $myImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_user_image));
                // $isUsers = $this->IsRegistredAlready($myImage, $rejoin);
                // if (!empty($isUsers) && $isUsers['status'] == false) {
                //     $end = microtime(true);
                //     $totalTime = $end - $start;
                //     $recognizedCode = $isUsers['recognized_code'];
                //     return response()->json(['message' => $isUsers['message'],  'totalTime' => $totalTime, 'isUser' => $isUsers, "status" => false], 406);
                // }
            }
            $filename = 'UserImage_' . time() . '.jpg';
            $objectKey = $this->encryptFilename($filename);
            // Storage::disk('s3')->put($objectKey, $myImage);

            // Upload to local directory with today's date folder
            $localDirectory = 'sessionImages/' . date('d-m-Y');
            if (!Storage::disk('public_uploads')->exists($localDirectory)) {
                Storage::disk('public_uploads')->makeDirectory($localDirectory);
            }
            Storage::disk('public_uploads')->put($localDirectory . '/' . $objectKey, $myImage);

            try {
                //code...
                $uploadSuccess = Storage::disk('s3')->put($objectKey, $myImage);
            } catch (\Exception $e) {
                // Log::error('Failed to upload file to S3.'.$e->getMessage());
                return response()->json([
                    'errors' =>  ['message' => 'Unable to upload file ' . $objectKey . '   ' . $e->getMessage() . ' ']
                ], 422);
            }


            $uuid = Str::uuid()->toString();
            $countryCode = $request->input('country_code');
            $country = Country::where(['phonecode' => $countryCode])->first();
            $venue_available_country =  json_decode($venueAddress->venue_available_country);
            $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);
            if (!$userCountry['allowed']) {
                return response()->json([
                    'status' => false,
                    'message' => $userCountry['message'],
                    'message_ur' => $userCountry['message_ur'],
                ]);
            }

            // $bookingNumber =  $tokenId;
            // $mobile = $countryCode . $validatedData['mobile'];

            $booking = new VisitorTempEntry;

            $booking->country_code = '+' . $countryCode;
            $booking->venueId = $venueAddress->id;

            $booking->phone = $validatedData['mobile'];
            $booking->user_ip =   $request->ip();
            $booking->recognized_code = (!empty($isUsers)) ?  $isUsers['recognized_code'] : $recognizedCode ;
            $booking->user_timezone = $request->input('timezone', null);
            $booking->source = $source;
            $booking->dua_type = $request->input('dua_type');
            $booking->lang = $request->input('lang', 'en');
            $booking->working_lady_id = ( $request->input('working_lady_id')) ? $request->input('working_lady_id', 0) : 0;
            $booking->working_qr_id = $request->input('QrCodeId');
            $booking->recognized_code =$objectKey;

            $booking->save();
            $bookingId = $booking->id;
            $completeNumber = '+' . $countryCode.$validatedData['mobile'];
            $message = "Thank you for your entry submission for Dua/Dum token. Kindly note that our system is processing all entries now on first come first serve basis one by one. Please don't make another submission and we kindly request you to please wait for few minutes while our system process all entries. We will send you an another update in few minutes with the status of your token if its issued or not.";
            SendInstantWhatsapp::dispatch($bookingId,$completeNumber,$message)->onQueue('whatsapp-instant-notification');

                return response()->json([
                    'message' => 'Booking submitted successfully',
                    "status" => true,
                    'bookingId' => $uuid,
                    'redirect_url' => route('thankyounew')
                ], 200);


        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];

            if ($errorCode === 1062) { // Error code for Duplicate Entry
                return response()->json([
                    'status' => false,
                    'refresh' => true,
                    'message' => trans('messages.slot_id'),
                ], 422);
            } else {
                // Handle other types of database errors
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    private function sendWhatsAppMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        try {
            $messageInstance =  $twilio->messages->create(
                "$to",
                [
                    'from' => "whatsapp:" . env('TWILIO_PHONE_WHATSAPP'),
                    'body' => $message,
                    "statusCallback" => route('twillio.status.callback')
                ]
            );
            $messageSid = $messageInstance->sid; // Get MessageSid
            $messageSentStatus = $messageInstance->status; // Get MessageSentStatus
            return [
                'data' => 'success',
                'sid' => $messageSid,
                'status' => $messageSentStatus
            ];
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function CheckAvilableSolt(Request $request)
    {
        $id = $request->input('id');
        if (Vistors::where('slot_id', $id)->exists()) {
            return response()->json(['message' => 'occupied', "status" => false], 422);
        } else {
            return response()->json(['message' => 'slot available', "status" => true], 200);
        }
    }

    protected function IsRegistredAlready($selfieImage, $rejoin)
    {
        return false;
        $filename = 'selfie_' . time() . '.jpg';
        $objectKey = $this->encryptFilename($filename);
        sleep(5);
        $userAll = Vistors::whereDate('created_at', date('Y-m-d'))->get(['recognized_code', 'id'])->toArray();
        // $userAll = Vistors::get(['recognized_code', 'id'])->toArray();
        $userArr = [];
        $count = 0;
        Storage::disk('s3')->put($objectKey, $selfieImage);


        $localDirectory = 'sessionImages/' . date('d-m-Y');
        if (!Storage::disk('public_uploads')->exists($localDirectory)) {
            Storage::disk('public_uploads')->makeDirectory($localDirectory);
        }
        Storage::disk('public_uploads')->put($localDirectory . '/' . $objectKey, $selfieImage);



        if (!empty($userAll) &&  $rejoin > 0) {

            try {

                $awsDefaultRegion = (env('AWS_DEFAULT_REGION')) ? env('AWS_DEFAULT_REGION') : 'us-east-1';
                $awsAccessKeyId = (env('AWS_ACCESS_KEY_ID')) ? env('AWS_ACCESS_KEY_ID') : 'AKIAWTTVS7OFB7GJU4AF';
                $awsSecretAcessKey = (env('AWS_SECRET_ACCESS_KEY')) ? env('AWS_SECRET_ACCESS_KEY') : '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';


                $rekognition = new RekognitionClient([
                    'version' => 'latest',
                    'region' => $awsDefaultRegion,
                    'credentials' => [
                        'key' => $awsAccessKeyId,
                        'secret' => $awsSecretAcessKey,
                    ],
                ]);
                $targetImages = [];
                $bucket = 'kahayfaqeer-booking-bucket';
                foreach ($userAll as $user) {
                    if (!empty($user['recognized_code'])) {
                        $targetImages[] = [
                            'S3Object' => [
                                'Bucket' => $bucket,
                                'Name' => $user['recognized_code'],
                            ],
                        ];
                    }
                }
                $response = $rekognition->compareFaces([
                    'SimilarityThreshold' => 90,
                    'SourceImage' => [
                        'S3Object' => [
                            'Bucket' => $bucket,
                            'Name' => $objectKey,
                        ],
                    ],
                    'TargetImage' => [
                        'S3Object' => [
                            'Bucket' => $bucket,
                            'Name' =>  $targetImages[0]['S3Object']['Name'],
                        ],
                    ],
                    'TargetFaces' => $targetImages,
                ]);

                $faceMatches = (!empty($response)) ? $response['FaceMatches'] : [];
                foreach ($faceMatches as $match) {
                    if ($match['Similarity'] >= 80) {
                        $userArr[] = $user['id'];
                    }
                }

                $count = (!empty($userAll)) ? count($userAll)  : 0;

                if (empty($userArr)) {
                    return ['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $objectKey, 'count' => $count];
                } else {
                    return ['recognized_code' => $objectKey, 'message' => 'Your token cannot be booked at this time. Please try again later.', 'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم کچھ دیر بعد کوشش کریں', 'status' => false, 'count' => $count];
                }
            } catch (\Exception $e) {
                Log::info("aws" . $e->getMessage());

                // return ['message' => 'We are encounter some error at application side please report this to admin. Or try after some time.',   'status' => false , 'recognized_code' => $objectKey];
                return ['message' => $e->getMessage(), 'status' => false , 'recognized_code' => $objectKey];
            }
        } else {
            Storage::disk('s3')->put($objectKey, $selfieImage);
            return ['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $objectKey];
        }
    }



    public function detectLiveness(Request $request)
    {
        $imageData = $request->input('image');
        $selfieImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

        $rekognition = new RekognitionClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // Detect faces in the image
        $result = $rekognition->detectFaces([
            'Image' => [
                'Bytes' => $selfieImage,
            ],
            'Attributes' => ['ALL'],
        ]);

        // Check for liveness by analyzing the result
        if (count($result['FaceDetails']) > 0) {
            // At least one face detected
            foreach ($result['FaceDetails'] as $face) {
                if ($face['Quality']['Sharpness'] >= 80) {
                    // Face is sharp, indicating a live person
                    return response()->json(['message' => 'Liveness detected.', 'status' => true], 200);
                }
            }
        }

        // No live faces detected
        return response()->json(['message' => 'Liveness not detected.', 'status' => false], 400);
    }



    protected function encryptFilename($filename)
    {
        $key = hash('sha256',uniqid(). date('Y-m-d H:i:s') . $filename . now()->toDateTimeString().uniqid());
        return  Str::uuid()->toString().$key;
    }


    public function bookingConfirmation(Request $request, $id)
    {
        $userBooking = Vistors::where('booking_uniqueid', $id)->first();
        if (!$userBooking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        // Get the user's slot time
        $userSlot = VenueSloting::find($userBooking->slot_id);
        $userSlotTime = $userSlot->slot_time;  // Assuming 'time' is the column where you store the slot time
        $venueAddress = VenueAddress::find($userSlot->venue_address_id);
        // Calculate the start of slots
        $startTime = $venueAddress->slot_starts_at;

        // Count bookings from the start time until the user's slot time
        $aheadPeople = Vistors::whereHas('venueSloting', function ($query) use ($startTime, $userSlotTime) {
            $query->where('slot_time', '>=', $startTime)
                ->where('slot_time', '<', $userSlotTime);
        })->count();

        $serveredPeople = Vistors::whereNotNull('meeting_doneAt')->get()->count();

        return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople', 'userBooking'));
    }

    public function thankyouPage($id)
    {
        $userBooking = Vistors::where('booking_uniqueid', $id)->first();
        return view('frontend.thankyou', compact('userBooking'));
    }


    public function home()
    {
        $visitos = Vistors::get()->count();
        $therapist = Role::where('name', 'therapist')->first();
        $siteadmin = Role::where('name', 'site-admin')->first();
        $userCountWiththripistRole = $therapist->users->count();
        $userCountWithsiteadminRole = $siteadmin->users->count();
        return view('home', compact('visitos', 'userCountWiththripistRole', 'userCountWithsiteadminRole'));
    }

    public function getTimzoneAjax(Request $request)
    {
        //  $venueAddress = VenueAddress::find($id);
        $id = $request->input('id');
        $timezone = $request->input('timezone');
        $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        $venueAddress =  VenueAddress::where('id', $id)
            // ->where(function ($query) use ($newDate) {
            //   $query->whereDate('venue_date', $newDate)
            //     ->orWhereDate('venue_date', date('Y-m-d'));
            // })
            ->first();
        // echo "<pre>"; print_r($venueAddress); die;

        $mytime = Carbon::now()->tz($timezone);
        $eventDate = Carbon::parse($venueAddress->venue_date, $timezone);
        $hoursRemaining = $eventDate->diffInHours($mytime);

        $slotsAppearAfter = intval($venueAddress->slot_appear_hours);

        // $currentTime = strtotime($mytime->addHour(24)->format('Y-m-d H:i:s'));
        // $evntTime = date('Y-m-d H:i:s',strtotime($venueAddress->venue_date .' '. $venueAddress->slot_starts_at));
        // $EventStartTime = strtotime($evntTime);
        $slotsArr = [];
        $slotsAppearAfter = intval($venueAddress->slot_appear_hours);
        $isVisiable = false;
        if ($slotsAppearAfter == 0) {
            $isVisiable = true;
        } else if ($hoursRemaining <= $slotsAppearAfter) {
            $isVisiable = true;
        }
        if ($isVisiable) {
            $slotArr = VenueSloting::where('venue_address_id', $id)
                ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                ->orderBy('slot_time', 'ASC')
                ->get(['venue_address_id', 'slot_time', 'id']);

            $slotsDataArr = [];

            $iso =  $venueAddress->venue->iso;
            $venueTimezone = Timezone::where(['country_code' => $iso])->first();
            $countryTz =  $venueTimezone->timezone;

            foreach ($slotArr as $k => $myslot) {


                $venueDate = $venueAddress->venue_date . ' ' . $myslot->slot_time;
                $carbonSlot = Carbon::parse($venueDate, $countryTz); // IST timezone
                $carbonSlot->timezone($timezone);

                // $carbonSlot = Carbon::parse($venueDate, 'Asia/Kolkata'); // IST timezone
                // $carbonSlot->setTimezone($timezone);
                $slotsDataArr[$k] = $myslot;
                // $convertedTimeSlots[] = $carbonSlot->toDateTimeString();

                $slotsDataArr[$k]['slot_time'] = $carbonSlot->format('H:i:s');

                // $slotsDataArr[$k]['slot_time'] = $carbonSlot->setTimezone($timezone)->format('H:i:s');
            }
            return response()->json([
                'status' => true,
                'message' => 'Slots are be avilable',
                'slots' =>  $slotsDataArr,
                'timezone' => $timezone,
                'app' => App::environment('production')
            ]);
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' =>  'Dua meeting tokens will be available only ' . $slotsAppearAfter . '  hours before the dua starts. Please try again later',
                    'slots' => [],
                    'app' => App::environment('production')

                ]
            );
        }
    }


    public function getTheripistByIp(Request $request)
    {

        $dataArr = [];

        if (App::environment('production')) {
            $ip = $request->header('X-Forwarded-For');
            if ($ip) {
                $ipArray = explode(',', $ip);
                $ip = trim($ipArray[0]); // Take the first IP address
            } else {
                $ip = $request->ip();
            }
            // $userDetail = $this->getIpDetails($ip);
            $ipInfo = Ipinformation::where(['user_ip' => $ip])->get()->first();
            if (!empty($ipInfo)) {
                $userDetail = json_decode($ipInfo['complete_data'], true);
            } else {
                $userDetail = $this->getIpDetails($ip);
            }
            $phoneCode = (isset($userDetail['phoneCode'])) ? $userDetail['phoneCode'] : '91';
        } else {
            $userDetail['countryCode'] = 'IN';
            $userDetail['countryName'] = 'India';
            $phoneCode = '91';
        }

        session(['phoneCode' => $phoneCode]);
        //    echo "<pre>"; print_r($userDetail); die(env('IP_API_KEY'));
        $countryCode = $userDetail['countryCode'];

        $countryName = ucwords($userDetail['countryName']);
        $countryId = Country::where(['nicename' => $countryName])->first();

        $venueAddress = VenueAddress::get();
        $timezone = Timezone::where(['country_code' => $countryCode])->get()->first();
        $currentTimezone = $timezone->timezone;

        if (!empty($venueAddress)) {
            foreach ($venueAddress as $venueAdd) {

                $thripist = $venueAdd->thripist;
                $venue_available_country =  json_decode($venueAdd->venue_available_country);

                if (is_array($venue_available_country) &&  in_array($countryId->id, $venue_available_country)) {
                    $dataArr[] = [
                        'id' => $thripist->id,
                        'name' => $thripist->name,
                        'profile_pic' => $thripist->profile_pic,
                        'currentTimezone' =>  $currentTimezone,
                        'type' => 'recommended',
                        'venueaddId' => $venueAdd->id,
                        'venue_available_country' => $venue_available_country

                    ];
                }
            }
        }
        $newArr = [];
        $existingIds = [];
        foreach ($dataArr  as $data) {
            if (isset($data['id']) && !in_array($data['id'], $existingIds)) {
                $newArr[] = $data;
                $existingIds[] = $data['id'];
            }
        }
        return response()->json([
            'status' => !(empty($newArr)) ? true : false,
            'data' => $newArr,
            'currentTimezone' => $currentTimezone,
            'countryCode' => $countryCode,
            'phoneCode' => $phoneCode
        ]);
    }



    public function getAjax(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $today = Carbon::now();
        $NextDate = $today->addDay();
        $newDate = $NextDate->format('Y-m-d');

        //  $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        //$newDate = date('Y-m-d');
        $newDate15Day = date('Y-m-d', strtotime(date('Y-m-d') . ' +15 day'));
        if ($type == 'venue_address') {
            $venuesListArr = VenueAddress::where(['venue_id' => $id])->get()->all();
            $dataArr = [];
            foreach ($venuesListArr as $venuesList) {
                $dataArr[] = [
                    'imgUrl' => env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
                    'address' => $venuesList->address,
                    'slot_start' => Carbon::createFromFormat('H:i:s', $venuesList->slot_starts_at)->format('H:i A'),
                    'slot_ends' => Carbon::createFromFormat('H:i:s', $venuesList->slot_ends_at)->format('H:i A'),
                    'venue_address_id' => $venuesList->id,
                    'venue_date' => $venuesList->venue_date
                ];
            }

            return response()->json([
                'status' => !(empty($dataArr)) ? true : false,
                'data' => $dataArr
            ]);
        }

        // if($type == 'working_lady'){
        //     return response()->json([
        //         'status' => true,
        //         'data' => []
        //     ]);
        // }

        if ($type == 'get_type') {
            // return ['today' => $newDate, 'wde' => $newDate15Day];
            if (App::environment('production')) {
                $addRess = VenueAddress::where('therapist_id', $id)
                    ->where(function ($query) use ($newDate, $newDate15Day) {
                        $query->whereDate('venue_date', '>=', $newDate)
                            ->whereDate('venue_date', '<=', $newDate15Day);
                    })
                    ->orderBy('venue_date', 'asc')
                    ->get();
            } else {
                $addRess = VenueAddress::where('therapist_id', $id)
                    ->where(function ($query) use ($newDate) {
                        $query->whereDate('venue_date', $newDate)
                            ->orWhereDate('venue_date', date('Y-m-d'));
                    })
                    ->orderBy('venue_date', 'asc')
                    ->get();
            }



            $dataArr = [];
            foreach ($addRess as $venuesList) {
                $eventDate = Carbon::parse($venuesList->venue_date . ' ' . $venuesList->slot_starts_at);
                $dataArr['type'][] = [
                    'name' => $venuesList->type,
                    'flag_path' =>  env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
                    'venue_address_id' => $venuesList->id,
                    // 'day_left' =>  Carbon::now()->diffInDays($eventDate)
                    'day_left' =>  $eventDate->format('d-M-Y')

                ];
            }
            return response()->json([
                'status' => !(empty($dataArr)) ? true : false,
                'data' => $dataArr
            ]);
        }
        if ($type == 'get_country') {


            $venuesListArr = VenueAddress::where('therapist_id', $id)
                ->where(function ($query) use ($newDate) {
                    $query->where('venue_date', '>=', $newDate) // Use '>=' instead of '>'
                        ->orWhereDate('venue_date', '=', now()->format('Y-m-d')); // Use now() instead of date()
                })
                ->where('venue_date', '>=', now()->format('Y-m-d'))

                ->get();
            $dataArr = [];
            foreach ($venuesListArr as $venuesList) {
                $countryName = $venuesList->venue->country_name;
                $flagPath = $venuesList->venue->flag_path;
                $venueId = $venuesList->venue_id;
                if (!isset($dataArr['country'][$venueId])) {
                    $dataArr['country'][$venueId] = [
                        'name' => $countryName,
                        'flag_path' =>  env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
                        'type' => $venuesList->type,
                        'id' => $venuesList->id,
                        'venue_id' =>  $venueId
                    ];
                }
            }
            if (!(empty($dataArr))) {
                $dataArr['country'] =   array_filter($dataArr['country']);
            }



            return response()->json([
                'status' => !(empty($dataArr)) ? true : false,
                'data' => $dataArr,
                'date' => $NextDate->format('Y-m-d H:i:s A'),
                'co' => $today->format('Y-m-d H:i:s A')
            ]);
        }

        if ($type == 'get_city' || $type == 'normal_person' || $type == 'working_lady') {

            $countryId = Venue::where(['iso' => 'PK'])->get()->first();
            if (empty($countryId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'There is no Venue in the system. Please try after some time',
                    'message_ur' => 'سسٹم میں کوئی وینیو نہیں ہے۔ تھوڑی دیر بعد کوشش کریں۔',

                ]);
            } else {
                $venuesListArr = VenueAddress::where('venue_id', $countryId->id)
                    ->where(function ($query) use ($newDate) {
                        $query->where('venue_date', '>=', $newDate) // Use '>=' instead of '>'
                            ->orWhereDate('venue_date', '=', now()->format('Y-m-d')); // Use now() instead of date()
                    })
                    ->where('venue_date', '>=', now()->format('Y-m-d'))
                    ->get();
            }


            if (empty($venuesListArr)) {
                return response()->json([
                    'status' => false,
                    'data' => []
                ]);
            }

            // $venuesListArr = VenueAddress::where('id', $id)
            //   ->where(function ($query) use ($newDate) {
            //     $query->whereDate('venue_date', $newDate)
            //       ->orWhereDate('venue_date', date('Y-m-d'));
            //   })
            //   ->get();
            $dataArr = [];
            foreach ($venuesListArr as $venuesList) {
                $cityName = $venuesList->city;
                $flagPath = $venuesList->venue->flag_path;
                //  $cityFlag = $venuesList->combinationData->city_image;
                // $seq = $venuesList->combinationData->city_sequence_to_show;
                if (!isset($dataArr['city'][$cityName])) {
                    $dataArr['city'][$cityName] = [
                        'name' => $cityName,
                        // 'flag_path' => ($cityFlag) ?   env('AWS_GENERAL_PATH') . 'city_image/' . $cityFlag :  env('AWS_GENERAL_PATH') . 'flags/' .  $flagPath,
                        'id' => $venuesList->venue->id,
                        'type' => $venuesList->type,
                        'venue_address_id' => $venuesList->id,
                        // 'seq' =>   $seq
                    ];
                }
            }
            // $dataArr['country'] = array_unique($dataArr['country'], SORT_REGULAR);


            return response()->json([
                'status' => !(empty($dataArr)) ? true : false,
                'data' => $dataArr,
                'date' => $newDate
            ]);
        }
        if ($type == 'get_date') {

            $venuesListArr = VenueAddress::where('venue_id', $request->input('id'))
                ->where('city',  $request->input('optional'))
                ->where('venue_date', '=', date('Y-m-d'))
                ->orderBy('venue_date', 'ASC')
                ->get();

            if (!empty($venuesListArr)) {
                return response()->json([
                    'status' =>  true,
                ]);
            } else {
                return response()->json([
                    'status' => false
                ]);
            }

            // $venuesListArr = VenueAddress::where([
            //   'venue_id' => $id, 'city' => $request->input('optional')
            // ]) ->where(function ($query) use ($newDate) {
            //     $query->where('venue_date', '>=', Carbon::now()->format('Y-m-d')) // Use '>=' instead of '>'
            //       ->orWhereDate('venue_date', '=', now()->format('Y-m-d')); // Use now() instead of date()
            //   })
            //   ->where('venue_date', '>', now()->format('Y-m-d'))
            //   ->get();


            $dataArr = [];
            foreach ($venuesListArr as $venuesList) {
                $venue_date = $venuesList->venue_date;
                $flagPath = $venuesList->venue->flag_path;
                $cityFlag = $venuesList->combinationData->city_image;
                $columnToShow = $venuesList->combinationData->columns_to_show;
                $venueStartTime = Carbon::parse($venuesList->venue_date . ' ' . $venuesList->slot_starts_at_morning);


                $dataArr['columnToShow'] =  $columnToShow;
                if (Carbon::now() <= $venueStartTime) {
                    $dataArr['date'][] = [
                        'venue_date' => $venue_date,
                        'type' => $venuesList->type,
                        'flag_path' => ($cityFlag) ?   env('AWS_GENERAL_PATH') . 'city_image/' . $cityFlag :  env('AWS_GENERAL_PATH') . 'flags/' . $flagPath,
                        'id' => $venuesList->venue->id,
                        'venue_address_id' => $venuesList->id
                    ];
                }
            }
            return response()->json([
                'status' => !(empty($dataArr)) ? true : false,
                'data' => $dataArr
            ]);
        }


        if ($type == 'get_slot_book') {

            // return date('Y-m-d');
            $currentTimezone = $request->input('timezone', 'America/New_York');
            // $duaType = $request->input('duaType');
            // $selectionType = $request->input('selection_type');
            // $duaType = $request->input('duaType');
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
            $today = getCurrentContryTimezone($request->input('id'));
            $venuesListArr = VenueAddress::where('venue_id', $request->input('id'))
                ->where('city',  operator: $request->input('optional'))
                ->whereDate('venue_date', $today)
                ->orderBy('venue_date', 'asc')
                ->first();

            if ($venuesListArr) {

                $city = $venuesListArr->city;
                $timeZoneD ='';


                if($city == 'London'){
                    $timeZoneD = 'Europe/London';
                }else{
                    $timeZoneD = $venuesListArr->timezone;
                }


                $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $timeZoneD);
                if (!$status['allowed']) {
                    return response()->json([
                        'status' => false,
                        'message' => $status['message'],
                        'message_ur' => $status['message_ur'],
                        'city' => $city

                    ]);
                }
                $phoneCode = session('phoneCode');
                $country = Country::where('phonecode', $phoneCode)->first();
                $venue_available_country =  json_decode($venuesListArr->venue_available_country);
                $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);

                if (!$userCountry['allowed']) {
                    session()->forget('phoneCode');


                    return response()->json([
                        'status' => false,
                        'message' => $userCountry['message'],
                        'message_ur' => $userCountry['message_ur'],
                        'phoneCode' => $phoneCode

                    ]);
                }
            }

            if (!empty($venuesListArr) && $venuesListArr->status == 'inactive') {
                return response()->json([
                    'status' => false,
                    'message' => 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You',
                    'message_ur' => 'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ',

                ]);
            }

            return response()->json([
                'city' => $request->input('optional'),
                'timezone' => $request->input('timezone'),
                'duaType' => $request->input('duaType'),
                'venueId' =>  $venuesListArr->id,
                'status' => true

            ]);
        }



        if ($type == 'get_slots') {
            $currentTimezone = $request->input('timezone', 'America/New_York');
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
            //  $venueAddress = VenueAddress::find($id);
            $venueAddress = VenueAddress::where('id', $id)
                ->where(function ($query) use ($newDate) {
                    $query->where('venue_date', '>=', $newDate) // Use '>=' instead of '>'
                        ->orWhereDate('venue_date', '=', now()->format('Y-m-d')); // Use now() instead of date()
                })
                ->where('venue_date', '>=', now()->format('Y-m-d'))
                ->get()->first();




            $isVisiable = false;
            if (!empty($venueAddress)) {


                $mytime = Carbon::now()->tz($currentTimezone);
                $countryTimeZone = $venueAddress->timezone;
                $eventDate = Carbon::parse($venueAddress->venue_date, $countryTimeZone);
                $hoursRemaining = $eventDate->diffInHours($mytime);

                $slotsAppearAfter = intval($venueAddress->slot_appear_hours);

                if ($slotsAppearAfter == 0) {
                    $isVisiable = true;
                } else if ($hoursRemaining <= $slotsAppearAfter) {
                    $isVisiable = true;
                }
            } else {

                return response()->json([
                    'status' => false,
                    'message' => 'Slots not Found',
                    'timezone' => $currentTimezone,
                    'app' => App::environment('production'),
                    // 'selfie' => ($venueAddress->selfie_verification == 1) ? true : false,
                    'slots' => [],
                ]);
            }



            $slotsArr = [];
            if ($isVisiable) {


                $slotArr = VenueSloting::where('venue_address_id', $id)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->orderBy('slot_time', 'ASC')
                    ->get(['venue_address_id', 'slot_time', 'id']);

                $slotsDataArr = [];
                $iso =  $venueAddress->venue->iso;
                $venueTimezone = Timezone::where(['country_code' => $iso])->first();
                $countryTz =  $venueTimezone->timezone;

                foreach ($slotArr as $k => $myslot) {
                    $venueDate = $venueAddress->venue_date . ' ' . $myslot->slot_time;

                    $carbonSlot = Carbon::parse($venueDate, $countryTz); // IST timezone
                    $carbonSlot->timezone($currentTimezone);
                    // $carbonSlot->setTimezone($currentTimezone);
                    $slotsDataArr[$k] = $myslot;
                    // $convertedTimeSlots[] = $carbonSlot->toDateTimeString();
                    $slotsDataArr[$k]['slot_time'] = $carbonSlot->format('H:i:s');
                    // $slotsDataArr[$k]['slot_time'] = $carbonSlot->setTimezone($currentTimezone)->format('H:i:s');
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Slots are be available',
                    'slots' =>  $slotsDataArr,
                    'timezone' => $currentTimezone,
                    // 'selfie' => ($venueAddress->selfie_verification == 1) ? true : false,
                    'app' => App::environment('production')
                ]);
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Dua meeting tokens will be available only ' . $slotsAppearAfter . '  hours before the dua starts. Please try again later',
                        'slots' => [],
                        'app' => App::environment('production'),
                        'hoursRemaining' => $hoursRemaining

                    ]
                );
            }
        }
    }

    public function SendOtpUser(Request $request)
    {

        // $request->validate([
        //   'mobile' => 'required|string|max:255|unique:vistors,phone',
        //   'country_code' => 'required'
        // ]);

        $request->validate([
            'email' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            // 'email' => 'required|string|max:255|unique:vistors,email',
            // 'mobile' => 'required|string|max:255|unique:vistors,phone',
            'country_code' => 'required'
        ]);

        $country = $request->input('country_code');
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $userDetail['country_code'] = $country;
        $userDetail['mobile'] = $request->input('mobile');
        $userDetail['email'] =  $request->input('email');
        $isMobile = true;
        $isEmail = true;
        // $this->SendOtp($mobile,$country,$isMobile=true,$isEmail = false);
        $result =  $this->SendOtp($userDetail, $isMobile, $isEmail);

        if ($result['status']) {
            return response()->json(['message' => 'Please check your email for OTP. If not received please check spam folder.', 'status' => true]);
            // return response()->json(['message' => 'OTP Sent successfully', 'status' => true]);
        } else {
            return response()->json(['message' => 'OTP failed to sent', 'status' => false]);
        }
    }

    public function verify(Request $request)
    {
        $userEnteredOTP = $request->input('otp'); // OTP entered by the user
        $result = $this->VerifyOtp($userEnteredOTP);
        // return $result;
        if ($result['status']) {
            return response()->json(['message' => 'OTP verified successfully', 'status' => true]);
        } else {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }
    }

    public function destroy($id)
    {
        Vistors::find($id)->delete();
        return redirect()->route('venues.index')->with('success', 'Venue deleted successfully');
    }
    public function getIpDetails($userIp)
    {

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://apiip.net/api/check?ip=' . $userIp . '&accessKey=' . env('IP_API_KEY'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            $sect = $_SERVER;
            $result = json_decode($response, true);

            curl_close($curl);

            $data = [
                'user_ip' => $userIp,
                'countryName' => (isset($result['countryName'])) ? $result['countryName'] : null,
                'regionName' => (isset($result['regionName'])) ? $result['regionName'] : null,
                'city' => (isset($result['city'])) ? $result['city'] : null,
                'postalCode' => (isset($result['postalCode'])) ? $result['postalCode'] : null,
                'complete_data' => $response,

            ];

            Ipinformation::create($data);
            return $result;
        } catch (\Exception $th) {
            //throw $th;
            return $th->getMessage();
        }
    }

    public  function deleteRows(Request $request)
    {
        $post = $request->all();
        $query = DB::table($post['table_name']);
        $query->whereIn('id', $post['idsToDelete'])->delete();
        return ['success' => 1, 'message' => 'deleted'];
    }

    public  function getVisitors(Request $request)
    {
        $request->validate([
            'dua_option' => 'required',
            'venueDate' => 'required'
        ]);
        $venueDateRange = explode(' - ', $request->input('venueDate'));
     //   $startDate = Carbon::createFromFormat('m/d/Y', $venueDateRange[0])->startOfDay();
      // $endDate = Carbon::createFromFormat('m/d/Y', $venueDateRange[1])->endOfDay();

        // Parse the start and end dates from the array and format them as Y-m-d
        $startDate = Carbon::createFromFormat('m/d/Y', $venueDateRange[0])->format('Y-m-d');
        $endDate = Carbon::createFromFormat('m/d/Y', $venueDateRange[1])->format('Y-m-d');


        if($request->input('dua_option') == 'All'){
            $visitors = Vistors::
            whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'booking_uniqueid', 'dua_type', 'created_at', 'phone', 'country_code']);
        }else{
            $visitors = Vistors::where('dua_type', $request->input('dua_option'))
                ->whereBetween('created_at', [$startDate, $endDate])

            ->get(['id', 'booking_uniqueid', 'dua_type', 'created_at', 'phone', 'country_code']);
        }


        // $visitors = Vistors::where(['dua_type' => $request->input('dua_option')])
        //     ->whereBetween('created_at', [$startDate, $endDate])
        //     ->select(['id', 'booking_uniqueid', 'dua_type', 'created_at', 'phone', 'country_code'])
        //     ->unique('phone')
        //     ->get();
        return response()->json(['success' => (!$visitors->IsEmpty()) ? true : false, 'data' => $visitors], 200);
    }

    public  function SendWhatsAppNotifications(Request $request)
    {

        $request->validate([
            'dua_option' => 'required',
            'venueDate' => 'required'
        ]);
    }


    public  function getSlotsAjax(Request $request)
    {

        $slotArr = VenueSloting::where(['venue_address_id' =>  $request->input('venueId'), 'type' => $request->dua_option])
            ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
            ->orderBy('id', 'ASC')
            ->get(['venue_address_id', 'token_id', 'id']);

        return response()->json(['success' => true, 'data' => $slotArr, 'venueId' =>  $request->input('venueId')], 200);
    }
    private function bookingMessageTemplate($name, $therapistName, $location, $bookingNumber, $venueString, $slot_duration, $rescheduleBooking, $cancelBooking, $confirmSpot, $appointMentStatus)
    {


        $message = <<<EOT
      Hi $name,
      Your dua appointment is confirmed as below:

      Token :
      #$bookingNumber

      Sahib-e-Dua:
      $therapistName

      Appointment duration:
      $slot_duration Minutes

      Venue:
      $venueString

      Venue location:
      $location

      Your appointment status link:
      $appointMentStatus

      In case you want to reschedule your appointment, please click below:
      $rescheduleBooking

      If you want to only cancel your appointment, please click below:
      $cancelBooking

      For your convenience, please visit only 15 mins before your appointment.

      KahayFaqeer.org
      EOT;


        return $message;
    }


    private function whatsAppConfirmationTemplate($venueAddress, $uuid, $tokenId, $userMobile, $duaType, $lang)
    {

        $venueDate = ($lang == 'en') ? date("d M Y", strtotime($venueAddress->venue_date)) : date("d m Y", strtotime($venueAddress->venue_date));

        $appointmentDuration = $venueAddress->slot_duration . ' minute 1 Question';

        $venueDateEn = date("d M Y", strtotime($venueAddress->venue_date));
        $venueDateUr =  date("d m Y", strtotime($venueAddress->venue_date));

        // $statusNote = ($lang == 'en') ? $venueAddress->status_page_note : $venueAddress->status_page_note_ur;
        $statusNote = '';

        $statusLink = route('booking.status', $uuid);

        $pdfLink = '';
        $duaby = '';

        $message  = <<<EOT
        Asalamualaikum,
        Please see below confirmation for your dua token.

        Your Dua Ghar : $venueAddress->city
        Your Dua Date : $venueDateEn
        Your Online Dua Token : $statusLink
        Your Token Number :  $tokenId
        Your Dua Type : $duaType
        Your registered mobile: $userMobile

        Please reach by 1pm to validate and print your token.

        Read and listen all books for free. Please visit KahayFaqeer.org
        EOT;
        return $message;
    }

    public function FinalBookingCheck($request)
    {

        $duaType = $request->input('duaType');
        $country_code = $request->input('country_code');
        $today = getCurrentContryTimezone($request->input('venueId'));
        $venuesListArr = VenueAddress::where('id', $request->input('venueId'))
            ->where('city',  $request->input('city'))
            ->whereDate('venue_date', $today)
            ->orderBy('venue_date', 'asc')
            ->first();

        if ($duaType == 'dua' && !empty($venuesListArr->reject_dua_id)) {
            $reason  = Reason::find($venuesListArr->reject_dua_id);
            return [
                'status' => false,
                'message' => $reason->reason_english,
                'message_ur' => $reason->reason_urdu,
            ];
        }
        if ($duaType == 'dum' && !empty($venuesListArr->reject_dum_id)) {
            $reason  = Reason::find($venuesListArr->reject_dum_id);
            return [
                'status' => false,
                'message' => $reason->reason_english,
                'message_ur' => $reason->reason_urdu,
            ];
        }
        if (!empty($venuesListArr) && $venuesListArr->status == 'inactive') {
            return [
                'status' => false,
                'message' => 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You',
                'message_ur' => 'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ',

            ];
        }

        if ($venuesListArr) {

            $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $venuesListArr->timezone);
            $phoneCode = (session('phoneCode')) ? session('phoneCode') : $country_code;
            $phoneCode = session('phoneCode');
            $country = Country::where('phonecode', $phoneCode)->first();
            $venue_available_country =  json_decode($venuesListArr->venue_available_country);
            $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);

            if (!$userCountry['allowed']) {
                session()->forget('phoneCode');


                return [
                    'status' => false,
                    'message' => $userCountry['message'],
                    'message_ur' => $userCountry['message_ur'],
                    'phoneCode' => $phoneCode

                ];
            }

            $status = isAllowedTokenBooking($venuesListArr->venue_date, $venuesListArr->slot_appear_hours, $venuesListArr->timezone);

            if ($status['allowed']) {

                // session()->forget('phoneCode');


                // $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                //     ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                //     ->where(['type' => $request->input('duaType')])
                //     ->orderBy('id', 'ASC')
                //     ->select(['venue_address_id', 'token_id', 'id'])->first();

                if (!empty($tokenIs)) {
                    return [
                        'status' =>  true,
                        // 'tokenId' => $tokenIs->token_id,
                        // 'slot_id' => $tokenIs->id,
                        'venuesListArr' => $venuesListArr
                    ];
                } else {
                    return [
                        'status' =>  true,
                        // 'tokenId' => $tokenIs->token_id,
                        // 'slot_id' => $tokenIs->id,
                        'venuesListArr' => $venuesListArr
                    ];
                    // return  [
                    //     'status' =>  false,
                    //     'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                    //     'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                    // ];
                }
            } else {

                return  [
                    'status' => false,
                    'message' => $status['message'],
                    'message_ur' => $status['message_ur'],

                ];
            }
        } else {
            return [
                'status' =>  false,
                'message' => 'There is no Dua / Dum token booking available for today. Please try again later.',
                'message_ur' => 'آج کے لیے کوئی دعا/دم ٹوکن بکنگ دستیاب نہیں ہے۔ براہ کرم کچھ دیر بعد کوشش کریں.',

            ];
        }
    }

    public function sendMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        try {
            $messageInstance = $twilio->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:" . env('TWILIO_PHONE_WHATSAPP'),
                    'body' => $message,
                    "statusCallback" => route('twillio.status.callback.notification')
                ]
            );
            $messageSid = $messageInstance->sid; // Get MessageSid
            $messageSentStatus = $messageInstance->status; // Get MessageSentStatus
            return [
                'data' => 'success',
                'sid' => $messageSid,
                'status' => $messageSentStatus
            ];
        } catch (\Exception $e) {
            //throw $th;
            return [
                'data' => $e->getMessage(),
                'sid' => '',
                'status' => ''
            ];
        }
    }
}
