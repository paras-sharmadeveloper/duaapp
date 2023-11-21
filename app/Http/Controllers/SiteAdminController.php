<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vistors,VenueSloting,VenueAddress};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiteAdminController extends Controller
{
    //
    public function ShowQueue(){
        $role = Auth::user()->roles->pluck('name')->first(); 
        if($role == 'admin'){
            $venueAddress = VenueAddress::where(['type' =>'on-site'])->orderBy('venue_date','asc')
            ->get();  
        }else{
            $venueAddress = VenueAddress::where(['type' =>'on-site','siteadmin_id'=>Auth::user()->id])->get();  
        }
        
        return view('site-admin.select-venue',compact('venueAddress')); 
    }
    public function ShowQueueList(Request $request, $id){
        if($request->ajax()){
            $venueSloting = VenueSloting::with('visitors','venueAddress')
            ->where(['venue_address_id' => $id])
            ->has('visitors') // Include only records with visitors
            ->get();
            // $venueSloting = VenueSloting::with('visitors')->where(['venue_address_id' => $id])->get(); 
            return response()->json(['success' => true , 'data' => $venueSloting],200); 
        }
        $venueSloting = VenueSloting::with('visitors','venueAddress')
            ->where(['venue_address_id' => $id])
            ->has('visitors') // Include only records with visitors
            ->get();
            // echo "<pre>"; print_r($venueSloting); die; 
       
 
        return view('site-admin.queue-list',compact('venueSloting')); 
    }

    public function VisitorUpdate(Request $request, $id){
        
        if($request->input('type') == 'start'){
            $col ='meeting_start_at';
        }else{
            $col ='meeting_ends_at';
        }
        Vistors::find($id)->update([$col => date('Y-m-d H:i:s')]);
        return response()->json(['success' => true]); 
    }

    public function WaitingQueueShow(Request $request,$id){
        // $venueadd = VenueSloting::where([
        //     'venue_address_id' => $id
        // ])->get(); 
        
        return view('frontend.waiting-queue'); 
    }

    
}
