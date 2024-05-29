<?php

namespace App\Http\Controllers;
use App\Models\{Venue, Reason , VenueSloting, VenueAddress, Vistors, Country, User, Notification, Timezone, Ipinformation, VenueStateCity};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Role;

class NewBookingController extends Controller
{
    //

    public function index(Request $request, $locale = ''){
        // if (!isMobileDevice($request)) {
        //     return abort('403');
        // }
        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }
        $userAll = Vistors::whereDate('created_at',date('Y-m-d'))->get(['recognized_code', 'id'])->toArray();



        // echo $locale; die;

        $therapistRole = Role::where('name', 'therapist')->first();
        $VenueList = Venue::all();
        $countryList = Country::all();
        $therapists = $therapistRole->users;
        $timezones = Country::with('timezones')->get();
        $reasons = Reason::where(['type' => 'announcement'])->first();
        return view('frontend.multistep.index', compact('VenueList', 'countryList', 'therapists', 'timezones', 'locale', 'reasons'));
    }

    public function ShowFilterPage(Request $request){
        $visitors = [];
        if($request->input('date')){
            $visitors =  Vistors::whereDate('created_at',$request->input('date'))->get();
        }
        return view('filters',compact('visitors'));
    }

    public function StatusLead(Request $request,$id){

        Vistors::find($id)->update([
            'token_status' => $request->input('status')
        ]);
        return redirect()->back()->with(['success' => 'Status updated']);

    }

    public function handleStatusUpdate(Request $request)
    {
        // Extract information from the Twilio webhook request
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        // Update your database or take any other necessary action based on the status update
        // Example: Update the message status in the database
        $message = Vistors::where('msg_sid', $messageSid)->first();
        if ($message) {
            $message->msg_sent_status = $status;
            $message->save();
        }

        // Respond to Twilio's webhook request with a 200 OK status
        return response()->json(['status' => 'success'], 200);
    }


}
