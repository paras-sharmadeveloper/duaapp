<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vistors,VenueSloting,VenueAddress};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiteAdminController extends Controller
{
    //
    public function ShowQueue(){
        $role = Auth::user()->roles->pluck('name')->first();
        if($role == 'admin'){
            $venueAddress = VenueAddress::where(['type' =>'on-site'])
           //  ->where('venue_date','>',date('Y-m-d'))
            ->orderBy('venue_date','asc')
            ->get();
        }else{
            $venueAddress = VenueAddress::where(['type' =>'on-site','siteadmin_id'=>Auth::user()->id])->get();
        }



       return view('site-admin.select-venue',compact('venueAddress'));
    }
    public function ShowQueueList(Request $request, $id){
        if($request->ajax()){
            // $venueSloting = VenueSloting::with('visitors','venueAddress')
            // ->where(['venue_address_id' => $id])
            // ->has('visitors') // Include only records with visitors
            // ->get();
            $venueSloting = VenueSloting::with(['visitors' => function ($query) {
                $query->orderBy('confirmed_at', 'asc');
            }, 'venueAddress'])
            ->where('venue_address_id', $id)
            ->has('visitors')
            ->get();
            $venueSloting->each(function ($item) {
                $item->visitors = $item->visitors->sortBy('confirmed_at');
            });

            return response()->json(['success' => true , 'data' => $venueSloting],200);
        }
        $venueSloting = VenueSloting::with('visitors','venueAddress')
            ->where(['venue_address_id' => $id])
            ->has('visitors') // Include only records with visitors
            ->get();


            return view('site-admin.verify-user-list',compact('venueSloting'));
        //return view('site-admin.queue-list',compact('venueSloting'));
    }

    public function VisitorUpdate(Request $request, $id){
        $update =[];
        $vistor = Vistors::find($id);

        $timezone = $vistor->venueSloting->venueAddress->timezone;
        $currentTime = Carbon::parse(date('Y-m-d H:i:s'));
        $now = $currentTime->timezone($timezone);

        $startAt = Carbon::parse($now->format('Y-m-d H:i:s'));
        $endAt = Carbon::parse($now->format('Y-m-d H:i:s'));


        if($request->input('type') == 'start'){

            $prevVisitor = Vistors::where('user_status', 'in-meeting')
            ->whereDate('meeting_start_at', '=', date('Y-m-d'))
            ->latest('meeting_start_at')
            ->first();

            if ($prevVisitor) {
                $totalTimeSpent = $prevVisitor->meeting_start_at->diffInSeconds($endAt);

                $prevVisitor->update([
                    'meeting_ends_at' =>  $endAt,
                    'user_status' => 'meeting-end',
                    'meeting_total_time' => $totalTimeSpent
                ]);
            }

            $update = [
                'meeting_start_at' => $startAt,
                'user_status' => 'in-meeting'
            ];


        }else if($request->input('type') == 'end'){

            $totalTimeSpent = $startAt->diffInSeconds($endAt);
            $update = [
                'meeting_ends_at' =>  $endAt,
                'user_status' => 'meeting-end',
                'meeting_total_time' => $totalTimeSpent
            ];

        }else if($request->input('type') == 'verify'){
            $update = [
                'confirmed_at' => $now->format('Y-m-d H:i:s'),
                'user_status' => 'admitted',
                'is_available' => 'confirmed'
            ];

        }
        $vistor->update( $update);
        return response()->json(['success' => true]);
    }

    public function WaitingQueueShow(Request $request,$id){

        $visitors = Vistors::join('venues_sloting', 'vistors.slot_id', '=', 'venues_sloting.id')
           ->join('venue_addresses', 'venues_sloting.venue_address_id', '=', 'venue_addresses.id')
            ->where(['venues_sloting.venue_address_id' => $id])
            ->where(['vistors.meeting_ends_at' => null])
            ->where(['vistors.user_status' => 'admitted'])->orWhere(['vistors.user_status' => 'in-meeting'])
            ->select('vistors.*', 'venues_sloting.*','venue_addresses.*')
            // ->orderBy('venues_sloting.slot_time', 'asc')
            ->orderBy('vistors.booking_number', 'asc')
            ->get();

            if($request->ajax()){
                return response()->json(['status' => true , 'data' => $visitors]);
            }

            $venueAddress = VenueAddress::find($id);


        return view('frontend.waiting-queue',compact('visitors' , 'venueAddress'));
    }


}
