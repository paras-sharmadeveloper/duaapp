<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, Country, User,Notification};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{SendMessage, SendEmail};
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use App\Events\BookingNotification;

class HomeController extends Controller
{
  use OtpTrait;
  public function __construct()
  {
    //  $this->middleware('auth');
  }



  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    $therapistRole = Role::where('name', 'therapist')->first();
    $VenueList = Venue::all();
    $countryList = Country::all();
    $therapists = $therapistRole->users;

    return view('frontend.bookseat', compact('VenueList', 'countryList', 'therapists'));
  }
  public function BookingSubmit(Request $request)
  {

    $validatedData = $request->validate([
      'fname' => 'required|string|max:255',
      'lname' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:vistors', // Check for duplicate email
      'mobile' => 'required|string|max:255|unique:vistors,phone',
      'user_question' => 'nullable|string',
      'selfie' => 'required',
      'country_code' => 'required',
      'slot_id' => 'required|numeric|unique:vistors,slot_id'
    ]);


    try {

      $selfieData = $request->input('selfie');
      $selfieImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selfieData));

      $isUsers = $this->IsRegistredAlready($selfieImage);

      if ($isUsers['status'] == false) {
        return response()->json(['message' => 'You already Booked a seat', "status" => false], 406);
      }

      $venueSlots = VenueSloting::find($request->input('slot_id'));
      $venueAddress = $venueSlots->venueAddress;
      $venue = $venueAddress->venue;

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
      $booking->recognized_code = $isUsers['recognized_code'];
      $booking->booking_number = $bookingNumber;
      $booking->meeting_type = $venueAddress->type;
      // Save the booking record
      $booking->save();
      $eventData = $venueAddress->venue_date . ' ' . $venueSlots->slot_time;
      $dateTime = Carbon::parse($eventData);
      $formattedDateTime = $dateTime->format('l F j, Y ⋅ g:i a') . ' – ' . $dateTime->addMinutes(30)->format('g:ia');
      $dynamicData = [
        'subject' => $validatedData['fname'] . ', your online dua appointment is confirmed - ' . $formattedDateTime . ' (Gulf Standard Time)',
        'first_name' => $validatedData['fname'],
        'email' => $validatedData['email'],
        'mobile' =>  '+' . $mobile,
        'country' =>  $venue->country_name,
        'event_name' => "1 Minute Online Dua Appointment",
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
        'video_conference_link' => ($venueAddress->type == 'virtual') ? route('join.conference.frontend', [$uuid]) : ''
      ];
      if ($venueAddress->type == 'on-site') {
        $Mobilemessage  = "Hi " . $validatedData['fname'] . ",\nYour Booking Confirmed with us.\nBookID: " . $bookingNumber . "\nHere is your Booking Status link:\n" . route('booking.status', [$uuid]) . ".\nWhen you visit the place, you can confirm your booking at this link:\n" . route('booking.confirm-spot') . "\nThanks,\nTeam Kahay Faqeer.";
      } else {
        $Mobilemessage  = "Hi " . $validatedData['fname'] . ",\nYour Booking Confirmed with us.\nBookID: " . $bookingNumber . "\nYou are Booking At: " . $formattedDateTime . "\nOn the below link, you can Join your Meeting:\n" . route('join.conference.frontend', [$uuid]) . "\nThank you,\nTeam Kahay Faqeer.";
      }

      

      SendMessage::dispatch($mobile, $Mobilemessage, $booking->is_whatsapp, $booking->id)->onConnection('sqs');
      SendEmail::dispatch($validatedData['email'], $dynamicData, $booking->id)->onConnection('sqs');
      $bookingMessage = "Just recived a booking for". $venue->country_name . "at" . $eventData ."by:".$validatedData['fname'];
      Notification::create(['message' => $bookingMessage,'read' =>false]); 
      $event=  event(new BookingNotification($bookingMessage));
      return response()->json(['message' => 'Booking submitted successfully', "status" => true], 200);
    } catch (\Exception $e) {
      Log::error('Booking error' . $e->getMessage());

      return response()->json(['message' => $e->getMessage(), "status" => false], 422);
    }
  }


  public function CheckAvilableSolt(Request $request){
    $id = $request->input('id');  
    if (Vistors::where('slot_id', $id)->exists()) {
          return response()->json(['message' => 'occupied', "status" => false], 422);
      } else {
         return response()->json(['message' =>'slot available', "status" => true], 200);
      }
    // check-available
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
      } catch (\Throwable $th) {
        return ['message' => 'There is some error in uploading your pic', 'status' => false];
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


  public function home()
  {
    $visitos = Vistors::get()->count();
    $therapist = Role::where('name', 'therapist')->first();
    $siteadmin = Role::where('name', 'site-admin')->first();
    $userCountWiththripistRole = $therapist->users->count();
    $userCountWithsiteadminRole = $siteadmin->users->count();
    return view('home', compact('visitos', 'userCountWiththripistRole', 'userCountWithsiteadminRole'));
  }


  public function getAjax(Request $request)
  {
    $type = $request->input('type');
    $id = $request->input('id');
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

      return response()->json($dataArr);
    }
    if ($type == 'get_type') {
      $addRess = VenueAddress::where(['therapist_id' => $id])->get()->all();
      $dataArr = [];
      foreach ($addRess as $venuesList) {
        $dataArr['type'][] = [
          'name' => $venuesList->type,
          'flag_path' =>  env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
          'venue_address_id' => $venuesList->id
        ];
      }
      return response()->json($dataArr);
    }
    if ($type == 'get_country') {
      $venuesListArr = VenueAddress::where(['id' => $id])->get()->all();
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
        // $dataArr['venue_address'][] = [
        //   'imgUrl' => env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
        //   'address' => $venuesList->address,
        //   'slot_start' => Carbon::createFromFormat('H:i:s', $venuesList->slot_starts_at)->format('H:i A'),
        //   'slot_ends' => Carbon::createFromFormat('H:i:s', $venuesList->slot_ends_at)->format('H:i A'),
        //   'venue_address_id' => $venuesList->id,
        //   'venue_date' => $venuesList->venue_date,
        //   'state' => $venuesList->state,
        //   'city' => $venuesList->city,
        //   'venue_id' => $venuesList->venue->id,
        //   'type' => $venuesList->type,

        // ];
      }
      $dataArr['country'] = array_unique($dataArr['country'], SORT_REGULAR);


      return response()->json($dataArr);
    }

    if ($type == 'get_city') {
      $venuesListArr = VenueAddress::where(['id' => $id])->get()->all();
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
        // $dataArr['venue_address'][] = [
        //   'imgUrl' => env('AWS_GENERAL_PATH') . 'flags/' . $venuesList->venue->flag_path,
        //   'address' => $venuesList->address,
        //   'slot_start' => Carbon::createFromFormat('H:i:s', $venuesList->slot_starts_at)->format('H:i A'),
        //   'slot_ends' => Carbon::createFromFormat('H:i:s', $venuesList->slot_ends_at)->format('H:i A'),
        //   'venue_address_id' => $venuesList->id,
        //   'venue_date' => $venuesList->venue_date,
        //   'state' => $venuesList->state,
        //   'city' => $venuesList->city,
        //   'venue_id' => $venuesList->venue->id,
        //   'type' => $venuesList->type,

        // ];
      }
      // $dataArr['country'] = array_unique($dataArr['country'], SORT_REGULAR);


      return response()->json($dataArr);
    }
    if ($type == 'get_date') {

      $venuesListArr = VenueAddress::where(['id' => $id])->get()->all();
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
      return response()->json($dataArr);
    }



    if ($type == 'get_slots') {
      $venueAddress = VenueAddress::find($id);

      $currentTime = strtotime(now()->addHour(24)->format('y-m-d H:i:s'));
      $EventStartTime = strtotime($venueAddress->venue_date . $venueAddress->slot_starts_at);


      if ($currentTime >= $EventStartTime) {
        $slotArr = VenueSloting::where('venue_address_id', $id)->whereNotIn('id', Vistors::pluck('slot_id')->toArray())->get(['venue_address_id', 'slot_time', 'id']);
        return response()->json(['status' => true, 'message' => 'Slots are be avilable', 'slots' => $slotArr]);
      } else {
        return response()->json(['status' => false, 'message' => 'Slots will be avilable only before 24 Hours of Event. Thanks for your Patience', 'data' => []]);
      }
    }
  }

  public function SendOtpUser(Request $request)
  {

    $validatedData = $request->validate([
      'mobile' => 'required|string|max:255|unique:vistors,phone',
      'country_code' => 'required'
    ]);

    $country = $request->input('country_code');
    $mobile = $request->input('mobile');

    $result =  $this->SendOtp($mobile, $country);

    if ($result['status']) {
      return response()->json(['message' => 'OTP Sent successfully', 'status' => true]);
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

  public function destroy($id){
    Vistors::find($id)->delete(); 
    return redirect()->route('venues.index')->with('success', 'Venue deleted successfully');
  }
}
