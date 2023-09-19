<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vistors,VenueSloting,VenueAddress};
use Illuminate\Support\Facades\Storage;

class SiteAdminController extends Controller
{
    //
    public function ShowQueue(){
        $venueAddress = VenueAddress::get();  
        return view('site-admin.select-venue',compact('venueAddress')); 
    }
    public function ShowQueueList(Request $request, $id){
        if($request->ajax()){
            $venueSloting = VenueSloting::with('visitors')
            ->where(['venue_address_id' => $id])
            ->has('visitors') // Include only records with visitors
            ->get();
            // $venueSloting = VenueSloting::with('visitors')->where(['venue_address_id' => $id])->get(); 
            return response()->json(['success' => true , 'data' => $venueSloting],200); 
        }
       
 
        return view('site-admin.queue-list'); 
    }

    
}
