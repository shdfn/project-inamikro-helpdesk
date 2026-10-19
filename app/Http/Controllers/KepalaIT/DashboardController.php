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
        // ═══ Statistik Utama ═══
        $total = Ticket::count();

        // Tiket terbuka = status aktif (belum selesai/ditutup)
        $open = Ticket::whereIn('status', [
            'Baru',
            'Ditangani IT',
            'Dibalas IT',
            'Dibalas Mentor'
        ])->count();

        $closed = Ticket::whereIn('status', ['Selesai', 'Ditutup'])->count();

        // Rata-rata respon pertama (format otomatis)
        $avgResponseRaw = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
            ->value('m') ?? 0;

        if ($avgResponseRaw < 60) {
            $avgResponse = round($avgResponseRaw, 2) . ' menit';
        } elseif ($avgResponseRaw < 1440) {
            $avgResponse = round($avgResponseRaw / 60, 2) . ' jam';
        } else {
            $days = floor($avgResponseRaw / 1440);
            $hours = floor(($avgResponseRaw % 1440) / 60);
            $avgResponse = $days . ' hari ' . $hours . ' jam';
        }

        // ═══ SLA (%) — Berdasarkan target respon ≤ 30 menit (sesuai PRD) ═══
        $totalRespon = Ticket::whereNotNull('first_response_at')->count();
        $slaOk = Ticket::whereNotNull('first_response_at')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at) <= 30')
            ->count();
        $slaPercent = $totalRespon > 0
            ? round(($slaOk / $totalRespon) * 100, 2)
            : 0;

        // ═══ Performa per IT Support ═══
        $itSupports = User::where('role', 'it_support')
            ->get()
            ->map(function ($it) {
                // Total tiket dengan respon
                $totalTiket = Ticket::where('it_support_id', $it->id)
                    ->whereNotNull('first_response_at')
                    ->count();

                // Tiket yang responnya ≤ 30 menit
                $slaOk = Ticket::where('it_support_id', $it->id)
                    ->whereNotNull('first_response_at')
                    ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at) <= 30')
                    ->count();

                // Rata-rata waktu respon
                $avg = Ticket::where('it_support_id', $it->id)
                    ->whereNotNull('first_response_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
                    ->value('m') ?? 0;

                // Setup atribut tambahan
                $it->initial = strtoupper(substr($it->name, 0, 2));
                $it->sla_percentage = $totalTiket > 0
                    ? round(($slaOk / $totalTiket) * 100, 2)
                    : 0;
                if ($avg < 60) {
                    $it->avg_response = round($avg, 2) . ' menit';
                } elseif ($avg < 1440) {
                    $it->avg_response = round($avg / 60, 2) . ' jam';
                } else {
                    $days = floor($avg / 1440);
                    $hours = floor(($avg % 1440) / 60);
                    $it->avg_response = $days . ' hari ' . $hours . ' jam';
                }
                
                // Status berdasarkan SLA %
                $it->status = $it->sla_percentage >= 90
                    ? 'Excellent'
                    : ($it->sla_percentage >= 75 ? 'Baik' : 'Perlu Perbaikan');

                $it->status_class = $it->sla_percentage >= 90
                    ? 'success'
                    : ($it->sla_percentage >= 75 ? 'warning' : 'danger');

                return $it;
            });

        // ═══ Kategori Populer (Top 5) ═══
        $kategoriPopuler = DB::table('tickets')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('categories.name')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // ═══ Tiket Lambat (> 120 menit / 2 jam belum selesai) ═══
        $lateTickets = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 120')
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // ═══ Distribusi Status (untuk Chart di View) ═══
        $statusDistribusi = [
            'Baru' => Ticket::where('status', 'Baru')->count(),
            'Ditangani IT' => Ticket::where('status', 'Ditangani IT')->count(),
            'Dibalas IT' => Ticket::where('status', 'Dibalas IT')->count(),
            'Dibalas Mentor' => Ticket::where('status', 'Dibalas Mentor')->count(),
            'Selesai' => Ticket::where('status', 'Selesai')->count(),
            'Ditutup' => Ticket::where('status', 'Ditutup')->count(),
        ];

        // ═══ Kirim ke View ═══
        return view('kepala-it.dashboard', compact(
            'total',
            'open',
            'closed',
            'avgResponse',
            'slaPercent',
            'itSupports',
            'kategoriPopuler',
            'lateTickets',
            'statusDistribusi'      // ← WAJIB dikirim (dipakai chart)
        ));
    }
}