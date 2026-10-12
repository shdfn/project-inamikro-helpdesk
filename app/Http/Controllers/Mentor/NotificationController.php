<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Ambil notifikasi untuk user ini
        $notifications = Notification::where('user_id', $userId)
            ->with(['ticket', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ⭐ MARK AS READ: tandai semua sebagai sudah dibaca
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('mentor.notifications.index', compact('notifications'));
    }
}