<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // ═══ First Response Time ═══
        $avgResponRaw = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        $avgRespon = $this->formatDuration($avgResponRaw);

        // ═══ SLA % (≤ 30 menit) ═══
        $totalRespon = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->count();

        $dalamSLA = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at) <= 30')
            ->count();

        $persenSLA = $totalRespon > 0 ? round(($dalamSLA / $totalRespon) * 100, 2) : 0;

        // ═══ Teknisi On-Duty ═══
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

        // ═══ Kategori Teratas ═══
        $kategoriTeratas = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->whereMonth('tickets.created_at', $month)
            ->whereYear('tickets.created_at', $year)
            ->select('categories.name', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('categories.name')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        return view('kepala-it.monitoring', compact(
            'avgRespon', 'persenSLA', 'totalRespon', 'dalamSLA',
            'onDuty', 'kategoriTeratas', 'month', 'year'
        ));
    }

    public function statistik()
    {
        return redirect()->route('kepala.statistik');
    }

    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return round($minutes, 2) . ' menit';
        } elseif ($minutes < 1440) {
            $hours = floor($minutes / 60);
            $mins = round($minutes % 60);
            return $hours . ' jam ' . $mins . ' menit';
        } else {
            $days = floor($minutes / 1440);
            $hours = floor(($minutes % 1440) / 60);
            return $days . ' hari ' . $hours . ' jam';
        }
    }
}