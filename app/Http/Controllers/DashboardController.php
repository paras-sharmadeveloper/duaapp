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
        $whatsappCount = Vistors::where('source', 'WhatsApp')->whereDate('created_at', $today)->count();
        $websiteCount = Vistors::where('source', 'Website')->whereDate('created_at', $today)->count();
        $todayVenue =VenueAddress::whereDate('venue_date', $today)->first();
        $duaTotal = 0;
        $dumTotal = 0;
        if($todayVenue){
            $duaTotal = VenueSloting::where('venue_address_id',$todayVenue->id)->where('type','dua')->count();
            $dumTotal = VenueSloting::where('venue_address_id',$todayVenue->id)->where('type','dum')->count();
        }



       //  $totalCount = $whatsappCount + $websiteCount;

       $percentageWhatsappDua = ($duaTotal > 0 ) ? ($whatsappCount / $duaTotal) * 100 : 0;
       $percentageWhatsappDum = ($dumTotal > 0 ) ? ($whatsappCount / $dumTotal) * 100 : 0;
       $percentageWebsiteDua = ($duaTotal > 0 ) ?  ($websiteCount / $dumTotal) * 100: 0;
       $percentageWebsiteDum = ($dumTotal > 0 ) ?  ($websiteCount / $dumTotal) * 100: 0;

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
                                ]);
    }
}
