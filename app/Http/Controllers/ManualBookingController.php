<?php

namespace App\Http\Controllers;

use App\Jobs\{WhatsAppConfirmation,WhatsAppTokenNotBookNotifcation};
use App\Models\VenueAddress;
use App\Models\VenueSloting;
use App\Models\VisitorTempEntry;
use App\Models\Vistors;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ManualBookingController extends Controller
{
    //

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
                $message = 'Today your booking will not confirm , Please try again';
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
            $booking->working_lady_id = $request->input('working_lady_id', 0);
            $booking->token_status = 'vaild';
            $booking->save();
            $bookingId = $booking->id;
            WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');

            $visitorTemp->update(['action_at' => date('Y-m-d H:i:s'),'action_status' => 'approved']);
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
}
