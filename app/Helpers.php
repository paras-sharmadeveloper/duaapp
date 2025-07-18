<?php

use App\Models\Timezone;
use App\Models\Venue;
use App\Models\{Reason, Vistors,VenueSloting, WorkingLady};
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
// use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Aws\S3\S3Client;

function isMobileDevice(Request $request)
{
    // echo "here";die;
    $userAgent = $request->header('User-Agent');

    // Use a regular expression to check if the user agent contains common mobile device keywords
    $isMobile = preg_match("/(android|blackberry|iphone|ipod|mobile|palm|phone|windows\s+ce)/i", $userAgent);

    return $isMobile;
}

function getObjectfromS3($sourceImageKey)
    {
        $bucket = 'kahayfaqeer-booking-bucket';
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' =>env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        $sourceImage = $s3->getObject([
            'Bucket' =>  $bucket,
            'Key' => $sourceImageKey,
        ]);
      return  $sourceImage['Body']->getContents();

    }

function getImagefromS3($imageName)
    {

        //  return "D".env('AWS_ACCESS_KEY_ID');
        try {
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => 'us-east-1',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = 'kahayfaqeer-booking-bucket';

            $imageObject = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $imageName,
            ]);

            $imageData = $imageObject['Body'];

        } catch (\Exception $e) {


            $imageData = $e->getMessage();
        }

        return $imageData;
    }

    function getWorkingLady($working_lady_id){
        return WorkingLady::findOrFail($working_lady_id);
    }

    function getVisitor($uuid){
        return Vistors::with(relations: 'slot')->where(['booking_uniqueid' => $uuid])->first();
    }

    function deleteObject($key){
        $bucket = 'kahayfaqeer-booking-bucket';
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' =>env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        $result = $s3->deleteObject([
            'Bucket' => $bucket,
            'Key'    => $key,
        ]);
        if ($result['DeleteMarker']) {
           return true;
        } else {
            return false;
        }
    }
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
                'message' => 'You cannot book dua/dum token from your country as online or phone dua appointment is not possible. For more information you can submit query via  "Contact Us" on our website KahayFaqeer.org',
                'message_ur' => 'آپ اپنے ملک سے دعا/دم ٹوکن بک نہیں کر سکتے کیونکہ آن لائن یا فون کی دعا اپائنٹمنٹ ممکن نہیں ہے۔ مزید معلومات کے لیے آپ ہماری ویب سائٹ KahayFaqeer.org پر "ہم سے رابطہ کریں" کے ذریعے استفسار جمع کر سکتے ہیں۔',
            ];
        }


    }

}

if (!function_exists('TokenBookingAllowed')) {
    function TokenBookingAllowed($venueStartDateTime, $venueEndDateTime, $timezone)
    {
        //  $timezone ='Europe/London';
        if($timezone == 'Asia/Karachi'){
             $venueStartTime = Carbon::parse($venueStartDateTime,$timezone);
        }else{
            $venueStartTime = Carbon::parse($venueStartDateTime);
            $venueStartTime = $venueStartTime->setTimezone($timezone);
        }


        // $venueStartTime = Carbon::parse($venueStartDateTime,$timezone);
        $venueEndTime = Carbon::parse($venueEndDateTime,$timezone);
        $currentTime = Carbon::now()->tz($timezone);

        if($currentTime->gte($venueStartTime) && $currentTime->lte($venueEndTime)){

            return [
                'allowed' => true,
                'message' => 'Token booking is allowed.' ,
                'message_ur' => 'ٹوکن بکنگ کی اجازت ہے۔',
                'currentTime' => $currentTime->format('d M y h:i A'),
                'mytime' => $currentTime->format('d M Y h:i A'),
            ];

        }else if ($currentTime->gt($venueEndTime)) {
            return [
                'allowed' => false,
                'mytime' => $currentTime->format('d M Y h:i A'),
                'message' => 'Dua / Dum token booking is now closed as all tokens are now allocated to visitors. Please try again next week. Thank you',
                'message_ur' =>  'دعا/دم ٹوکن کی بکنگ اب بند ہے کیونکہ تمام ٹوکنز اب زائرین کے لیے مختص ہیں۔ براہ کرم اگلے ہفتے دوبارہ کوشش کریں۔ شکریہ',

            ];

        }else{
            return [
                'tz' => $timezone,
                'allowed' => false,
                'mytime' => $currentTime->format('d M Y h:i A'),
                'message' =>'Token Booking for Dua / Dum has not started yet. Kindly try again at below mentioned time: '.$venueStartTime->format('d M Y').' at  '.$venueStartTime->format('h:i A').' ('.$timezone.') Time zone',
                'message_ur' => 'دعا/دم ملاقات کے لیے ٹوکن بکنگ ابھی شروع نہیں ہوئی ہے۔ براہ مہربانی نیچے دیئے گئے وقت پر دوبارہ کوشش کریں۔ '.$venueStartTime->format('d-M-Y').' at '.$venueStartTime->format('h:i A').' ('.$timezone.') Timezon',
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
                'message' => 'Token booking has not started yet.  Kindly Wait for ' . $hoursRemaining . ' hours ' ,
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

        $user = Vistors::where('phone', $mobile)
               ->orderBy('id', 'desc')
               ->first();


            if (!empty($user)) {
                $recordAge = $user->created_at->diffInDays(now());
                $rejoin = $rejoin_venue_after;
                $daysRemaining = $rejoin  - $recordAge;


                if ($rejoin > 0 && $recordAge <= $rejoin &&  $daysRemaining > 0) {

                    return [
                        'allowed' => false,
                        'message' => 'You already Booked a Token with us. Please Try after some days',
                        'message_ur' => 'آپ نے پہلے ہی ہمارے ساتھ ایک ٹوکن بک کر رکھا ہے۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                       // 'message_ur' => 'آپ نے پہلے ہی ہمارے ساتھ ایک ٹوکن بک کر رکھا ہے۔ براہ کرم ' . $daysRemaining . ' دن کے بعد کوشش کریں۔',
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
                    'days_remaining' => 0,
                ];
            }
        }
    }


if (!function_exists('getCurrentContryTimezone')) {
    function getCurrentContryTimezone($id)
    {

        // $currentCountry = Venue::find($id);
        $currentCountry = Venue::first();
        $timezone =  Timezone::where(['country_code' => $currentCountry->iso])->first();
        Config::set('app.timezone', $timezone->timezone);
        $time = Carbon::now()->tz($timezone->timezone);
        //  $countryDate = Carbon::parse(date('Y-m-d'),$timezone->timezone);
        return $time->format('Y-m-d');
    }
}
if (!function_exists('getTotalTokens')) {
    function getTotalTokens($venueId , $type){
        return VenueSloting::where(['venue_address_id' => $venueId , 'type' => $type])->count();

    }
}

if (!function_exists('getNoVenueReason')) {
    function getNoVenueReason($lang){
        return Reason::where(['type' => "novenue"])->pluck($lang);
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
