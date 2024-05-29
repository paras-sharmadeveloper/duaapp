<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\{Vistors, VenueSloting, VenueAddress, Ipinformation, Timezone};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home');
    }
    public function getData(Request $request)
    {

        $data = Vistors::with(['venueSloting'])
            ->select(['booking_number as token','id', 'created_at as date',
             'country_code', 'phone', 'source', 'booking_uniqueid as token_url_link', 'id as dua_ghar', 'dua_type', 'slot_id' , 'user_question' , 'recognized_code']);


        if ($request->has('dua_type') && !empty($request->input('dua_type'))) {
            $data->where('dua_type', $request->input('dua_type'));
        }

        if ($request->has('venue_date')) {
            $data->where('created_at', 'LIKE', $request->input('venue_date') . '%');
            // $data->whereDate('created_at', $request->input('date'));
        }
        $filteredData = $data->orderBy('created_at','desc') ->get();

        foreach ($filteredData as $visitor) {
            // Generate token_url_link URL
            // $visitor->token_url_link = '<a href="'.route('booking.status', [$visitor->token_url_link]).'">Book Status</a>';
           // $id = base64_encode($visitor->id);
            $url =   route('booking.status', $visitor->token_url_link);
            //    $visitor->token_url_link = '<a href="' . $url . '">Book Status</a>';
            $visitor->token_url_link = $url;

            $visitor->date = date('Y-m-d', strtotime($visitor->date));
            $image = ($visitor->recognized_code)  ? getImagefromS3($visitor->recognized_code) : '';
            $visitor->recognized_code = ($image) ? base64_encode($image) : 'VBORw0KGgoAAAANSUhEUgAAAgAAAAIACAMAAADDpiTIAAAC+lBMVEUAAAAfHxkdHRscHBoeHh4dHRscHBodHRsdHRsdHRsYGBgdHR0fHx8fHx8dHRsdHRsdHRscHBoTExMdHRsXFxccHBscHBobGxscHBkcHBsdHRodHRocHBocHBscHBscHBwcHBscHBwfHxceHhkbGxsdHRsVFRUcHBocHBocHBskJCQcHBodHRocHBobGxsdHRofHx8cHBocHBskJBIdHRscHBwdHRscHBscHBocHBsdHRsdHRkcHBscHBscHBscHBwdHRocHBsdHRsdHRogIBgdHRsdHRkcHBocHBseHhgcHBwaGhocHBodHRocHBsbGxsdHRscHBscHBsdHRocHBodHRobGxsdHRsdHRsdHRocHBoeHh4dHRscHBsZGRkeHhscHBkcHBoeHhshIRYdHRodHRsdHRseHhkdHRodHR0eHhscHBsdHRscHBocHBsaGhodHRsbGxsdHRobGxscHBwdHRocHBscHBwdHRocHBsdHRocHBsdHRodHRkcHBodHRodHRscHBocHBodHRkdHRscHBodHRscHBwdHRocHBkcHBobGxsdHRscHBodHRoeHhodHRsdHRscHBwcHBodHRocHBsaGhofHxobGxsfHxgcHBodHR0dHRscHBocHBwcHBwcHBkcHBscHBodHRodHRsdHRocHBodHRodHRsbGxsdHRodHRodHRscHBocHBwcHBodHRoeHhscHBseHhsdHRobGxsaGhocHBocHBoeHhodHRoeHhscHBodHRodHRocHBscHBodHRscHBkcHBsdHRseHhkdHRsdHRocHBsdHRocHBwcHBwcHBsdHRodHRobGxsbGxsaGhoaGhocHBodHRscHBocHBwdHRodHRodHRodHRseHhoZGRkcHBodHRsbGxseHhccHBwcHBodHRodHRscHBocHBsiIiIdHRsdHRoXFxcdHRscHBozMzMeHhsdHR0cHBocHBsdHRsZGRkdHRoqKiodHRkAAAAcHBwAAAAAAAAAAAAeHh4eHhthjin5AAAA/nRSTlMAKP/nIv74/fv8FRoQCPPZgPcN9Qvfhy5sxb/2+bDCCfFRIDNSpgxYc8wHq1eiSbcYa7oObxvY1vCX8k+osaBHvtV60x+KPYjgKjY57prNHIGqvMjUY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';
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



                // Access VenueAddress data through venueSloting
                // Example: $visitor->venueSloting->venueAddress->some_attribute
            }
        }

        // return DataTables::of($filteredData)
        // ->addColumn('token_url_link', function ($visitor) {
        //     $url = route('booking.status', [$visitor->token_url_link]);
        //     return '<a href="' . $url . '">Book Status</a>';
        // })
        // ->editColumn('date', function ($visitor) {
        //     return date('Y-m-d', strtotime($visitor->date));
        // })
        // ->editColumn('dua_ghar', function ($visitor) {
        //     if ($visitor->venueSloting && $visitor->venueSloting->venueAddress) {
        //         return $visitor->venueSloting->venueAddress->city;
        //     }
        //     return '';
        // })
        // ->rawColumns(['token_url_link'])
        // ->make(true);


        return DataTables::of($filteredData)->make(true);
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

        $totalTokens =  $duaTotal + $dumTotal;

        $totalCollectedTokens = $whatsappCountDua + $whatsappCountDum + $websiteCountDua + $websiteCountDum;

        // Calculate percentages
        $percentageWhatsappDua = ($duaTotal > 0) ? ($whatsappCountDua / $totalCollectedTokens) * 100 : 0;
        $percentageWhatsappDum = ($dumTotal > 0) ? ($whatsappCountDum / $totalCollectedTokens) * 100 : 0;
        $percentageWebsiteDua = ($duaTotal > 0) ? ($websiteCountDua / $totalCollectedTokens) * 100 : 0;
        $percentageWebsiteDum = ($dumTotal > 0) ? ($websiteCountDum / $totalCollectedTokens) * 100 : 0;

        // Calculate total tokens and percentages
        $totalTokenWebsite = $websiteCountDua + $websiteCountDum;


        $totalTokenWhatsApp = $whatsappCountDua + $whatsappCountDum;
        $totalWhatsAppPercentage = $percentageWhatsappDua + $percentageWhatsappDum;

        // Calculate grand totals and percentages



        $totalWebsitePercentage =  ($totalTokens > 0) ? ($totalTokenWebsite / $totalCollectedTokens) * 100 : 0;
        $totalWhatsAppPercentage =  ($totalTokens > 0) ? ($totalTokenWhatsApp / $totalCollectedTokens) * 100 : 0;


        $percentageTotalTokens = ($totalTokens > 0) ? ($totalCollectedTokens / $totalCollectedTokens) * 100 : 0;

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
