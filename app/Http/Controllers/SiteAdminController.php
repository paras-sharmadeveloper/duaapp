<?php

namespace App\Http\Controllers;

use App\Jobs\WhatsAppConfirmation;
use Illuminate\Http\Request;
use App\Models\{Vistors, VenueSloting, VenueAddress,VisitorTempEntry};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class SiteAdminController extends Controller
{
    //

    public function manualToken(){
        $venueAddress = VenueAddress::where(['type' => 'on-site'])
                ->whereDate('venue_date', '>=', date('Y-m-d'))
                ->orderBy('venue_date', 'asc')
                ->first();
        $slots = VenueSloting::where(['venue_address_id' => $venueAddress->id])
        ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
        ->orderBy('slot_time', 'ASC')
        ->get();
        $visitorList = VisitorTempEntry::whereDate('created_at',date('Y-m-d'))->orderBy('id','asc')->get();
        // echo "<pre>"; print_r($venueAddress); die;
        return view('site-admin.manualToken',compact('venueAddress','slots','visitorList'));

    }

    public function manualTokenStore(Request $request){
        $vaildation = [
            'mobile' => 'required|string|digits:10|max:10',
            'user_question' => 'nullable|string',
            'country_code' => 'required'
        ];

        try {
            $slot = VenueSloting::find( $request->input('slot_id'));
            $uuid = Str::uuid()->toString();
            $booking = new Vistors;

            $booking->country_code = $request->input('country_code');
            $booking->phone = $request->input('phone');
            $booking->slot_id =  $request->input('slot_id');
            $booking->booking_uniqueid = $uuid;
            $booking->user_ip =   $request->ip();
            $booking->booking_number =$slot->token_id;
            $booking->user_timezone = $request->input('timezone', null);
            $booking->source = 'Website';
            $booking->dua_type = $request->input('dua_type');
            $booking->lang = $request->input('lang', 'en');
            $booking->save();
            $bookingId = $booking->id;
            WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');
            return redirect()->back()->with('success', 'Token Issued');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            //throw $th;
        }




    }
    public function ShowQueue()
    {
        $role = Auth::user()->roles->pluck('name')->first();
        if ($role == 'admin') {
            $venueAddress = VenueAddress::where(['type' => 'on-site'])
                ->whereDate('venue_date', '>=', date('Y-m-d'))
                // ->where('venue_date','>=',date('Y-m-d'))
                ->orderBy('venue_date', 'asc')
                ->get();
        } else {
            $venueAddress = VenueAddress::where(['type' => 'on-site', 'siteadmin_id' => Auth::user()->id]) ->whereDate('venue_date', '>=', date('Y-m-d'))
            // ->where('venue_date','>=',date('Y-m-d'))
            ->orderBy('venue_date', 'asc')
            ->get();;
        }
        return view('site-admin.select-venue', compact('venueAddress'));
    }


    public function fetchDuaDumTokens(){

        $q = Vistors::where('dua_type','dua')->whereIn('user_status' ,['admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $q2 = Vistors::where('dua_type','dum')->whereIn('user_status' ,['admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $q3 = Vistors::where('dua_type','working_lady_dua')->whereIn('user_status' ,['admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $q4 = Vistors::where('dua_type','working_lady_dum')->whereIn('user_status' ,['admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $data['dua'] = $q->first();
        $data['dum'] = $q2->first();

        $data['working_dua'] = $q3->first();
        $data['working_dum'] = $q4->first();
        if(!$q->count() >= 1){
            $data['dua'] =Vistors::where('dua_type','dua')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }

        if(!$q2->count() >= 1){
            $data['dum'] =Vistors::where('dua_type','dum')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }

        if(!$q3->count() >= 1){
            $data['working_dua'] =Vistors::where('dua_type','working_lady_dua')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }

        if(!$q4->count() >= 1){
            $data['working_dum'] =Vistors::where('dua_type','working_lady_dum')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }


        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function VisitorUpdate(Request $request, $id)
    {
        $update = [];
        $vistor = Vistors::find($id);

        $timezone = $vistor->venueSloting->venueAddress->timezone;
        $currentTime = Carbon::parse(date('Y-m-d H:i:s'));
        $now = $currentTime->timezone($timezone);

        $startAt = Carbon::parse($now->format('Y-m-d H:i:s'));
        $endAt = Carbon::parse($now->format('Y-m-d H:i:s'));

        if ($request->input('type') == 'start') {

            $duaType = $request->input('duaType');

            $prevVisitor = Vistors::where('user_status', 'in-meeting')
                ->whereDate('meeting_start_at', '=', date('Y-m-d'))
                ->where('dua_type',  $duaType)
                ->orderBy('id', 'desc')
                ->first();

            // return $prevVisitor;
            if ($prevVisitor) {
                $prevMeetingStartAt = Carbon::parse($prevVisitor->meeting_start_at);
                $totalTimeSpent = $prevMeetingStartAt->diffInSeconds($endAt);

                //  $totalTimeSpent = $prevVisitor->meeting_start_at->diffInSeconds($endAt);
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
        } else if ($request->input('type') == 'end') {

            $totalTimeSpent = $startAt->diffInSeconds($endAt);
            $update = [
                'meeting_ends_at' =>  $endAt,
                'user_status' => 'meeting-end',
                'meeting_total_time' => $totalTimeSpent
            ];
        } else if ($request->input('type') == 'verify') {
            $update = [
                'confirmed_at' => $now->format('Y-m-d H:i:s'),
                'user_status' => 'admitted',
                'is_available' => 'confirmed'
            ];
        }
        $vistor->update($update);

        $query = Vistors::where('dua_type','dua')->where(['user_status' => 'admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $query2 = Vistors::where('dua_type','dum')->where(['user_status' => 'admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $query3 = Vistors::where('dua_type','working_lady_dua')->where(['user_status' => 'admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $query4 = Vistors::where('dua_type','working_lady_dum')->where(['user_status' => 'admitted'])
        ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc');

        $data['dua'] =   $query->first();
        $data['dum'] =   $query2->first();
        $data['working_dua'] =   $query3->first();
        $data['working_dum'] =   $query4->first();

        if($query->count() == 0){
            $data['dua'] =Vistors::where('dua_type','dua')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }

        if($query2->count() == 0){
            $data['dum'] =Vistors::where('dua_type','dum')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }

        if($query3->count() == 0){
            $data['working_dua'] =Vistors::where('dua_type','working_lady_dua')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }

        if($query4->count() == 0){
            $data['working_dum'] =Vistors::where('dua_type','working_lady_dum')->whereIn('user_status' ,['in-meeting'])
            ->whereDate('created_at',date('Y-m-d'))->whereNotNull('confirmed_at')->orderBy('slot_id', 'asc')->first();
        }
        return response()->json(['success' => true , 'data' => $data]);
    }


    public function ShowQueueList(Request $request, $id)
    {

        $route = request()->route()->getName();


        if ($request->ajax()) {
            $from = $request->input('from');

            if ($from == 'siteadmin.pending.list') {

                $venueSloting = VenueSloting::with(['visitors' => function ($query) {
                    $query->where('user_status', 'no_action');
                    $query->orWhere('is_available', 'not_confirmed');
                    $query->orderBy('confirmed_at', 'asc');
                }, 'venueAddress'])
                    ->where('venue_address_id', $id)
                    ->has('visitors')
                    ->get();
                    return response()->json(['success' => true, 'data' => $venueSloting, 'route' => $route], 200);
            } else {


                $data['dua'] = VenueSloting::with(['visitors' => function ($query) {
                    //  $query->where('user_status', 'admitted');

                    },'venueAddress'])
                        ->where('venue_address_id', $id)
                        // ->where('type', 'dua')
                        ->has('visitors')
                        ->first();

                    $data['dum'] = VenueSloting::with(['visitors' => function ($query) {
                        $query->where('user_status', 'admitted');
                        $query->whereNull('confirmed_at');
                        // $query->orWhere('user_status', 'in-meeting');
                        // $query->orderBy('confirmed_at', 'asc');
                    }, 'venueAddress'])
                        ->where('venue_address_id', $id)
                        ->where('type', 'dum')
                        ->has('visitors')
                        ->first();
                        $data['dua'] = Vistors::with(['venueSloting'])->get();
                return response()->json(['success' => true, 'data' => $data, 'route' => $route], 200);
            }

        }


        // $venueSloting = VenueSloting::with('visitors','venueAddress')
        // ->where(['venue_address_id' => $id])
        // ->has('visitors') // Include only records with visitors
        // ->get();

        $venueSloting = VenueSloting::with(['visitors' => function ($query) {
            // $query->where('source', 'Phone');
            $query->where('user_status', 'admitted');
        }, 'venueAddress'])
            ->where('venue_address_id', $id)
            ->has('visitors')
            ->get();
        // return view('site-admin.verify-phone-ivr', compact('venueSloting', 'route'));
        if(request()->routeIs('siteadmin.pending.list')) {

            return view('site-admin.verify-user-list',compact('venueSloting' , 'route'));
        }else{
            return view('site-admin.test',compact('venueSloting' , 'route'));
        }




    }


    public function searchVisitors(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('id');
        $type = $request->input('type');
        if ($type == 'token') {

            if (!empty($search)) {

                $venueSloting = VenueSloting::with('visitors')
                    ->whereHas('visitors', function ($query) use ($search) {
                        // $query->where('source', 'Phone');
                        $query->whereIn('user_status', ['no_action', 'admitted', 'in-meeting']);
                        //  $query->where('user_status', 'no_action');
                        $query->where('booking_number', $search);
                        // ->orWhere('user_status', 'like', '%' . $search . '%')
                        // ->orWhere('country_code', 'like', '%' . $search . '%')
                        // ->orWhere('phone', 'like', '%' . $search . '%');
                    })
                    ->where('venue_address_id', $id)
                    ->get();
            } else {
                $venueSloting = VenueSloting::with('visitors')
                    ->whereHas('visitors', function ($query) use ($search) {
                        // $query->where('source', 'Phone');
                        $query->whereIn('user_status', ['no_action', 'admitted', 'in-meeting']);
                        //  $query->where('user_status', 'no_action');
                        // $query->where('booking_number', $search);
                        // ->orWhere('user_status', 'like', '%' . $search . '%')
                        // ->orWhere('country_code', 'like', '%' . $search . '%')
                        // ->orWhere('phone', 'like', '%' . $search . '%');
                    })
                    ->where('venue_address_id', $id)
                    ->get();
            }
        } else {

            if (!empty($search)) {

                $venueSloting = VenueSloting::with('visitors')
                    ->whereHas('visitors', function ($query) use ($search) {
                        //  $query->where('source', 'Phone');
                        $query->whereIn('user_status', ['no_action', 'admitted', 'in-meeting']);
                        //  $query->where('user_status', 'no_action');
                        $query->where('booking_number', 'like', '%' . $search . '%')
                            ->orWhere('user_status', 'like', '%' . $search . '%')
                            ->orWhere('country_code', 'like', '%' . $search . '%')
                            ->orWhere('source', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    })

                    ->where('venue_address_id', $id)
                    ->get();
            } else {
                $venueSloting = VenueSloting::with('visitors')
                    ->whereHas('visitors', function ($query) use ($search) {
                        //  $query->where('source', 'Phone');
                        $query->whereIn('user_status', ['no_action', 'admitted', 'in-meeting']);
                        //  $query->where('user_status', 'no_action');
                        // $query->where('booking_number', 'like', '%' . $search . '%')
                        //     ->orWhere('user_status', 'like', '%' . $search . '%')
                        //     ->orWhere('country_code', 'like', '%' . $search . '%')
                        //     ->orWhere('source', 'like', '%' . $search . '%')
                        //     ->orWhere('phone', 'like', '%' . $search . '%');
                    })

                    ->where('venue_address_id', $id)
                    ->get();
            }
        }



        return view('site-admin.search-div', compact('venueSloting'));
    }

    public function verifyPhoneIvr($id)
    {
        $venueSloting = VenueSloting::with(['visitors' => function ($query) {
            $query->where('user_status', 'admitted');
            $query->orWhere('user_status', 'in-meeting');
            $query->orderBy('confirmed_at', 'asc');
        }, 'venueAddress'])
            ->where('venue_address_id', $id)
            ->has('visitors')
            ->get();


        return view('site-admin.verify-phone-ivr', compact('venueSloting', 'route'));
    }





    public function WaitingQueueShow(Request $request, $id)
    {

        $visitors = Vistors::join('venues_sloting', 'visitors.slot_id', '=', 'venues_sloting.id')
            ->join('venue_addresses', 'venues_sloting.venue_address_id', '=', 'venue_addresses.id')
            ->where(['venues_sloting.venue_address_id' => $id])
            ->where(['visitors.meeting_ends_at' => null])
            ->where(['visitors.user_status' => 'in-meeting'])
            // ->where(['visitors.user_status' => 'admitted'])
            // ->orWhere(['visitors.user_status' => 'in-meeting'])
            ->select('visitors.*', 'venues_sloting.*', 'venue_addresses.*')
            // ->orderBy('venues_sloting.slot_time', 'asc')
            ->orderBy('visitors.booking_number', 'asc')
            ->get();

        if ($request->ajax()) {
            return response()->json(['status' => true, 'data' => $visitors]);
        }

        $venueAddress = VenueAddress::find($id);


        return view('frontend.waiting-queue', compact('visitors', 'venueAddress'));
    }
}
