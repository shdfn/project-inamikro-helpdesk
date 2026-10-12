@php
    $user = auth()->user();
    $role = $user->role ?? 'guest';

    if ($role === 'mentor') {
        $badgeCount = \App\Models\Ticket::where('mentor_id', $user->id)
            ->whereIn('status', ['Baru', 'Diproses', 'Selesai'])
            ->count();
    } elseif ($role === 'it_support') {
        $badgeMasuk = \App\Models\Ticket::where('status', 'Baru')->count();
        $badgeDitangani = \App\Models\Ticket::where('it_support_id', $user->id)
            ->where('status', 'Diproses')->count();
    }
@endphp

<div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column">
    <div class="d-flex align-items-center mb-4 ps-2">
        <img src="{{ asset('images/logo-inamikro.png') }}" alt="INAmikro" style="height: 35px;">
    </div>

    <nav class="nav flex-column mb-auto">
        @if($role === 'mentor')
            <a class="nav-link {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}"
               href="{{ route('mentor.dashboard') }}">
                <i class="bi bi-grid me-2"></i> Dashboard Utama
            </a>
            <a class="nav-link {{ request()->routeIs('mentor.tickets.create') ? 'active' : '' }}"
               href="{{ route('mentor.tickets.create') }}">
                <i class="bi bi-plus-circle me-2"></i> Lapor Kendala Baru
            </a>
            <a class="nav-link {{ request()->routeIs('mentor.tickets.index') || request()->routeIs('mentor.tickets.show') ? 'active' : '' }}"
               href="{{ route('mentor.tickets.index') }}">
                <i class="bi bi-ticket-detailed me-2"></i> Tiket Saya
                @if($badgeCount > 0)
                    <span class="badge bg-light text-dark float-end rounded-pill">{{ $badgeCount }}</span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('mentor.notifications.*') ? 'active' : '' }}"
               href="{{ route('mentor.notifications.index') }}">
                <i class="bi bi-bell me-2"></i> Notifikasi
            </a>

        @elseif($role === 'it_support')
            <a class="nav-link {{ request()->routeIs('it.dashboard') ? 'active' : '' }}"
               href="{{ route('it.dashboard') }}">
                <i class="bi bi-grid me-2"></i> Dashboard & Antrean
            </a>
            <a class="nav-link {{ request()->routeIs('it.tickets.incoming') ? 'active' : '' }}"
               href="{{ route('it.tickets.incoming') }}">
                <i class="bi bi-inbox me-2"></i> Tiket Masuk
                @if($badgeMasuk > 0)
                    <span class="badge bg-danger text-white float-end rounded-pill">{{ $badgeMasuk }}</span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('it.tickets.my') ? 'active' : '' }}"
               href="{{ route('it.tickets.my') }}">
                <i class="bi bi-hand-index me-2"></i> Tiket Ditangani Saya
                @if($badgeDitangani > 0)
                    <span class="badge bg-light text-dark float-end rounded-pill">{{ $badgeDitangani }}</span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('it.tickets.history') ? 'active' : '' }}"
               href="{{ route('it.tickets.history') }}">
                <i class="bi bi-clock-history me-2"></i> Riwayat & Arsip
            </a>
            <a class="nav-link {{ request()->routeIs('it.notifications.*') ? 'active' : '' }}"
               href="{{ route('it.notifications.index') }}">
                <i class="bi bi-bell me-2"></i> Notifikasi
            </a>
        @endif
    </nav>

    <div class="mt-auto p-3 bg-light rounded-3 d-flex align-items-center">
        <div class="text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center"
             style="width: 40px; height: 40px; background-color: var(--inamikro-red);">
            <i class="bi bi-person"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold" style="font-size: 0.85rem;">{{ $user->name }}</div>
            <div class="text-muted" style="font-size: 0.75rem;">{{ ucfirst(str_replace('_', ' ', $role)) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</div>