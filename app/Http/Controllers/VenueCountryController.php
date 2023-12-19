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
        //  echo "<pre>"; print_r( $countryList); die;
        $cityList = [];
        $venueCityArr = []; 
        return view('venues.venueCountry',compact('countryList','cityList','venueCityArr'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // echo "<pre>"; print_r($request->all()); die; 
        $request->validate([
            'country_name' => 'required', 
            'flag_path' => 'required|mimes:jpeg,png,jpg,gif|max:2048',  
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
            'iso' => $request->input('iso')
        ]); 
        
        return redirect()->route('country.edit',$venue->id)->with(['success' => 'Country created successfully', 'is_true' => true]);
  

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
        // echo "<pre>"; print_r( $countryList); die; 
        $venueCityStates = VenueStateCity::where(['venue_id' => $id])->get(); 
         
        return view('venues.venueCountry', compact('venue','countryList','venueCityStates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $venue = VenueCountry::findOrFail($id);
        $request->validate([ 'country_name' => 'required']); 
      

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
        $states = VenueStateCity::where('venue_id', $request->venue_id)->get();
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get();
        return response()->json($cities);
    }

    public function CityImagesUplaod(Request $request){
        $request->validate([
            'state_name' => 'required|string',
            'city_name' => 'required|string',
            'columns_to_show' => 'required|integer',
            'city_sequence_to_show' =>  'required',
            'city_image' => 'image|mimes:jpeg,png,gif|max:2048',  
        ]);

        $state = $request->input('state_name');
        $id = $request->input('id');
        $city = $request->input('city_name');
        $columnToShow = $request->input('columns_to_show');
        $city_sequence_to_show = $request->input('city_sequence_to_show');
        $combinationName = $request->input('state_name'). '_' .$request->input('city_name')  ;
        $imageName = ''; 
        if ($request->hasFile('city_image')) {
            $image = $request->file('city_image');
            $imageName = time() . 'city_image.' . $image->getClientOriginalExtension();
            Storage::disk('s3_general')->put('city_image/' . $imageName, file_get_contents($image));
          
        }  
        $update = [
            'venue_id' => $request->input('venue_id'),
            'state_name' => $state , 
            'city_name' => $city, 
            'columns_to_show' => $columnToShow,
            'combination_name' => $combinationName,
            'city_sequence_to_show' =>  $city_sequence_to_show
            
        ]; 
        
        if($request->input('id')){
            $venueCity = VenueStateCity::find($id);
            $update['city_image'] = ($imageName) ? $imageName :  $venueCity->city_image;
            $imageName = $update['city_image']; 
            $venueCity->update($update);
            return response()->json(['message' => 'Form update successfully','update' => true , 'image' =>  $imageName , 'id' =>  $id], 200);
        }else{ 
            $update['city_image'] = $imageName ;
           $vnd =  VenueStateCity::create($update);
            return response()->json(['message' => 'Form submitted successfully','update' => false , 'image' =>  $imageName,'id' =>  $vnd->id ], 200);

        }

        
        



    }
    public function CityImagesRemove(Request $request)
    {
        $id = $request->input('id'); 
        VenueStateCity::find($id)->delete(); 
        return response()->json(['message' => 'Deleted'], 200);
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
