<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, CountryListing, User};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{SendMessage, SendEmail};
use App\Mail\BookingConfirmationEmail;
use Illuminate\Support\Facades\Mail;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
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
    $VenueList = Venue::all();
    $countryList = CountryListing::all();

    return view('frontend.bookseat', compact('VenueList', 'countryList'));
  }
  public function BookingSubmit(Request $request)
  {
    $validatedData = $request->validate([
      'fname' => 'required|string|max:255',
      'lname' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique_email', // Check for duplicate email
      'mobile' => 'required|string|max:255|unique_phone', 
      'user_question' => 'nullable|string',
      'selfie' => 'required',
      'country_code' =>'required'
    ]);

    $selfieData = $request->input('selfie');
    $selfieImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selfieData));

    $isUsers = $this->IsRegistredAlready($selfieImage);

    if ($isUsers['status'] == false) {
      return response()->json(['message' => 'You already Booked a seat', "status" => false],406);
    }

    $uuid = Str::uuid()->toString();
    $countryCode = $request->input('country_code');
    // Create a new Vistors record in the database
    $mobile = $countryCode . $validatedData['mobile'];
    $booking = new Vistors;
    $booking->fname = $validatedData['fname'];
    $booking->lname = $validatedData['lname'];
    $booking->email = $validatedData['email'];
    $booking->phone = $mobile;
    $booking->user_question = $validatedData['user_question'];
    $booking->slot_id = $request->input('slot_id');
    $booking->is_whatsapp = $request->has('is_whatsapp') ? 'yes' : 'no';
    $booking->booking_uniqueid = $uuid;
    $booking->user_ip =   $request->ip();
    $booking->recognized_code = $isUsers['recognized_code'];
    // Save the booking record
    $booking->save();

    $venueSlots = VenueSloting::find($request->input('slot_id'));
    $venueAddress = $venueSlots->venueAddress;
    $venue = $venueAddress->venue;

    $Mobilemessage  = "Hi, Here is your Booking Confirmed with us.\nHere is your Booking link\n" . route('book.confirmation', [$uuid]);

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
      "meeting_link" => route('book.confirmation', [$uuid]),
      'meeting_cancel_link' => route('book.cancel', [$uuid]),
      'meeting_reschedule_link' => route('book.reschudule', [$uuid]),
      'unsubscribe_link' => '',
      'meeting_date_time' => $formattedDateTime,
      'meeting_location' => $venueAddress->type,
      'therapist_name' => $venueAddress->user->name,
    ];

    SendMessage::dispatch($mobile, $Mobilemessage, $booking->is_whatsapp,$booking->id);
    SendEmail::dispatch($validatedData['email'], $dynamicData,$booking->id);

    return response()->json(['message' => 'Booking submitted successfully', "status" => true],200);
  }


  protected function IsRegistredAlready($selfieImage)
  {

    $filename = 'selfie_' . time() . '.jpg';
    $objectKey = $this->encryptFilename($filename);
    $userAll = Vistors::get(['recognized_code', 'id'])->toArray();
    $userArr = [];
    if (!empty($userAll)) {

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
    return view('home');
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
          'imgUrl' => env('AWS_GENERAL_PATH').'flags/'.$venuesList->venue->flag_path,
          'address' => $venuesList->address,
          'slot_start' => Carbon::createFromFormat('H:i:s', $venuesList->slot_starts_at)->format('H:i A'),
          'slot_ends' => Carbon::createFromFormat('H:i:s', $venuesList->slot_ends_at)->format('H:i A'),
          'venue_address_id' => $venuesList->id,
          'venue_date' => $venuesList->venue_date

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
        return response()->json(['status' => true, 'message' => 'Slots are be avilable', 'data' => $slotArr]);
      } else {
        return response()->json(['status' => false, 'message' => 'Slots will be avilable only before 24 Hours of Event. Thanks for your Patience', 'data' => []]);
      }
    }
  }
}
