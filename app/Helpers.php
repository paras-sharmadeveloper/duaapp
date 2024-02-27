<?php

use Carbon\Carbon;

if (!function_exists('isAllowedTokenBooking')) {
    function isAllowedTokenBooking($venuedateTime, $slot_appear_hours , $timezone)
    {

        $eventDateTime = Carbon::parse($venuedateTime, $timezone);
        $currentTime = now()->tz($timezone);
        $hoursRemaining = $currentTime->diffInHours($eventDateTime, false); // false ensures negative values if eventDateTime is in the past
        $slotsAppearBefore = intval($slot_appear_hours);
        $waitTime = $hoursRemaining - $slotsAppearBefore;
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
