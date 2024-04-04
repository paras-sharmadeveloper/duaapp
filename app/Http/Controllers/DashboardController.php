<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VenueAddress;
use App\Models\VenueSloting;
use App\Models\Vistors;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function filter(Request $request)
{
    $date = $request->input('date');

    // Fetch today's venue
    $todayVenue = VenueAddress::whereDate('venue_date', $date)->first();

    // Count visitors by source and type
    $whatsappCountDua = Vistors::where('source', 'WhatsApp')->where('dua_type', 'dua')->whereDate('created_at', $date)->count();
    $whatsappCountDum = Vistors::where('source', 'WhatsApp')->where('dua_type', 'dum')->whereDate('created_at', $date)->count();
    $websiteCountDua = Vistors::where('source', 'Website')->where('dua_type', 'dua')->whereDate('created_at', $date)->count();
    $websiteCountDum = Vistors::where('source', 'Website')->where('dua_type', 'dum')->whereDate('created_at', $date)->count();

    // Calculate total slots for dua and dum at today's venue
    $duaTotal = 0;
    $dumTotal = 0;
    if ($todayVenue) {
        $duaTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dua')->count();
        $dumTotal = VenueSloting::where('venue_address_id', $todayVenue->id)->where('type', 'dum')->count();
    }

    // Calculate percentages
    $percentageWhatsappDua = ($duaTotal > 0) ? ($whatsappCountDua / $duaTotal) * 100 : 0;
    $percentageWhatsappDum = ($dumTotal > 0) ? ($whatsappCountDum / $dumTotal) * 100 : 0;
    $percentageWebsiteDua = ($duaTotal > 0) ? ($websiteCountDua / $duaTotal) * 100 : 0;
    $percentageWebsiteDum = ($dumTotal > 0) ? ($websiteCountDum / $dumTotal) * 100 : 0;

    // Calculate total tokens and percentages
    $totalTokenWebsite = $websiteCountDua + $websiteCountDum;


    $totalTokenWhatsApp = $whatsappCountDua + $whatsappCountDum;
    $totalWhatsAppPercentage = $percentageWhatsappDua + $percentageWhatsappDum;

    // Calculate grand totals and percentages
    $totalCollectedTokens = $whatsappCountDua + $whatsappCountDum + $websiteCountDua + $websiteCountDum;
    $totalTokens =  $duaTotal + $dumTotal;

    $totalWebsitePercentage = ($totalTokenWebsite / $totalTokens) * 100 ;
    $totalWhatsAppPercentage = ($totalTokenWhatsApp / $totalTokens) * 100 ;


    $percentageTotalTokens = ($totalTokens > 0) ? ($totalCollectedTokens / $totalTokens) * 100 : 0;

    // Prepare response data
    $calculations = [
        'website-total' => $totalTokenWebsite,
        'website-total-percentage' => number_format($totalWebsitePercentage, 2) . '%',
        'website-total-dua' => $websiteCountDua,
        'website-total-percentage-dua' => number_format($percentageWebsiteDua, 2) . '%',
        'website-total-dum' => $websiteCountDum,
        'website-total-percentage-dum' => number_format($percentageWebsiteDum, 2) . '%',

        'whatsapp-total' => $totalTokenWhatsApp,
        'whatsapp-total-percentage' => number_format($totalWhatsAppPercentage, 2) . '%',
        'whatsapp-total-dua' => $whatsappCountDua,
        'whatsapp-total-percentage-dua' => number_format($percentageWhatsappDua, 2) . '%',
        'whatsapp-total-dum' => $whatsappCountDum,
        'whatsapp-total-percentage-dum' => number_format($percentageWhatsappDum, 2) . '%',

        'grand-total' => $totalCollectedTokens,
        'grand-percentage' => number_format($percentageTotalTokens, 2) . '%'
    ];

    return response()->json(['calculations' => $calculations]);
}


    public function percentage(Request $request)
    {
        // $today = Carbon::now();
        $today = $request->input('date');
        $whatsappCountDua = Vistors::where('source', 'WhatsApp')->where('dua_type','dua')->whereDate('created_at', $today)->count();
        $whatsappCountDum = Vistors::where('source', 'WhatsApp')->where('dua_type','dum')->whereDate('created_at', $today)->count();
        $websiteCountDua = Vistors::where('source', 'Website')->where('dua_type','dua')->whereDate('created_at', $today)->count();
        $websiteCountDum = Vistors::where('source', 'Website')->where('dua_type','dum')->whereDate('created_at', $today)->count();
        $todayVenue =VenueAddress::whereDate('venue_date', $today)->first();
        $duaTotal = 0;
        $dumTotal = 0;
        if($todayVenue){
            $duaTotal = VenueSloting::where('venue_address_id',$todayVenue->id)->where('type','dua')->count();
            $dumTotal = VenueSloting::where('venue_address_id',$todayVenue->id)->where('type','dum')->count();
        }

        $totalTokenBookedWhatsApp = $whatsappCountDua + $whatsappCountDum;
        $totalTokenBookedWebsite = $websiteCountDua + $websiteCountDum;

        $totalBookDua = $whatsappCountDua + $websiteCountDua;
        $totalBookDum = $whatsappCountDum + $websiteCountDum;



       //  $totalCount = $whatsappCount + $websiteCount;

       $percentageWhatsappDua = ($duaTotal > 0 ) ? ($whatsappCountDua / $duaTotal) * 100 : 0;
       $percentageWhatsappDum = ($dumTotal > 0 ) ? ($whatsappCountDum / $dumTotal) * 100 : 0;
       $percentageWebsiteDua = ($duaTotal > 0 ) ?  ($websiteCountDua / $dumTotal) * 100: 0;
       $percentageWebsiteDum = ($dumTotal > 0 ) ?  ($websiteCountDum / $dumTotal) * 100: 0;

       $totalWhatsAppCount = $percentageWhatsappDua + $percentageWhatsappDum;
       $totalWebsiteCount = $percentageWebsiteDua + $percentageWebsiteDum;





      //   $percentageWhatsapp = ($totalCount > 0 ) ? ($whatsappCount / $totalCount) * 100 : 0;
      //  $percentageWebsite = ($totalCount > 0 ) ?  ($websiteCount / $totalCount) * 100: 0;

        return response()->json(['whatsapp_percentage' => $totalWhatsAppCount,
                                  'website_percentage' => $totalWebsiteCount ,
                                  'whatsAppDua' =>$percentageWhatsappDua ,
                                  'whatsAppDum' =>$percentageWhatsappDum ,
                                  'websiteDua' =>$percentageWebsiteDua ,
                                  'websiteDum' =>$percentageWebsiteDum ,
                                  'duatoken' => $duaTotal,
                                  'dumtoken' => $dumTotal,
                                  'totalTokenBookedWhatsApp' => $totalTokenBookedWhatsApp,
                                  'totalTokenBookedWebsite' => $totalTokenBookedWebsite,
                                  'totalBookDua' => $totalBookDua,
                                  'totalBookDum' => $totalBookDum


                                ]);
    }
}
