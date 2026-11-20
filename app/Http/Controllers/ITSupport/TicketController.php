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
    public function incoming(Request $request)
    {
        Ticket::autoCloseExpiredTickets();

        $query = Ticket::with(['mentor', 'itSupport', 'category', 'messages.user'])
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('status')) $query->where('status', $request->status);

        $tickets = $query
            ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
            ->orderBy('created_at', 'asc')
            ->get();

        $categories = Category::all();
        return view('it-support.tickets.incoming', compact('tickets', 'categories'));
    }

    public function myTickets(Request $request)
    {
        Ticket::autoCloseExpiredTickets();

        $query = Ticket::with(['mentor', 'itSupport', 'category', 'messages.user'])
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor']);

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

    public function history(Request $request)
    {
        Ticket::autoCloseExpiredTickets();

        $query = Ticket::with(['mentor', 'category', 'messages.user'])
            ->whereIn('status', ['Selesai', 'Ditutup']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $tickets = $query->orderBy('updated_at', 'desc')->get();
        $categories = Category::all();
        return view('it-support.tickets.history', compact('tickets', 'categories'));
    }

    public function show($id)
    {
        Ticket::autoCloseExpiredTickets();

        $ticket = Ticket::with(['category', 'mentor', 'itSupport', 'messages.user'])
            ->findOrFail($id);

        return view('it-support.tickets.show', compact('ticket'));
    }

    // ============ TANGANI / AMBIL ALIH TIKET ============
    public function assign($id)
    {
        Ticket::autoCloseExpiredTickets();

        $ticket = Ticket::findOrFail($id);

        // Kalau tiket sudah ditangani IT lain, izinkan takeover (ambil alih)
        if ($ticket->it_support_id && $ticket->it_support_id !== auth()->id()) {
            $oldSupport = $ticket->itSupport->name ?? 'IT lain';

            $ticket->update([
                'it_support_id' => auth()->id(),
                'status' => 'Ditangani IT',
                'first_response_at' => $ticket->first_response_at ?? now(),
            ]);

            // Notifikasi ke Mentor
            NotificationHelper::send(
                $ticket->mentor_id,
                $ticket->id,
                auth()->id(),
                'assigned',
                'Tiket Diambil Alih',
                auth()->user()->name . ' mengambil alih tiket #' . $ticket->ticket_number . ' dari ' . $oldSupport
            );

            return redirect()->route('it.tickets.show', $ticket->id)
                ->with('success', 'Tiket berhasil diambil alih dari ' . $oldSupport . '.');
        }

        // Kalau belum ditangani siapa pun
        $ticket->update([
            'it_support_id' => auth()->id(),
            'status' => 'Ditangani IT',
            'first_response_at' => $ticket->first_response_at ?? now(),
        ]);

        NotificationHelper::send(
            $ticket->mentor_id,
            $ticket->id,
            auth()->id(),
            'assigned',
            'Tiket Sedang Ditangani',
            auth()->user()->name . ' sedang menangani tiket #' . $ticket->ticket_number
        );

        return redirect()->route('it.tickets.show', $ticket->id)
            ->with('success', 'Tiket berhasil diambil.');
    }

    // ============ KIRIM PESAN DISKUSI (BEBAS SIAPA SAJA) ============
    public function sendMessage(Request $request, $id)
    {
        Ticket::autoCloseExpiredTickets();

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status === 'Ditutup') {
            return back()->with('error', 'Tiket sudah ditutup.');
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,log,txt|max:10240',
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

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

        // Otomatis set IT Support yang membalas pertama kali jika belum ada
        $updateData = [
            'status' => $ticket->first_response_at ? 'Dibalas IT' : 'Ditangani IT',
            'first_response_at' => $ticket->first_response_at ?? now(),
        ];

        // Jika yang membalas adalah user dengan role it_support DAN belum ada PIC
        if (!$ticket->it_support_id && auth()->user()->role === 'it_support') {
            $updateData['it_support_id'] = auth()->id();
        }

        $ticket->update($updateData);

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
    public function markResolved(Request $request, $id)
    {
        Ticket::autoCloseExpiredTickets();

        $ticket = Ticket::findOrFail($id);

        if (!in_array($ticket->status, ['Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])) {
            return back()->with('error', 'Tiket tidak bisa ditandai selesai pada status ini.');
        }

        $validator = Validator::make($request->all(), [
            'resolution_note' => 'required|string|min:5|max:1000',
        ], [
            'resolution_note.required' => 'Alasan atau catatan penyelesaian wajib diisi.',
            'resolution_note.min' => 'Catatan penyelesaian minimal 5 karakter.',
            'resolution_note.max' => 'Catatan penyelesaian maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_resolve_modal', true);
        }

        $ticket->update([
            'status' => 'Selesai',
            'resolved_at' => now(),
            'resolution_note' => trim($request->resolution_note),
            'it_support_id' => $ticket->it_support_id ?? auth()->id(),
        ]);

        NotificationHelper::send(
            $ticket->mentor_id,
            $ticket->id,
            auth()->id(),
            'resolved',
            'Tiket Selesai Ditangani',
            'Tiket #' . $ticket->ticket_number . ' telah selesai. Menunggu konfirmasi Anda.'
        );

        return back()->with('success', 'Tiket ditandai selesai.');
    }

    public function export(Request $request)
    {
        Ticket::autoCloseExpiredTickets();

        $query = Ticket::with(['mentor', 'itSupport', 'category'])
            ->whereIn('status', ['Selesai', 'Ditutup']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $tickets = $query->orderBy('updated_at', 'desc')->get();
        $filename = 'riwayat-tiket-' . str_replace(' ', '-', auth()->user()->name) . '-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID Tiket', 'Judul Kendala', 'Kategori', 'Prioritas', 'Pelapor', 'Status', 'Tanggal Dibuat', 'Tanggal Selesai']);
            foreach ($tickets as $t) {
                fputcsv($file, [
                    $t->ticket_number, $t->title, $t->category->name ?? '-', $t->priority,
                    $t->mentor->name ?? '-', $t->status,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->resolved_at ? $t->resolved_at->format('d/m/Y H:i') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}