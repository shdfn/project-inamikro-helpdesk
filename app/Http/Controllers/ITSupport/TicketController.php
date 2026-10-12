<?php

namespace App\Http\Controllers\ITSupport;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Category;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    // ============ TIKET MASUK ============
    public function incoming(Request $request)
    {
        $query = Ticket::with(['mentor', 'category'])
            ->whereIn('status', ['Baru', 'Diproses']);

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter prioritas
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->get();

        $categories = Category::all();

        return view('it-support.tickets.incoming', compact('tickets', 'categories'));
    }

    // ============ TIKET DITANGANI SAYA ============
    public function myTickets(Request $request)
    {
        $query = Ticket::with(['mentor', 'category'])
            ->where('it_support_id', auth()->id())
            ->where('status', 'Diproses');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('updated_at', 'desc')->get();
        $categories = Category::all();

        return view('it-support.tickets.my', compact('tickets', 'categories'));
    }

    // ============ RIWAYAT & ARSIP ============
    public function history(Request $request)
    {
        $query = Ticket::with(['mentor', 'category'])
            ->where('it_support_id', auth()->id())
            ->whereIn('status', ['Selesai', 'Ditutup']);

        // Filter search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%");
            });
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

        $tickets = $query->orderBy('updated_at', 'desc')->get();
        $categories = Category::all();

        return view('it-support.tickets.history', compact('tickets', 'categories'));
    }
    
    // ============ EXPORT CSV ============
    public function export(Request $request)
    {
        // Query sama persis dengan history()
        $query = Ticket::with(['mentor', 'category'])
            ->where('it_support_id', auth()->id())
            ->whereIn('status', ['Selesai', 'Ditutup']);

        // Filter search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%");
            });
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

        $tickets = $query->orderBy('updated_at', 'desc')->get();

        // Nama file
        $filename = 'riwayat-tiket-' . str_replace(' ', '-', auth()->user()->name) . '-' . now()->format('Ymd-His') . '.csv';

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

            // BOM UTF-8 (supaya Excel bisa baca karakter Indonesia)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom
            fputcsv($file, [
                'ID Tiket',
                'Judul Kendala',
                'Kategori',
                'Prioritas',
                'Pelapor',
                'Status',
                'Tanggal Dibuat',
                'Tanggal Selesai',
            ]);

            // Isi data
            foreach ($tickets as $t) {
                fputcsv($file, [
                    $t->ticket_number,
                    $t->title,
                    $t->category->name ?? '-',
                    $t->priority,
                    $t->mentor->name ?? '-',
                    $t->status,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->resolved_at ? $t->resolved_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ============ DETAIL TIKET ============
    public function show($id)
    {
        $ticket = Ticket::with(['category', 'mentor', 'itSupport', 'messages.user'])
            ->findOrFail($id);

        return view('it-support.tickets.show', compact('ticket'));
    }

    // ============ TANGANI TIKET ============
    public function assign($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->it_support_id && $ticket->it_support_id !== auth()->id()) {
            return back()->with('error', 'Tiket sudah ditangani oleh IT Support lain.');
        }

        $ticket->update([
            'it_support_id' => auth()->id(),
            'status' => 'Diproses',
            'first_response_at' => $ticket->first_response_at ?? now(),
        ]);

        // 📬 Kirim notifikasi ke Mentor (pelapor tiket)
        NotificationHelper::send(
            $ticket->mentor_id,
            $ticket->id,
            auth()->id(),
            'assigned',
            'Tiket Sedang Ditangani',
            auth()->user()->name . ' sedang menangani tiket #' . $ticket->ticket_number
        );

        return redirect()->route('it.tickets.show', $ticket->id)
            ->with('success', 'Tiket berhasil diambil. Silakan tangani.');
    }

    // ============ KIRIM PESAN DISKUSI ============
    public function sendMessage(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // IT Support hanya bisa balas kalau tiket ditangani olehnya
        if ($ticket->it_support_id !== auth()->id()) {
            return back()->with('error', 'Anda belum mengambil tiket ini.');
        }

        if (in_array($ticket->status, ['Ditutup'])) {
            return back()->with('error', 'Tiket sudah ditutup.');
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,log,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        // Set first_response_at kalau belum ada
        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        // 📬 Kirim notifikasi ke Mentor
        NotificationHelper::send(
            $ticket->mentor_id,
            $ticket->id,
            auth()->id(),
            'message',
            'Balasan dari IT Support',
            auth()->user()->name . ' membalas tiket #' . $ticket->ticket_number
        );

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    // ============ TANDAI SELESAI ============
    public function markResolved($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->it_support_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak berhak menandai tiket ini selesai.');
        }

        $ticket->update([
            'status' => 'Selesai',
            'resolved_at' => now(),
        ]);

        // 📬 Kirim notifikasi ke Mentor
        NotificationHelper::send(
            $ticket->mentor_id,
            $ticket->id,
            auth()->id(),
            'resolved',
            'Tiket Selesai Ditangani',
            'Tiket #' . $ticket->ticket_number . ' telah selesai. Menunggu konfirmasi Anda.'
        );

        return back()->with('success', 'Tiket ditandai selesai. Menunggu konfirmasi Mentor.');
    }
}