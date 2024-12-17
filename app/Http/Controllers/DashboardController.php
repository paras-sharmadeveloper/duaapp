<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\{Vistors, VenueSloting, VenueAddress, Ipinformation, Timezone};
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

    public function generatePdf(Request $request){
        $date = $request->input('date');
        $todayVenue = VenueAddress::whereDate('venue_date', $date)->first();
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
