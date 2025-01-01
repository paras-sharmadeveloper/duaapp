<?php

namespace App\Http\Controllers;

use App\Jobs\{WhatsAppConfirmation, WhatsAppTokenNotBookNotifcation};
use App\Models\VenueAddress;
use App\Models\VenueSloting;
use App\Models\VisitorTempEntry;
use App\Models\Vistors;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\DahuaHelper;
use Carbon\Carbon;

class ManualBookingController extends Controller
{
    //

    private $dahuaHelper;

    private $ip;
    public function __construct()
    {
        // Instantiate DahuaHelper with username and password
        // You can set these as env variables or hard-code them
        $username = env('DAHUA_USERNAME', 'admin'); // Username from .env or default
        $password = env('DAHUA_PASSWORD', 'admin@123'); // Password from .env or default
        $ip = '192.168.31.200';
        $this->dahuaHelper = new DahuaHelper($username, $password);
    }
    public function getVisitorList(Request $request)
    {
        $date = $request->input('filter_date', date('Y-m-d'));  // Get filter date from the request, default to today
        $startTime = microtime(true);  // Start time for performance tracking

        $endDate = Carbon::today();
        $targetDate = Carbon::parse($date);  // Parse the filter date

        // Handle sorting, searching, and pagination parameters sent by DataTable
        $searchValue = $request->input('search.value');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);  // Number of records per page

        // Start with the main query to fetch visitors (not just phone numbers)
        $visitorQuery = VisitorTempEntry::whereDate('created_at', $targetDate)
            ->select('phone', 'created_at', 'venueId', 'id', 'country_code', 'recognized_code', 'dua_type', 'msg_sid', 'action_at', 'action_status')
            ->with('venueAddress'); // Assuming 'venueAddress' is a relation for venue info

        // Apply search filter if search value is provided (to filter by phone number or other fields)
        if ($searchValue) {
            $visitorQuery->where(function ($query) use ($searchValue) {
                $query->where('phone', 'like', '%' . $searchValue . '%')
                    ->orWhere('created_at', 'like', '%' . $searchValue . '%');
            });
        }

        // Get the total number of records before pagination (for `recordsTotal`)
        $totalRecords = $visitorQuery->count();

        // Apply pagination on the visitor query
        $visitorQuery->skip($start)->take($length);

        // Execute the query to get the paginated results
        $visitorList = $visitorQuery->get();

        $visitorData = [];
        $endDate = Carbon::today();
        $totalVisits = $visitorList->count();
        // Loop through each visitor record to populate the response data
        foreach ($visitorList as $entry) {
            $venueId = $entry->venueId;
            $venueAddress = $entry->venueAddress;
            $repeatVisitorDays = $venueAddress ? $venueAddress->repeat_visitor_days : 0;
            $startDate = $targetDate->copy()->subDays($repeatVisitorDays);  // Calculate the start date for repeat visitors
            // Get the last visit entry

            $lastVisit = VisitorTempEntry::where('phone', $entry->phone)
                ->whereDate('created_at', '<', $request->input('filter_date'))  // Filter to get visits before or on the filter date
                ->orderBy('created_at', 'desc')  // Sort by the most recent visit (descending order)
                ->first();  // Get the most recent visit (last visit)

            // Check if the last visit exists and set its created_at as the last visit date
            $lastVisitDate = $lastVisit ? $lastVisit->created_at->toDateString() : null;

            // Calculate total visits for the phone number in the specified date range
            $totalVisits = VisitorTempEntry::where('phone', $entry->phone)
                ->whereDate('created_at', '<=', $targetDate)  // Filter visits before or on the filter date
                ->count();

            $visitorData[] = [
                'phone_number' => $entry->phone,
                'total_visits' => $totalVisits,
                'last_visit' => $lastVisitDate,
                'last' => $lastVisit,
                // 'last_visit' => $entry->created_at->toDateString(),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'visitor_id' => $entry->id,
                'created_at' => $entry->created_at->format('Y-m-d'),
                'country_code' => $entry->country_code,
                'phone' => $entry->phone,
                'recognized_code' => $entry->recognized_code,
                'dua_type' => $entry->dua_type,
                'msg_sid' => $entry->msg_sid,
                'action_at' => $entry->action_at,
                'action_status' => $entry->action_status
            ];
        }

        $endTime = microtime(true);  // End time for performance tracking
        $executionTime = $endTime - $startTime;  // Calculate the execution time

