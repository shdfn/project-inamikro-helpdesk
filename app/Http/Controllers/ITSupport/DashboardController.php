<?php

namespace App\Http\Controllers\ITSupport;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'masuk' => Ticket::where('status', 'Baru')->count(),
            'ditangani' => Ticket::where('it_support_id', $userId)->where('status', 'Diproses')->count(),
            'menunggu' => Ticket::where('status', 'Diproses')->whereNull('it_support_id')->count(),
            'selesai' => Ticket::where('it_support_id', $userId)->whereIn('status', ['Selesai', 'Ditutup'])->count(),
        ];

        $tickets = Ticket::with(['mentor', 'category'])
            ->whereIn('status', ['Baru', 'Diproses'])
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        return view('it-support.dashboard', compact('stats', 'tickets'));
    }
}