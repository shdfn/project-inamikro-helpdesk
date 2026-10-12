<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationHelper;

class TicketController extends Controller
{
    // ============ INDEX ============
    public function index(Request $request)
    {
        $query = Ticket::where('mentor_id', auth()->id())->with('category');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $statusMap = [
                'baru' => 'Baru',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai',
            ];
            if (isset($statusMap[$request->status])) {
                $query->where('status', $statusMap[$request->status]);
            }
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $counts = [
            'semua' => Ticket::where('mentor_id', auth()->id())->count(),
            'baru' => Ticket::where('mentor_id', auth()->id())->where('status', 'Baru')->count(),
            'diproses' => Ticket::where('mentor_id', auth()->id())->where('status', 'Diproses')->count(),
            'selesai' => Ticket::where('mentor_id', auth()->id())->where('status', 'Selesai')->count(),
        ];

        return view('mentor.tickets.index', compact('tickets', 'counts'));
    }

    // ============ CREATE ============
    public function create()
    {
        $categories = Category::all();
        return view('mentor.tickets.create', compact('categories'));
    }

    // ============ STORE ============
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:Rendah,Sedang,Tinggi',
            'description' => 'required',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,log,txt|max:10240',
        ], [
            'title.required' => 'Judul Kendala Wajib Diisi.',
            'category_id.required' => 'Kategori Wajib Dipilih.',
            'priority.required' => 'Prioritas Wajib Dipilih.',
            'description.required' => 'Deskripsi Wajib Diisi.',
            'attachment.mimes' => 'Format file harus JPG, PNG, PDF, atau LOG.',
            'attachment.max' => 'Ukuran file maksimal 10MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('mentor.tickets.create')->withErrors($validator)->withInput();
        }

        $lastId = Ticket::max('id') ?? 0;
        $ticketNumber = 'TK-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        Ticket::create([
            'ticket_number' => $ticketNumber,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'category_id' => $request->category_id,
            'mentor_id' => auth()->id(),
            'status' => 'Baru',
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('mentor.tickets.index')
            ->with('success', 'Tiket berhasil dikirim!');
    }

    // ============ SHOW ============
    public function show($id)
    {
        $ticket = Ticket::with(['category', 'itSupport', 'messages.user'])
            ->where('mentor_id', auth()->id())
            ->findOrFail($id);

        return view('mentor.tickets.show', compact('ticket'));
    }

    // ============ SEND MESSAGE ============
    public function sendMessage(Request $request, $id)
    {
        $ticket = Ticket::where('mentor_id', auth()->id())->findOrFail($id);

        if ($ticket->status === 'Ditutup') {
            return back()->with('error', 'Tiket sudah ditutup, tidak bisa mengirim pesan.');
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

        // 📬 NOTIFIKASI KE IT SUPPORT
        if ($ticket->it_support_id) {
            NotificationHelper::send(
                $ticket->it_support_id,
                $ticket->id,
                auth()->id(),
                'message',
                'Pesan Baru dari Mentor',
                auth()->user()->name . ' mengirim pesan pada tiket #' . $ticket->ticket_number
            );
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    // ============ CONFIRM RESOLUTION ============
    public function confirmResolution($id)
    {
        $ticket = Ticket::where('mentor_id', auth()->id())->findOrFail($id);

        if ($ticket->status !== 'Selesai') {
            return back()->with('error', 'Tiket belum bisa dikonfirmasi. Tunggu IT Support menandai selesai.');
        }

        $ticket->update([
            'status' => 'Ditutup',
            'resolved_at' => now(),
        ]);

        // 📬 NOTIFIKASI KE IT SUPPORT
        if ($ticket->it_support_id) {
            NotificationHelper::send(
                $ticket->it_support_id,
                $ticket->id,
                auth()->id(),
                'closed',
                'Tiket Dikonfirmasi Selesai',
                'Tiket #' . $ticket->ticket_number . ' telah dikonfirmasi selesai oleh Mentor.'
            );
        }

        return back()->with('success', 'Tiket berhasil dikonfirmasi selesai.');
    }
}