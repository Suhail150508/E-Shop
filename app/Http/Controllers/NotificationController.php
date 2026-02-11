<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Show unread notifications
    public function index()
    {
        $notifications = Auth::user()->unreadNotifications()->limit(10)->get();

        return response()->json($notifications);
    }
    // Mark all notifications as read
    public function markRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
    // Mark a single notification as read
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}
