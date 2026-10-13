<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    // ================= SLA IT SUPPORT =================
    public function sla()
    {
        // Rata-rata respon pertama (menit)
        $avgResponse = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
            ->value('m') ?? 0;

        // SLA keseluruhan
        $resolved = Ticket::whereIn('status', ['Selesai', 'Ditutup'])->count();
        $slaOk    = Ticket::whereNotNull('resolved_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();
        $slaPercent = $resolved > 0 ? round(($slaOk / $resolved) * 100, 1) : 0;

        // Teknisi on-duty
        $onDuty = User::where('role', 'it_support')->count();

        // Performa per IT Support
        $itSupports = User::where('role', 'it_support')->get()->map(function ($u) {
            $avg = Ticket::where('it_support_id', $u->id)
                ->whereNotNull('first_response_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
                ->value('m') ?? 0;

            $totalHandled = Ticket::where('it_support_id', $u->id)->count();

            $selesai = Ticket::where('it_support_id', $u->id)
                ->whereIn('status', ['Selesai', 'Ditutup'])->count();

            $slaOk = Ticket::where('it_support_id', $u->id)
                ->whereNotNull('resolved_at')
                ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
                ->count();

            $slaPct = $selesai > 0 ? round(($slaOk / $selesai) * 100) : 0;

            // Label & warna
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
                'role_label'     => 'IT Support',
                'handled'        => $totalHandled,
                'resolved'       => $selesai,
                'avg_response'   => round($avg) . ' mnt',
                'sla_percentage' => $slaPct,
                'status'         => $status,
                'status_class'   => $class,
            ];
        });

        return view('kepala-it.monitoring.sla', compact(
            'avgResponse', 'slaPercent', 'onDuty', 'itSupports'
        ));
    }

    // ================= STATISTIK KENDALA =================
    public function statistik()
    {
        // Total tiket
        $total = Ticket::count();

        // Rata-rata MTTR (Mean Time To Resolve) dalam menit
        $avgMttr = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as m')
            ->value('m') ?? 0;

        // Top 5 kategori
        $topCategories = Ticket::select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Statistik per bulan (6 bulan terakhir)
        $ticketsPerMonth = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('kepala-it.monitoring.statistik', compact(
            'total', 'topCategories', 'avgMttr', 'ticketsPerMonth'
        ));
    }
}