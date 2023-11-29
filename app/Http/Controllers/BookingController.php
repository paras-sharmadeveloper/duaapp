<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vistors,VenueSloting,VenueAddress,Ipinformation,Timezone};
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
class BookingController extends Controller
{
    use OtpTrait;
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
                $isMobile=true;
                $isEmail = true;
                $otp = $this->SendOtp($userDetail,$isMobile,$isEmail);
                if ($otp['status']) {
                    return redirect()->back()
                    ->with(['success' => 'Opt Has been sent successfully', 'enable' => true, 'booking_number' => $phone ]);
                } else {
                    return redirect()->back()->with(['error' => 'failed to sent otp . please check your details.', 
                    'enable' => false, 
                    'booking_number' => $phone
                ]);
                }
            } else {
                return redirect()->back()->with(['error' => 'Mobile Number not matched','enable' => false]);
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

                $message =<<<EOT
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
    public function BookingReschdule(Request $request,$id)
    {
        $vistor = [];
        $vistor = Vistors::where(['booking_uniqueid' => $id])->get()->first();
        if (!$vistor) {
            $message = "No Booking found.";
            return view('frontend.not-found', compact('message'));
        }
        return view('frontend.booking-reschdule',compact('vistor'));
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
            if(!$visitor){
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
                $isMobile=true;
                $isEmail = true;
                $otp = $this->SendOtp($userDetail,$isMobile,$isEmail);
                if ($otp['status']) {
                    return redirect()->back()->with(['success' => 'Opt Has been sent successfully', 
                    'enable' => true, 
                    'booking_number' => $bookingId
                ]);
                } else {
                    return redirect()->back()->with(['error' => 'failed to sent otp . please check your details.', 
                    'enable' => false, 
                    'booking_number' => $bookingId
                   ]);
                }
             
        } else if ($request->has('_token') &&  $currentRoute == 'booking.confirm-spot.otp.post') {
 
            $otp = $request->input('otp'); 
            $result = $this->VerifyOtp($otp);
            if ($result['status']) {
                $url = route('booking.status',[$visitor->booking_uniqueid]); 
                $date = date('Y-m-d H:i:s A'); 
                Vistors::where('id', $visitor->id)->update(['is_available' => 'confirmed','confirmed_at' => date('Y-m-d H:i:s')]); 

                $message =<<<EOT
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


    public function CustomerBookingStatus(Request $request,$id)
    {

        $userBooking = Vistors::where('booking_uniqueid', $id)->get()->first();
        echo "<pre>"; print_r( $userBooking); die ; 

        if (!$userBooking) {
            $message = "Not found.";
            return view('frontend.not-found', compact('message'));
        }
        
        // Get the user's slot time
        $userSlot = VenueSloting::where(['id' => $userBooking->slot_id])->get();
        
        $userSlotTime = $userSlot->slot_time;  
        // Assuming 'time' is the column where you store the slot time
        $venueAddress = VenueAddress::find($userSlot->venue_address_id);
        // Calculate the start of slots
        $startTimemrg = $venueAddress->slot_starts_at_morning; 
        

        // Count bookings from the start time until the user's slot time
        $aheadPeople = Vistors::whereHas('slot', function ($query) use ($startTimemrg,$userSlotTime) {
            $query->where('slot_time', '>=', $startTimemrg)
                ->where('slot_time', '<', $userSlotTime);
        })->count();
        $serveredPeople = Vistors::whereNotNull('meeting_ends_at')->get()->count();
        return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople'));
    }
}
