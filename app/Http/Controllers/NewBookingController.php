<?php

namespace App\Http\Controllers;

use App\Jobs\WhatsAppTokenNotBookNotifcation;
use App\Models\{Venue, Reason, Vistors, Country, DoorLogs, VisitorTemp};

use App\Http\Controllers\Controller;
use App\Models\VenueSloting;
use App\Models\WhatsappNotificationLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class NewBookingController extends Controller
{
    //
    public $countries;
    public $timezones;
    public function __construct()
    {
        $this->countries =  new Country;
        $this->timezones = $this->countries::with('timezones')->get();
    }

    public function index(Request $request, $locale = '')
    {
        if (!isMobileDevice($request) && env('APP_ENV') != 'local') {
            return abort('403');
        }
        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }
        $userAll = Vistors::whereDate('created_at', date('Y-m-d'))->get(['recognized_code', 'id'])->toArray();



        // echo $locale; die;

        $therapistRole = Role::where('name', 'therapist')->first();
        $VenueList = Venue::all();
        $countryList =  $this->countries->get();
        $therapists = $therapistRole->users;
        $timezones = $this->timezones;
        $reasons = Reason::where(['type' => 'announcement'])->first();
        return view('frontend.multistep.index', compact('VenueList', 'countryList', 'therapists', 'timezones', 'locale', 'reasons'));
    }

    public function ShowFilterPage(Request $request)
    {
        $visitors = [];
        if ($request->input('date')) {
            $visitors =  Vistors::whereDate('created_at', $request->input('date'))->get();
        }
        return view('filters', compact('visitors'));
    }

    public function StatusLead(Request $request, $id)
    {
        $visitor = Vistors::find($id);
        $completeNumber = $visitor->country_code . $visitor->phone;
        $tokenId = $visitor->booking_number;
        $message = "We have found that the token ".$tokenId." is either duplicate or issued via error from our system or a same person tried to get 2 tokens using different mobile numbers. Therefore our system has deleted this token. Kindly don't use or show this token at dua ghar because it is now invalid and deleted in our system.";
        WhatsAppTokenNotBookNotifcation::dispatch($visitor->id , $completeNumber,$message)->onQueue('whatsapp-notification-not-approve');

        $visitor->update([
            'token_status' => $request->input('status')
        ]);
        return redirect()->back()->with(['success' => 'Status updated']);
    }

    public function handleStatusUpdate(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');
        $message = Vistors::where('msg_sid', $messageSid)->first();
        if ($message) {
            $message->msg_sent_status = $status;
            $message->save();
        }
        return response()->json(['status' => 'success'], 200);
    }

    public function handleStatusUpdateNotification(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');
        $message = WhatsappNotificationLogs::where('msg_sid', $messageSid)->first();
        if ($message) {
            $message->msg_sent_status = $status;
            $message->msg_date = date('Y-m-d H:i:s');
            $message->save();
        }

        return response()->json(['status' => 'success'], 200);
    }
    public function handleStatusUpdateVisitorTemp(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        $message = VisitorTemp::where('msg_sid', $messageSid)->first();
        if ($message) {
            $message->msg_sent_status = $status;
            $message->msg_date = date('Y-m-d H:i:s');
            $message->save();
        }

        // Respond to Twilio's webhook request with a 200 OK status
        return response()->json(['status' => 'success'], 200);
    }



    public function showLogs()
    {
        $logFile = storage_path('logs/laravel.log'); // Path to your log file
        $logs = file_get_contents($logFile);

        return view('frontend.server-logs', ['logs' => $logs]);
    }

    public function ShowDoorLogs()
    {
        $doorLogs = DoorLogs::with('visitor')->orderBy('id', 'desc')->get();

        // echo "<pre>"; print_r($doorLogs); die;

        return view('doorlog', ['logs' => $doorLogs]);
    }



    public function clearLog()
    {
        $logFile = storage_path('logs/laravel.log'); // Path to your log file
        file_put_contents($logFile, '');
        return redirect()->back();
    }
}
