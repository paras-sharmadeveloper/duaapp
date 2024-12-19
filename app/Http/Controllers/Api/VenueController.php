<?php

namespace App\Http\Controllers\Api;

use App\Models\{Venue, VenueSloting, VenueAddress, User, Vistors, Timezone, Country, Reason};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Twilio\Rest\Client;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Auth;
use PDO;
use App\Jobs\{CreateVenuesSlots, CreateFutureDateVenues};
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $post = $request->all();
        // echo "<pre>"; print_r($post); die;
        if (isset($post['inactive']) && $post['inactive'] == 'true') {
            $venuesAddress = VenueAddress::whereDate('venue_date', '<=', date('Y-m-d'))->get();
            $visitors = Vistors::all();
        } else {
            $venuesAddress = VenueAddress::whereDate('venue_date', '>=', date('Y-m-d'))->get();
            $visitors = Vistors::all();
        }
        return view('venues.list', compact('venuesAddress', 'visitors'));
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
        $venueAddress = [];
        $venueCountry = Country::all();
        $reasons = Reason::where(['type' => 'reject_reason'])->get();
        return view('venues.create', compact('countries', 'therapists', 'siteAdmins', 'venueCountry', 'venueAddress', 'reasons'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'siteadmin_id' => 'required',
            'venue_date' => 'required|date',
            'venue_date_end' => 'required|date',
            'venue_addresses' => 'required',
            'city' => 'required',
            // 'slot_appear_hours' => 'required',
            'rejoin_venue_after' => 'required',
            'status_page_note' => 'required',
            'status_page_note_ur' => 'required',
            'venue_addresses_ur' => 'required',
            // 'swtich_dua' => 'required',
            // 'swtich_dum' => 'required',
        ]);

        // Conditional validation based on the values of 'swtich_dua' and 'swtich_dum'
        if ($request->input('swtich_dua') == 'on') {
            $request->validate([
                'dua_slots' => 'required|integer|between:1,1000',
            ]);
        } else {
            $request->validate([
                'reject_dua_id' => 'required|integer',
            ]);
        }

        if ($request->input('swtich_dum') == 'on') {
            $request->validate([
                'dum_slots' => 'required|integer|between:1001,2000',
            ]);
        } else {
            $request->validate([
                'reject_dum_id' => 'required|integer',
            ]);
        }



        $therapistRole = Role::where('name', 'therapist')->first();


        $thripistId = $therapistRole->id;

        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venue_date_end = $request->input('venue_date_end');
        // $venueStartsMorning = $request->input('slot_starts_at_morning');
        // $venueEndsMorning = $request->input('slot_ends_at_morning');
        // $venueStartsEvening = $request->input('slot_starts_at_evening', null);
        // $venueEndsEvening = $request->input('slot_ends_at_evening', null);
        // $slotDuration = $request->input('slot_duration');
        $IsRecuureing = $request->input('is_recurring');
        $duaSlots = $request->input('dua_slots');
        $dumSlots = $request->input('dum_slots');
        $SpecialTokenQuote = $request->input('special_token_quote');


        $recuureingTill = $request->input('recurring_till', 0);
        $rejoin_venue_after = $request->input('rejoin_venue_after', 0);
        $venue_available_country = json_encode($request->input('venue_available_country', 0));



        $dataArr = [];
        $dayToSet = [];
        // $roomDetail = [];
        // if ($request->input('video_room')) {
        //     $roomName = str_replace(' ', '_', $request->input('video_room'));
        //     $roomDetail =  $this->createConferencePost($roomName);
        // }


        $country = Venue::where(['iso' => 'PK'])->first();

        $timezone = Timezone::where(['country_code' => $country->iso])->first();

        $dataArr = [
            'city' => $request->input('city'),
            'combination_id' =>  $request->input('combination_id'),
            'state' =>  $request->input('state', null),
            'address' => $venueAdd,
            'venue_date' => $venueDate,
            'venue_date_end' => $venue_date_end,
            // 'slot_starts_at_morning' =>  $venueStartsMorning,
            // 'slot_ends_at_morning' =>  $venueEndsMorning,
            // 'slot_starts_at_evening' =>  $venueStartsEvening,
            // 'slot_ends_at_evening' =>  $venueEndsEvening,
            'venue_id' => $country->id,
            'therapist_id' =>  $thripistId,
            'siteadmin_id' =>  $request->input('siteadmin_id'),
            'type' => $request->input('type', 'on-site'),
            //'room_name' => (isset($roomDetail['room_name'])) ? $roomDetail['room_name'] : null,
            //'room_sid' => (isset($roomDetail['room_sid'])) ? $roomDetail['room_sid'] : null,
            //  'slot_duration' => $slotDuration,
            'recurring_till' => (!empty($recuureingTill)) ? $recuureingTill : 0,
            'selfie_verification' => ($request->has('selfie_verification')) ? 1 : 0,
            'rejoin_venue_after' => $rejoin_venue_after,
            // 'slot_appear_hours' => $request->input('slot_appear_hours'),
            'venue_available_country' => $venue_available_country,
            'dua_slots' => ($request->input('swtich_dua') == 'on') ? $duaSlots : 0,
            'dum_slots' => ($request->input('swtich_dum') == 'on') ? $dumSlots : 0,
            'working_lady_dua' => $request->input('working_lady_dua',0),
            'working_lady_dum' => $request->input('working_lady_dum',0),
            'special_token_quote' => $request->input('special_token_quote',0),
            'reject_dua_id' => ($request->input('swtich_dua') !== 'on' && $request->input('reject_dua_id')) ? $request->input('reject_dua_id') : null,
            'reject_dum_id' => ($request->input('swtich_dum') !== 'on' && $request->input('reject_dum_id')) ? $request->input('reject_dum_id') : null,

            // 'timezone' => $timezone->timezone,
            'status_page_note_ur' => $request->input('status_page_note_ur'),
            'address_ur' => $request->input('venue_addresses_ur'),
            'status_page_note' => $request->input('status_page_note'),
            'timezone' => $timezone->timezone
        ];

        if (!empty($IsRecuureing)) {
            foreach ($IsRecuureing as $key => $recuureing) {
                $dataArr['is_' . $key] = ($recuureing == 'on') ? 1 : 0;
                $dayToSet[] = $key;
            }
        }

        if (!empty($dayToSet)) {
            if (!VenueAddress::whereDate('venue_date', $venueDate)->where('venue_id', $dataArr['venue_id'])->exists()) {
                $venueAddress = VenueAddress::create($dataArr);
                $this->CraeteVenueSlots($venueAddress->id, $duaSlots, $dumSlots, $request->input('working_lady_dua'), $request->input('working_lady_dum'),$SpecialTokenQuote);
                // CreateVenuesSlots::dispatch($venueAddress->id)->onQueue('create-slots')->onConnection('database');
            }
            CreateFutureDateVenues::dispatch($dataArr, $dayToSet, $recuureingTill)->onQueue('create-future-dates')->onConnection("database");
        } else {

            $venueAddress =   VenueAddress::create($dataArr);
            $this->CraeteVenueSlots($venueAddress->id, $duaSlots, $dumSlots, $request->input('working_lady_dua'), $request->input('working_lady_dum'),$SpecialTokenQuote);
            // CreateVenuesSlots::dispatch($venueAddress->id)->onQueue('create-slots')->onConnection('database');
            // $this->createVenueTimeSlots($venueAddress->id, $slotDuration);
        }
        return redirect()->route('venues.index')->with('success', 'Venue Creating In backend . Please wait for few seconds');
    }

    public function edit($id)
    {
        $venueAddress = VenueAddress::findOrFail($id);

        $currentTime = Carbon::now()->tz($venueAddress->timezone ?? 'Asia/Karachi');

        if ($currentTime->gte($venueAddress->venue_date)) {
            return redirect()->route('venues.index')->with('error', 'Edit Not Possiable at the momment you can Pause the venue');
        }

        $countries = Venue::all();
        $therapists = User::whereHas('roles', function ($query) {
            $query->where('name', 'therapist');
        })->get();
        $siteAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'site-admin');
        })->get();
        $venueCountry = Country::all();
        $reasons = Reason::where(['type' => 'reject_reason'])->get();
        return view('venues.create', compact('venueAddress', 'countries', 'therapists', 'siteAdmins', 'venueCountry', 'reasons'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data

        $therapistRole = Role::where('name', 'therapist')->first();


        $thripistId = $therapistRole->id;

        $VenueAddress = VenueAddress::findOrFail($id);
        $request->validate([
            // 'venue_id' => 'required',
            // 'therapist_id' => 'required',
            // 'siteadmin_id' => 'required',
            // 'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            //'slot_starts_at_morning' => 'required',
            // 'slot_ends_at_morning' => 'required',
            'city' => 'required',
            // 'video_room' => 'required_if:type,virtual',
            // 'slot_duration' => 'required',
            // 'slot_appear_hours' => 'required',
            'rejoin_venue_after' => 'required',
            // 'combination_id' => 'required',
            'status_page_note' => 'required',
            'status_page_note_ur' => 'required',
            'venue_addresses_ur' => 'required',

        ]);
        // $country = Venue::find($request->input('venue_id'));

        $country = Venue::where(['iso' => 'PK'])->first();

        $timezone = Timezone::where(['country_code' => $country->iso])->first();
        // $roomDetail = [];
        // if ($request->input('video_room') !== $VenueAddress->room_name) {
        //     $roomDetail =  $this->createConferencePost($request->input('video_room'));
        // }
        $combination_id = $VenueAddress->combination_id;

        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venue_date_end = $request->input('venue_date_end');

        // $venueStartsMorning = $request->input('slot_starts_at_morning');
        // $venueEndsMorning = $request->input('slot_ends_at_morning');
        // $venueStartsEvening = $request->input('slot_starts_at_evening', null);
        // $venueEndsEvening = $request->input('slot_ends_at_evening', null);
        // $slotDuration = $request->input('slot_duration');
        $rejoin_venue_after = $request->input('rejoin_venue_after', 0);
        $venue_available_country = json_encode($request->input('venue_available_country', 0));

        $duaSlots = $request->input('dua_slots');
        $dumSlots = $request->input('dum_slots');
        $SpecialTokenQuote = $request->input('special_token_quote');

        $dataArr = [
            'city' => $request->input('city'),
            // 'combination_id' =>  $combination_id,
            // 'state' =>  $request->input('state', null),
            'address' => $venueAdd,

            'venue_date' => $venueDate,
            'venue_date_end' => $venue_date_end,
            // 'slot_starts_at_morning' =>  $venueStartsMorning,
            // 'slot_ends_at_morning' =>  $venueEndsMorning,
            // 'slot_starts_at_evening' =>  $venueStartsEvening,
            // 'slot_ends_at_evening' =>  $venueEndsEvening,
            'venue_id' => $VenueAddress->venue_id,
            'therapist_id' => $thripistId,
            'siteadmin_id' =>  $request->input('siteadmin_id'),
            'type' => $request->input('type', 'on-site'),
            //'room_name' => (isset($roomDetail['room_name'])) ? $roomDetail['room_name'] : null,
            //'room_sid' => (isset($roomDetail['room_sid'])) ? $roomDetail['room_sid'] : null,
            // 'slot_duration' => $slotDuration,
            // 'slot_appear_hours' => $request->input('slot_appear_hours'),
            'recurring_till' => $request->input('recurring_till'),
            // 'selfie_verification' => ($request->has('selfie_verification')) ? 1 : 0,
            'rejoin_venue_after' => $rejoin_venue_after,
            'venue_available_country' => $venue_available_country,
            'timezone' => $timezone->timezone,
            'status_page_note' => $request->input('status_page_note'),
            'status_page_note_ur' => $request->input('status_page_note_ur'),
            'address_ur' => $request->input('venue_addresses_ur'),


            'dua_slots' => ($request->input('swtich_dua') == 'on') ? $duaSlots : 0,
            'dum_slots' => ($request->input('swtich_dum') == 'on') ? $dumSlots : 0,
            'working_lady_dua' => $request->input('working_lady_dua',0),
            'working_lady_dum' => $request->input('working_lady_dum',0),
            'special_token_quote' => $request->input('special_token_quote',0),
            'reject_dua_id' => ($request->input('swtich_dua') !== 'on' && $request->input('reject_dua_id')) ? $request->input('reject_dua_id') : null,
            'reject_dum_id' => ($request->input('swtich_dum') !== 'on' && $request->input('reject_dum_id')) ? $request->input('reject_dum_id') : null,
        ];

        $VenueAddress->update($dataArr);

        if ($request->has('update_slots')) {
            if (VenueSloting::where(['venue_address_id' => $id])->exists()) {
                VenueSloting::where(['venue_address_id' => $id])->delete();
            }

            $this->CraeteVenueSlots($id, $duaSlots, $dumSlots, $request->input('working_lady_dua'), $request->input('working_lady_dum'),$SpecialTokenQuote);
            // CreateVenuesSlots::dispatch($id)->onQueue('create-slots')->onConnection('database');
            //  $this->createVenueTimeSlots($id, $slotDuration);
        }
        return redirect()->route('venues.index')->with('success', 'Venue updated successfully');
    }

    private function CraeteVenueSlots($venueId, $duaSlots, $dumSlots, $working_lady_dua, $working_lady_dum,$SpecialTokenQuote)
    {

        for ($token = 1; $token <= $duaSlots; $token++) {

            VenueSloting::create([
                'venue_address_id' => $venueId,
                'slot_time' =>  date("Y-m-d H:i:s"),
                'token_id' => $token,
                'type' => 'dua'
            ]);
        }

        for ($token = 1001; $token <= $dumSlots; $token++) {

            VenueSloting::create([
                'venue_address_id' => $venueId,
                'slot_time' => date("Y-m-d H:i:s"),
                'token_id' => $token,
                'type' => 'dum'
            ]);
        }
        if($working_lady_dua){
            for ($token = 801; $token <= $working_lady_dua; $token++) {

                VenueSloting::create([
                    'venue_address_id' => $venueId,
                    'slot_time' => date("Y-m-d H:i:s"),
                    'token_id' => $token,
                    'type' => 'working_lady_dua'
                ]);
            }
        }

        if($working_lady_dum){

            for ($token = 1801; $token <= $working_lady_dum; $token++) {

                    VenueSloting::create([
                        'venue_address_id' => $venueId,
                        'slot_time' => date("Y-m-d H:i:s"),
                        'token_id' => $token,
                        'type' => 'working_lady_dum'
                    ]);
                }
        }

        if($SpecialTokenQuote){

            for ($token = 2001; $token <= $SpecialTokenQuote; $token++) {

                    VenueSloting::create([
                        'venue_address_id' => $venueId,
                        'slot_time' => date("Y-m-d H:i:s"),
                        'token_id' => $token,
                        'type' => 'special_token'
                    ]);
                }
        }
    }

    private function RecurringDays($tillMonths, $day)
    {
        $currentDate = Carbon::now();
        $nextTwoMonths = $currentDate->copy()->addMonths($tillMonths);

        $mondaysInNextTwoMonths = [];

        while ($currentDate->lte($nextTwoMonths)) {
            if ($day == 'monday' && $currentDate->dayOfWeek === Carbon::MONDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day == 'tuesday' && $currentDate->dayOfWeek === Carbon::TUESDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day == 'wednesday' && $currentDate->dayOfWeek === Carbon::WEDNESDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day == 'thursday' && $currentDate->dayOfWeek === Carbon::THURSDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day == 'friday' && $currentDate->dayOfWeek === Carbon::FRIDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day == 'saturday' && $currentDate->dayOfWeek === Carbon::SATURDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            if ($day == 'sunday' && $currentDate->dayOfWeek === Carbon::SUNDAY) {
                $mondaysInNextTwoMonths[] = $currentDate->copy();
            }
            $currentDate->addDay();
        }
        $allDates = [];

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

        //  echo "<pre>"; print_r( $venueAddress); die;


        if (!$venueAddress) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        // Define start and end times
        $startTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at_morning);
        $endTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at_morning);

        if (!empty($venueAddress->slot_starts_at_evening) && !empty($venueAddress->slot_ends_at_evening)) {

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

    public function pauseResumeVenue(Request $request, $id)
    {
        $cuurentStatus  = ($request->input('status') == 'active') ? 'inactive' : 'active';
        VenueAddress::find($id)->update(['status' => $cuurentStatus]);
        return redirect()->back()->with(['success' => 'Updated']);
    }
}
