<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\{DoorLogs, Vistors, VenueSloting, VenueAddress, Ipinformation, Timezone};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use DataTables;

use Spatie\LaravelPdf\Facades\Pdf;
use function Spatie\LaravelPdf\Support\pdf;


class DashboardController extends Controller
{

    public function index()
    {
        return view('home');
    }
    public function UpdateOuTofSq(Request $request, $id)
    {
        // Find the door log entry by its ID
        $logs = DoorLogs::find($id);

        // Ensure the log entry exists
        if ($logs) {
            // Update the 'out_of_seq' field based on the request type
            $logs->out_of_seq = $request->input('out_of_seq');
            $logs->save();  // Save the updated record

            // Return a success response
            return response()->json(['status' => 'success', 'message' => 'Status updated successfully']);
        } else {
            // If the log entry doesn't exist, return an error response
            return response()->json(['status' => 'error', 'message' => 'Door log not found'], 404);
        }
    }



    public function generatePdf(Request $request)
{
    $date = $request->input('date');
    $todayVenue = VenueAddress::whereDate('venue_date', $date)->first();

    // Website Counts and WhatsApp Confirmation
    $types = ['dua', 'dum', 'working_lady_dua', 'working_lady_dum'];
    $staff = [
        'Waheed' => 'admin1-f3ae07bc-0fe8-4849-a121-81ff5c4a4dfc',
        'Dr Azhar' => 'admin2-f3fa2c6e-ecfe-4fef-b8f2-a59ac65addb9',
        'Naseem' => 'admin3-c9d46b5c-ffd9-4d7d-a8e1-8b93cd28b1d5',
        'Admin4' => 'admin4-e3af7047-e371-4659-85ec-fc9ef644720f',
        'Admin5' => 'admin5-93219a2c-9c0a-4814-80f9-c0475f6c4236',
    ];
    $websiteCounts = [];
    $whatsappCounts = [];
    $checkIns = [];
    $printCounts = [];
    $grandTotalCheckIn = 0;
    $outOfSeqCounts = [];
    $staffAccessCounts = [];

    foreach ($types as $type) {
        $websiteCounts[$type] = Vistors::filterByDate($date)
            ->filterBySource('Website')
            ->filterByDuaType($type)
            ->count();

        $whatsappCounts[$type] = Vistors::filterByDate($date)
            ->filterBySource('Website')
            ->filterByDuaType($type)
            ->whereNotNull('msg_sid')
            ->count();

        $checkIns[$type] = Vistors::filterByDate($date)
            ->filterBySource('Website')
            ->filterByDuaType($type)
            ->where('user_status', 'admitted')
            ->count();

        $printCounts[$type] = Vistors::filterByDate($date)
            ->filterBySource('Website')
            ->filterByDuaType($type)
            ->pluck('print_count')
            ->sum();

        $doorLogCounts[$type] = Vistors::filterByDate($date)
            ->filterBySource('Website')
            ->filterByDuaType($type)
            ->withCount('doorLogs') // Counts related DoorLogs
            ->get()
            ->sum('door_logs_count'); // Sum all doorLogs for this token type

              // New: Count Out of Sequence Door Logs (out_of_seq = 1)
        $outOfSeqCounts[$type] = Vistors::filterByDate($date)
        ->filterBySource('Website')
        ->filterByDuaType($type)
        ->whereHas('doorLogs', function($query) {
            $query->where('out_of_seq', 1);
        })
        ->count();

        $grandTotalCheckIn += $checkIns[$type];
    }

    foreach ($staff as $staffName => $staffId) {
        // Count DoorLogs for each staff member in the time range
        $accessCount = DoorLogs::where('user_id', $staffId)
            ->whereBetween('created_at', [
                Carbon::parse($date . ' 14:00:00'),
                Carbon::parse($date . ' 17:30:00')
            ])
            ->count();

            $accessLogs = DoorLogs::where('user_id', $staffId)
            ->whereBetween('created_at', [
                Carbon::parse($date . ' 14:00:00'),
                Carbon::parse($date . ' 17:30:00'),
            ])
            ->get();

        $staffAccessCounts[$staffName] = $accessCount;

        // Store the logs and the total count for each staff member
        $staffAccessLogs[$staffName] = $accessLogs;
        $staffTotalCounts[$staffName] = $accessLogs->count();


    }


    $doorLogs = DoorLogs::with('visitor')->get();


    $totalAccess = array_sum($staffAccessCounts);
    $grandTotalAccess = array_sum($staffTotalCounts);


    // Venue Slot Totals
    $slotCounts = [
        'dua' => $todayVenue ? VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dua')->count() : 0,
        'dum' => $todayVenue ? VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dum')->count() : 0,
        'working_lady_dua' => $todayVenue ? VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'working_lady_dua')->count() : 0,
        'working_lady_dum' => $todayVenue ? VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'working_lady_dum')->count() : 0,
    ];

    $calculations = [
        'website-total-dua' => $websiteCounts['dua'],
        'website-total-wa-dua' => $whatsappCounts['dua'],
        'website-checkIn-dua' => $checkIns['dua'],
        'website-printToken-dua' => $printCounts['dua'],
        'website-doorAccess-dua' => $doorLogCounts['dua'],

        'website-total-dum' => $websiteCounts['dum'],
        'website-total-wa-dum' => $whatsappCounts['dum'],
        'website-checkIn-dum' => $checkIns['dum'],
        'website-printToken-dum' => $printCounts['dum'],
        'website-doorAccess-dum' => $doorLogCounts['dum'],

        'website-total-wldua' => $websiteCounts['working_lady_dua'],
        'website-total-wa-wldua' => $whatsappCounts['working_lady_dua'],
        'website-checkIn-wldua' => $checkIns['working_lady_dua'],
        'website-printToken-wldua' => $printCounts['working_lady_dua'],
        'website-doorAccess-wldua' => $doorLogCounts['working_lady_dua'],

        'website-total-wldum' => $websiteCounts['working_lady_dum'],
        'website-total-wa-wldum' => $whatsappCounts['working_lady_dum'],
        'website-checkIn-wldum' => $checkIns['working_lady_dum'],
        'website-printToken-wldum' => $printCounts['working_lady_dum'],
        'website-doorAccess-wldum' => $doorLogCounts['working_lady_dum'],


        'website-outOfSeq-dua' => $outOfSeqCounts['dua'],
        'website-outOfSeq-dum' => $outOfSeqCounts['dum'],
        'website-outOfSeq-wldua' => $outOfSeqCounts['working_lady_dua'],
        'website-outOfSeq-wldum' => $outOfSeqCounts['working_lady_dum'],

        'grand-total' => array_sum($websiteCounts),
        'grand-wa' => array_sum($whatsappCounts),
        'grand-checkIn' => $grandTotalCheckIn,
        'grand-printToken' => array_sum($printCounts),

        'staff-access' => $staffAccessCounts,
        'total-access' => $totalAccess,

        'staff-access-logs' => $staffAccessLogs,
        'staff-total-counts' => $staffTotalCounts,
        'grand-total-access' => $grandTotalAccess,
        'door-logs' => $doorLogs,
    ];

    return view('summary-report', compact('calculations'));
}


    public function generatePdfOld(Request $request){
        $date = $request->input('date');
        // DoorLogs::with('visitor')->whereDate('created_at',$date)->get();

        $todayVenue = VenueAddress::whereDate('venue_date', $date)->first();
        $websiteCountDua = Vistors::with('doorLogs')->where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->count();
        $websiteCountDum = Vistors::with('doorLogs')->where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->count();
        $websiteCountWlDua =   Vistors::with('doorLogs')->where('source', 'Website')->where('dua_type', 'working_lady_dua')->whereDate('created_at', $date)->count();
        $websiteCountWlDum =   Vistors::with('doorLogs')->where('source', 'Website')->where('dua_type', 'working_lady_dum')->whereDate('created_at', $date)->count();
        $websiteDuaCheckIn =   Vistors::with('doorLogs')->where(['source' => 'Website','dua_type' => 'dua','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $websiteDumCheckIn =   Vistors::with('doorLogs')->where(['source' => 'Website','dua_type' => 'dum','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $websiteWlDuaCheckIn = Vistors::with('doorLogs')->where(['source' => 'Website','dua_type' => 'working_lady_dua','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $websiteWlDumCheckIn = Vistors::with('doorLogs')->where(['source' => 'Website','dua_type' => 'working_lady_dum','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $grandTotalCheckIn = $websiteDuaCheckIn + $websiteDumCheckIn + $websiteWlDuaCheckIn + $websiteWlDumCheckIn ;

        // Calculate total slots for dua and dum at today's venue
        $duaTotal = 0;
        $dumTotal = 0;
        $duaTotalwl = 0;
        $dumTotalwl = 0;
        $whatsappDua = 0;
        $whatsappDum = 0;
        $whatsappDuaWl = 0;
        $whatsappDumWl = 0;

        if ($todayVenue) {
            $duaTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dua')->count();
            $dumTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dum')->count();
            $duaTotalwl = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'working_lady_dua')->count();
            $dumTotalwl = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'working_lady_dum')->count();
            $whatsappDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
            $whatsappDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
            $whatsappDuaWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dua')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
            $whatsappDumWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dum')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
        }
        $printDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $printDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $printDuaWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dua')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $printDumWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dum')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $totalTokens =  $duaTotal + $dumTotal + $duaTotalwl + $dumTotalwl;
        $grandPrintToken = $printDua + $printDum +$printDuaWl + $printDumWl;
       // $totalCollectedTokens = $whatsappCountDua + $whatsappCountDum + $websiteCountDua + $websiteCountDum;
       $totalCollectedTokens = $websiteCountWlDua + $websiteCountWlDum + $websiteCountDua + $websiteCountDum;
       $totalWhatsappTokens = $whatsappDua + $whatsappDum + $whatsappDuaWl + $whatsappDumWl;
        // Calculate total tokens and percentages
        $totalTokenWebsite = $websiteCountDua + $websiteCountDum + $websiteCountWlDua + $websiteCountWlDum;

        // Prepare response data
        $calculations = [
            'website-total' => $totalTokenWebsite,
            'website-total-wa' => $totalWhatsappTokens,
            'website-total-dua' => $websiteCountDua,
            'website-total-wa-dua' => $whatsappDua,
            'website-total-dum' => $websiteCountDum,
            'website-total-wa-dum' => $whatsappDum,
            'website-checkIn-dua' => $websiteDuaCheckIn,
            'website-checkIn-dum' => $websiteDumCheckIn,
            'website-checkIn-wldua' => $websiteWlDuaCheckIn,
            'website-checkIn-wldum' => $websiteWlDumCheckIn,
            'grand-checkIn' => $grandTotalCheckIn,
            'website-checkIn' => $grandTotalCheckIn,
            'website-printToken-dua' => ($printDua) ? $printDua : 0,
            'website-printToken-dum' => ($printDum) ? $printDum : 0 ,
            'website-printToken-wldua' => ($printDuaWl) ? $printDuaWl : 0,
            'website-printToken-wldum' => ($printDumWl) ? $printDumWl : 0,
            'grand-printToken' => $grandPrintToken,
            'website-printToken' => $grandPrintToken,
            'website-total-wa-wl' => 0,
            'website-total-wldua' => $websiteCountWlDua,
            'website-total-wa-wldua' => $whatsappDuaWl,
            'website-total-wldum' => $websiteCountWlDum,
            'website-total-wa-wldum' => $whatsappDumWl,
            'grand-total' => $totalCollectedTokens,
            'grand-wa' => $totalWhatsappTokens
        ];


        return view('summary-report', compact('calculations'));
    }
    public function getData(Request $request)
    {

       // $data = Vistors::with(['venueSloting'])->whereDate('created_at',date('Y-m-d'));

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Retrieve data for one month
        $data = Vistors::with(['venueSloting'])->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        if ($request->has('dua_type') && !empty($request->input('dua_type'))) {
            $data->where('dua_type', $request->input('dua_type'));
        }

        if ($request->has('venue_date')) {
            $data->where('created_at', 'LIKE', $request->input('venue_date') . '%');
            // $data->whereDate('created_at', $request->input('date'));
        }
        $filteredData = $data->orderBy('id','desc')->get(['booking_number as token','id', 'created_at as date',
        'country_code', 'phone', 'source', 'booking_uniqueid as token_url_link', 'id as dua_ghar', 'dua_type', 'slot_id' , 'user_question' , 'recognized_code']);

        foreach ($filteredData as $visitor) {
            // Generate token_url_link URL
            // $visitor->token_url_link = '<a href="'.route('booking.status', [$visitor->token_url_link]).'">Book Status</a>';
           // $id = base64_encode($visitor->id);
            $url =   route('booking.status', $visitor->token_url_link);
            //    $visitor->token_url_link = '<a href="' . $url . '">Book Status</a>';
            $visitor->token_url_link = $url;

            $visitor->date = date('Y-m-d', strtotime($visitor->date));
            // $image = ($visitor->recognized_code)  ? getImagefromS3($visitor->recognized_code) : '';
            // $visitor->recognized_code = ($image) ? base64_encode($image) : '';
            $daaate = date('l d-M-Y', strtotime($visitor->date));


            if ($visitor->venueSloting && $visitor->venueSloting->venueAddress) {

                if($visitor->dua_type){
                    $visitor->dua_ghar =  $visitor->venueSloting->venueAddress->city.' / ' . $visitor->dua_type;
                }else{
                    $visitor->dua_ghar =  $visitor->venueSloting->venueAddress->city;
                }

                $duaType = ($visitor->dua_type) ? $visitor->dua_type : 'Dua';
                $visitor->user_question = ''.strtoupper($duaType).' TOKEN - '.$daaate.' - '.strtoupper($visitor->venueSloting->venueAddress->city).' Dua Ghar';

                if($visitor->country_code){
                    $visitor->phone =  $visitor->country_code.'  ' . $visitor->phone;
                }
            }
        }
        return DataTables::of($filteredData)->make(true);
    }



    public function filter(Request $request)
    {
        $date = $request->input('date');

        // Fetch today's venue
        $todayVenue = VenueAddress::whereDate('venue_date', $date)->first();

        // Count visitors by source and type
      //  $whatsappCountDua = Vistors::where('source', 'WhatsApp')->where('dua_type', 'dua')->whereDate('created_at', $date)->count();
      //  $whatsappCountDum = Vistors::where('source', 'WhatsApp')->where('dua_type', 'dum')->whereDate('created_at', $date)->count();
        $websiteCountDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->count();
        $websiteCountDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->count();

        $websiteCountWlDua =   Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dua')->whereDate('created_at', $date)->count();
        $websiteCountWlDum =   Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dum')->whereDate('created_at', $date)->count();
        $websiteDuaCheckIn =   Vistors::where(['source' => 'Website','dua_type' => 'dua','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $websiteDumCheckIn =   Vistors::where(['source' => 'Website','dua_type' => 'dum','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $websiteWlDuaCheckIn = Vistors::where(['source' => 'Website','dua_type' => 'working_lady_dua','user_status' =>'admitted'])->whereDate('created_at', $date)->count();
        $websiteWlDumCheckIn = Vistors::where(['source' => 'Website','dua_type' => 'working_lady_dum','user_status' =>'admitted'])->whereDate('created_at', $date)->count();

        $grandTotalCheckIn = $websiteDuaCheckIn + $websiteDumCheckIn + $websiteWlDuaCheckIn + $websiteWlDumCheckIn ;

        // Calculate total slots for dua and dum at today's venue
        $duaTotal = 0;
        $dumTotal = 0;
        $duaTotalwl = 0;
        $dumTotalwl = 0;
        $whatsappDua = 0;
        $whatsappDum = 0;
        $whatsappDuaWl = 0;
        $whatsappDumWl = 0;
        if ($todayVenue) {
            $duaTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dua')->count();
            $dumTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dum')->count();
            $duaTotalwl = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'working_lady_dua')->count();
            $dumTotalwl = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'working_lady_dum')->count();

            $whatsappDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
            $whatsappDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
            $whatsappDuaWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dua')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();
            $whatsappDumWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dum')->whereDate('created_at', $date)->whereNotNull('msg_sid')->count();


        }

        $printDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $printDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $printDuaWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dua')->whereDate('created_at', $date)->pluck('print_count')->sum();
        $printDumWl = Vistors::where('source', 'Website')->where('dua_type', 'working_lady_dum')->whereDate('created_at', $date)->pluck('print_count')->sum();


        $totalTokens =  $duaTotal + $dumTotal + $duaTotalwl + $dumTotalwl;
        $grandPrintToken = $printDua + $printDum +$printDuaWl + $printDumWl;

       // $totalCollectedTokens = $whatsappCountDua + $whatsappCountDum + $websiteCountDua + $websiteCountDum;
       $totalCollectedTokens = $websiteCountWlDua + $websiteCountWlDum + $websiteCountDua + $websiteCountDum;
       $totalWhatsappTokens = $whatsappDua + $whatsappDum + $whatsappDuaWl + $whatsappDumWl;

        // Calculate percentages
     ////   $percentageWhatsappDua = ($duaTotal > 0) ? ($whatsappCountDua / $totalCollectedTokens) * 100 : 0;
       //  $percentageWhatsappDum = ($dumTotal > 0) ? ($whatsappCountDum / $totalCollectedTokens) * 100 : 0;
        // $percentageWebsiteDua = ($totalCollectedTokens > 0 ) ? ($websiteCountDua / $totalCollectedTokens) * 100 : 0;
        // $percentageWebsiteDum = ($totalCollectedTokens > 0) ? ($websiteCountDum / $totalCollectedTokens) * 100 : 0;

        // $percentageWebsiteDuawl = ($totalCollectedTokens > 0) ? ($websiteCountWlDua / $totalCollectedTokens) * 100 : 0;
        // $percentageWebsiteDumwl = ($totalCollectedTokens > 0) ? ($websiteCountWlDum / $totalCollectedTokens) * 100 : 0;

        // Calculate total tokens and percentages
        $totalTokenWebsite = $websiteCountDua + $websiteCountDum + $websiteCountWlDua + $websiteCountWlDum;


        // $totalTokenWhatsApp = $whatsappCountDua + $whatsappCountDum;
     //   $totalWhatsAppPercentage = $percentageWhatsappDua + $percentageWhatsappDum;

        // Calculate grand totals and percentages



       // $totalWebsitePercentage =  ($totalCollectedTokens > 0) ? ($totalTokenWebsite / $totalCollectedTokens) * 100 : 0;
        // $totalWhatsAppPercentage =  ($totalTokens > 0) ? ($totalTokenWhatsApp / $totalCollectedTokens) * 100 : 0;
        // $totalWhatsAppPercentage =  ($totalTokens > 0) ? ($totalTokenWhatsApp / $totalCollectedTokens) * 100 : 0;


      //  $percentageTotalTokens = ($totalCollectedTokens > 0) ? ($totalCollectedTokens / $totalCollectedTokens) * 100 : 0;

        // Prepare response data
        $calculations = [
            'website-total' => $totalTokenWebsite,
            'website-total-percentage' => $totalWhatsappTokens,
            'website-total-dua' => $websiteCountDua,
            'website-total-percentage-dua' => $whatsappDua,
            'website-total-dum' => $websiteCountDum,
            'website-total-percentage-dum' => $whatsappDum,

            'website-checkIn-dua' => $websiteDuaCheckIn,
            'website-checkIn-dum' => $websiteDumCheckIn,

            'website-checkIn-wldua' => $websiteWlDuaCheckIn,
            'website-checkIn-wldum' => $websiteWlDumCheckIn,
            'grand-checkIn' => $grandTotalCheckIn,
            'website-checkIn' => $grandTotalCheckIn,
            'website-printToken-dua' => ($printDua) ? $printDua : 0,
            'website-printToken-dum' => ($printDum) ? $printDum : 0 ,
            'website-printToken-wldua' => ($printDuaWl) ? $printDuaWl : 0,
            'website-printToken-wldum' => ($printDumWl) ? $printDumWl : 0,
            'grand-printToken' => $grandPrintToken,
            'website-printToken' => $grandPrintToken,
            'website-total-percentage-wl' => 0,
            'website-total-wldua' => $websiteCountWlDua,
            'website-total-percentage-wldua' => $whatsappDuaWl,
            'website-total-wldum' => $websiteCountWlDum,
            'website-total-percentage-wldum' => $whatsappDumWl,
            'grand-total' => $totalCollectedTokens,
            'grand-percentage' => $totalWhatsappTokens
        ];

        return response()->json(['calculations' => $calculations]);
    }


    public function percentage(Request $request)
    {
        // $today = Carbon::now();
        $today = $request->input('date');
        $whatsappCountDua = Vistors::where('source', 'WhatsApp')->where('dua_type', 'dua')->whereDate('created_at', $today)->count();
        $whatsappCountDum = Vistors::where('source', 'WhatsApp')->where('dua_type', 'dum')->whereDate('created_at', $today)->count();
        $websiteCountDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $today)->count();
        $websiteCountDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $today)->count();
        $todayVenue = VenueAddress::whereDate('venue_date', $today)->first();
        $duaTotal = 0;
        $dumTotal = 0;
        if ($todayVenue) {
            $duaTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dua')->count();
            $dumTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dum')->count();
        }

        $totalTokenBookedWhatsApp = $whatsappCountDua + $whatsappCountDum;
        $totalTokenBookedWebsite = $websiteCountDua + $websiteCountDum;

        $totalBookDua = $whatsappCountDua + $websiteCountDua;
        $totalBookDum = $whatsappCountDum + $websiteCountDum;
        //  $totalCount = $whatsappCount + $websiteCount;

        $percentageWhatsappDua = ($duaTotal > 0) ? ($whatsappCountDua / $duaTotal) * 100 : 0;
        $percentageWhatsappDum = ($dumTotal > 0) ? ($whatsappCountDum / $dumTotal) * 100 : 0;
        $percentageWebsiteDua = ($duaTotal > 0) ?  ($websiteCountDua / $dumTotal) * 100 : 0;
        $percentageWebsiteDum = ($dumTotal > 0) ?  ($websiteCountDum / $dumTotal) * 100 : 0;

        $totalWhatsAppCount = $percentageWhatsappDua + $percentageWhatsappDum;
        $totalWebsiteCount = $percentageWebsiteDua + $percentageWebsiteDum;

        //   $percentageWhatsapp = ($totalCount > 0 ) ? ($whatsappCount / $totalCount) * 100 : 0;
        //  $percentageWebsite = ($totalCount > 0 ) ?  ($websiteCount / $totalCount) * 100: 0;

        return response()->json([
            'whatsapp_percentage' => $totalWhatsAppCount,
            'website_percentage' => $totalWebsiteCount,
            'whatsAppDua' => $percentageWhatsappDua,
            'whatsAppDum' => $percentageWhatsappDum,
            'websiteDua' => $percentageWebsiteDua,
            'websiteDum' => $percentageWebsiteDum,
            'duatoken' => $duaTotal,
            'dumtoken' => $dumTotal,
            'totalTokenBookedWhatsApp' => $totalTokenBookedWhatsApp,
            'totalTokenBookedWebsite' => $totalTokenBookedWebsite,
            'totalBookDua' => $totalBookDua,
            'totalBookDum' => $totalBookDum


        ]);
    }







}
