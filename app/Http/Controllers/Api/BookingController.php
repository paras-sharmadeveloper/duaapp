<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\{Vistors, VenueSloting, VenueAddress, Ipinformation, Timezone , WorkingLady};
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Aws\S3\S3Client;


class BookingController extends Controller
{
    use OtpTrait;

    public function showQrScan(Request $request)
    {
        return view('site-admin.scan-qr');
    }
    public function showGunScan(Request $request)
    {
        return view('site-admin.bar-code');
    }


    public function CountTotalPrints(Request $request)
    {
        try {
            $visitor = Vistors::findOrFail($request->input('id'));
            // FindOrFail will throw an exception if the visitor is not found

            // Increment the print count
            $visitor->print_count++;

            // Save the updated count
            $visitor->save();

            return response()->json(['success' => true, 'message' => 'Print count incremented successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to increment print count']);

        }


    }


    public function processScan(Request $request)
    {
        // Process the scanned content here
        $id = $request->input('id');
        $workingLady = $databaseImage = '';

        $update =[];
        $visitor = Vistors::where(['booking_uniqueid' => $id ])->first();

        if(!empty($visitor->working_lady_id) ){
            $workingLady = WorkingLady::findOrFail($visitor->working_lady_id);
           $databaseImage = getImagefromS3($workingLady->session_image);
        //    $databaseImage = '';
        }
        $UserImage = '';
        $localImage = '';
        $localImageStroage = 'sessionImages/' . date('d-m-Y').'/'. (!empty($visitor)) ? $visitor->recognized_code :'';
        if (!empty($localImageStroage) && !Storage::disk('public_uploads')->exists($localImageStroage)) {
            $localImage = (!empty($visitor->recognized_code)) ? $visitor->recognized_code:'';
        }



        $timezone = $visitor->venueSloting->venueAddress->timezone;
        if(!empty($visitor->recognized_code) && empty($localImage)){
            $UserImage = getImagefromS3($visitor->recognized_code);
        }
        $currentTime = Carbon::parse(date('Y-m-d H:i:s'));
        $now = $currentTime->timezone($timezone);

        $startAt = Carbon::parse($now->format('Y-m-d H:i:s'));
        $endAt = Carbon::parse($now->format('Y-m-d H:i:s'));

        $printToken = view('frontend.print-token',compact('visitor','UserImage','workingLady','databaseImage','localImage'))->render();

        if($visitor->user_status == 'admitted' && $visitor->is_available == 'confirmed') {
            return response()->json(['success' => false , 'message' => 'User already Confirmed and Already Printed','printToken' => $printToken ,'print' => false ]);
        }


        if($request->input('type') == 'start'){
            $update = [
                'meeting_start_at' => $startAt,
                'user_status' => 'in-meeting'
            ];

        }else if($request->input('type') == 'end'){
            $totalTimeSpent = $startAt->diffInSeconds($endAt);
            $update = [
                'meeting_ends_at' =>  $endAt,
                'user_status' => 'meeting-end',
                'meeting_total_time' => $totalTimeSpent
            ];

        }else if($request->input('type') == 'verify'){
            $update = [
                'confirmed_at' => $now->format('Y-m-d H:i:s'),
                'user_status' => 'admitted',
                'is_available' => 'confirmed'
            ];

        }
        $visitor->update($update);

        return response()->json(['success' => true ,'printToken' => $printToken ,'token' => $visitor->slot->token_id ,
        'message' => 'Confirmed. Token Number '. $visitor->slot->token_id  ]);

        // Perform necessary actions based on the scanned content

    }

    public function getImagefromS3($imageName)
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' =>env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $bucket = 'kahayfaqeer-booking-bucket';

        $imageObject = $s3->getObject([
            'Bucket' => $bucket,
            'Key' => $imageName,
        ]);

        $imageData = $imageObject['Body'];

