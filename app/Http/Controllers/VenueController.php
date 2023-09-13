<?php

namespace App\Http\Controllers;

use App\Models\{Venue, VenueSloting, VenueAddress,User};
use Illuminate\Http\Request;
use Carbon\Carbon;

class VenueController extends Controller
{
    public function index()
    {
        $venuesAddress = VenueAddress::all();
        return view('venues.list', compact('venuesAddress'));
    }

    public function venueCountry()
    {
        return view('venues.venueCountry');
    }
    public function venueCountryEdit($id)
    {
        $venue = Venue::findOrFail($id);
       
        return view('venues.venueCountry', compact('venue'));
    }
    public function venueCountryShow()
    {
        return view('venues.venueCountry');
    }
    public function show()
    {
        return view('venues.venueCountry');
    }

    public function venueCountryList(){
        $venues = Venue::all();
        return view('venues.listCountry',compact('venues'));
    }


    public function create()
    {
        $countries = Venue::all();  
        $therapists = User::whereHas('roles', function ($query) {
            $query->where('name', 'therapist');
        })->get();  
        return view('venues.create',compact('countries','therapists'));
    }
    public function venueCountryUpdate(Request $request,$id){
        $venue = Venue::findOrFail($id);
        $request->validate([
            'country_name' => 'required',
            'type' => 'required',
            'flag_path' => 'required|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size limits

        ]);
        if ($request->hasFile('flag_path')) {
            $flagPath = $request->file('flag_path')->storeAs('public/flags', $request->file('flag_path')->getClientOriginalName());
        } else {
            $flagPath = null;
        }
        $venue->update([
            'country_name' => $request->input('country_name'),
            'flag_path' =>  $flagPath,
            'type' => $request->input('type'),
        ]);
    }

    public function venueCountryStore(Request $request){
        $request->validate([
            'country_name' => 'required',
            'type' => 'required', 
            'flag_path' => 'required|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size limits

        ]);
 



        if ($request->hasFile('flag_path')) {
            $imageName = time() . '.' . $request->flag_path->extension();
            $flagPath = $request->flag_path->move(public_path('images'), $imageName);

            //            $flagPath = $request->file('flag_path')->storeAs('links', $request->file('flag_path')->getClientOriginalName());
        } else {
            $imageName = null;
        }

        $venue = Venue::create([
            'country_name' => $request->input('country_name'),
            'flag_path' =>  $imageName,
            'type' => $request->input('type'),
        ]); 
        return redirect()->route('venues.index')->with('success', 'Venue created successfully');



    }
    public function store(Request $request)
    {
        // Validate the request data

        $request->validate([
            'venue_id' => 'required',
            'therapist_id' =>'required',
            'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            'venue_starts' => 'required',
            'venue_ends' => 'required', 

        ]);

        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venueStarts = $request->input('venue_starts');
        $venueEnds = $request->input('venue_ends');
 
            $venueAddress =   VenueAddress::create([
                'address' => $venueAdd,
                'venue_date' => $venueDate,
                'slot_starts_at' =>  $venueStarts,
                'slot_ends_at' =>  $venueEnds,
                'venue_id' => $request->input('venue_id'),
                'therapist_id' => $request->input('therapist_id'),
                'type' => $request->input('type')

            ]);
            $this->createVenueTimeSlots($venueAddress->id);
         
        return redirect()->route('venues.index')->with('success', 'Venue created successfully');
    }

    public function edit($id)
    {
        $venueAddress = VenueAddress::findOrFail($id);
        $countries = Venue::all();  
        $therapists = User::whereHas('roles', function ($query) {
            $query->where('name', 'therapist');
        })->get(); 

        return view('venues.create', compact('venueAddress','countries','therapists'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
         
        $VenueAddress = VenueAddress::findOrFail($id);
        $request->validate([
            'venue_id' => 'required',
            'therapist_id' =>'required',
            'type' => 'required',
            'venue_date' => 'required',
            'venue_addresses' => 'required',
            'venue_starts' => 'required',
            'venue_ends' => 'required', 

        ]);
        
        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venueStarts = $request->input('venue_starts');
        $venueEnds = $request->input('venue_ends');
         
        $VenueAddress->update([
            'address' => $venueAdd,
            'venue_date' => $venueDate,
            'slot_starts_at' =>  $venueStarts,
            'slot_ends_at' =>  $venueEnds,
            'venue_id' => $request->input('venue_id'),
            'therapist_id' => $request->input('therapist_id'),
            'type' => $request->input('type')

        ]);

         if($request->has('update_slots')){
            VenueSloting::where(['venue_address_id' => $id])->delete();
            $this->createVenueTimeSlots($id);
         }
            
        

        return redirect()->route('venues.index')->with('success', 'Venue updated successfully');
    }

    public function destroy($id)
    {
        $venue = Venue::findOrFail($id);
        $venue->delete();

        return redirect()->route('venues.index')->with('success', 'Venue deleted successfully');
    }

    protected function createVenueTimeSlots($venueId)
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
            $currentTime->addMinute(); // Move to the next minute
        }

        return response()->json(['message' => 'Time slots created successfully'], 200);
    }
}
