<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, Country, User, Notification, Timezone, Ipinformation, VenueStateCity};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{SendMessage, SendEmail, PushEmailToSandlane};
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Log;
use App\Events\BookingNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Reason;
use Twilio\Rest\Client;

class HomeController extends Controller
{
    use OtpTrait;
    public function __construct()
    {
        //  $this->middleware('auth');
    }

    public function WhatsAppNotificationsDDD(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'whatsAppMessage' => 'required',
                'user_mobile' => 'required'
            ]);
            $userMobile = $request->input('user_mobile');
            $dataMessage = $request->input('whatsAppMessage');

            foreach ($userMobile as $id => $phone) {

                // $visitor = Vistors::where(['id' => $id])->get(['id','booking_uniqueid' ,'dua_type' ,'created_at','phone','country_code'])->first();

                  $visitor = Vistors::find($id,['id','booking_uniqueid' ,'dua_type' ,'created_at','phone','country_code']);
                // return response()->json(['success' => true, 'message' => $visitors]);

                    $dataMessage = str_replace('{token_url}', route('booking.status', [$visitor->booking_uniqueid]), $dataMessage);
                    $dataMessage= str_replace('{dua_type}', $visitor->dua_type, $dataMessage);
                    $dataMessage= str_replace('{date}', date('d M Y', strtotime($visitor->created_at)), $dataMessage);
                    $dataMessage= str_replace('{mobile}', $visitor->phone, $dataMessage);
                    $dataMessage= str_replace('{id}', $visitor->id, $dataMessage);
                    $mobile =  $visitor->country_code .  $visitor->phone;
                    $message = <<<EOT
                        Please see below urgent message for your kind attention:
                        $dataMessage
                        EOT;
                    $response =   $this->sendMessage($mobile, $message);


                // $message = <<<EOT
                // Please see below urgent message for your kind attention:
                // $dataMessage
                // EOT;
                // $response =   $this->sendMessage($phone, $message);
            }




            return response()->json(['success' => true, 'message' => $response]);
        }

        return view('whatsappNotifications.index');
    }

    public function WhatsAppNotifications(Request $request)
{
    if ($request->ajax()) {
        $request->validate([
            'whatsAppMessage' => 'required',
            'user_mobile.*' => 'required'
        ]);

        $userMobile = $request->input('user_mobile');
        $token_template = $request->input('token_template');
        $dataMessage ='';
        $userId = [];
        foreach ($userMobile as $id => $phone) {
            $userId[] = $id;
        }

        if(isset($token_template)){

        }

       $visitors = Vistors::whereIn('id',$userId)->get(['id','booking_uniqueid' ,'dua_type' ,'created_at','phone','country_code','booking_number','slot_id']);
        $messhhhs = [];
        foreach($visitors as $visitor){

            $vennueAdd = $visitor->slot->venueAddress;

            $currentMessage  = $request->input('whatsAppMessage');
            $mobile          =  $visitor->country_code .  $visitor->phone;
            $currentMessage0 = str_replace('{token_url}', route('booking.status', [$visitor->booking_uniqueid]), $currentMessage);
            $currentMessage1 = str_replace('{dua_type}', $visitor->dua_type, $currentMessage0);
            $currentMessage2 = str_replace('{date}', date('d M Y', strtotime($visitor->created_at)), $currentMessage1);
            $currentMessage3 = str_replace('{mobile}',$mobile, $currentMessage2);
            $currentMessage4 = str_replace('{city}',$vennueAdd->city, $currentMessage3);
            $finalMessage    = str_replace('{token_number}', $visitor->booking_number, $currentMessage4);

            if(isset($token_template)){
            $message = <<<EOT
            $finalMessage
            EOT;
            }else{
                $message = <<<EOT
                Please see below urgent message for your kind attention :
                $finalMessage
                EOT;
            }

            $this->sendMessage($mobile, $message);

        }




        return response()->json(['success' => true]);
    }

    return view('whatsappNotifications.index');
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

    public function indexTest(Request $request, $locale = '')
    {
        // if (!isMobileDevice($request)) {
        //     return abort('403');
        // }

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
        return view('frontend.bookseat-test', compact('VenueList', 'countryList', 'therapists', 'timezones', 'locale', 'reasons'));
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


    public function deleteVisitorShow(){
        $visitors = Vistors::whereNotNull(['recognized_code'])->get();
        return view('workingLady.visitorsdelete', compact('visitors'));

    }

    public function deleteVisitor(Request $request, $id ){

        $is = deleteObject($id);
        if($is){
            Vistors::where(['recognized_code' => $id])->delete();
            return redirect()->back()->with(['success' => "Object Cleared"]);
        }else{
            return redirect()->back()->with(['error' => "Failed to delete object"]);
        }
    }

    public function BookingSubmit(Request $request)
    {
        $from = $request->input('from', 'null');
        $vaildation = [];
        if ($from == 'admin') {

            $vaildation =  [
                // 'fname' => 'required|string|max:255',
                //  'lname' => 'required|string|max:255',
                // 'email' => 'required|email|max:255',

                'mobile' => 'required|string|max:255',
                'user_question' => 'nullable|string',
                'country_code' => 'required',
                'slot_id' => 'required|numeric|unique:visitors,slot_id'
            ];
        } else {

            $vaildation =  [
                // 'fname' => 'required|string|max:255',
                //   'lname' => 'required|string|max:255',
                // 'email' => 'required|email|max:255|unique:visitors', // Check for duplicate email
                //'mobile' => 'required|string|max:255|unique:visitors,phone',
                // 'email' => 'required|email|max:255', // Check for duplicate email
                'mobile' => 'required|string|max:255',
                'user_question' => 'nullable|string',
                // 'otp' => 'required',
                'country_code' => 'required',
                // 'otp-verified' => 'required',
                'slot_id' => 'required|numeric|unique:visitors,slot_id'
            ];
            // if($request->input('selfie_required') == 'yes'){
            //    $vaildation['selfie'] =   'required';
            // }
        }
        $messages = [
            'slot_id.required' => 'The slot ID field is required.',
            'slot_id.numeric' => 'The slot ID must be a number.',
            'slot_id.unique' => trans('messages.slot_id'),
        ];
        $validatedData = $request->validate($vaildation, $messages);

        // $validatedData = $request->validate($vaildation);


        try {
            $selfieData = "";
            $selfieImage = "";
            $isUsers = [];
            // if ($from != 'admin') {
            //   $selfieData = $request->input('selfie');
            //   $selfieImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selfieData));

            // }
            $venueSlots = VenueSloting::find($request->input('slot_id'));
            $venueAddress = $venueSlots->venueAddress;
            // $tokenId = $venueSlots->token_id;
            $tokenId = str_pad($venueSlots->token_id, 2, '0', STR_PAD_LEFT);
            $tokenType = $venueSlots->type;


            $venue = $venueAddress->venue;
            $source = "Website";

            // if($venueAddress->rejoin_venue_after > 0){
            //   $isUsers = $this->IsRegistredAlready($selfieImage);
            // }
            // $user = Vistors::where('email', $validatedData['email'])->orWhere('phone', $validatedData['mobile'])->first();
            $rejoin = $venueAddress->rejoin_venue_after;
            //   $rejoinStatus = userAllowedRejoin($validatedData['mobile'], $rejoin);
            //   $user = Vistors::Where('phone', $validatedData['mobile'])->first();
            // $user = Vistors::where('phone',$validatedData['mobile'])->first();

            // if (!empty($user)) {
            // $recordAge = $user->created_at->diffInDays(now());
            // $rejoin = $venueAddress->rejoin_venue_after;
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
            if($captured_user_image){
                $imahee = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_user_image));
                // $rekognition = new RekognitionClient([
                //     'version' => 'latest',
                //     'region' => env('AWS_DEFAULT_REGION'),
                //     'credentials' => [
                //         'key' => env('AWS_ACCESS_KEY_ID'),
                //         'secret' => env('AWS_SECRET_ACCESS_KEY'),
                //     ],
                // ]);
                // dd($rekognition) ;

                // $userAll = Vistors::whereDate('created_at',date('Y-m-d'))->get(['recognized_code', 'id'])->toArray();
                // return response()->json(['message' => $userAll]);
                $isUsers = $this->IsRegistredAlready($imahee);
                if (!empty($isUsers) && $isUsers['status'] == false) {

                        return response()->json(['message' => $isUsers['message'],   'ites' => env('AWS_ACCESS_KEY_ID'), 'isUser' => $isUsers , "status" => false], 406);
                }
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



            $timestamp = Carbon::now()->format('Yis'); // Current timestamp
            $randomString = rand(2, 100); // Generate a random string of 6 characters

            // $bookingNumber = $timestamp . $randomString;
            $bookingNumber =  $tokenId;
            $tokenType = $request->input('dua_type');
            // Create a new Vistors record in the database
            $mobile = $countryCode . $validatedData['mobile'];
            $booking = new Vistors;

            $booking->country_code = '+' . $countryCode;
            $booking->phone = $validatedData['mobile'];
            $booking->user_question =  $request->input('user_question', null);
            $booking->slot_id = $request->input('slot_id');
            $booking->is_whatsapp = $request->has('is_whatsapp') ? 'yes' : 'no';
            $booking->booking_uniqueid = $uuid;
            $booking->user_ip =   $request->ip();
            $booking->recognized_code = (!empty($isUsers)) ?  $isUsers['recognized_code'] : null;
            $booking->booking_number = $bookingNumber;
            $booking->meeting_type = $venueAddress->type;
            $booking->user_timezone = $request->input('timezone', null);
            $booking->source = $source;
            $booking->dua_type = $request->input('dua_type');
            $booking->lang = $request->input('lang', 'en');
            $booking->working_lady_id = $request->input('working_lady_id');

            // Save the booking record
            $booking->save();

            $mobile =  'whatsapp:+' . $countryCode . $validatedData['mobile'];
            $mymobile = '+' . $countryCode . $validatedData['mobile'];

            $token  = $tokenId . ' (' . ucwords($tokenType) . ')' . ' (' . $source . ')';
            $message =  $this->whatsAppConfirmationTemplate($venueAddress, $uuid, $token, $mymobile, $request->input('lang'));
            $this->sendWhatsAppMessage($mobile, $message);
            //   $eventData = $venueAddress->venue_date . ' ' . $venueSlots->slot_time;
            //   $slotDuration = $venueAddress->slot_duration;
            //   $userTimeZone = Carbon::parse($eventData)->tz($request->input('timezone'));
            //   $dateTime = Carbon::parse($eventData);
            //   $userSlot = VenueSloting::find($request->input('slot_id'));
            //   $iso = $venue->iso;

            //   $venueTimezone = Timezone::where(['country_code' => $iso])->first();
            //   $countryTz =  $venueTimezone->timezone;

            //   $venueDate = $venueAddress->venue_date . ' ' . $userSlot->slot_time;
            //   $currentContryTimezone = Carbon::parse($venueDate, $countryTz);
            //   $currentContryTimezone->timezone($countryTz);

            //   $userSelectedTimezone = Carbon::parse($venueDate, $countryTz);
            //   $userSelectedTimezone->timezone($request->input('timezone'));

            //   $formattedDateTime = $currentContryTimezone->format('l F j, Y ⋅ g:i a') . ' – ' . $currentContryTimezone->addMinutes(30)->format('g:ia');
            //   $userTimezoneFormat = $userSelectedTimezone->format('l F j, Y ⋅ g:i a') . ' – ' . $userSelectedTimezone->addMinutes(30)->format('g:ia');
            //   $userLocationTime = ' As per Selected Timezone ' . $userTimezoneFormat . '(' . $request->input('timezone') . ')';

            // $dynamicData = [
            //   'subject' => $validatedData['fname'] . ', your online dua appointment is confirmed - ' . $userTimezoneFormat . '('.$request->input('timezone').')',
            //   'userTime' => $userTimezoneFormat,
            //   'venueTz' => $countryTz,
            //   'userTz' => $request->input('timezone'),
            //   'first_name' => $validatedData['fname'],
            //   // 'email' => $validatedData['email'],
            //   'mobile' =>  '+' . $mobile,
            //   'country' =>  $venue->country_name,
            //   'event_name' => $slotDuration . " Minute Online Dua Appointment",
            //   'location' => ($venueAddress->type == 'on-site') ? $venueAddress->address : "Online Video Call",
            //   'userLocationTime' => $userLocationTime,
            //   'spot_confirmation' => route('booking.confirm-spot', [$uuid]),
            //   "meeting_status_link" => route('booking.status', [$uuid]),
            //   'meeting_cancel_link' => route('book.cancle', [$uuid]),
            //   'meeting_reschedule_link' => route('book.reschdule', [$uuid]),
            //   'unsubscribe_link' => '',
            //   'meeting_date_time' => $formattedDateTime,
            //   'meeting_location' =>  ($venueAddress->type == 'on-site') ? $venueAddress->address . ' At' .   $userLocationTime   : "Online Video Call",
            //   'therapist_name' => $venueAddress->user->name,
            //   'booking_number' => $bookingNumber,
            //   'slotDuration' => $slotDuration,
            //   'venue_address' => $venueAddress->address,
            //   'video_conference_link' => ($venueAddress->type == 'virtual') ? route('join.conference.frontend', [$uuid]) : ''
            // ];

            // $appointMentStatus = route('booking.status', [$uuid]);
            // $confirmSpot = route('booking.confirm-spot');
            // $cancelBooking = route('book.cancle', [$uuid]);
            // $rescheduleBooking = route('book.reschdule', [$uuid]);
            // $name = $validatedData['fname'];
            // $therapistName = $venueAddress->thripist->name;

            // // $venueString =  $venueAddress->venue_date  . ' At.' . date("g:i A", strtotime($userSlot->slot_time));
            // $whatsappTims = Carbon::parse($venueDate,$countryTz); // IST timezone
            // $whatsappTims->timezone($request->input('timezone'));
            // $venueString = $whatsappTims->format('d-M-Y g:i A') . ' ('.$countryTz.')';
            // $slot_duration = $venueAddress->slot_duration;
            // if ($venueAddress->type == 'on-site') {
            //   $location = $venueAddress->address;
            //   $confirmSpot = route('booking.confirm-spot');
            // } else {
            //   $location = 'Online Meeting';
            //   $confirmSpot = route('join.conference.frontend', [$uuid]);
            // }
            //WhatsApp Template
            // $message = $this->bookingMessageTemplate($name, $therapistName, $location, $bookingNumber, $venueString, $slot_duration, $rescheduleBooking, $cancelBooking, $confirmSpot, $appointMentStatus);
            // SendMessage::dispatch($mobile, $message, $booking->is_whatsapp, $booking->id)->onQueue('send-message')->onConnection('database');
            // SendEmail::dispatch($validatedData['email'], $dynamicData, $booking->id)->onQueue('send-email')->onConnection('database');

            // PushEmailToSandlane::dispatch($validatedData['email'], $name)->onQueue('push-to-sandlane')->onConnection('database');

            // $NotificationMessage = "Just recived a booking for <b> " . $venue->country_name . " </b> at <b> " . $eventData . "</b> by: <br></b>" . $validatedData['fname'] . " " . $validatedData['lname'] . "</b>";
            // Notification::create(['message' => $NotificationMessage, 'read' => false]);

            // event(new BookingNotification($NotificationMessage));
            if ($from == 'admin') {
                return  redirect()->route('booking.status', $uuid);
                // booking.status
                // return redirect()->back()->with('success', 'Booking created successfully');
            } else {
                return response()->json(['message' => 'Booking submitted successfully', "status" => true, 'bookingId' => $uuid,
                'redirect_url' => route('booking.status',[$uuid])
            ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Booking error' . $e);

            return response()->json(['message' => $e->getMessage(), "status" => false], 422);
        }
    }

    private function sendWhatsAppMessage($to, $message)
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




    public function CheckAvilableSolt(Request $request)
    {
        $id = $request->input('id');
        if (Vistors::where('slot_id', $id)->exists()) {
            return response()->json(['message' => 'occupied', "status" => false], 422);
        } else {
            return response()->json(['message' => 'slot available', "status" => true], 200);
        }
    }


    protected function IsRegistredAlready($selfieImage)
    {

        $filename = 'selfie_' . time() . '.jpg';
        $objectKey = $this->encryptFilename($filename);
         $userAll = Vistors::whereDate('created_at',date('Y-m-d'))->get(['recognized_code', 'id'])->toArray();
        //  $userAll = Vistors::get(['recognized_code', 'id'])->toArray();


        $userArr = [];

        if (!empty($userAll)) {


            try {
                $rekognition = new RekognitionClient([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);
                $path = 'SessionImages/';
                Storage::disk('s3_general')->put($path.$objectKey, $selfieImage);
                    foreach ($userAll as $user) {

                        // $result = $rekognition->detectFaces([
                        //     'Attributes' => ['ALL'],
                        //     'Image' => [
                        //         'S3Object' => [
                        //             'Bucket' => env('AWS_BUCKET'),
                        //             'Name' => $objectKey, // path to the photo in your S3 bucket
                        //         ],
                        //     ],
                        // ]);
                        $response = [];
                        if(!empty( $user['recognized_code'])){
                            $bucket ='kahayfaqeer-booking-bucket';

                            $response = $rekognition->compareFaces([
                                'SimilarityThreshold' => 90,
                                'SourceImage' => [
                                    'Bytes' => 'blob',
                                    'S3Object' => [
                                        'Bucket' => 'kahayfaqeer-general-bucket',
                                        'Name' => $objectKey,
                                    ],
                                ],
                                'TargetImage' => [
                                    'Bytes' => 'blob',
                                    'S3Object' => [
                                        'Bucket' => $bucket,
                                        'Name' => $user['recognized_code'],
                                    ],
                                ],
                            ]);
                        }


                        $faceMatches = (!empty($response)) ? $response['FaceMatches'] : [];

                        if (count($faceMatches) > 0) {

                            foreach ($faceMatches as $match) {
                                if ($match['Similarity'] >= 80) {
                                    $userArr[] = $user['id'];
                                }
                            }
                        }
                    }




                if (empty($userArr)) {

                    return ['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $objectKey];
                } else {
                    return ['message' => 'Your token cannot be booked at this time. Please try again later.', 'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم کچھ دیر بعد کوشش کریں' , 'status' => false];
                }
            } catch (\Exception $e) {
                return ['message' => $e->getMessage(), 'status' => false];
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
        $key = hash('sha256', date('Y-m-d') . $filename . now());
        //  $hashedPassword = Hash::make($filename.now());
        return $key;
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
            $ipInfo = Ipinformation::where(['user_ip' => $request->ip()])->get()->first();
            if (!empty($ipInfo)) {
                $userDetail = json_decode($ipInfo['complete_data'], true);
            } else {
                $userDetail = $this->getIpDetails($request->ip());
            }
            $phoneCode = (isset($userDetail['phoneCode'])) ? $userDetail['phoneCode'] : '91';
        } else {
            $userDetail['countryCode'] = 'IN';
            $userDetail['countryName'] = 'India';
            $phoneCode = '91';
        }

        session(['phoneCode' => $phoneCode]);
        // echo "<pre>"; print_r($userDetail); die;
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

        if ($type == 'get_city' || $type =='normal_person' || $type == 'working_lady') {

            $countryId = Venue::where(['iso' => 'PK'])->get()->first();
            if(empty($countryId)){
                return response()->json([
                    'status' => false,
                    'message' => 'There is no Venue in the system. Please try after some time',
                    'message_ur' => 'سسٹم میں کوئی وینیو نہیں ہے۔ تھوڑی دیر بعد کوشش کریں۔',

                ]);

            }else{
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
            $duaType = $request->input('duaType');
            $selectionType = $request->input('selection_type');
            // $duaType = $request->input('duaType');
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
            $today = getCurrentContryTimezone($request->input('id'));
            $venuesListArr = VenueAddress::where('venue_id', $request->input('id'))
                ->where('city',  $request->input('optional'))
                //->where('venue_date','LIKE',"%{$today}%")
                ->whereDate('venue_date', $today)
                ->orderBy('venue_date', 'asc')
                ->first();



            $isVisible = false;

            if ($duaType == 'dua' && !empty($venuesListArr->reject_dua_id)) {
                $reason  = Reason::find($venuesListArr->reject_dua_id);
                return response()->json([
                    'status' => false,
                    'message' => $reason->reason_english,
                    'message_ur' => $reason->reason_urdu,
                    'se' =>  $selectionType

                ]);
            }
            if ($duaType == 'dum' && !empty($venuesListArr->reject_dum_id)) {
                $reason  = Reason::find($venuesListArr->reject_dum_id);
                return response()->json([
                    'status' => false,
                    'message' => $reason->reason_english,
                    'message_ur' => $reason->reason_urdu,
                    'se' =>  $selectionType

                ]);
            }


            if (!empty($venuesListArr) && $venuesListArr->status == 'inactive') {
                return response()->json([
                    'status' => false,
                    'message' => 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You',
                    'message_ur' => 'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ',

                ]);
            }



            if ($venuesListArr) {

                $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $venuesListArr->timezone);
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

                //  $status = isAllowedTokenBooking($venuesListArr->venue_date, $venuesListArr->slot_appear_hours , $venuesListArr->timezone);

                if ($status['allowed']) {

                    session()->forget('phoneCode');


                    $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                        ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                        ->where(['type' => $request->input('duaType')])
                        ->orderBy('id', 'ASC')
                        ->select(['venue_address_id', 'token_id', 'id'])->first();

                    if (!empty($tokenIs)) {
                        return response()->json([
                            'status' =>  true,
                            'token_id' => $tokenIs->token_id,
                            'slot_id' => $tokenIs->id,
                            //   'hours_until_open' => $status['hours_until_open'],
                            // 'slotsAppearBefore' => $status['slotsAppearBefore'],
                        ]);
                    } else {
                        return response()->json([
                            'status' =>  false,
                            'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                            'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                            //  'message' => "There is no token avilable",
                            'dt' => $request->input('duaType'),
                            'dtd' => $venuesListArr->id,
                            //   'hours_until_open' => $status['hours_until_open'],
                            //   'slotsAppearBefore' => $status['slotsAppearBefore'],
                        ]);
                    }
                } else {

                    return response()->json([
                        'status' => false,
                        'message' => $status['message'],
                        'message_ur' => $status['message_ur'],

                    ]);
                }
            } else {
                return response()->json([
                    'status' =>  false,
                    'message' => 'There is no Dua / Dum token booking available for today. Please try again later.',
                    'message_ur' => 'آج کے لیے کوئی دعا/دم ٹوکن بکنگ دستیاب نہیں ہے۔ براہ کرم کچھ دیر بعد کوشش کریں.',

                ]);
            }
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

            // $venueAddress =  VenueAddress::where('id', $id)
            //   ->where(function ($query) use ($newDate) {
            //     $query->whereDate('venue_date', $newDate)
            //       ->orWhereDate('venue_date', date('Y-m-d'));
            //   })
            //   ->get()->first();


            // if (App::environment('production')) {
            //   $ipInfo = Ipinformation::where(['user_ip' => $request->ip()])->get()->first();
            //   if (!empty($ipInfo)) {
            //     $userDetail = json_decode($ipInfo['complete_data'], true);
            //   } else {
            //     $userDetail = $this->getIpDetails($request->ip());
            //   }
            //   $countryCode = $userDetail['countryCode'];
            //   $timezone = Timezone::where(['country_code' => $countryCode])->get()->first();
            //   $currentTimezone = $timezone->timezone;
            // } else {
            //   $currentTimezone = 'America/New_York';
            // }


            $isVisiable = false;
            if (!empty($venueAddress)) {


                $mytime = Carbon::now()->tz($currentTimezone);
                $countryTimeZone = $venueAddress->timezone;
                $eventDate = Carbon::parse($venueAddress->venue_date . ' ' . $venueAddress->slot_starts_at_morning, $countryTimeZone);
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
        $result = json_decode($response, true);

        curl_close($curl);

        $data = [
            'user_ip' => $userIp,
            'countryName' => (isset($result['countryName'])) ? $result['countryName'] : null,
            'regionName' => (isset($result['regionName'])) ? $result['regionName'] : null,
            'city' => (isset($result['city'])) ? $result['city'] . "enc=v" . env('IP_API_KEY') : "enc=v" . env('IP_API_KEY'),
            'postalCode' => (isset($result['postalCode'])) ? $result['postalCode'] : null,
            'complete_data' => $response
        ];

        Ipinformation::create($data);
        return $result;
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
        $visitors = Vistors::where(['dua_type' => $request->input('dua_option')])->whereDate('created_at', $request->input('venueDate'))
        ->get(['id','booking_uniqueid' ,'dua_type' ,'created_at','phone','country_code']);
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


    private function whatsAppConfirmationTemplate($venueAddress, $uuid, $tokenId, $userMobile, $lang)
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


                $message = <<<EOT
                آپ کی دعا قبلہ سید سرفراز احمد شاہ سے تصدیق شدہ ✅

                واقعہ کی تاریخ : $venueDateUr

                مقام: $venueAddress->city

                $venueAddress->address_ur

                ٹوکن # $tokenId

                آپ کا موبائل : $userMobile

                ملاقات کا دورانیہ : $appointmentDuration

                ٹوکن URL:
                $statusLink

                Your Dua Appointment Confirmed With Qibla Syed Sarfraz Ahmad Shah ✅

                Event Date : $venueDateEn

                Venue : $venueAddress->city

                $venueAddress->address

                Token # $tokenId

                Your Mobile : $userMobile

                Appointment Duration : $appointmentDuration

                Token URL:
                $statusLink
                EOT;

        // if ($lang == 'en') {
        //    // $pdfLink = 'Subscribe to Syed Sarfraz Ahmad Shah Official YouTube Channel  https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1';

        //     $message = <<<EOT


        //     Your Dua Appointment Confirmed $duaby

        //     Event Date :  $venueDate

        //     Venue :  $venueAddress->city

        //     $venueAddress->address

        //     Token # $tokenId

        //     Your Mobile :  $userMobile

        //     Appointment Duration :  $appointmentDuration

        //     $statusNote

        //     To view your token online please click below:

        //     $statusLink

        //     $pdfLink
        //     EOT;
        // } else {

        //    // $pdfLink = 'سید سرفراز احمد شاہ آفیشل یوٹیوب چینل کو سبسکرائب کریں https://www.youtube.com/@syed-sarfraz-a-shah-official/?sub_confirmation=1';

        //     $message = <<<EOT

        //     آپ کی دعا ملاقات کی تصدیق ہوگئ ہے سید سرفراز احمد شاہ صاحب کے ساتھ $duaby

        //     تاریخ : $venueDate

        //     دعا گھر : $venueAddress->city

        //     $venueAddress->address_ur

        //     ٹوکن #$tokenId

        //     آپ کا موبائل : $userMobile

        //     ملاقات کا دورانیہ : $appointmentDuration

        //     $statusNote

        //     اپنا ٹوکن آن لائن دیکھنے کے لیے براہ کرم نیچے کلک کریں:

        //     $statusLink

        //     $pdfLink

        //     EOT;
        // }
        return $message;
    }

    public function sendMessage($to, $message)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        try {
            $twilio->messages->create(
                "whatsapp:$to",
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
