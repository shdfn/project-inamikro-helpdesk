<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // === Statistik Utama ===
        $total   = Ticket::count();
        $open    = Ticket::whereIn('status', ['Baru', 'Diproses'])->count();
        $closed  = Ticket::whereIn('status', ['Selesai', 'Ditutup'])->count();

        // === Rata-rata Response (menit) ===
        $avgResponse = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
            ->value('m') ?? 0;

        // === SLA Keseluruhan ===
        $slaCompliant = Ticket::whereNotNull('resolved_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();
        $slaPercent = $closed > 0 ? round(($slaCompliant / $closed) * 100, 1) : 0;

        // === Performa SLA IT Support ===
        $itSupports = User::where('role', 'it_support')->get()->map(function ($u) {
            $avg = Ticket::where('it_support_id', $u->id)
                ->whereNotNull('first_response_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
                ->value('m') ?? 0;

            $handled = Ticket::where('it_support_id', $u->id)
                ->whereIn('status', ['Selesai', 'Ditutup'])->count();

            $slaOk = Ticket::where('it_support_id', $u->id)
                ->whereNotNull('resolved_at')
                ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
                ->count();

            $slaPct = $handled > 0 ? round(($slaOk / $handled) * 100) : 0;

            // Tentukan label & class warna
            if ($slaPct >= 95) {
                $status = 'Sangat Cepat'; $class = 'success';
            } elseif ($slaPct >= 90) {
                $status = 'On Track';     $class = 'primary';
            } elseif ($slaPct >= 85) {
                $status = 'Mendekati Target'; $class = 'warning';
            } else {
                $status = 'Perlu Perhatian';  $class = 'danger';
            }

            return (object) [
                'name'           => $u->name,
                'initial'        => strtoupper(substr($u->name, 0, 2)),
                'avg_response'   => round($avg) . ' mnt',
                'handled'        => $handled,
                'sla_percentage' => $slaPct,
                'status'         => $status,
                'status_class'   => $class,
            ];
        });

        // === Top 5 Kategori ===
        $topCategories = Ticket::select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // === Tiket Lambat (> 2 jam / 120 menit) ===
        $lateTickets = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Diproses'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 120')
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        return view('kepala-it.dashboard', compact(
            'total',
            'open',
            'closed',
            'avgResponse',
            'slaPercent',
            'itSupports',
            'topCategories',
            'lateTickets'
        ));
    }
}