        return $imageData;
    }



    public function BookingCancle(Request $request, $id)
    {
        $vistor = [];
        $vistor = Vistors::where(['booking_uniqueid' => $id])->get()->first();
        if (!$vistor) {
            $message = "No Booking found.";
            return view('frontend.not-found', compact('message'));
        }
        $currentRoute = $request->route()->getName();
        if ($request->has('_token') &&  $currentRoute == 'book.cancle') {
            $phone = $request->input('phone');


            $userDetail = [];
            if ($vistor->phone  == $phone) {
                $userDetail['country_code'] = $vistor->country_code;
                $userDetail['mobile'] =  $vistor->phone;
                $userDetail['email'] =   $vistor->email;
                $country = $vistor->country_code;
                $isMobile = true;
                $isEmail = true;
                $otp = $this->SendOtp($userDetail, $isMobile, $isEmail);
                if ($otp['status']) {
                    return redirect()->back()
                        ->with(['success' => 'Opt Has been sent successfully', 'enable' => true, 'booking_number' => $phone]);
                } else {
                    return redirect()->back()->with([
                        'error' => 'failed to sent otp . please check your details.',
                        'enable' => false,
                        'booking_number' => $phone
                    ]);
                }
            } else {
                return redirect()->back()->with(['error' => 'Mobile Number not matched', 'enable' => false]);
            }
        } else if ($request->has('_token') &&  $currentRoute == 'book.cancle.otp') {

            $otp = $request->input('otp');
            $result = $this->VerifyOtp($otp);

            if ($result['status']) {
                $ipInfo = Ipinformation::where(['user_ip' => $request->ip()])->get()->first();
                $userDetail = json_decode($ipInfo['complete_data'], true);
                $slotTime =  $vistor->venueSloting->slot_time;
                $countryCode = $userDetail['countryCode'];
                $timezone = Timezone::where(['country_code' => $countryCode])->get()->first();
                $currentTimezone = $timezone->timezone;
                $veneueAddressId = $vistor->venueSloting->venue_address_id;
                $venueAddress = VenueAddress::find($veneueAddressId);
                $mytime = Carbon::now()->tz($currentTimezone);
                $eventDate = Carbon::parse($venueAddress->venue_date . ' ' . $slotTime, $currentTimezone)->format('d-M-Y g:i A');

                $url = route('book.show');
                $date = date('Y-m-d H:i A');

                $message = <<<EOT
                Hi $vistor->fname,

                Your appointment ref # $vistor->booking_number on $eventDate has been successfully cancelled.

                You can book a new appointment again by clicking below link:
                $url

                Thanks
                KahayFaqeer.org
                EOT;
                // $message = "Your Booking (" . $vistor->booking_uniqueid . ") has been Succssfully Cancelled. \n You can Book Your Slot Again at here " . route('book.show') . "\nThanks\n Team KahayFaqeer";
                $this->SendMessage($vistor->country_code, $vistor->phone, $message);
                if (Vistors::where(['booking_uniqueid' => $id])->delete()) {
                    // Storage::disk('s3')->delete($vistor->recognized_code);
                    return redirect()->back()->with(['success' => 'Your booking Cancelled Successfully', 'book_seat' => true]);
                }
            }
            return redirect()->back()->with(['error' =>  $result['message']]);
        } else {
            return view('frontend.booking-cancle', compact('vistor'));
        }
    }
    public function BookingReschdule(Request $request, $id)
    {
        $vistor = [];
        $vistor = Vistors::where(['booking_uniqueid' => $id])->get()->first();
        if (!$vistor) {
            $message = "No Booking found.";
            return view('frontend.not-found', compact('message'));
        }
        return view('frontend.booking-reschdule', compact('vistor'));
    }
    public function ConfirmBookingAvailabilityShow(Request $request)
    {
        return view('frontend.confirm-spot');
    }
    public function ConfirmBookingAvailabilityOptConfirmation(Request $request)
    {
        return view('frontend.confirm-spot');
    }


    public function ConfirmBookingAvailability(Request $request)
    {

        $request->validate([
            'booking_number' => 'required'
        ]);
        $bookingId = $request->input('booking_number');

        $currentRoute = $request->route()->getName();
        $visitor = Vistors::where('email', $bookingId)
            ->orWhere('phone', $bookingId)
            ->orWhere('booking_number', $bookingId)
            ->get()
            ->first();
        if (!$visitor) {
            return redirect()->back()->with(['error' => 'We Unable to find any record with your information Provided. Please try with some other detail or recheck.']);
        }
        if ($request->has('_token') &&  $currentRoute == 'booking.confirm-spot.post') {
            $phone = $visitor->phone;
            $country = $visitor->country_code;
            // $otp = $this->SendOtp($phone, $country);
            $userDetail['country_code'] = $visitor->country_code;
            $userDetail['mobile'] =  $visitor->phone;
            $userDetail['email'] =   $visitor->email;
            // $country = $visitor->country_code;
            $isMobile = true;
            $isEmail = true;
            $otp = $this->SendOtp($userDetail, $isMobile, $isEmail);
            if ($otp['status']) {
                return redirect()->back()->with([
                    'success' => 'Opt Has been sent successfully',
                    'enable' => true,
                    'booking_number' => $bookingId
                ]);
            } else {
                return redirect()->back()->with([
                    'error' => 'failed to sent otp . please check your details.',
                    'enable' => false,
                    'booking_number' => $bookingId
                ]);
            }
        } else if ($request->has('_token') &&  $currentRoute == 'booking.confirm-spot.otp.post') {

            $otp = $request->input('otp');
            $result = $this->VerifyOtp($otp);
            if ($result['status']) {
                $url = route('booking.status', [$visitor->booking_uniqueid]);
                $date = date('Y-m-d H:i:s A');
                Vistors::where('id', $visitor->id)->update(['is_available' => 'confirmed', 'confirmed_at' => date('Y-m-d H:i:s')]);

                $message = <<<EOT
                Hi  $visitor->fname,

                Your appointment ref # $visitor->booking_number on $date has been successfully confirmed.

                You can now check your status by clicking below link:
                $url

                Thanks
                KahayFaqeer.org
                EOT;

                // $message = "Hi ".$visitor->fname.",\nYour Booking " . $visitor->booking_number . " has been Succssfully Confirmed. \nYou can Wait for Number. \nYou can check  you Status here\n" .route('booking.status',[$visitor->booking_uniqueid]) ."\nThanks\nTeam\nKahayFaqeer";
                $this->SendMessage($visitor->country_code, $visitor->phone, $message);

                return redirect()->back()->with(['success' => 'Your booking has been Confirmed Successfully']);
            }
            return redirect()->back()->with(['error' =>  $result['message']]);
        }
    }

    public function CustomerBookingStatusWithId(Request $request, $id)
    {
         $decodeId = base64_decode($id);
        $userBooking = Vistors::where('id', $decodeId)->get()->first();
        if (!$userBooking) {
            $message = "Not found.";
            return view('frontend.not-found', compact('message'));
        }
        $userSlot = VenueSloting::where(['id' => $userBooking->slot_id])->get()->first();

        $userSlotTime = $userSlot->slot_time;
        $slotType = $userSlot->type;
        // Assuming 'time' is the column where you store the slot time
        $venueAddress = VenueAddress::find($userSlot->venue_address_id);
        // Calculate the start of slots
        $startTimemrg = $venueAddress->slot_starts_at_morning;


        // Count bookings from the start time until the user's slot time
        $aheadPeople = Vistors::whereHas('slot', function ($query) use ($startTimemrg, $userSlotTime) {
            // $query->where('slot_time', '>=', $startTimemrg)
            //     ->where('slot_time', '<', $userSlotTime);
        })->count();
        $serveredPeople = Vistors::whereNotNull('meeting_ends_at')->get()->count();


        App::setLocale($userBooking->lang);

        return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople', 'userBooking' ,'slotType'));
    }

    public function CustomerBookingStatusD(Request $request)
    {
        $id = $request->input('i');
        $userBooking = Vistors::where('booking_uniqueid', $id)->get()->first();
        if (!$userBooking) {
            $message = "Not found.";
            return view('frontend.not-found', compact('message'));
        }
        // Get the user's slot time
        $userSlot = VenueSloting::where(['id' => $userBooking->slot_id])->get()->first();

        $userSlotTime = $userSlot->slot_time;
        $slotType = $userSlot->type;
        // Assuming 'time' is the column where you store the slot time
        $venueAddress = VenueAddress::find($userSlot->venue_address_id);
        // Calculate the start of slots
        $startTimemrg = $venueAddress->slot_starts_at_morning;


        // Count bookings from the start time until the user's slot time
        $aheadPeople = Vistors::whereHas('slot', function ($query) use ($startTimemrg, $userSlotTime) {
            // $query->where('slot_time', '>=', $startTimemrg)
            //     ->where('slot_time', '<', $userSlotTime);
        })->count();
        $serveredPeople = Vistors::whereNotNull('meeting_ends_at')->get()->count();


        App::setLocale($userBooking->lang);

        return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople', 'userBooking' ,'slotType'));
    }


    public function CustomerBookingStatus(Request $request, $id)
    {

        $userBooking = Vistors::where('booking_uniqueid', $id)->get()->first();
        if (!$userBooking) {
            $message = "Not found.";
            return view('frontend.not-found', compact('message'));
        }

        // Get the user's slot time
        $userSlot = VenueSloting::where(['id' => $userBooking->slot_id])->get()->first();

        $userSlotTime = $userSlot->slot_time;
        $slotType = $userSlot->type;
        // Assuming 'time' is the column where you store the slot time
        $venueAddress = VenueAddress::find($userSlot->venue_address_id);
        // Calculate the start of slots
        $startTimemrg = $venueAddress->slot_starts_at_morning;


        // Count bookings from the start time until the user's slot time
        $aheadPeople = Vistors::whereHas('slot', function ($query) use ($startTimemrg, $userSlotTime) {
            // $query->where('slot_time', '>=', $startTimemrg)
            //     ->where('slot_time', '<', $userSlotTime);
        })->count();
        $serveredPeople = Vistors::whereNotNull('meeting_ends_at')->get()->count();


        App::setLocale($userBooking->lang);

        return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople', 'userBooking' ,'slotType'));
    }









    private function PdfHtml($logoDataUri, $imageUrl ,$bookingStatus, $bookUrl,   $eventDate, $venueDateTime, $venueAddress, $userBooking ,$translations)
    {


        $pdf_title = $translations['pdf_title_1'] ;
        $pdf_title_confirm = $translations['pdf_title_confirm'] ;
        $pdf_title_confirm_with = $translations['pdf_title_confirm_with'] ;
        $pdf_event_date_label = $translations['pdf_event_date_label'] ;
        $pdf_event_venue_label = $translations['pdf_event_venue_label'] ;
        $pdf_event_token_label = $translations['pdf_event_token_label'] ;
        $pdf_event_token_view_label = $translations['pdf_event_token_view_label'];
        $pdf_event_token_appointment_lable = $translations['pdf_event_token_appointment_lable'];
        $pdf_event_token_question = $translations['pdf_event_token_question'];
        $pdf_event_token_mint = $translations['pdf_event_token_mint'];
        $pdf_event_token_mints = $translations['pdf_event_token_mints'];
        $city = $translations[$venueAddress->city ];

        $Week_day = $translations['Week_day_'.$eventDate];

        $txt = $pdf_event_token_mints;
        if ($venueAddress->slot_duration == 1) {
            $txt = $pdf_event_token_mint;
        }

        $imgtag ="<img src='".$imageUrl."'>";


        return <<<HTML

        <style>
            body{ font-family: 'Jameel Noori Nastaleeq Regular','Regular'}
                span.text-center.text-success.confirm{font-size:21px}.venue-info h6,.stats h3{color:#000}.queue-number span{font-size:20px;color:#000}.orng{color:#000}
             h6{color:#000;text-align:center;font-size:14px}h2{color:#000;text-align:center;margin-top:1px;font-size:20px}.ahead-number{font-size:20px;color:#000;border:3px solid #000;margin:20px 0;padding:5px 4px;border-radius:10px;font-weight:700;width:50%;text-align:center}h3{color:#000;text-align:center;margin-top:10px;font-size:20px}p{text-align:center;font-weight:400;font-size:18px;color:#000}.stats{border-radius:10px;width:80%;margin-top:20px}h4{color:#000;font-weight:500;text-align:center;font-size:14px;margin-bottom:2px}span{color:#000;font-size:18px;font-weight:600}.blue-btn{background-color:#004aad;color:#fff;padding:10px 20px;border:0;border-radius:5px;font-size:18px;cursor:pointer;margin:10px 0;width:100%;transition:background-color .3s}.blue-btn:hover{background-color:#00367a}.column.second{background-color:transparent;box-shadow:none}.column.third{width:30%;max-height:540px;overflow-y:auto;background-color:#fff;box-shadow:0 4px 10px rgba(0,0,0,0.1);padding:20px}.visitor-list{list-style-type:none;padding:0;margin:0}.visitor-item{border-bottom:1px solid #e0e0e0;padding:10px 0}.visitor-item h4{color:#000;margin-bottom:5px}.visitor-item p{color:black;margin-bottom:5px}.booking-details{display:flex;justify-content:space-between;align-items:center}.booking-id{color:orange}.slot-time{color:lightgrey;display:flex;align-items:center}.slot-time i{margin-right:5px}.column.second{width:30%}@media only screen and (max-width:992px){@font-face{font-family:'Jameel-Noori-Nastaleeq-Regular';src:url({{public_path('assets/fonts/Jameel-Noori-Nastaleeq-Regular.ttf')}}) format('truetype')}.urdu-text{font-family:'Jameel-Noori-Nastaleeq-Regular',sans-serif}.column.first,.column.second,.column.third{width:100%;margin-bottom:20px}.blue-btn{width:48%;margin-right:4%;margin-bottom:10px}.blue-btn:nth-child(even){margin-right:0}
             .queue-number {
                font-size: 28px;
                color: #000;
                border: 3px solid #000;
                margin: 2px auto; /* Set margin-top and margin-bottom to 2px, and auto for left and right margins to center horizontally */
                border-radius: 10px;
                font-weight: 700;
                width: 70%; /* Set width to 70% */
                text-align: center; /* Center text within the div */
                display: flex;
                align-items: center;
            }
            .container{display:flex;width:100%;padding:4px}h1.text-center{text-align:center;font-size:23px}}
            .first{
                text-align :center;
                width:100% !important;
                flex-direction: column !important;
                align-items: center;
            }
            .queue-qr-scan{
                margin-top:10px;
            }
            </style>
        <section id="mainsection">
            <div class="container">
                <!-- main content -->
                <div class="main-content" id="main-target">
                    <div class="d-flex justify-content-center " style="text-align: center">
                        <a href="bookUrl" class="logoo  d-flex align-items-center wuto">
                            <img src="$logoDataUri" alt="" style="height:100px" >
                        </a>
                    </div>
                    <a href="https://kahayfaqeer.org/" ><h2>KahayFaqeer.org </h2></a>

                    <h2 class="text-center" style="font-family:Jameel Noori Nastaleeq Regular"> $pdf_title  <span class="text-center text-success h2 statement-notes" style="color:green"> <b> $pdf_title_confirm </b>
                    </span> <br> <b> $pdf_title_confirm_with </b>
                   </h2>

                    <div class="first">
                        <h2 class="">$pdf_event_date_label : $Week_day $venueDateTime </h4>

                            <h2 class=""> $pdf_event_venue_label :  $city </h2>

                                <h5>$venueAddress->address</h5>

                            <div class="queue-number">
                            $pdf_event_token_label # $userBooking->booking_number
                                <p>$userBooking->country_code   $userBooking->phone </p>

                            </div>
                            <div class="queue-qr-scan">
                                $imgtag
                            </div>

                            <h3>$pdf_event_token_appointment_lable</h3>

                            <div class="stats text-center" style="text-align: center; width:100%">
                              <p>$venueAddress->slot_duration $txt 1 $pdf_event_token_question </p>

                              <p class="urdu-text1" style="text-align: center; white-space: nowrap;">$venueAddress->status_page_note</p>


                                <p style="text-align: center" >$pdf_event_token_view_label:</p>
                                <p style="text-align: center" > <a style="text-align: center" href="$bookingStatus"
                                        target="_blank">$bookingStatus </a>
                                </p>
                            </div>

                    </div>
                </div>
            </div>
        </section>
        HTML;
    }
}
