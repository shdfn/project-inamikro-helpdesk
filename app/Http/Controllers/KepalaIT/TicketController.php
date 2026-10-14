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

        // Search engine
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status & kategori
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::all();

        $counts = [
            'semua'    => Ticket::count(),
            'baru'     => Ticket::where('status', 'Baru')->count(),
            'diproses' => Ticket::where('status', 'Diproses')->count(),
            'selesai'  => Ticket::whereIn('status', ['Selesai', 'Ditutup'])->count(),
        ];

        return view('kepala-it.tickets.index', compact('tickets', 'categories', 'counts'));
    }

    // ================= TIKET LAMBAT =================
    public function late()
    {
        $tickets = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Baru', 'Diproses'])
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

        // Gunakan view IT Support untuk detail (bisa dipakai ulang)
        return view('it-support.tickets.show', compact('ticket'));
    }
}