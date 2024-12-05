<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;


use App\Models\WhatsAppNotificationNumbers;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWhatsAppMessage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PhoneNumbersImport;

class NotificationController extends Controller
{
    public function index()
    {
        // Retrieve unread notifications
        $notifications = Notification::where('read', false)->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        // Mark a notification as read
        $notification = Notification::find($id);

        if ($notification) {
            $notification->read = true;
            $notification->save();
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function showForm()
    {

        $recipients = WhatsAppNotificationNumbers::all();  // Adjust to your model and table

        return view('whatsappNotifications.whatsapp-notifications', compact('recipients'));
    }

    public function import(Request $request)
    {
        // Validate CSV upload
        $validated = $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        // Import CSV data to the phone numbers table
        Excel::import(new PhoneNumbersImport, $request->file('file'));

        return redirect()->route('whatsapp.form')->with('success', 'CSV Imported Successfully.');
    }

    public function sendMessages(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');

        // Get all phone numbers
        $phoneNumbers = PhoneNumber::all();

        foreach ($phoneNumbers as $phoneNumber) {
            // Dispatch a job to send the message
            SendWhatsAppMessage::dispatch($phoneNumber->country_code, $phoneNumber->phone, $message);
        }

        return back()->with('success', 'Messages are queued for sending.');
    }
}
