<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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

    return view('frontend.bookseat', compact('VenueList'));
  }
  public function BookingSubmit(Request $request)
  {

    $validatedData = $request->validate([
      'fname' => 'required|string|max:255',
      'lname' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'mobile' => 'required|string|max:255',
      'user_question' => 'nullable|string',
    ]);
    $uuid = Str::uuid()->toString();
    // Create a new Vistors record in the database
    $booking = new Vistors;
    $booking->fname = $validatedData['fname'];
    $booking->lname = $validatedData['lname'];
    $booking->email = $validatedData['email'];
    $booking->phone = $validatedData['mobile'];
    $booking->user_question = $validatedData['user_question'];
    $booking->slot_id = $request->input('slot_id');
    $booking->is_whatsapp = $request->has('is_whatsapp') ? 'yes' : 'no';
    $booking->booking_uniqueid = $uuid;
    $booking->user_ip =   $request->ip();

    // Save the booking record
    $booking->save();

    // You can also add additional logic here, such as sending emails, etc.

    // Return a response (e.g., a success message)
    return response()->json(['message' => 'Booking submitted successfully', "status" => true]);
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
          'imgUrl' => asset('images/' . $venuesList->venue->flag_path),
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
      $currentTime = strtotime(now()->addHour(24));
      $EventStartTime = strtotime($venueAddress->venue_date . $venueAddress->slot_starts_at);

      if ($currentTime >= $EventStartTime) {
        $slotArr = VenueSloting::where('venue_address_id', $id)->whereNotIn('id', Vistors::pluck('slot_id')->toArray())->get();
        return response()->json(['status' => true, 'message' => 'Slots are be avilable', 'data' => $slotArr]);
      } else {
        return response()->json(['status' => false, 'message' => 'Slots will be avilable only before 24 Hours of Event. Thanks for your Patience', 'data' => []]);
      }
    }
  }
}
