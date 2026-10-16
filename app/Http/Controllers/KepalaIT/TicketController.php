<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // ================= SEMUA TIKET =================
    public function index(Request $request)
    {
        $query = Ticket::with(['mentor', 'itSupport', 'category']);

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter prioritas
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter tanggal (BARU)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::all();

        return view('kepala-it.tickets.index', compact('tickets', 'categories'));
    }

    // ================= TIKET LAMBAT =================
    public function late()
    {
        // ⚠️ FIX: ganti 'Diproses' ke status baru
        $tickets = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 120')
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->get();

        $jumlahTiket = $tickets->count();

        // Rata-rata keterlambatan (menit)
        $avgKeterlambatan = 0;
        if ($jumlahTiket > 0) {
            $totalMenit = $tickets->sum(function ($t) {
                return $t->created_at->diffInMinutes(now()) - 120;
            });
            $avgKeterlambatan = round($totalMenit / $jumlahTiket);
        }

        // SLA bulan ini
        $totalSelesaiBulanIni = Ticket::whereIn('status', ['Selesai', 'Ditutup'])
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->count();

        $tepatWaktu = Ticket::whereIn('status', ['Selesai', 'Ditutup'])
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();

        $slaSekarang = $totalSelesaiBulanIni > 0
            ? round(($tepatWaktu / $totalSelesaiBulanIni) * 100, 1)
            : 0;

        // Proyeksi SLA (0.5% penalti per tiket lambat)
        $slaProjected = round(max($slaSekarang - ($jumlahTiket * 0.5), 0), 1);

        return view('kepala-it.tickets.late', compact(
            'tickets',
            'jumlahTiket',
            'avgKeterlambatan',
            'slaSekarang',
            'slaProjected'
        ));
    }

    // ================= DETAIL =================
    public function show($id)
    {
        $ticket = Ticket::with(['mentor', 'itSupport', 'category', 'messages.user'])
            ->findOrFail($id);

        // ⚠️ FIX: pakai view kepala-it (read-only), bukan it-support
        return view('kepala-it.tickets.show', compact('ticket'));
    }

    // ================= EXPORT CSV (BARU) =================
    public function export(Request $request)
    {
        $query = Ticket::with(['mentor', 'itSupport', 'category']);

        // Filter search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter prioritas
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        // Nama file
        $filename = 'laporan-tiket-' . now()->format('Ymd-His') . '.csv';

        // Header response
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Callback untuk stream CSV
        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 (Excel bisa baca karakter Indonesia)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom
            fputcsv($file, [
                'ID Tiket',
                'Judul Kendala',
                'Kategori',
                'Prioritas',
                'Pelapor',
                'IT Support',
                'Status',
                'Tanggal Dibuat',
                'Tanggal Respon Pertama',
                'Tanggal Selesai',
            ]);

            // Data
            foreach ($tickets as $t) {
                fputcsv($file, [
                    $t->ticket_number,
                    $t->title,
                    $t->category->name ?? '-',
                    $t->priority,
                    $t->mentor->name ?? '-',
                    $t->itSupport->name ?? '-',
                    $t->status,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->first_response_at ? $t->first_response_at->format('d/m/Y H:i') : '-',
                    $t->resolved_at ? $t->resolved_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}