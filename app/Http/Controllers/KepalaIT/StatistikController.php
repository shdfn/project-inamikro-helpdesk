<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // ═══ Statistik Global (bulan ini) ═══
        $totalTiket = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $totalSelesai = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['Selesai', 'Ditutup'])
            ->count();

        $totalDitutup = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'Ditutup')
            ->count();

        $sedangDitangani = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->count();

        // ═══ Avg Penyelesaian (format hari/jam/menit) ═══
        $avgResolutionRaw = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        $avgResolution = $this->formatDuration($avgResolutionRaw);

        // ═══ Tren 6 Bulan ═══
        $trenBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trenBulanan[] = [
                'bulan' => $date->format('M Y'),
                'total' => Ticket::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
                'selesai' => Ticket::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->whereIn('status', ['Selesai', 'Ditutup'])->count(),
            ];
        }

        // ═══ Kategori Top ═══
        $kategoriTop = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->whereMonth('tickets.created_at', $month)
            ->whereYear('tickets.created_at', $year)
            ->select('categories.name', DB::raw('COUNT(*) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('kepala-it.statistik', compact(
            'totalTiket', 'totalSelesai', 'totalDitutup', 'sedangDitangani',
            'avgResolution', 'trenBulanan', 'kategoriTop', 'month', 'year'
        ));
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