<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Ticket::where('mentor_id', Auth::id())
            ->whereIn('status', ['Diproses', 'Selesai', 'Ditutup'])
            ->with('category')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('mentor.notifications.index', compact('notifications'));
    }
}