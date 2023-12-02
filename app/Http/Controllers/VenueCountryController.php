<?php

namespace App\Http\Controllers;

use App\Models\{VenueCountry,Country,City,State,VenueStateCity};
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
        $countryList = Country::all();
        $cityList = City::all();
        $venueCityArr = []; 
        return view('venues.venueCountry',compact('countryList','cityList','venueCityArr'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required', 
            'flag_path' => 'required|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size limits
            'city_name.*' => 'required', 
            'city_image.*' => 'required|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        $cityArr = $request->input('city_id'); 
        $cityImagArr = $request->file('city_image'); 
        $stateArr = $request->input('state_id'); 
        $StateNameArr = $request->input('state_name'); 
        $CityNameArr = $request->input('city_name');  
        
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
            'iso' => $request->input('iso')
        ]); 
        // echo "<pre>"; print_r($cityImagArr); die; 
        // foreach($cityArr as $k => $city){
        //     $imageNameAdd = time() . 'city_.' .$cityImagArr[$k]->getClientOriginalExtension();
           
        //     $combinationNAme =  $request->input('country_name').'_'. $StateNameArr[$k] . '_' . $CityNameArr[$k] ; 

        //     Storage::disk('s3_general')->put('city_image/' . $imageNameAdd, file_get_contents($cityImagArr[$k]));
        //     VenueStateCity::create([
        //         'venue_id' =>  $venue->id,
        //         'country_id' => $request->input('country_id'),
        //         'state_id' => $stateArr[$k],
        //         'city_id' =>    $cityArr[$k], 
        //         'city_image' => $imageNameAdd, 
        //         'combination_name' => $combinationNAme
        //     ]);
        // }
        return redirect()->back()->with('success', 'Country created successfully');

        // return redirect()->route('country.index')->with('success', 'Country created successfully');

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
        $countryList = Country::all();
        $cities = City::get(); 
        $states = State::get(); 
        $venueCityArr = VenueStateCity::where(['venue_id' => $id])->get(); 
        $editData = []; 
         foreach( $venueCityArr as  $cs){
              
            $editData['states'] = State::where(['country_id' => $cs['country_id']])->get()->toArray(); 
            $editData['all'][] = $cs->toArray(); 
            $editData['state_name'][] = $cs->state->name; 
            $editData['city_name'][] = $cs->city->name;
            $editData['cities'][$cs['state_id']] = City::where(['state_id' => $cs['state_id']])->get()->toArray();
         }
        // echo "<pre>"; print_r($editData); die; 
        return view('venues.venueCountry', compact('venue','countryList','cities','editData','states'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $venue = VenueCountry::findOrFail($id);
        $request->validate([ 'country_name' => 'required']);
        $cityArr = $request->input('city_id'); 
        $cityImagArr = $request->file('city_image'); 
        $stateArr = $request->input('state_id'); 
        $StateNameArr = $request->input('state_name'); 
        $CityNameArr = $request->input('city_name');
        $VenueCityArrIds = $request->input('venue_city_id');
      

        $imageName  = null;
        if ($request->hasFile('flag_path')) {
            $image = $request->file('flag_path');
            $imageName = time() . 'flag.' . $image->getClientOriginalExtension();
            Storage::disk('s3_general')->put('flags/' . $imageName, file_get_contents($image));
          
        }  
        $venue->update([
            'country_name' => $request->input('country_name'),
            'flag_path' =>  ($imageName) ? $imageName : $venue->flag_path,
            'iso' => $request->input('iso')
       
        ]);
        
        return redirect()->back()->with('success', 'Country updated successfully');
    }


    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country_id)->get();
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get();
        return response()->json($cities);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {   $venue = VenueCountry::findOrFail($id);
        $venue->delete(); 
        return redirect()->route('country.index')->with('success', 'Country deleted successfully');
    }
}
