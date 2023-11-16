<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, Country, User, Notification, Timezone, Ipinformation};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{SendMessage, SendEmail};
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Log;
use App\Events\BookingNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
  use OtpTrait;
  public function __construct()
  {
    //  $this->middleware('auth');
  }

  public function bookingAdmin($id)
  {
    $slots = VenueSloting::where(['venue_address_id' => $id])
      ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
      ->get();
    $countries = Country::all();
    $venueAddress = VenueAddress::find($id);
    return view('admin-booking', compact('id', 'slots', 'countries', 'venueAddress'));
  }


  public function index()
  {
    $therapistRole = Role::where('name', 'therapist')->first();
    $VenueList = Venue::all();
    $countryList = Country::all();
    $therapists = $therapistRole->users;
    $timezones = Country::with('timezones')->get();
    return view('frontend.bookseat', compact('VenueList', 'countryList', 'therapists', 'timezones'));
  }

  public function BookingSubmit(Request $request)
  {
    $from = $request->input('from', 'null');
    $vaildation = [];
    if ($from == 'admin') {

      $vaildation =  [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'mobile' => 'required|string|max:255',
        'user_question' => 'nullable|string',
        'country_code' => 'required',
        'slot_id' => 'required|numeric|unique:vistors,slot_id'
      ];
    } else {

      $vaildation =  [
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        // 'email' => 'required|email|max:255|unique:vistors', // Check for duplicate email
        //'mobile' => 'required|string|max:255|unique:vistors,phone',
        'email' => 'required|email|max:255', // Check for duplicate email
        'mobile' => 'required|string|max:255',
        'user_question' => 'nullable|string', 
        'otp' => 'required',
        'country_code' => 'required',
        'otp-verified' => 'required',
        'slot_id' => 'required|numeric|unique:vistors,slot_id'
      ];
      if($request->input('selfie_required') == 'yes'){
         $vaildation['selfie'] =   'required'; 
      }
    }
    $validatedData = $request->validate($vaildation);

    try {
      $selfieData = "";
      $selfieImage = "";
      $isUsers = [];
      if ($from != 'admin') {
        $selfieData = $request->input('selfie');
        $selfieImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selfieData));
        
      }
      $venueSlots = VenueSloting::find($request->input('slot_id'));
      $venueAddress = $venueSlots->venueAddress;
      $venue = $venueAddress->venue;

      if($venueAddress->rejoin_venue_after > 0){
        $isUsers = $this->IsRegistredAlready($selfieImage);
      }

      $user = Vistors::where('email', $validatedData['email'])->orWhere('phone', $validatedData['mobile'])->first();
      if ($user) {
        $recordAge = $user->created_at->diffInDays(now());
        $rejoin = $venueAddress->rejoin_venue_after;
        if ($rejoin > 0 && $recordAge <= $rejoin   && $from != 'admin') {
          return response()->json(['message' => 'You already Booked a seat Before ' . $recordAge . ' Day You can Rejoin only After ' . $venueAddress->rejoin_venue_after . ' ', "status" => false], 406);
        } else if ($rejoin > 0 && $recordAge <= $rejoin   && $from == 'admin') {
          return redirect()->back()->withErrors(['error' => 'You already Booked a seat Before ' . $recordAge . ' Day You can Rejoin only After ' . $venueAddress->rejoin_venue_after]);
        }
       
      }

      if (!empty($isUsers) && $isUsers['status'] == false) {
        return response()->json(['message' => $isUsers['message'], 'isUser' => $isUsers, "status" => false], 406);
      }




      $uuid = Str::uuid()->toString();
      $countryCode = $request->input('country_code');
      $timestamp = Carbon::now()->format('YmdHis'); // Current timestamp
      $randomString = rand(10, 100); // Generate a random string of 6 characters

      $bookingNumber = $timestamp . $randomString;
      // Create a new Vistors record in the database
      $mobile = $countryCode . $validatedData['mobile'];
      $booking = new Vistors;
      $booking->fname = $validatedData['fname'];
      $booking->lname = $validatedData['lname'];
      $booking->email = $validatedData['email'];
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

      // Save the booking record
      $booking->save();
      $eventData = $venueAddress->venue_date . ' ' . $venueSlots->slot_time;
      $slotDuration = $venueAddress->slot_duration;
      $userTimeZone = Carbon::parse($eventData)->tz($request->input('timezone'));
      $dateTime = Carbon::parse($eventData);
      $formattedDateTime = $dateTime->format('l F j, Y ⋅ g:i a') . ' – ' . $dateTime->addMinutes(30)->format('g:ia');
      $userTimezoneFormat = $userTimeZone->format('l F j, Y ⋅ g:i a') . ' – ' . $userTimeZone->addMinutes(30)->format('g:ia');
      $dynamicData = [
        'subject' => $validatedData['fname'] . ', your online dua appointment is confirmed - ' . $formattedDateTime . ' (Gulf Standard Time)',
        'userTime' => $userTimezoneFormat,
        'userTz' => $request->input('timezone'),
        'first_name' => $validatedData['fname'],
        'email' => $validatedData['email'],
        'mobile' =>  '+' . $mobile,
        'country' =>  $venue->country_name,
        'event_name' => $slotDuration . " Minute Online Dua Appointment",
        'location' => ($venueAddress->type == 'on-site') ? $venueAddress->address . ' At. ' .   $formattedDateTime   : "Online Video Call",
        "spot_confirmation" => route('booking.confirm-spot', [$uuid]),
        "meeting_link" => route('booking.status', [$uuid]),
        'meeting_cancel_link' => route('book.cancle', [$uuid]),
        'meeting_reschedule_link' => route('book.reschdule', [$uuid]),
        'unsubscribe_link' => '',
        'meeting_date_time' => $formattedDateTime,
        'meeting_location' => $venueAddress->type,
        'therapist_name' => $venueAddress->user->name,
        'booking_number' => $bookingNumber,
        'slotDuration' => $slotDuration,
        'venue_address' => $venueAddress->address,
        'video_conference_link' => ($venueAddress->type == 'virtual') ? route('join.conference.frontend', [$uuid]) : ''
      ];

      $appointMentStatus = route('booking.status', [$uuid]);
      $confirmSpot = route('booking.confirm-spot');
      $cancelBooking = route('book.cancle', [$uuid]);
      $rescheduleBooking = route('book.reschdule', [$uuid]);
      $name = $validatedData['fname'];
      $therapistName = $venueAddress->thripist->name;




      $userSlot = VenueSloting::find($request->input('slot_id'));

      $venueDate = $venueAddress->venue_date . ' ' . $userSlot->slot_time;
      $carbonSlot = Carbon::parse($venueDate); // IST timezone
      $carbonSlot->timezone($request->input('timezone')); 

      // $venueString =  $venueAddress->venue_date  . ' At.' . date("g:i A", strtotime($userSlot->slot_time));

      $venueString = $carbonSlot->format('d-M-Y g:i A'); 
      $slot_duration = $venueAddress->slot_duration;
      if ($venueAddress->type == 'on-site') {
        $location = $venueAddress->address;
        $confirmSpot = route('booking.confirm-spot');
      } else {
        $location = 'Online Meeting';
        $confirmSpot = route('join.conference.frontend', [$uuid]);
      }
      //WhatsApp Template 
      $message = $this->bookingMessageTemplate($name, $therapistName, $location, $bookingNumber, $venueString, $slot_duration, $rescheduleBooking, $cancelBooking, $confirmSpot, $appointMentStatus);
      SendMessage::dispatch($mobile, $message, $booking->is_whatsapp, $booking->id)->onQueue('send-message')->onConnection('database');
      SendEmail::dispatch($validatedData['email'], $dynamicData, $booking->id)->onQueue('send-email')->onConnection('database');
      $NotificationMessage = "Just recived a booking for <b> " . $venue->country_name . " </b> at <b> " . $eventData . "</b> by: <br></b>" . $validatedData['fname'] . " " . $validatedData['lname'] . "</b>";
      Notification::create(['message' => $NotificationMessage, 'read' => false]);
      event(new BookingNotification($NotificationMessage));
      if ($from == 'admin') {
        return redirect()->back()->with('success', 'Booking created successfully');
      } else {
        return response()->json(['message' => 'Booking submitted successfully', "status" => true, 'bookingId' => $uuid], 200);
      }
    } catch (\Exception $e) {
      Log::error('Booking error' . $e->getMessage());

      return response()->json(['message' => $e->getMessage(), "status" => false], 422);
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
    $userAll = Vistors::get(['recognized_code', 'id'])->toArray();
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
        Storage::disk('s3')->put($objectKey, $selfieImage);

        foreach ($userAll as $user) {

          $response = $rekognition->compareFaces([
            'SourceImage' => [
              'S3Object' => [
                'Bucket' => env('AWS_BUCKET'),
                'Name' => $objectKey,
              ],
            ],
            'TargetImage' => [
              'S3Object' => [
                'Bucket' => env('AWS_BUCKET'),
                'Name' => $user['recognized_code'],
              ],
            ],
          ]);

          $faceMatches = $response['FaceMatches'];
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
          return ['message' => 'You already Registered with us', 'status' => false];
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
    return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople'));
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
      ->where(function ($query) use ($newDate) {
        $query->whereDate('venue_date', $newDate)
          ->orWhereDate('venue_date', date('Y-m-d'));
      })
      ->get()->first();


    $mytime = Carbon::now()->tz($timezone);
    $eventDate = Carbon::parse($venueAddress->venue_date . ' ' . $venueAddress->slot_starts_at, $timezone);
    $hoursRemaining = $eventDate->diffInHours($mytime);

    // $currentTime = strtotime($mytime->addHour(24)->format('Y-m-d H:i:s'));
    // $evntTime = date('Y-m-d H:i:s',strtotime($venueAddress->venue_date .' '. $venueAddress->slot_starts_at)); 
    // $EventStartTime = strtotime($evntTime);
    $slotsArr = [];
    if ($hoursRemaining <= 24 || $hoursRemaining > 24) {

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
          'message' => 'Slots will be avilable only before 24 Hours of Event. Thanks for your Patience',
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
    }else{
      $userDetail['countryCode'] = 'IN'; 
      $userDetail['countryName'] = 'India'; 
    }
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
            'venue_available_country' => $venue_available_country
          ];
        } 
      }
    } 
    $newArr = [];
    $existingIds = [];
      foreach($dataArr  as $data){
        if (isset($data['id']) && !in_array($data['id'], $existingIds)) {
          $newArr[] = $data;
          $existingIds[] = $data['id'];
      } 
    }   
    return response()->json([
      'status' => !(empty($newArr)) ? true : false,
      'data' => $newArr, 
      'currentTimezone' => $currentTimezone
    ]);


  }



  public function getAjax(Request $request)
  {
    $type = $request->input('type');
    $id = $request->input('id');
    // $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
    $newDate = date('Y-m-d');
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
    if ($type == 'get_type') {
      // return ['today' => $newDate, 'wde' => $newDate15Day]; 
      if (App::environment('production')) { 
        $addRess = VenueAddress::where('therapist_id', $id)
        ->where(function ($query) use ($newDate,$newDate15Day) {
            $query->whereDate('venue_date', '>=', $newDate)
                  ->whereDate('venue_date', '<=', $newDate15Day);
        })
        ->orderBy('venue_date', 'asc')
        ->get();
      }else{
        $addRess = VenueAddress::where('therapist_id', $id)
        // ->where(function ($query) use ($newDate) {
        //   $query->whereDate('venue_date', $newDate)
        //     ->orWhereDate('venue_date', date('Y-m-d'));
        // })
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
      $venuesListArr = VenueAddress::where('id', $id)
        // ->where(function ($query) use ($newDate) {
        //   $query->whereDate('venue_date', $newDate)
        //     ->orWhereDate('venue_date', date('Y-m-d'));
        // })
        ->get();
      $dataArr = [];
      foreach ($venuesListArr as $venuesList) {
        $countryName = $venuesList->venue->country_name;
        $flagPath = $venuesList->venue->flag_path;


        $dataArr['country'][] = [
          'name' => $countryName,
          'flag_path' =>  env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
          'type' => $venuesList->type,
          'id' => $venuesList->id
        ];
      }
      $dataArr['country'] = array_unique($dataArr['country'], SORT_REGULAR);


      return response()->json([
        'status' => !(empty($dataArr)) ? true : false,
        'data' => $dataArr
      ]);
    }

    if ($type == 'get_city') {
      $venuesListArr = VenueAddress::where('id', $id)
        // ->where(function ($query) use ($newDate) {
        //   $query->whereDate('venue_date', $newDate)
        //     ->orWhereDate('venue_date', date('Y-m-d'));
        // })
        ->get();
      $dataArr = [];
      foreach ($venuesListArr as $venuesList) {
        $cityName = $venuesList->city;
        $flagPath = $venuesList->venue->flag_path;


        $dataArr['city'][] = [
          'name' => $cityName,
          'flag_path' =>  env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
          'id' => $venuesList->venue->id,
          'type' => $venuesList->type,
          'venue_address_id' => $venuesList->id
        ];
      }
      // $dataArr['country'] = array_unique($dataArr['country'], SORT_REGULAR);


      return response()->json([
        'status' => !(empty($dataArr)) ? true : false,
        'data' => $dataArr
      ]);
    }
    if ($type == 'get_date') {


      $venuesListArr = VenueAddress::where('id', $id)
        // ->where(function ($query) use ($newDate) {
        //   $query->whereDate('venue_date', $newDate)
        //     ->orWhereDate('venue_date', date('Y-m-d'));
        // })
        ->get();
      $dataArr = [];
      foreach ($venuesListArr as $venuesList) {
        $venue_date = $venuesList->venue_date;
        $flagPath = $venuesList->venue->flag_path;


        $dataArr['date'][] = [
          'venue_date' => $venue_date,
          'type' => $venuesList->type,
          'flag_path' =>  env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
          'id' => $venuesList->venue->id,
          'venue_address_id' => $venuesList->id
        ];
      }
      return response()->json([
        'status' => !(empty($dataArr)) ? true : false,
        'data' => $dataArr
      ]);
    }



    if ($type == 'get_slots') {
      $currentTimezone = $request->input('timezone','America/New_York'); 
      $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
      //  $venueAddress = VenueAddress::find($id); 

      $venueAddress =  VenueAddress::where('id', $id)
        ->where(function ($query) use ($newDate) {
          $query->whereDate('venue_date', $newDate)
            ->orWhereDate('venue_date', date('Y-m-d'));
        })
        ->get()->first();


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

      if (!empty($venueAddress)) {
        

        $mytime = Carbon::now()->tz($currentTimezone);
        $eventDate = Carbon::parse($venueAddress->venue_date . ' ' . $venueAddress->slot_starts_at, $currentTimezone);
        $hoursRemaining = $eventDate->diffInHours($mytime);
      } else {

        return response()->json([
          'status' => false,
          'message' => 'Slots will be available only before 24 Hours of Event. Thanks for your Patience',
          'timezone' => $currentTimezone,
          'app' => App::environment('production'),
          'selfie' => ($venueAddress->selfie_verification == 1) ? true : false,
          'slots' => [],
        ]);
      }


      // $currentTime = strtotime($mytime->addHour(24)->format('Y-m-d H:i:s'));
      // $evntTime = date('Y-m-d H:i:s',strtotime($venueAddress->venue_date .' '. $venueAddress->slot_starts_at)); 
      // $EventStartTime = strtotime($evntTime);
      $slotsArr = [];
      if ($hoursRemaining <= 24 || $hoursRemaining > 24) {

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
          'selfie' => ($venueAddress->selfie_verification == 1) ? true : false,
          'app' => App::environment('production')
        ]);
      } else {
        return response()->json(
          [
            'status' => false,
            'message' => 'Slots will be available only before 24 Hours of Event. Thanks for your Patience',
            'slots' => [],
            'app' => App::environment('production'),
            'hoursRemaining' => $hoursRemaining
            //  'EventStartTime' => $venueAddress->venue_date .' '. $venueAddress->slot_starts_at,
            //  'eventDate' => $eventDate, 

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
    $isMobile=true;
    $isEmail = true;
    // $this->SendOtp($mobile,$country,$isMobile=true,$isEmail = false); 
    $result =  $this->SendOtp($userDetail,$isMobile,$isEmail);

    if ($result['status']) {
      return response()->json(['message' => 'Please check your email for OTP. If not Recived then check Spam Email.', 'status' => true]);
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
      'city' => (isset($result['city'])) ? $result['city'] : null,
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

  private function bookingMessageTemplate($name, $therapistName, $location, $bookingNumber, $venueString, $slot_duration, $rescheduleBooking, $cancelBooking, $confirmSpot, $appointMentStatus)
  {
    $message = <<<EOT
      Hi $name,
      Your dua appointment is confirmed as below:
      
      Appointment ID : 
      $bookingNumber
      
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
      
      When you visit the dua place, you need to enter into virtual queue by clicking below link:
      $confirmSpot
      
      In case you want to reschedule your appointment, please click below:
      $rescheduleBooking
      
      If you want to only cancel your appointment, please click below:
      $cancelBooking
      
      For your convenience, please visit only 15 mins before your appointment.
      
      KahayFaqeer.org
      EOT;
    return $message;
  }
}
