<?php

use App\Models\Timezone;
use App\Models\Venue;
use App\Models\Vistors;
use Carbon\Carbon;

if (!function_exists('isAllowedTokenBooking')) {
    function isAllowedTokenBooking($venuedateTime, $slot_appear_hours , $timezone)
    {

        $eventDateTime = Carbon::parse($venuedateTime, $timezone);
        $currentTime = now()->tz($timezone);
        $hoursRemaining = $currentTime->diffInHours($eventDateTime, false); // false ensures negative values if eventDateTime is in the past
        $slotsAppearBefore = intval($slot_appear_hours);
        $waitTime = $hoursRemaining - $slotsAppearBefore;
        if($slotsAppearBefore == 0){
            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.',
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',
                'hours_remaining' => $hoursRemaining,
            ];
        }
        if ($hoursRemaining >= 0 && $hoursRemaining <= $slotsAppearBefore) {
            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.',
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',
                'hours_remaining' => $hoursRemaining,
            ];
        } elseif ($hoursRemaining < 0) {
            return [
                'allowed' => false,
                'message' => 'Token booking time has passed.',
                'message_ur' => 'ٹوکن بکنگ کا وقت گزر چکا ہے۔',
                'hours_passed' => abs($hoursRemaining),
            ];
        } else {
            return [
                'allowed' => false,
                'message' => 'Token booking is not yet allowed.  Kindly Wait for ' .$waitTime.' hours' ,
                'message_ur' => 'ٹوکن بکنگ کی ابھی اجازت نہیں ہے۔ برائے مہربانی '.$waitTime.' کا انتظار کریں۔ گھنٹے',
                'hours_until_open' => $hoursRemaining,
            ];
        }

        // return $hoursRemaining >= 0 && $hoursRemaining <= $slotsAppearBefore;
    }


}

if (!function_exists('userAllowedRejoin')) {
    function userAllowedRejoin($mobile, $rejoin_venue_after)
    {

        $user = Vistors::Where('phone', $mobile)->first();
        if ($user) {
            $recordAge = $user->created_at->diffInDays(now());
            $rejoin = $rejoin_venue_after;
            $daysRemaining = $rejoin  - $recordAge;
            if ($rejoin > 0 && $recordAge <= $rejoin ){
                return [
                    'allowed' => false,
                    'message' => 'You already Booked a Token with us. Please Try after '.$daysRemaining.' days',
                    'message_ur' => 'آپ نے پہلے ہی ہمارے ساتھ ایک ٹوکن بک کر رکھا ہے۔ براہ کرم '.$daysRemaining.' دن کے بعد کوشش کریں۔',
                    'days_remaining' => $daysRemaining,
                    'recordAge' => $recordAge
                ];

            }else{
                return [
                    'allowed' => true,
                    'message' => 'allowed',
                    'message_ur' => 'اجازت دی',
                    'recordAge' => $recordAge,
                    'days_remaining' => $daysRemaining,
                ];
            }
        }

    }
}

if (!function_exists('getCurrentContryTimezone')) {
    function getCurrentContryTimezone($id)
    {

        $currentCountry = Venue::find($id);
        $timezone =  Timezone::where(['iso' => $currentCountry->iso])->first();
        $countryDate = Carbon::parse(date('Y-m-d'),$timezone->timezone);
        return $countryDate->format('Y-m-d');

    }
}


if (!function_exists('getCurrentTimezone')) {
    function getCurrentContryTimezone($venueDate, $tz)
    {

        $countryDate = Carbon::parse($venueDate, $tz);
        return $countryDate->format('Y-m-d');

    }
}

