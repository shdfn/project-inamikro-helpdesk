<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ═══ Filter Periode (default: bulan ini) ═══
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // ═══ Statistik Utama (berdasarkan filter periode) ═══
        $total = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $open = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->count();

        $closed = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('status', ['Selesai', 'Ditutup'])
            ->count();

        // ═══ First Response Time (rata-rata menit) ═══
        $avgResponseRaw = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        $avgResponse = $this->formatDuration($avgResponseRaw);

        // ═══ SLA % (First Response ≤ 30 menit) ═══
        $totalRespon = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->count();

        $slaOk = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at) <= 30')
            ->count();

        $slaPercent = $totalRespon > 0
            ? round(($slaOk / $totalRespon) * 100, 2)
            : 0;

        // ═══ Resolution Time (rata-rata menit) ═══
        $avgResolutionRaw = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        $avgResolution = $this->formatDuration($avgResolutionRaw);

        // ═══ Performa per IT Support (bulan ini) ═══
        $itSupports = User::where('role', 'it_support')
        ->get()
        ->map(function ($it) use ($month, $year) {
            // Total tiket yang pernah direspons IT ini
            $totalTiket = Ticket::where('it_support_id', $it->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereNotNull('first_response_at')
                ->count();

            // Tiket selesai/ditutup
            $tiketSelesai = Ticket::where('it_support_id', $it->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereIn('status', ['Selesai', 'Ditutup'])
                ->count();

            // ✅ BARU: Tiket menggantung (masih aktif, belum closed)
            $tiketMenggantung = Ticket::where('it_support_id', $it->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
                ->count();

            // Tiket yang memenuhi SLA (respon ≤ 30 menit)
            $slaOk = Ticket::where('it_support_id', $it->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereNotNull('first_response_at')
                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at) <= 30')
                ->count();

            // Rata-rata response time
            $avg = Ticket::where('it_support_id', $it->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereNotNull('first_response_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
                ->value('m') ?? 0;

            $it->initial = strtoupper(substr($it->name, 0, 2));
            $it->total_tiket = $totalTiket;
            $it->tiket_selesai = $tiketSelesai;
            $it->tiket_menggantung = $tiketMenggantung; // ✅ BARU
            $it->sla_percentage = $totalTiket > 0 ? round(($slaOk / $totalTiket) * 100, 2) : 0;
            $it->avg_response = $this->formatDuration($avg);
            $it->status = $it->sla_percentage >= 90
                ? 'Excellent'
                : ($it->sla_percentage >= 75 ? 'Baik' : 'Perlu Perbaikan');
            $it->status_class = $it->sla_percentage >= 90
                ? 'success'
                : ($it->sla_percentage >= 75 ? 'warning' : 'danger');

            return $it;
        });

        // ═══ Kategori Populer ═══
        $kategoriPopuler = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->whereMonth('tickets.created_at', $month)
            ->whereYear('tickets.created_at', $year)
            ->select('categories.name', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('categories.name')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // ═══ Tiket Lambat (> 120 menit belum selesai) ═══
        $lateTickets = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 120')
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // ═══ Distribusi Status ═══
        $statusDistribusi = [
            'Baru' => Ticket::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Baru')->count(),
            'Ditangani IT' => Ticket::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Ditangani IT')->count(),
            'Dibalas IT' => Ticket::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Dibalas IT')->count(),
            'Dibalas Mentor' => Ticket::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Dibalas Mentor')->count(),
            'Selesai' => Ticket::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Selesai')->count(),
            'Ditutup' => Ticket::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status', 'Ditutup')->count(),
        ];

        return view('kepala-it.dashboard', compact(
            'total', 'open', 'closed',
            'avgResponse', 'avgResolution', 'slaPercent',
            'itSupports', 'kategoriPopuler', 'lateTickets',
            'statusDistribusi', 'month', 'year'
        ));
    }

    /**
     * Format durasi (menit) ke "X jam Y menit" atau "X hari Y jam".
     */
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