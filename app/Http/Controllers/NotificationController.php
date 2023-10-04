<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

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
}
