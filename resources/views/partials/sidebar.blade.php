@php
    $user = auth()->user();
    $role = $user->role ?? 'guest';

    // ═══ Badge untuk tiket ═══
    if ($role === 'mentor') {
        $badgeCount = \App\Models\Ticket::where('mentor_id', $user->id)
            ->whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor', 'Selesai'])
            ->count();
    } elseif ($role === 'it_support') {
        $badgeMasuk = \App\Models\Ticket::where('status', 'Baru')->count();
        $badgeDitangani = \App\Models\Ticket::where('it_support_id', $user->id)
            ->whereIn('status', ['Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->count();
    } elseif ($role === 'kepala_it') {
        $lateCount = \App\Models\Ticket::whereIn('status', ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 120')
            ->count();
    }

    // ⭐ Hitung notifikasi — SAMA dengan topbar
    $notifCount = \App\Models\Notification::where('user_id', $user->id)
        ->where('is_read', false)
        ->count();
@endphp

<div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column">
    <div class="d-flex align-items-center mb-4 ps-2">
        <img src="{{ asset('images/logo-inamikro.png') }}" alt="INAmikro" style="height: 35px;">
    </div>

    <nav class="nav flex-column mb-auto">

        {{-- ================= MENTOR ================= --}}
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
                    <span class="badge {{ request()->routeIs('mentor.tickets.index') || request()->routeIs('mentor.tickets.show') ? 'bg-white text-danger' : 'bg-light text-dark' }} float-end rounded-pill">
                        {{ $badgeCount }}
                    </span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('mentor.notifications.*') ? 'active' : '' }}"
               href="{{ route('mentor.notifications.index') }}">
                <i class="bi bi-bell me-2"></i> Notifikasi
                @if($notifCount > 0)
                    <span class="badge {{ request()->routeIs('mentor.notifications.*') ? 'bg-white text-danger' : 'bg-danger text-white' }} float-end rounded-pill">
                        {{ $notifCount }}
                    </span>
                @endif
            </a>

        {{-- ================= IT SUPPORT ================= --}}
        @elseif($role === 'it_support')
            <a class="nav-link {{ request()->routeIs('it.dashboard') ? 'active' : '' }}"
               href="{{ route('it.dashboard') }}">
                <i class="bi bi-grid me-2"></i> Dashboard & Antrean
            </a>
            <a class="nav-link {{ request()->routeIs('it.tickets.incoming') ? 'active' : '' }}"
               href="{{ route('it.tickets.incoming') }}">
                <i class="bi bi-inbox me-2"></i> Tiket Masuk
                @if($badgeMasuk > 0)
                    <span class="badge {{ request()->routeIs('it.tickets.incoming') ? 'bg-white text-danger' : 'bg-danger text-white' }} float-end rounded-pill">
                        {{ $badgeMasuk }}
                    </span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('it.tickets.my') ? 'active' : '' }}"
               href="{{ route('it.tickets.my') }}">
                <i class="bi bi-hand-index me-2"></i> Tiket Ditangani Saya
                @if($badgeDitangani > 0)
                    <span class="badge {{ request()->routeIs('it.tickets.my') ? 'bg-white text-danger' : 'bg-light text-dark' }} float-end rounded-pill">
                        {{ $badgeDitangani }}
                    </span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('it.tickets.history') ? 'active' : '' }}"
               href="{{ route('it.tickets.history') }}">
                <i class="bi bi-clock-history me-2"></i> Riwayat & Arsip
            </a>
            <a class="nav-link {{ request()->routeIs('it.notifications.*') ? 'active' : '' }}"
               href="{{ route('it.notifications.index') }}">
                <i class="bi bi-bell me-2"></i> Notifikasi
                @if($notifCount > 0)
                    <span class="badge {{ request()->routeIs('it.notifications.*') ? 'bg-white text-danger' : 'bg-danger text-white' }} float-end rounded-pill">
                        {{ $notifCount }}
                    </span>
                @endif
            </a>

        {{-- ================= KEPALA IT ================= --}}
        @elseif($role === 'kepala_it')

            <small class="text-muted px-3 mt-2 mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">MONITORING</small>

            <a class="nav-link {{ request()->routeIs('kepala.dashboard') ? 'active' : '' }}"
               href="{{ route('kepala.dashboard') }}">
                <i class="bi bi-grid-1x2 me-2"></i> Dashboard
            </a>

            <a class="nav-link {{ request()->routeIs('kepala.monitoring') || request()->routeIs('kepala.monitoring.sla') ? 'active' : '' }}"
               href="{{ route('kepala.monitoring.sla') }}">
                <i class="bi bi-activity me-2"></i> SLA IT Support
            </a>

            <a class="nav-link {{ request()->routeIs('kepala.statistik') || request()->routeIs('kepala.monitoring.statistik') ? 'active' : '' }}"
               href="{{ route('kepala.statistik') }}">
                <i class="bi bi-bar-chart-line me-2"></i> Statistik Kendala
            </a>

            <small class="text-muted px-3 mt-3 mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">TIKET</small>

            <a class="nav-link {{ request()->routeIs('kepala.tickets.index') ? 'active' : '' }}"
               href="{{ route('kepala.tickets.index') }}">
                <i class="bi bi-ticket-detailed me-2"></i> Semua Tiket
            </a>

            <a class="nav-link {{ request()->routeIs('kepala.tickets.late') ? 'active' : '' }}"
               href="{{ route('kepala.tickets.late') }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Tiket Lambat
                @if($lateCount > 0)
                    <span class="badge {{ request()->routeIs('kepala.tickets.late') ? 'bg-white text-danger' : 'bg-danger text-white' }} float-end rounded-pill">
                        {{ $lateCount }}
                    </span>
                @endif
            </a>

            @if(Route::has('kepala.eskalasi'))
                <a class="nav-link {{ request()->routeIs('kepala.eskalasi') ? 'active' : '' }}"
                   href="{{ route('kepala.eskalasi') }}">
                    <i class="bi bi-shield-exclamation me-2"></i> Eskalasi
                </a>
            @endif

            <small class="text-muted px-3 mt-3 mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">LAINNYA</small>

            @if(Route::has('kepala.notifications.index'))
                <a class="nav-link {{ request()->routeIs('kepala.notifications.*') ? 'active' : '' }}"
                   href="{{ route('kepala.notifications.index') }}">
                    <i class="bi bi-bell me-2"></i> Notifikasi
                    @if($notifCount > 0)
                        <span class="badge {{ request()->routeIs('kepala.notifications.*') ? 'bg-white text-danger' : 'bg-danger text-white' }} float-end rounded-pill">
                            {{ $notifCount }}
                        </span>
                    @endif
                </a>
            @endif

            <a class="nav-link {{ request()->routeIs('kepala.users.*') ? 'active' : '' }}"
               href="{{ route('kepala.users.index') }}">
                <i class="bi bi-people me-2"></i> Manajemen User
            </a>
        @endif

    </nav>

    {{-- Profil User --}}
    <div class="mt-auto p-3 bg-light rounded-3 d-flex align-items-center">
        <div class="text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center"
             style="width: 40px; height: 40px; background-color: var(--inamikro-red);">
            <i class="bi bi-person"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold" style="font-size: 0.85rem;">{{ $user->name }}</div>
            <div class="text-muted" style="font-size: 0.75rem;">
                {{ $role === 'kepala_it' ? 'Kepala IT' : ucfirst(str_replace('_', ' ', $role)) }}
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>