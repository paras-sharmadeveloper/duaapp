<?php

use Carbon\Carbon;

if (!function_exists('isAllowedTokenBooking')) {
    function isAllowedTokenBooking($venuedateTime, $slot_appear_hours , $timezone)
    {

        $eventDateTime = Carbon::parse($venuedateTime, $timezone);
        $currentTime = now()->tz($timezone);
        $hoursRemaining = $currentTime->diffInHours($eventDateTime, false); // false ensures negative values if eventDateTime is in the past
        $slotsAppearBefore = intval($slot_appear_hours);
        if ($hoursRemaining >= 0 && $hoursRemaining <= $slotsAppearBefore) {
            return [
                'allowed' => true,
                'message' => 'Ticket booking is allowed.',
                'hours_remaining' => $hoursRemaining,
            ];
        } elseif ($hoursRemaining < 0) {
            return [
                'allowed' => false,
                'message' => 'Ticket booking time has passed.',
                'hours_passed' => abs($hoursRemaining),
            ];
        } else {
            return [
                'allowed' => false,
                'message' => 'Ticket booking is not yet allowed.  Kindly Wait for ' .$hoursRemaining.' hours' ,
                'hours_until_open' => $hoursRemaining,
            ];
        }

        // return $hoursRemaining >= 0 && $hoursRemaining <= $slotsAppearBefore;
    }
}
