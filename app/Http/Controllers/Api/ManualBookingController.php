<?php

namespace App\Http\Controllers\Api;

use App\Jobs\{WhatsAppConfirmation, WhatsAppTokenNotBookNotifcation};
use App\Models\VenueAddress;
use App\Models\VenueSloting;
use App\Models\VisitorTempEntry;
use App\Models\Vistors;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\DahuaHelper;

class ManualBookingController extends Controller
{
    private $dahuaHelper;
    private $ip;

    public function __construct()
    {
        $username = env('DAHUA_USERNAME', 'admin');
        $password = env('DAHUA_PASSWORD', 'admin@123');
        $this->ip = '192.168.31.200';
        $this->dahuaHelper = new DahuaHelper($username, $password);
    }

    public function list()
    {
        $visitorList = VisitorTempEntry::whereDate('created_at', date('Y-m-d'))->orderBy('id', 'asc')->get();

        // Replace the view() function with JSON response or other API-friendly logic
        // return view('manualBooking.list', compact('visitorList'));
        return response()->json(['data' => $visitorList], 200);
    }

    public function ApproveDisapproveBulk(Request $request)
    {
        $ids = $request->input('ids');
        $type = $request->input('type');

        foreach ($ids as $id) {
            $visitorTemp = VisitorTempEntry::find($id);

            if ($type == 'approve') {
                $this->approveVisitor($visitorTemp, $request);
            } elseif ($type == 'disapprove') {
                $this->disapproveVisitor($visitorTemp);
            }
        }

        return response()->json([
            'message' => 'Operation Successful',
            'status' => true,
        ], 200);
    }

    public function ApproveDisapprove(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $visitorTemp = VisitorTempEntry::find($id);

        if ($type == 'approve') {
            return $this->approveVisitor($visitorTemp, $request);
        } elseif ($type == 'disapprove') {
            return $this->disapproveVisitor($visitorTemp);
        }

        return response()->json(['message' => 'Invalid request', 'status' => false], 400);
    }

    private function approveVisitor($visitorTemp, Request $request)
    {
        $uuid = Str::uuid()->toString();
        $venueAddress = VenueAddress::find($visitorTemp->venueId);

        $tokenIs = VenueSloting::where('venue_address_id', $visitorTemp->venueId)
            ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
            ->where(['type' => $visitorTemp->dua_type])
            ->orderBy('id', 'ASC')
            ->select(['venue_address_id', 'token_id', 'id'])->first();

        if (empty($tokenIs)) {
            return response()->json([
                'status' => false,
                'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week.',
                'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔',
            ], 200);
        }

        if (Vistors::where(['phone' => $visitorTemp->phone])->whereDate('created_at', date('Y-m-d'))->exists()) {
            $visitorTemp->update(['action_at' => now(), 'action_status' => 'Already Token Received']);
            return response()->json([
                'status' => false,
                'message' => 'This person already got the token.',
                'message_ur' => 'اس شخص کو پہلے ہی ٹوکن مل گیا ہے۔',
            ], 200);
        }

        $booking = new Vistors;
        $booking->fill([
            'country_code' => $visitorTemp->country_code,
            'phone' => $visitorTemp->phone,
            'user_question' => $request->input('user_question', null),
            'slot_id' => $tokenIs->id,
            'is_whatsapp' => $request->has('is_whatsapp') ? 'yes' : 'no',
            'booking_uniqueid' => $uuid,
            'user_ip' => $request->ip(),
            'recognized_code' => $visitorTemp->recognized_code,
            'booking_number' => $tokenIs->token_id,
            'meeting_type' => $venueAddress->type,
            'user_timezone' => $visitorTemp->user_timezone,
            'source' => 'Website',
            'dua_type' => $visitorTemp->dua_type,
            'lang' => $visitorTemp->lang,
            'working_lady_id' => $visitorTemp->working_lady_id,
            'token_status' => 'valid',
        ]);
        $booking->save();

        WhatsAppConfirmation::dispatch($booking->id)->onQueue('whatsapp-notification');

        $visitorTemp->update(['action_at' => now(), 'action_status' => 'approved']);

        return response()->json([
            'message' => 'Token Issued ' . $tokenIs->token_id,
            'status' => true,
        ], 200);
    }

    private function disapproveVisitor($visitorTemp)
    {
        $message = "Kindly please be informed that all dua & dum tokens today have been issued on a first-come-first-served basis. Your entry came after the token quota was completed. Kindly try again next week.";

        $visitorTemp->update(['action_at' => now(), 'action_status' => 'disapproved']);

        $completeNumber = $visitorTemp->country_code . $visitorTemp->phone;
        WhatsAppTokenNotBookNotifcation::dispatch($visitorTemp->id, $completeNumber, $message)->onQueue('whatsapp-notification-not-approve');

        return response()->json([
            'message' => 'Disapproved',
            'status' => false,
        ], 200);
    }
}
