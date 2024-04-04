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
      //  $type = $request->input('type');
      //   $duas = Vistors::where('date', $date)->where('dua_type', $type)->get();
    //   $visitors = Vistors::whereDate('created_at', $date)->get();
      $todayVenue =VenueAddress::whereDate('venue_date', $date)->first();

      $whatsappCountDua = Vistors::where('source', 'WhatsApp')->where('dua_type','dua')->whereDate('created_at', $date)->count();
      $whatsappCountDum = Vistors::where('source', 'WhatsApp')->where('dua_type','dum')->whereDate('created_at', $date)->count();
      $websiteCountDua = Vistors::where('source', 'Website')->where('dua_type','dua')->whereDate('created_at', $date)->count();
      $websiteCountDum = Vistors::where('source', 'Website')->where('dua_type','dum')->whereDate('created_at', $date)->count();
        $duaTotal = 0;
        $dumTotal = 0;
        if($todayVenue){
            $duaTotal = VenueSloting::where('venue_address_id',$todayVenue->id)->where('type','dua')->count();
            $dumTotal = VenueSloting::where('venue_address_id',$todayVenue->id)->where('type','dum')->count();
        }
        $percentageWhatsappDua = ($duaTotal > 0 ) ? ($whatsappCountDua / $duaTotal) * 100 : 0;
        $percentageWhatsappDum = ($dumTotal > 0 ) ? ($whatsappCountDum / $dumTotal) * 100 : 0;
        $percentageWebsiteDua = ($duaTotal > 0 ) ?  ($websiteCountDua / $dumTotal) * 100: 0;
        $percentageWebsiteDum = ($dumTotal > 0 ) ?  ($websiteCountDum / $dumTotal) * 100: 0;

        $totalTokenWebsite = $websiteCountDua + $websiteCountDum;
        $totalTokenWhatsApp = $whatsappCountDua + $whatsappCountDum;

        $totalWhatsAppPercentage = $percentageWhatsappDua + $percentageWhatsappDum;
        $totalWebsitePercentage = $percentageWebsiteDua + $percentageWebsiteDum;

            $calculations = [];
            $calculations['website-total'] = $totalTokenWebsite;
            $calculations['website-total-percentage']= number_format($totalWebsitePercentage, 2).'%';

            $calculations['website-total-dua'] = $websiteCountDua;
            $calculations['website-total-percentage-dua']= number_format($percentageWebsiteDua, 2).'%';

            $calculations['website-total-dum'] = $websiteCountDum;
            $calculations['website-total-percentage-dum']=  number_format($percentageWebsiteDum, 2).'%';


            $calculations['whatsapp-total']= $totalTokenWhatsApp;
            $calculations['whatsapp-total-percentage']= number_format($totalWhatsAppPercentage, 2) .'%';


            $calculations['whatsapp-total-dua'] = $whatsappCountDua;
            $calculations['whatsapp-total-percentage-dua']= number_format($percentageWhatsappDua, 2).'%';

            $calculations['whatsapp-total-dum'] = $whatsappCountDum;
            $calculations['whatsapp-total-percentage-dum']= number_format($percentageWhatsappDum, 2).'%';

            $totalCollectedTokens =$whatsappCountDua + $whatsappCountDum + $websiteCountDua + $websiteCountDum;
            $totalTokens =$whatsappCountDua + $duaTotal + $dumTotal;



            $percentageTotalTokens = ($totalTokens > 0 ) ?  ($totalCollectedTokens / $totalTokens) * 100: 0;

            $calculations['grand-total']= $totalCollectedTokens;
            $calculations['grand-percentage']= number_format($percentageTotalTokens, 2).'%';


        return response()->json(['calculations' => $calculations]);
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
