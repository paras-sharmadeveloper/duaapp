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
        $type = $request->input('type');

        $duas = Vistors::where('date', $date)->where('dua_type', $type)->get();

        return response()->json(['duas' => $duas]);
    }

    public function percentage()
    {
        $today = Carbon::now();
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
