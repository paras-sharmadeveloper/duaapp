<?php

namespace App\Http\Controllers;

use App\Models\VenueCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $venues = VenueCountry::all();
        return view('venues.listCountry',compact('venues'));
         
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('venues.venueCountry');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required',
            'type' => 'required', 
            'flag_path' => 'required|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size limits

        ]);

        $imageName  = null;
        if ($request->hasFile('flag_path')) {
            $image = $request->file('flag_path');
            $imageName = time() . 'flag.' . $image->getClientOriginalExtension();
            Storage::disk('s3_general')->put('flags/' . $imageName, file_get_contents($image));
            // $image->move(public_path('/flags'), $imageName); 
            
        } 

        $venue = VenueCountry::create([
            'country_name' => $request->input('country_name'),
            'flag_path' =>  $imageName,
            'type' => $request->input('type'),
        ]); 
        return redirect()->route('country.index')->with('success', 'Country created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(VenueCountry $venueCountry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VenueCountry $venueCountry,$id)
    {
        $venue = VenueCountry::findOrFail($id);
       
        return view('venues.venueCountry', compact('venue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $venue = VenueCountry::findOrFail($id);
        $request->validate([
            'country_name' => 'required',
            'type' => 'required', 

        ]);
        $imageName  = null;
        if ($request->hasFile('flag_path')) {
            $image = $request->file('flag_path');
            $imageName = time() . 'flag.' . $image->getClientOriginalExtension();
            Storage::disk('s3_general')->put('flags/' . $imageName, file_get_contents($image));
           // $image->move(public_path('/flags'), $imageName); 
            
        } 
        $venue->update([
            'country_name' => $request->input('country_name'),
            'flag_path' =>  ($imageName) ? $imageName : $venue->flag_path,
            'type' => $request->input('type'),
        ]);
        return redirect()->route('country.index')->with('success', 'Country updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VenueCountry $venueCountry)
    {
        $venueCountry->delete(); 
        return redirect()->route('country.index')->with('success', 'Country deleted successfully');
    }
}
