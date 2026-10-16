<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class EskalasiController extends Controller
{
    public function index()
    {
        // Tiket eskalasi = belum selesai & > 24 jam
        $tiketEskalasi = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24')
            ->orderBy('created_at', 'asc')
            ->get();

        // Metrik
        $totalEskalasi = $tiketEskalasi->count();
        $slaTercapai = Ticket::whereNotNull('resolved_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();
        $totalResolved = Ticket::whereNotNull('resolved_at')->count();
        $persenSLA = $totalResolved > 0 ? round(($slaTercapai / $totalResolved) * 100, 1) : 0;

        return view('kepala-it.eskalasi', compact(
            'tiketEskalasi', 'totalEskalasi', 'persenSLA'
        ));
    }
}