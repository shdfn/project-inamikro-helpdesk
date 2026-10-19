<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Halaman Monitoring Eksekutif (default).
     * Menampilkan: rata-rata respon, penyelesaian SLA, teknisi on-duty, kepuasan, kategori teratas.
     */
    public function index(Request $request)
    {
        // Filter bulan (default: bulan ini)
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $avgResponRaw = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        // Format otomatis (Menit -> Jam -> Hari)
        if ($avgResponRaw < 60) {
            $avgRespon = round($avgResponRaw, 2) . ' menit';
        } elseif ($avgResponRaw < 1440) { // < 24 jam
            $avgRespon = round($avgResponRaw / 60, 2) . ' jam';
        } else {
            $days = floor($avgResponRaw / 1440);
            $hours = floor(($avgResponRaw % 1440) / 60);
            $avgRespon = $days . ' hari ' . $hours . ' jam';
        }
        // Penyelesaian SLA (< 24 jam)
        $totalSelesai = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['Selesai', 'Ditutup'])
            ->count();

        $dalamSLA = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('resolved_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();

        $persenSLA = $totalSelesai > 0 ? round(($dalamSLA / $totalSelesai) * 100, 1) : 0;

        // Teknisi On-Duty (yang sedang menangani tiket)
        $onDuty = User::where('role', 'it_support')
            ->withCount([
                'ticketsAsSupport as sedang_ditangani' => function ($q) {
                    $q->whereIn('status', ['Ditangani IT', 'Dibalas IT', 'Dibalas Mentor']);
                },
                'ticketsAsSupport as total_ditangani' => function ($q) {
                    $q->whereIn('status', ['Selesai', 'Ditutup']);
                },
            ])
            ->get();

        // 5 Kategori Kendala Teratas (bulan ini)
        $kategoriTeratas = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->whereMonth('tickets.created_at', $month)
            ->whereYear('tickets.created_at', $year)
            ->select('categories.name', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('categories.name')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // Kepuasan Pengguna (dummy)
        $kepuasan = 4.5;

        return view('kepala-it.monitoring', compact(
            'avgRespon', 'persenSLA', 'totalSelesai', 'dalamSLA',
            'onDuty', 'kategoriTeratas', 'kepuasan', 'month', 'year'
        ));
    }

    /**
     * Alias method SLA — sama dengan index().
     * Dipakai oleh route `kepala.monitoring.sla`.
     */
    public function sla(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Alias method statistik — redirect ke halaman statistik.
     * Dipakai oleh route `kepala.monitoring.statistik`.
     */
    public function statistik()
    {
        return redirect()->route('kepala.statistik');
    }
}