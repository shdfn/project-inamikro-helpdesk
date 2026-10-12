<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'total' => Ticket::where('mentor_id', $userId)->count(),
            'menunggu' => Ticket::where('mentor_id', $userId)->where('status', 'Baru')->count(),
            'diproses' => Ticket::where('mentor_id', $userId)->where('status', 'Diproses')->count(),
            'selesai' => Ticket::where('mentor_id', $userId)->whereIn('status', ['Selesai', 'Ditutup'])->count(),
        ];

        $recentTickets = Ticket::where('mentor_id', $userId)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('mentor.dashboard', compact('stats', 'recentTickets'));
    }
}