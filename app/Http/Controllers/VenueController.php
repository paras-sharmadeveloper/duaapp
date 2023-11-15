<?php

namespace App\Http\Controllers;

use App\Models\{Venue, VenueSloting, VenueAddress, User, Vistors, Timezone, Country};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Twilio\Rest\Client;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Auth;
use PDO; 
use App\Jobs\{CreateVenuesSlots,CreateFutureDateVenues}; 
class VenueController extends Controller
{
    public function index()
    {
        $venuesAddress = VenueAddress::all();
        return view('venues.list', compact('venuesAddress'));
    }


    public function show()
    {
        return view('venues.venueCountry');
    }


    public function create()
    {
        $countries = Venue::all();
        $therapists = User::whereHas('roles', function ($query) {
            $query->where('name', 'therapist');
        })->get();
        $siteAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'site-admin');
        })->get();

        $venueCountry = Country::all(); 
        return view('venues.create', compact('countries', 'therapists', 'siteAdmins','venueCountry'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'venue_id' => 'required',
            'therapist_id' => 'required',
            'siteadmin_id' => 'required',
            'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            'slot_starts_at_morning' => 'required',
            'slot_ends_at_morning' => 'required',
            'city' => 'required',
            'video_room' => 'required_if:type,virtual',
            'slot_duration' => 'required',
            'rejoin_venue_after' => 'required'

        ]);

        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venueStartsMorning = $request->input('slot_starts_at_morning');
        $venueEndsMorning = $request->input('slot_ends_at_morning');
        $venueStartsEvening = $request->input('slot_starts_at_evening', null);
        $venueEndsEvening = $request->input('slot_ends_at_evening', null);
        $slotDuration = $request->input('slot_duration');
        $IsRecuureing = $request->input('is_recurring');
        $recuureingTill = $request->input('recurring_till',0);
        $rejoin_venue_after = $request->input('rejoin_venue_after',0);
        $venue_available_country = json_encode($request->input('venue_available_country',0));
        


        $dataArr = [];
        $dayToSet = [];
        $roomDetail = [];
        if ($request->input('video_room')) {
            $roomName = str_replace(' ', '_', $request->input('video_room'));
            $roomDetail =  $this->createConferencePost($roomName);
        }

        $dataArr = [
            'city' => $request->input('city'),
            'state' =>  $request->input('state', null),
            'address' => $venueAdd,
            'venue_date' => $venueDate,
            'slot_starts_at_morning' =>  $venueStartsMorning,
            'slot_ends_at_morning' =>  $venueEndsMorning,
            'slot_starts_at_evening' =>  $venueStartsEvening,
            'slot_ends_at_evening' =>  $venueEndsEvening,
            'venue_id' => $request->input('venue_id'),
            'therapist_id' => $request->input('therapist_id'),
            'siteadmin_id' =>  $request->input('siteadmin_id'),
            'type' => $request->input('type'),
            'room_name' => (isset($roomDetail['room_name'])) ? $roomDetail['room_name'] : null,
            'room_sid' => (isset($roomDetail['room_sid'])) ? $roomDetail['room_sid'] : null,
            'slot_duration' => $slotDuration,
            'recurring_till' => (!empty($recuureingTill)) ? $recuureingTill : 0,
            'rejoin_venue_after' => $rejoin_venue_after,
            'venue_available_country' => $venue_available_country
        ];
        if (!empty($IsRecuureing)) {
            foreach ($IsRecuureing as $key => $recuureing) {
                $dataArr['is_'. $key] = ($recuureing == 'on') ? 1 : 0;
                 
                $dayToSet[] = $key; 
            }
        }

     
        if(!empty($dayToSet)){
            CreateFutureDateVenues::dispatch($dataArr,$dayToSet,$recuureingTill,$slotDuration)->onQueue('create-future-dates')->onConnection("database"); 
            // foreach($dayToSet as $day){
            //     $futureDates = $this->RecurringDays($recuureingTill,$day);
            // }
            // CreateVenuesSlots::dispatch($venueAddress->id ,  $slotDuration)->onConnection('database');
          
            // foreach($futureDates as $dates ){
            //     $dataArr['venue_date'] = $dates; 
            //     $venueAddress =   VenueAddress::create($dataArr);
            //     CreateVenuesSlots::dispatch($venueAddress->id ,  $slotDuration)->onConnection('database');
            //     // $this->createVenueTimeSlots($venueAddress->id, $slotDuration);
            // } 
        }else{
            $venueAddress =   VenueAddress::create($dataArr);
            CreateVenuesSlots::dispatch($venueAddress->id ,  $slotDuration)->onQueue('create-slots')->onConnection('database');
            // $this->createVenueTimeSlots($venueAddress->id, $slotDuration);
        }
        return redirect()->route('venues.index')->with('success', 'Venue created successfully');
    }

    public function edit($id)
    {
        $venueAddress = VenueAddress::findOrFail($id);
        $countries = Venue::all();
        $therapists = User::whereHas('roles', function ($query) {
            $query->where('name', 'therapist');
        })->get();
        $siteAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'site-admin');
        })->get();
        $venueCountry = Country::all(); 
        return view('venues.create', compact('venueAddress', 'countries', 'therapists', 'siteAdmins','venueCountry'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data

        $VenueAddress = VenueAddress::findOrFail($id);
        $request->validate([
            'venue_id' => 'required',
            'therapist_id' => 'required',
            'siteadmin_id' => 'required',
            'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            'slot_starts_at_morning' => 'required',
            'slot_ends_at_morning' => 'required',
            'city' => 'required',
            'video_room' => 'required_if:type,virtual',
            'slot_duration' => 'required',
            'rejoin_venue_after' => 'required'

        ]);
        $roomDetail = [];
        if ($request->input('video_room') !== $VenueAddress->room_name) {
            $roomDetail =  $this->createConferencePost($request->input('video_room'));
        }
        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');

        $venueStartsMorning = $request->input('slot_starts_at_morning');
        $venueEndsMorning = $request->input('slot_ends_at_morning');
        $venueStartsEvening = $request->input('slot_starts_at_evening', null);
        $venueEndsEvening = $request->input('slot_ends_at_evening', null);
        $slotDuration = $request->input('slot_duration');
        $rejoin_venue_after = $request->input('rejoin_venue_after',0);
        $venue_available_country = json_encode($request->input('venue_available_country',0));

        $dataArr = [
            'city' => $request->input('city'),
            'state' =>  $request->input('state', null),
            'address' => $venueAdd,
            'venue_date' => $venueDate,
            'slot_starts_at_morning' =>  $venueStartsMorning,
            'slot_ends_at_morning' =>  $venueEndsMorning,
            'slot_starts_at_evening' =>  $venueStartsEvening,
            'slot_ends_at_evening' =>  $venueEndsEvening,
            'venue_id' => $request->input('venue_id'),
            'therapist_id' => $request->input('therapist_id'),
            'siteadmin_id' =>  $request->input('siteadmin_id'),
            'type' => $request->input('type'),
            'room_name' => (isset($roomDetail['room_name'])) ? $roomDetail['room_name'] : null,
            'room_sid' => (isset($roomDetail['room_sid'])) ? $roomDetail['room_sid'] : null,
            'slot_duration' => $slotDuration,
            'recurring_till' => $request->input('recurring_till'),
            'rejoin_venue_after' => $rejoin_venue_after,
            'venue_available_country' => $venue_available_country
        ];
 
        $VenueAddress->update($dataArr);

        if ($request->has('update_slots')) {
            VenueSloting::where(['venue_address_id' => $id])->delete();
            CreateVenuesSlots::dispatch($id,  $slotDuration)->onConnection('database');
            // $this->createVenueTimeSlots($id, $slotDuration);
        }
        return redirect()->route('venues.index')->with('success', 'Venue updated successfully');
    }

    private function RecurringDays($tillMonths,$day){
        $currentDate = Carbon::now();
        $nextTwoMonths = $currentDate->copy()->addMonths($tillMonths);
         
        $mondaysInNextTwoMonths = [];

        while ($currentDate->lte($nextTwoMonths)) {
            if ($day=='monday' && $currentDate->dayOfWeek === Carbon::MONDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='tuesday' && $currentDate->dayOfWeek === Carbon::TUESDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='wednesday' && $currentDate->dayOfWeek === Carbon::WEDNESDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='thursday' && $currentDate->dayOfWeek === Carbon::THURSDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='friday' && $currentDate->dayOfWeek === Carbon::FRIDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='saturday' && $currentDate->dayOfWeek === Carbon::SATURDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day=='sunday' && $currentDate->dayOfWeek === Carbon::SUNDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            $currentDate->addDay();
        } 
        $allDates =[];

        foreach ($mondaysInNextTwoMonths as $monday) {
            $allDates[] = $monday->format('Y-m-d');  
        }
        return  $allDates; 
    }

    public function destroy($id)
    {

        VenueAddress::destroy($id);
        return redirect()->route('venues.index')->with('success', 'Venue deleted successfully');
    }

    protected function createVenueTimeSlots($venueId, $slotDuration)
    {
        $venueAddress = VenueAddress::find($venueId);

        // $timezones = Timezone::join('venues', 'timezone.country_code', '=', 'venues.iso')
        // ->where(['venues.id' => $venueId])
        // ->select('timezone.*', 'venues.country_name')
        // ->get()->first();

        // echo "<pre>"; print_r( $timezones); die; 


        if (!$venueAddress) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        // Define start and end times
        $startTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at_morning);
        $endTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at_morning);

        if(!empty($venueAddress->slot_starts_at_evening) && !empty($venueAddress->slot_ends_at_evening)){

            $startTimeevng = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at_evening);
            $endTimeEvn = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at_evening);

            $currentTimeT = $startTimeevng;
            while ($currentTimeT < $endTimeEvn) {
                $slotTime = $currentTimeT->format('H:i');
                VenueSloting::create([
                    'venue_address_id' => $venueId,
                    'slot_time' => $slotTime,
                ]);
                $currentTimeT->addMinute($slotDuration); // Move to the next minute
            }

        } 
        // Create time slots
        $currentTime = $startTime;
        while ($currentTime < $endTime) {
            $slotTime = $currentTime->format('H:i');
            VenueSloting::create([
                'venue_address_id' => $venueId,
                'slot_time' => $slotTime,
            ]);
            $currentTime->addMinute($slotDuration); // Move to the next minute
        }
        return response()->json(['message' => 'Time slots created successfully'], 200);
    }

    private function createConferencePost($roomName)
    {

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $room = $twilio->video->v1->rooms->create([
            'uniqueName' =>  $roomName,
            'type' => 'peer-to-peer',
        ]);
        return ['room_name' => $roomName, 'room_sid' => $room->sid];
    }
}
