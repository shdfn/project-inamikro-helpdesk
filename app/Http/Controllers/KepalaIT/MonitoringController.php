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

        // ═══ 1. First Response Time & SLA Global ═══
        $avgResponRaw = Ticket::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;
        $avgRespon = $this->formatDuration($avgResponRaw);

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

        // ═══ 2, 3, & 4. Metrik Per IT Support ═══
        $onDuty = User::where('role', 'it_support')
            ->get()
            ->map(function ($it) use ($month, $year) {
                // [Poin 4] Total Tiket & Status
                $totalTiket = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count();

                $tiketSelesai = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->whereIn('status', ['Selesai', 'Ditutup'])
                    ->count();

                $tiketMenggantung = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
                    ->count();

                // [Poin 1] First Response Time (Rata-rata & Persentase)
                $slaOk = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->whereNotNull('first_response_at')
                    ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at) <= 30')
                    ->count();

                $totalResponIT = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->whereNotNull('first_response_at')
                    ->count();
                
                $slaPct = $totalResponIT > 0 ? round(($slaOk / $totalResponIT) * 100, 2) : 0;

                $avgMinutes = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->whereNotNull('first_response_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as m')
                    ->value('m') ?? 0;

                // [Poin 2] Total Waktu Penyelesaian (Resolution Time)
                $avgResolutionRaw = Ticket::where('it_support_id', $it->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->whereNotNull('resolved_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as m')
                    ->value('m') ?? 0;

                // [Poin 3] Keaktifan & Keterlibatan (Fallback: pakai data dari tabel tickets)
                // Karena tabel log belum ada, kita asumsikan "terlibat" = total tiket yang dihandle
                $tiketTerlibat = $totalTiket; 
                
                // Menggunakan total tiket sebagai proxy jumlah aktivitas jika log belum ada
                $jumlahAktivitas = $totalTiket;

                // First Responder: Berapa banyak tiket di mana dia adalah IT yang merespon pertama
                // Karena belum ada tabel log, kita asumsikan semua tiket yang dia tangani adalah respon pertamanya
                $jadiFirstResponder = $totalResponIT; 

                // --- Memasukkan data ke object ---
                $it->total_tiket = $totalTiket;
                $it->tiket_selesai = $tiketSelesai;
                $it->tiket_menggantung = $tiketMenggantung;
                $it->sla_percentage = $slaPct;
                $it->avg_response = $this->formatDuration($avgMinutes);
                $it->avg_resolution = $this->formatDuration($avgResolutionRaw); 
                
                // Proxy data untuk Poin 3
                $it->jumlah_balasan = $jumlahAktivitas;
                $it->tiket_terlibat = $tiketTerlibat;
                $it->first_responder_count = $jadiFirstResponder;
                
                // Logika Label
                if ($slaPct >= 90) {
                    $it->status_class = 'success'; $it->status_label = 'Sangat Cepat';
                } elseif ($slaPct >= 75) {
                    $it->status_class = 'primary'; $it->status_label = 'Cepat';
                } elseif ($slaPct >= 50) {
                    $it->status_class = 'warning'; $it->status_label = 'Lambat';
                } else {
                    $it->status_class = 'danger'; $it->status_label = 'Sangat Lambat';
                }
                
                $it->total_ditangani = $tiketSelesai;
                $it->sedang_ditangani = $tiketMenggantung;

                return $it;
            });

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