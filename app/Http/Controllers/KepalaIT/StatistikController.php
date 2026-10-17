<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        $totalTiket = Ticket::count();
        $totalSelesai = Ticket::whereIn('status', ['Selesai', 'Ditutup'])->count();
        $totalDitutup = Ticket::where('status', 'Ditutup')->count();
        $sedangDitangani = Ticket::whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])->count();

        // Tren 6 bulan terakhir
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

        // Rata-rata waktu penyelesaian
        $avgResolution = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        // Kategori teratas
        $kategoriTop = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('kepala-it.statistik', compact(
            'totalTiket', 'totalSelesai', 'totalDitutup', 'sedangDitangani',
            'trenBulanan', 'avgResolution', 'kategoriTop'
        ));
    }
}