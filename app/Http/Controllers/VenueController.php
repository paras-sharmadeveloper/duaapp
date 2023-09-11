<?php

namespace App\Http\Controllers;

use App\Models\{Venue,VenueSloting,VenueAddress};
use Illuminate\Http\Request;
use Carbon\Carbon;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::all();
        return view('venues.list', compact('venues'));
    }

    public function create()
    {
        return view('venues.create');
    }

    public function store(Request $request)
    {
        // Validate the request data

        $request->validate([
            'country_name' => 'required',
            'type' => 'required', 
            'venue_date.0' => 'required',
            'venue_addresses.0' => 'required',
            'venue_starts.0' => 'required',
            'venue_ends.0' => 'required',
            'flag_path' => 'required|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size limits

        ]);

        $venueAdd = $request->input('venue_addresses');
        $venueDate = $request->input('venue_date');
        $venueStarts = $request->input('venue_starts');
        $venueEnds = $request->input('venue_ends');

         

        if ($request->hasFile('flag_path')) {
            $imageName = time().'.'.$request->flag_path->extension();
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
        foreach ($venueAdd as $i => $venues ){
          $venueAddress =   VenueAddress::create([
                'address' => $venues, 
                'venue_date' => $venueDate[$i],
                'slot_starts_at' =>  $venueStarts[$i],
                'slot_ends_at' =>  $venueEnds[$i],
                'venue_id' => $venue->id
            ]);
            $this->createVenueTimeSlots($venueAddress->id); 
        } 

        return redirect()->route('venues.index')->with('success', 'Venue created successfully');
    }

    public function edit($id)
    {
        $venue = Venue::findOrFail($id);
        return view('venues.create', compact('venue'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data

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
