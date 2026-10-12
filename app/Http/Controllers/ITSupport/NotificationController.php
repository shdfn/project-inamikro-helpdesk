<?php

namespace App\Http\Controllers\ITSupport;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $notifications = Notification::where('user_id', $userId)
            ->with(['ticket', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ⭐ MARK AS READ
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('it-support.notifications.index', compact('notifications'));
    }
}