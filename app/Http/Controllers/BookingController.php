<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vistors,VenueSloting,VenueAddress};
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Storage;

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
            if ($vistor->phone  == $phone) {
                $country = $vistor->country_code;
                $otp = $this->SendOtp($phone, $country);
                if ($otp['status']) {
                    return redirect()->back()->with(['success' => 'Opt Has been sent successfully', 'enable' => true, 'input_data' => $request->all()]);
                } else {
                    return redirect()->back()->with(['error' => 'failed to sent otp . please check your details.', 'enable' => false, 'input_data' => $request->all()]);
                }
            } else {
                return redirect()->back()->with(['error' => 'Mobile Number not matched']);
            }
        } else if ($request->has('_token') &&  $currentRoute == 'book.cancle.otp') {

            $otp = $request->input('otp');

            $result = $this->VerifyOtp($otp);
            if ($result['status']) {
                $message = "Your Booking (" . $vistor->booking_uniqueid . ") has been Succssfully Cancelled. \n You can Book Your Slot Again at here " . route('book.show') . "\nThanks\n Team KahayFaqeer";
                $this->SendMessage($vistor->country_code, $vistor->phone, $message);
                if (Vistors::where(['booking_uniqueid' => $id])->delete()) {
                    Storage::disk('s3')->delete($$vistor->recognized_code);
                }

                return redirect()->back()->with(['success' => 'Your booking Cancelled Successfully', 'book_seat' => true]);
            }
            return redirect()->back()->with(['error' =>  $result['message']]);
        } else {
            return view('frontend.booking-cancle', compact('vistor'));
        }
    }
    public function BookingReschdule(Request $request)
    {
        return view('frontend.booking-reschdule');
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
                $otp = $this->SendOtp($phone, $country);
                if ($otp['status']) {
                    return redirect()->back()->with(['success' => 'Opt Has been sent successfully', 
                    'enable' => true, 
                    'booking_number' => $request->input('booking_number')
                ]);
                } else {
                    return redirect()->back()->with(['error' => 'failed to sent otp . please check your details.', 
                    'enable' => false, 
                    'booking_number' => $request->input('booking_number')
                   ]);
                }
             
        } else if ($request->has('_token') &&  $currentRoute == 'booking.confirm-spot.otp.post') {
 
            $otp = $request->input('otp'); 
            $result = $this->VerifyOtp($otp);
            if ($result['status']) {
                Vistors::where('id', $visitor->id)->update(['is_available' => 'confirmed','confirmed_at' => date('Y-m-d H:i:s') ]); 
                $message = "Hi ".$visitor->fname.",\nYour Booking " . $visitor->booking_number . " has been Succssfully Confirmed. \nYou can Wait for Number. \nYou can check  you Status here\n" .route('booking.status',[$visitor->booking_uniqueid]) ."\nThanks\nTeam\nKahayFaqeer";
                $this->SendMessage($visitor->country_code, $visitor->phone, $message);
        
                return redirect()->back()->with(['success' => 'Your booking has been Confirmed Successfully']);
            }
            return redirect()->back()->with(['error' =>  $result['message']]);
        }  
    }


    public function CustomerBookingStatus(Request $request,$id)
    {

        $userBooking = Vistors::where('booking_uniqueid', $id)->first();

        if (!$userBooking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // Get the user's slot time
        $userSlot = VenueSloting::find($userBooking->slot_id);
        $userSlotTime = $userSlot->slot_time;  
        // Assuming 'time' is the column where you store the slot time
        $venueAddress = VenueAddress::find($userSlot->venue_address_id);
        // Calculate the start of slots
        $startTime = $venueAddress->slot_starts_at;

        // Count bookings from the start time until the user's slot time
        $aheadPeople = Vistors::whereHas('venueSloting', function ($query) use ($startTime, $userSlotTime) {
            $query->where('slot_time', '>=', $startTime)
                ->where('slot_time', '<', $userSlotTime);
        })->count();
        $serveredPeople = Vistors::whereNotNull('meeting_doneAt')->get()->count();
        return view('frontend.queue-status', compact('aheadPeople', 'venueAddress', 'userSlot', 'serveredPeople'));
    }
}
