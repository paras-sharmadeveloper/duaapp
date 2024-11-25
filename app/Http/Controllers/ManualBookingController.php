<?php

namespace App\Http\Controllers;

use App\Jobs\{WhatsAppConfirmation,WhatsAppTokenNotBookNotifcation};
use App\Models\VenueAddress;
use App\Models\VenueSloting;
use App\Models\VisitorTempEntry;
use App\Models\Vistors;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\DahuaHelper;

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
        $ip ='192.168.31.200';
        $this->dahuaHelper = new DahuaHelper($username, $password);
    }

    public function list(){
        $visitorList = VisitorTempEntry::whereDate('created_at',date('Y-m-d'))->orderBy('id','asc')->get();

        return view('manualBooking.list',compact('visitorList'));
    }

    public function ApproveDisapproveBulk(Request $request){
        $ids = $request->input('ids');
        $type = $request->input('type');
        $message = [];
        foreach($ids as $id){
            $visitorTemp = VisitorTempEntry::find($id);
            if($type  == 'approve'){
                $uuid = Str::uuid()->toString();

                $venueAddress = VenueAddress::find($visitorTemp->venueId);
                $tokenIs = VenueSloting::where('venue_address_id', $visitorTemp->venueId)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $visitorTemp->dua_type])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id'])->first();

                if(empty($tokenIs)){
                    return response()->json([
                            'status' =>  false,
                            'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                            'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                        ], 200);
                }

                $isPerson = Vistors::where(['phone' => $visitorTemp->phone])->whereDate('created_at',date('Y-m-d'))->count();

                if( $isPerson  > 0)
                {

                    $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'Already Token Recived']);
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

                $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'approved']);


            }else if($type  == 'disapprove'){
                $message = "Kindly please be informed that all dua & dum tokens today have been issued to people at first come first serve basis. Your entry came when the token quota was already completed. Therefore our system is unable to issue you token today. Kindly please try again next week at 8:00 AM sharp.";

                $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'disapproved']);
                $completeNumber = $visitorTemp->country_code.$visitorTemp->phone;
                WhatsAppTokenNotBookNotifcation::dispatch($visitorTemp->id , $completeNumber,$message)->onQueue('whatsapp-notification-not-approve');


            }
        }

        return response()->json([
            'message' => 'Operation Successfull',
            "status" => true,
        ], 200);

    }

    public function ApproveDisapprove(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $visitorTemp = VisitorTempEntry::find($id);
        if($type  == 'approve'){
            $uuid = Str::uuid()->toString();

            $venueAddress = VenueAddress::find($visitorTemp->venueId);
            $tokenIs = VenueSloting::where('venue_address_id', $visitorTemp->venueId)
                ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                ->where(['type' => $visitorTemp->dua_type])
                ->orderBy('id', 'ASC')
                ->select(['venue_address_id', 'token_id', 'id'])->first();

            if(empty($tokenIs)){

                return response()->json([
                        'status' =>  false,
                        'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                        'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                    ], 200);
            }

            $isPerson = Vistors::where(['phone' => $visitorTemp->phone])->whereDate('created_at',date('Y-m-d'))->count();

            if( $isPerson  > 0)
            {
                $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'Already Token Recived']);

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

            $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'approved']);

            $cardNo = mt_rand(10000000, 9999999999); // Current date and time
            $validStartDate = date('Ymd%20His');  // Current date and time

            $validEndDate = date('Ymd%20His', strtotime('+1 day'));
            $response = $this->dahuaHelper->addAccessCard($this->ip, $bookingId, 'add_user', $cardNo, 1, $validStartDate, $validEndDate);
            $responseArray = explode('=', $response);
            $recNo = '';
            if (isset($responseArray[1])) {
                $recNo = $responseArray[1];  // This will be '4'
                $localImageStroage = 'sessionImages/30-09-2024/' . !empty($visitorTemp->recognized_code)? $visitorTemp->recognized_code : '';
                $compressedImagePath = 'sessionImages/30-09-2024/compressed_' . (!empty($visitorTemp->recognized_code) ? $visitorTemp->recognized_code : '') . '.jpg';

                if ($this->compressImage($localImageStroage, $compressedImagePath)) {
                    // Convert the compressed image to Base64
                    $base64Image = $this->imageToBase64($compressedImagePath);

                    // Now you can use the $base64Image in your API request
                    // Example: Pass the Base64 encoded image in the API request
                    $this->dahuaHelper->addFaceAccess($this->ip, $recNo, 'add_user_'.$bookingId, $base64Image);

                   // echo "Base64 Image: " . $base64Image; // Debugging output
                }



             }



            return response()->json([
                'message' => 'token Issued ' .$tokenId,
                "status" => true,
            ], 200);

        }else if($type  == 'disapprove'){
            $message = "Kindly please be informed that all dua & dum tokens today have been issued to people at first come first serve basis. Your entry came when the token quota was already completed. Therefore our system is unable to issue you token today. Kindly please try again next week at 8:00 AM sharp.";
            $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'disapproved']);
            $completeNumber = $visitorTemp->country_code.$visitorTemp->phone;
            WhatsAppTokenNotBookNotifcation::dispatch($visitorTemp->id , $completeNumber,$message)->onQueue('whatsapp-notification-not-approve');

            return response()->json([
                'message' => 'Disapproved',
                "status" => false,
            ], 200);

        }

    }

    function compressImage($source, $destination, $maxSize = 100000) {
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

    function imageToBase64($imagePath) {
        // Get the image contents
        $imageData = file_get_contents($imagePath);

        // Encode the image into base64
        return base64_encode($imageData);
    }

}
