<?php

use App\Models\Timezone;
use App\Models\Venue;
use App\Models\{Vistors,VenueSloting};
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;


use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\BluetoothPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;



if (!function_exists('VenueAvilableInCountry')) {

    function VenueAvilableInCountry($AllowedcountryArr,$callFromCountryId)
    {

        if (in_array($callFromCountryId, $AllowedcountryArr)) {
            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.',
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',

            ];
        }else{
            return [
                'allowed' => false,
                'message' => 'Currently Your Country not allowed to book token with us. please try again in future venues.',
                'message_ur' => 'فی الحال آپ کے ملک کو ہمارے ساتھ ٹوکن بک کرنے کی اجازت نہیں ہے۔ براہ کرم مستقبل کے مقامات پر دوبارہ کوشش کریں۔',
            ];
        }


    }

}

if (!function_exists('TokenBookingAllowed')) {
    function TokenBookingAllowed($venueStartDateTime, $venueEndDateTime, $timezone)
    {

        $venueStartTime = Carbon::parse($venueStartDateTime,$timezone);
        $venueEndTime = Carbon::parse($venueEndDateTime,$timezone);

        $currentTime = Carbon::now($timezone)->tz($timezone);

        if($currentTime->gte($venueStartTime) && $currentTime->lte($venueEndTime)){

            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.' ,
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',
                'currentTime' => $currentTime->format('d M y h:i A')
            ];

        }else if ($currentTime->gt($venueEndTime)) {
            return [
                'allowed' => false,
                'mytime' => Carbon::now()->format('d M Y h:i A'),
                'message' => 'You are not allowed to book now . because time is already passed for this venue. Please try next time Thank You',
                'message_ur' =>  'آپ کو ابھی بک کرنے کی اجازت نہیں ہے۔ کیونکہ اس مقام کے لیے وقت گزر چکا ہے۔ براہ کرم اگلی بار کوشش کریں شکریہ',

            ];

        }else{
            return [
                'allowed' => false,
                'mytime' => Carbon::now()->format('d M Y h:i A'),
                'message' =>'Token Booking for Dua / Dum Appointment has not yet started. Kindly try again at below mentioned time: '.$venueStartTime->format('d M Y').' at  '.$venueStartTime->format('h:i A').' ('.$timezone.') Time zone',
                'message_ur' => 'دعا/دم ملاقات کے لیے ٹوکن بکنگ ابھی شروع نہیں ہوئی ہے۔ براہ مہربانی نیچے دیئے گئے وقت پر دوبارہ کوشش کریں۔
                '.$venueStartTime->format('d M Y').' at '.$venueStartTime->format('h:i A').' ('.$timezone.') Timezon',


            ];

        }



    }
}





if (!function_exists('isAllowedTokenBooking')) {
    function isAllowedTokenBooking($venuedateTime, $slot_appear_hours, $timezone)
    {

        $eventDateTime = Carbon::parse($venuedateTime,$timezone);
        $currentTime = Carbon::now()->tz($timezone);

        $hoursRemaining = $currentTime->diffInHours($eventDateTime, false);
        $slotsAppearBefore = intval($slot_appear_hours);
        $waitTime = abs($hoursRemaining) - abs($slotsAppearBefore);
        if ($slotsAppearBefore == 0) {
            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.',
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',
                'hours_remaining' => $hoursRemaining,
                'hours_until_open' => $hoursRemaining,
                'slotsAppearBefore' => $slotsAppearBefore,
                'currentTime' => $currentTime->format('d M y h:i A')
            ];
        }
        if ($hoursRemaining <= $slotsAppearBefore) {
            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.',
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',
                'hours_remaining' => $hoursRemaining,
                'hours_until_open' => $hoursRemaining,
                'slotsAppearBefore' => $slotsAppearBefore,
                'currentTime' => $currentTime->format('d M y h:i A')
            ];
        } elseif ($hoursRemaining < 0) {
            return [
                'allowed' => false,
                'message' => 'Token booking time has passed.',
                'message_ur' => 'ٹوکن بکنگ کا وقت گزر چکا ہے۔',
                'hours_passed' => abs($hoursRemaining),
                'hours_until_open' => $hoursRemaining,
                'slotsAppearBefore' => $slotsAppearBefore,
                'currentTime' => $currentTime->format('d M y h:i A')


            ];
        } else {
            return [
                'allowed' => false,
                'message' => 'Token booking is not yet allowed.  Kindly Wait for ' . $hoursRemaining . ' hours ' ,
                'message_ur' => 'ٹوکن بکنگ کی ابھی اجازت نہیں ہے۔ برائے مہربانی ' . $hoursRemaining . ' کا انتظار کریں۔ گھنٹے',
                'hours_until_open' => $hoursRemaining,
                'slotsAppearBefore' => $slotsAppearBefore,
                'asde' => Carbon::now()->format('d M Y h:i A')
            ];
        }

        // return $hoursRemaining >= 0 && $hoursRemaining <= $slotsAppearBefore;
    }
}

