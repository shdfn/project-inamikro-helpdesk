@php
    // Hitung jumlah tiket mentor yang statusnya bukan Ditutup (untuk badge)
    $sidebarTicketCount = \App\Models\Ticket::where('mentor_id', auth()->id())
        ->whereIn('status', ['Baru', 'Diproses', 'Selesai'])
        ->count();
@endphp

<div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column">
    <div class="d-flex align-items-center mb-4 ps-2">
        <h3 class="fw-bold m-0" style="color: var(--inamikro-red);">INAmikro</h3>
    </div>

    <nav class="nav flex-column mb-auto">
        <a class="nav-link {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}"
           href="{{ route('mentor.dashboard') }}">
            <i class="bi bi-grid me-2"></i> Dashboard Utama
        </a>

        {{-- Cek create DULU sebelum index agar tidak bentrok --}}
        <a class="nav-link {{ request()->routeIs('mentor.tickets.create') ? 'active' : '' }}"
           href="{{ route('mentor.tickets.create') }}">
            <i class="bi bi-plus-circle me-2"></i> Lapor Kendala Baru
        </a>

        <a class="nav-link {{ request()->routeIs('mentor.tickets.index') || request()->routeIs('mentor.tickets.show') ? 'active' : '' }}"
           href="{{ route('mentor.tickets.index') }}">
            <i class="bi bi-ticket-detailed me-2"></i> Tiket Saya
            @if($sidebarTicketCount > 0)
                <span class="badge bg-light text-dark float-end rounded-pill">{{ $sidebarTicketCount }}</span>
            @endif
        </a>

        <a class="nav-link {{ request()->routeIs('mentor.notifications.*') ? 'active' : '' }}"
           href="{{ route('mentor.notifications.index') }}">
            <i class="bi bi-bell me-2"></i> Notifikasi
        </a>
    </nav>

    <div class="mt-auto p-3 bg-light rounded-3 d-flex align-items-center">
        <div class="text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center"
             style="width: 40px; height: 40px; background-color: var(--inamikro-red);">
            <i class="bi bi-person"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold" style="font-size: 0.85rem;">{{ auth()->user()->name }}</div>
            <div class="text-muted" style="font-size: 0.75rem;">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</div>