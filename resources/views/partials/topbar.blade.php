<div class="bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
    <form action="{{ route('mentor.tickets.index') }}" method="GET" class="w-50">
        <div class="input-group">
            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}"
                class="form-control bg-light border-0"
                placeholder="Cari ID tiket, judul, atau deskripsi...">
            <button type="submit" class="btn btn-light border-0">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <div class="d-flex align-items-center gap-3">
        {{-- Notifikasi Bell --}}
        @php
            $notifCount = \App\Models\Ticket::where('mentor_id', auth()->id())
                ->whereIn('status', ['Diproses', 'Selesai'])
                ->count();
        @endphp
        <a href="{{ route('mentor.notifications.index') }}" class="position-relative text-decoration-none">
            <i class="bi bi-bell fs-5 text-secondary"></i>
            @if($notifCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      style="font-size: 0.6rem;">{{ $notifCount }}</span>
            @endif
        </a>

        <div class="d-flex align-items-center gap-2 border-start ps-3">
            <div class="text-white rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 35px; height: 35px; background-color: var(--inamikro-red);">
                <i class="bi bi-person"></i>
            </div>
            <div class="lh-1">
                <div class="fw-bold small">{{ auth()->user()->name }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
            </div>
        </div>
    </div>
</div>