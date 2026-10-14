<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class NotificationController extends Controller
{
    public function index()
    {
        // Tiket lambat (>2 jam) dan belum selesai
        $lateTickets = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Diproses'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 120')
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kepala-it.notifications.index', compact('lateTickets'));
    }
}