<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik global
        $totalTickets    = Ticket::count();
        $resolvedTickets = Ticket::whereIn('status', ['Selesai', 'Ditutup'])->count();
        $pendingTickets  = Ticket::whereIn('status', ['Baru', 'Diproses'])->count();

        // Rata-rata waktu penyelesaian (jam)
        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        // SLA: tiket selesai < 24 jam
        $slaCompliant = Ticket::whereNotNull('resolved_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();
        $slaPercentage = $resolvedTickets > 0 ? round(($slaCompliant / $resolvedTickets) * 100) : 0;

        // Top kategori
        $topCategories = Ticket::select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Tiket per bulan (6 bulan terakhir)
        $ticketsPerMonth = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('kepala-it.dashboard', compact(
            'totalTickets',
            'resolvedTickets',
            'pendingTickets',
            'avgResolutionTime',
            'slaPercentage',
            'topCategories',
            'ticketsPerMonth'
        ));
    }
}