        // Return the data in the required format for DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,  // Total records before any filtering
            'recordsFiltered' => $totalRecords,  // You can adjust this for filtering results
            'data' => $visitorData,  // The visitor data to be displayed in the table
            'executionTime' => $executionTime,  // Include the execution time in the response (optional)
        ]);
    }



    public function getVisitorListold(Request $request)
    {
        $date = $request->input('filter_date', date('Y-m-d'));  // Get filter date from the request, default to today
        $startTime = microtime(true);  // Start time for performance tracking

        $endDate = Carbon::today();
        $targetDate = Carbon::parse($date);  // Parse the filter date

        // Handle sorting, searching, and pagination parameters sent by DataTable
        $searchValue = $request->input('search.value');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);  // Number of records per page



        // Fetch distinct phone numbers for the given filter date
        $phoneNumbersQuery = VisitorTempEntry::whereDate('created_at', $targetDate)
            // ->distinct('phone')
            ->select('phone', 'created_at', 'venueId', 'id');

        // Apply search filter if search value is provided (to filter by phone number or other fields)
        if ($searchValue) {
            $phoneNumbersQuery->where(function ($query) use ($searchValue) {
                $query->where('phone', 'like', '%' . $searchValue . '%')
                    ->orWhere('created_at', 'like', '%' . $searchValue . '%');
            });
        }

        // Get the filtered phone numbers with pagination
        $phoneNumbers = $phoneNumbersQuery->skip($start)->take($length)->get();
        $totalRecords = $phoneNumbersQuery->count();  // Get total records before pagination

        $visitorData = [];

        // Loop through each phone number and retrieve corresponding visitor data
        foreach ($phoneNumbers as $data) {
            $venueId = $data->venueId;
            $venueAddress = VenueAddress::find($venueId, ['repeat_visitor_days']);
            $repeatVisitorDays = $venueAddress ? $venueAddress->repeat_visitor_days : 0;
            $startDate = $targetDate->copy()->subDays($repeatVisitorDays);  // Calculate the start date for repeat visitors

            // Fetch the list of visitors based on the phone number and date range
            $visitorListQuery = VisitorTempEntry::where('phone', $data->phone)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'asc');

            // Apply search filter on the visitor list query
            if ($searchValue) {
                $visitorListQuery->where(function ($query) use ($searchValue) {
                    $query->where('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('created_at', 'like', '%' . $searchValue . '%');
                });
            }

            // Get the visitor entries with pagination
            $visitorList = $visitorListQuery->skip($start)->take($length)->get();

            // Get total visits count for the phone number
            $totalVisits = $visitorList->count();
            $lastVisit = $visitorList->last();  // Get the last visit entry

            // Add the visitor data to the response array
            foreach ($visitorList as $entry) {
                $visitorData[] = [
                    'phone_number' => $data->phone,
                    'total_visits' => $totalVisits,
                    'last_visit' => $lastVisit ? $lastVisit->created_at->toDateString() : null,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'visitor_id' => $entry->id,
                    'created_at' => Carbon::parse($entry->created_at)->format('Y-m-d'),
                    'country_code' => $entry->country_code,
                    'phone' => $entry->phone,
                    'recognized_code' => $entry->recognized_code,
                    'dua_type' => $entry->dua_type,
                    'msg_sid' => $entry->msg_sid,
                    'action_at' => $entry->action_at,
                    'action_status' => $entry->action_status
                ];
            }
        }

        $endTime = microtime(true);  // End time for performance tracking
        $executionTime = $endTime - $startTime;  // Calculate

        // Return the data in the required format for DataTables
        return response()->json([
            'start' => $start,
            'take' => $length,
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,  // Total records before any filtering
            'recordsFiltered' => $totalRecords,  // You can adjust this for filtering results
            'data' => $visitorData,  // The data to be displayed in the table
            'executionTime' => $executionTime,  // Include the execution time in the response (optional)
        ]);
    }


    // public function list(request $request)
    // {
    //     $date = $request->input('filter_date',date('Y-m-d'));

    //     $startTime = microtime(true);
    //     $endDate = Carbon::today();
    //     $targetDate = Carbon::parse($date );

    //     $phoneNumbers = VisitorTempEntry::whereDate('created_at', $targetDate)->distinct('phone')->get(['phone','created_at','venueId']);
    //     // echo "<pre>"; print_r($phoneNumbers); die;
    //     $venueId = $phoneNumbers->isNotEmpty() ? $phoneNumbers[0]->venueId : null;
    //         if ($venueId) {
    //             $venueAddress = VenueAddress::find($venueId, ['repeat_visitor_days', 'id']);
    //         } else {
    //             $venueAddress = null;
    //         }
    //         $repeatVisitorDays = $venueAddress ? $venueAddress->repeat_visitor_days : 0;
    //     $visitorData = [];
    //     foreach ($phoneNumbers as $data) {
    //         $startDate = $targetDate->subDays($repeatVisitorDays);
    //         $visitorList = VisitorTempEntry::where('phone',  $data['phone'])
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->orderBy('created_at', 'asc')
    //             ->get();
    //         $totalVisits = $visitorList->count();
    //         $lastVisit = $visitorList->last();
    //         $visitorData[] = [
    //             'phone_number' =>  $data['phone'],
    //             'total_visits' => $totalVisits,
    //             'last_visit' => $lastVisit ? $lastVisit->created_at->toDateString() : null, // Format the last visit date
    //             'start_date' => $startDate->toDateString(),
    //             'end_date' => $endDate->toDateString(),
    //             'visitorList' => $visitorList,
    //         ];
    //     }
    //     $endTime = microtime(true);
    //     $executionTime = $endTime - $startTime;
    //     return view('manualBooking.list', compact('visitorData','executionTime'));
    // }





    public function list(request $request)
    {
        // RecurringDays
        $date = $request->input('filter_date', date('Y-m-d'));
        //$visitorList = VisitorTempEntry:: orderBy('id','asc')->get();
        $visitorList = VisitorTempEntry::whereDate('created_at', $date)
            ->orderBy('id', 'asc')
            ->get();
        // echo "<pre>"; print_r($visitorList); die;
        return view('manualBooking.list', compact('visitorList'));
    }


    public function ApproveDisapproveBulk(Request $request)
    {
        $ids = $request->input('ids');
        $type = $request->input('type');
        $message = [];
        foreach ($ids as $id) {
            $visitorTemp = VisitorTempEntry::find($id);
            if ($type  == 'approve') {
                $uuid = Str::uuid()->toString();

                $venueAddress = VenueAddress::find($visitorTemp->venueId);
                $tokenIs = VenueSloting::where('venue_address_id', $visitorTemp->venueId)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $visitorTemp->dua_type])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id'])->first();

                if (empty($tokenIs)) {
                    return response()->json([
                        'status' =>  false,
                        'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                        'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                    ], 200);
                }

                $isPerson = Vistors::where(['phone' => $visitorTemp->phone])->whereDate('created_at', date('Y-m-d'))->count();

                if ($isPerson  > 0) {

                    $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'), 'action_status' => 'Already Token Recived']);
                    continue;
                    // return response()->json([
                    //     'status' =>  false,
                    //     'message' => 'This Person already got the token.',
                    //     'message_ur' => 'اس شخص کو پہلے ہی ٹوکن مل گیا ہے۔',
                    // ], 200);
                }

                $booking = new Vistors;

                $tokenId =  $tokenIs->token_id;
                $slot_id =  $tokenIs->id;

                $booking->country_code = $visitorTemp->country_code;
                $booking->phone = $visitorTemp->phone;
                $booking->user_question =  $request->input('user_question', null);
                $booking->slot_id =  $slot_id;
                $booking->is_whatsapp = $request->has('is_whatsapp') ? 'yes' : 'no';
                $booking->booking_uniqueid = $uuid;
                $booking->user_ip =   $request->ip();
                $booking->recognized_code = $visitorTemp->recognized_code;
                $booking->booking_number = $tokenId;
                $booking->meeting_type = $venueAddress->type;
                $booking->user_timezone = $visitorTemp->user_timezone;
                $booking->source = 'Website';
                $booking->dua_type = $visitorTemp->dua_type;
                $booking->lang = $visitorTemp->lang;
                $booking->working_lady_id = $request->input('working_lady_id', 0);
                $booking->token_status = 'vaild';
                $booking->save();
                $bookingId = $booking->id;
                WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');

                $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'), 'action_status' => 'approved']);
            } else if ($type  == 'disapprove') {
                $message = "Kindly please be informed that all dua & dum tokens today have been issued to people at first come first serve basis. Your entry came when the token quota was already completed. Therefore our system is unable to issue you token today. Kindly please try again next week at 8:00 AM sharp.";

                $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'), 'action_status' => 'disapproved']);
                $completeNumber = $visitorTemp->country_code . $visitorTemp->phone;
                WhatsAppTokenNotBookNotifcation::dispatch($visitorTemp->id, $completeNumber, $message)->onQueue('whatsapp-notification-not-approve');
            }
        }

        return response()->json([
            'message' => 'Operation Successfull',
            "status" => true,
        ], 200);
    }

    public function ApproveDisapprove(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $visitorTemp = VisitorTempEntry::find($id);
        if ($type  == 'approve') {
            $uuid = Str::uuid()->toString();

            $venueAddress = VenueAddress::find($visitorTemp->venueId);
            $tokenIs = VenueSloting::where('venue_address_id', $visitorTemp->venueId)
                ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                ->where(['type' => $visitorTemp->dua_type])
                ->orderBy('id', 'ASC')
                ->select(['venue_address_id', 'token_id', 'id'])->first();

            if (empty($tokenIs)) {

                return response()->json([
                    'status' =>  false,
                    'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                    'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                ], 200);
            }

            $isPerson = Vistors::where(['phone' => $visitorTemp->phone])->whereDate('created_at', date('Y-m-d'))->count();

            if ($isPerson  > 0) {
                $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'), 'action_status' => 'Already Token Recived']);

                return response()->json([
                    'status' =>  false,
                    'message' => 'This Person already got the token.',
                    'message_ur' => 'اس شخص کو پہلے ہی ٹوکن مل گیا ہے۔',
                ], 200);
            }

            $booking = new Vistors;

            $tokenId =  $tokenIs->token_id;
            $slot_id =  $tokenIs->id;

            $booking->country_code = $visitorTemp->country_code;
            $booking->phone = $visitorTemp->phone;
            $booking->user_question =  $request->input('user_question', null);
            $booking->slot_id =  $slot_id;
            $booking->is_whatsapp = $request->has('is_whatsapp') ? 'yes' : 'no';
            $booking->booking_uniqueid = $uuid;
            $booking->user_ip =   $request->ip();
            $booking->recognized_code = $visitorTemp->recognized_code;
            $booking->booking_number = $tokenId;
            $booking->meeting_type = $venueAddress->type;
            $booking->user_timezone = $visitorTemp->user_timezone;
            $booking->source = 'Website';
            $booking->dua_type = $visitorTemp->dua_type;
            $booking->lang = $visitorTemp->lang;
            $booking->working_lady_id = $visitorTemp->working_lady_id;
            $booking->qr_code_image = $visitorTemp->working_qr_id;

            $booking->token_status = 'vaild';
            $booking->save();
            $bookingId = $booking->id;
            WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');

            $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'), 'action_status' => 'approved']);

            // Dahua Code

            // $cardNo = mt_rand(10000000, 9999999999); // Current date and time
            // $validStartDate = date('Ymd%20His');  // Current date and time

            // $validEndDate = date('Ymd%20His', strtotime('+1 day'));
            // $response = $this->dahuaHelper->addAccessCard($this->ip, $bookingId, 'add_user', $cardNo, 1, $validStartDate, $validEndDate);
            // $responseArray = explode('=', $response);
            // $recNo = '';
            // if (isset($responseArray[1])) {
            //     $recNo = $responseArray[1];  // This will be '4'
            //     $localImageStroage = 'sessionImages/30-09-2024/' . !empty($visitorTemp->recognized_code)? $visitorTemp->recognized_code : '';
            //     $compressedImagePath = 'sessionImages/30-09-2024/compressed_' . (!empty($visitorTemp->recognized_code) ? $visitorTemp->recognized_code : '') . '.jpg';

            //     if ($this->compressImage($localImageStroage, $compressedImagePath)) {
            //         // Convert the compressed image to Base64
            //         $base64Image = $this->imageToBase64($compressedImagePath);

            //         // Now you can use the $base64Image in your API request
            //         // Example: Pass the Base64 encoded image in the API request
            //         $this->dahuaHelper->addFaceAccess($this->ip, $recNo, 'add_user_'.$bookingId, $base64Image);

            //        // echo "Base64 Image: " . $base64Image; // Debugging output
            //     }
            //  }



            return response()->json([
                'message' => 'token Issued ' . $tokenId,
                "status" => true,
            ], 200);
        } else if ($type  == 'disapprove') {
            $message = "Kindly please be informed that all dua & dum tokens today have been issued to people at first come first serve basis. Your entry came when the token quota was already completed. Therefore our system is unable to issue you token today. Kindly please try again next week at 8:00 AM sharp.";
            $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'), 'action_status' => 'disapproved']);
            $completeNumber = $visitorTemp->country_code . $visitorTemp->phone;
            WhatsAppTokenNotBookNotifcation::dispatch($visitorTemp->id, $completeNumber, $message)->onQueue('whatsapp-notification-not-approve');

            return response()->json([
                'message' => 'Disapproved',
                "status" => false,
            ], 200);
        }
    }

    function compressImage($source, $destination, $maxSize = 100000)
    {
        // Get the image info
        list($width, $height, $type) = getimagesize($source);
        $image = null;

        // Load the image based on its type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        // Set compression quality
        $quality = 90;
        $outputFile = $destination;

        // Compress the image until the size is under the maxSize
        while (filesize($outputFile) > $maxSize && $quality > 10) {
            // Save the image with reduced quality
            imagejpeg($image, $outputFile, $quality);
            $quality -= 5; // Decrease quality by 5 with each iteration
        }

        // Free the memory
        imagedestroy($image);

        // Return true if image is successfully compressed
        return true;
    }

    function imageToBase64($imagePath)
    {
        // Get the image contents
        $imageData = file_get_contents($imagePath);
        // Encode the image into base64
        return base64_encode($imageData);
    }
}
