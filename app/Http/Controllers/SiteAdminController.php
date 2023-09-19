<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vistors,VenueSloting,VenueAddress};
class SiteAdminController extends Controller
{
    //
    public function ShowQueue(){
        $venueAddress = VenueAddress::get();  
        return view('site-admin.queue',compact('venueAddress')); 
    }
    public function ShowQueueList(Request $request, $id){
        if($request->ajax()){
            $venueSloting = VenueSloting::with('visitors')->where(['venue_address_id' => $id])->get();  
        }
       
 
        return view('site-admin.queue',compact('venueSloting')); 
    }

    
}
