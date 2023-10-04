<?php

namespace App\Http\Controllers;

use App\Models\{Venue, VenueSloting, VenueAddress,User,Vistors};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Twilio\Rest\Client;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Auth;

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
        return view('venues.create',compact('countries','therapists','siteAdmins'));
    }
     
    
    public function store(Request $request)
    {
        // Validate the request data

        $request->validate([
            'venue_id' => 'required',
            'therapist_id' =>'required',
            'siteadmin_id' => 'required',
            'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            'venue_starts' => 'required',
            'venue_ends' => 'required', 
            'city' => 'required',
            'video_room' => 'required_if:type,virtual',
            'slot_duration' => 'required',
 
        ]);
        
        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venueStarts = $request->input('venue_starts');
        $venueEnds = $request->input('venue_ends'); 
        $slotDuration = $request->input('slot_duration');  

        $roomDetail = []; 
        if($request->input('video_room')){
            $roomDetail =  $this->createConferencePost($request->input('video_room'));
        }

            $venueAddress =   VenueAddress::create([
                'city' => $request->input('city'), 
                'state' =>  $request->input('state',null), 
                'address' => $venueAdd,
                'venue_date' => $venueDate,
                'slot_starts_at' =>  $venueStarts,
                'slot_ends_at' =>  $venueEnds,
                'venue_id' => $request->input('venue_id'),
                'therapist_id' => $request->input('therapist_id'),
                'siteadmin_id' =>  $request->input('siteadmin_id'),
                'type' => $request->input('type'),
                'room_name' =>  (isset($roomDetail['room_name'])) ? $roomDetail['room_name'] : null,
                'room_sid' =>  (isset($roomDetail['room_sid'])) ? $roomDetail['room_sid'] : null,  
                'slot_duration' => $slotDuration
            ]);
            $this->createVenueTimeSlots($venueAddress->id,$slotDuration);
         
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

        return view('venues.create', compact('venueAddress','countries','therapists','siteAdmins'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
         
        $VenueAddress = VenueAddress::findOrFail($id);
        $request->validate([
            'venue_id' => 'required',
            'therapist_id' =>'required',
            'siteadmin_id' => 'required',
            'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            'venue_starts' => 'required',
            'venue_ends' => 'required', 
            'city' => 'required',
            'video_room' => 'required_if:type,virtual',
            'slot_duration' => 'required',
 
        ]);
        $roomDetail = []; 
        if($request->input('video_room')!== $VenueAddress->room_name){
            $roomDetail =  $this->createConferencePost($request->input('video_room'));
        }       
        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venueStarts = $request->input('venue_starts');
        $venueEnds = $request->input('venue_ends');
        $slotDuration = $request->input('slot_duration');  
        $VenueAddress->update([
            'city' => $request->input('city'), 
            'state' =>  $request->input('state',null), 
            'address' => $venueAdd,
            'venue_date' => $venueDate,
            'slot_starts_at' =>  $venueStarts,
            'slot_ends_at' =>  $venueEnds,
            'venue_id' => $request->input('venue_id'),
            'therapist_id' => $request->input('therapist_id'),
            'siteadmin_id' =>  $request->input('siteadmin_id'),
            'type' => $request->input('type'),
            'room_name' =>  (isset($roomDetail['room_name'])) ? $roomDetail['room_name'] : null,
            'room_sid' =>  (isset($roomDetail['room_sid'])) ? $roomDetail['room_sid'] : null, 
            'slot_duration' => $slotDuration
        ]);

         if($request->has('update_slots')){
            
            VenueSloting::where(['venue_address_id' => $id])->delete();
            $this->createVenueTimeSlots($id , $slotDuration);
         }
            
        

        return redirect()->route('venues.index')->with('success', 'Venue updated successfully');
    }

    public function destroy($id)
    {
        // VenueSloting::where(['venue_address_id' => $id])->delete(); 
        $venue = Vistors::findOrFail($id);
        $venue->delete();
        return redirect()->route('venues.index')->with('success', 'Venue deleted successfully');
    }

    protected function createVenueTimeSlots($venueId,$slotDuration)
    {
        $venueAddress = VenueAddress::find($venueId);

        if (!$venueAddress) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        // Define start and end times
        $startTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_starts_at);
        $endTime = Carbon::createFromFormat('H:i:s', $venueAddress->slot_ends_at);


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

        private function createConferencePost($roomName){

             
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

            $room = $twilio->video->v1->rooms->create([
                'uniqueName' =>  $roomName,
                'type' => 'peer-to-peer',
            ]);
            return ['room_name' =>$roomName,'room_sid' => $room->sid ]; 



            // VideoConference::create(['room_name' =>$roomName,'room_sid' => $room->sid ]);
            // $message = "Hi ,\n Join Meeting here\n".route('join.conference.show',[$room->sid]); 
            // $this->SendMessage('+91','8950990009',$message); 

            // $userName = Auth::user()->name;   
            // $roomName = $this->fetchRoomName($room->sid); 
            // $accessToken = $this->generateAccessToken($roomName,$userName);
            // $room->sid
            //  $room->uniqueName $room->type 
            // return redirect()->route('join.conference.show',[$room->sid])->with([
            //     'accessToken' => $accessToken,
            //     'roomName' => $roomName,
            //     'success' => 'You joined this Meeting',
            //     'enable' => false,
            //     'roomId' => $room->sid
            // ]);  
        } 
}