if (!function_exists('userAllowedRejoin')) {
    function userAllowedRejoin($mobile, $rejoin_venue_after)
    {

        $user = Vistors::Where('phone','LIKE',$mobile)->first();
            if (!empty($user)) {
                $recordAge = $user->created_at->diffInDays(now());
                $rejoin = $rejoin_venue_after;
                $daysRemaining = $rejoin  - $recordAge;
                if ($rejoin > 0 && $recordAge <= $rejoin) {
                    return [
                        'allowed' => false,
                        'message' => 'You already Booked a Token with us. Please Try after ' . $daysRemaining . ' days',
                        'message_ur' => 'آپ نے پہلے ہی ہمارے ساتھ ایک ٹوکن بک کر رکھا ہے۔ براہ کرم ' . $daysRemaining . ' دن کے بعد کوشش کریں۔',
                        'days_remaining' => $daysRemaining,
                        'recordAge' => $recordAge
                    ];
                } else {
                    return [
                        'allowed' => true,
                        'message' => 'allowed',
                        'message_ur' => 'اجازت دی',
                        'recordAge' => $recordAge,
                        'days_remaining' => $daysRemaining,
                    ];
                }
            }else{
                return [
                    'allowed' => true,
                    'message' => 'allowed',
                    'message_ur' => 'اجازت دی',
                    'recordAge' => 0,
                    'days_remaining' =>0,
                ];
            }
        }
    }


if (!function_exists('getCurrentContryTimezone')) {
    function getCurrentContryTimezone($id)
    {

        $currentCountry = Venue::find($id);
        $timezone =  Timezone::where(['country_code' => $currentCountry->iso])->first();
        Config::set('app.timezone', $timezone->timezone);
        $time = Carbon::now()->setTimezone($timezone->timezone);
        //  $countryDate = Carbon::parse(date('Y-m-d'),$timezone->timezone);
        return $time->format('Y-m-d');
    }
}
if (!function_exists('getTotalTokens')) {
    function getTotalTokens($venueId , $type){
        return VenueSloting::where(['venue_address_id' => $venueId , 'type' => $type])->count();

    }
}



if (!function_exists('printToken')) {

    // printToken($token, 'wifi', '00:11:22:33:44:55');
    function printToken($token, $connectionType, $connectionAddress)
    {
        // Set up printer connection based on the provided connection type and address
        // if ($connectionType === 'wifi') {
        //     $connector = new NetworkPrintConnector($connectionAddress, 9100);
        // } elseif ($connectionType === 'bluetooth') {
        //     $connector = new BluetoothPrintConnector($connectionAddress);
        // } else {
        //     // Default to file connector
        //     $connector = new FilePrintConnector("/dev/usb/lp0"); // Adjust the path as per your system configuration
        // }

        // $printer = new Printer($connector);

        // try {
        //     // Print token
        //     $printer->text("Thank you for visiting here\n");
        //     $printer->text("#" . $token . "\n");
        //     $printer->text("Verified\n");

        //     // Cut paper (if supported)
        //     $printer->cut();

        //     // Close printer connection
        //     $printer->close();

        //     return "Token printed successfully";
        // } catch (\Exception $e) {
        //     // Handle any errors
        //     return "Error printing token: " . $e->getMessage();
        // }
    }
}
