@php
    $role = auth()->user()->role ?? 'guest';
    $notifCount = 0;
    $notifUrl = '#';

    // === Notifikasi per role (Sinkron dengan Sidebar) ===
    if ($role === 'mentor') {
        $notifCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)->count();
        $notifUrl = route('mentor.notifications.index');
    } elseif ($role === 'it_support') {
        $notifCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)->count();
        $notifUrl = route('it.notifications.index');
    } elseif ($role === 'kepala_it') {
        $notifCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)->count();
        $notifUrl = route('kepala.notifications.index');
    }

    // === URL action form search per role ===
    $searchAction = match($role) {
        'mentor'    => route('mentor.tickets.index'),
        'it_support'=> route('it.tickets.incoming'),
        'kepala_it' => route('kepala.tickets.index'),
        default     => '#',
    };

    // ⭐ BREADCRUMB DINAMIS ⭐
    $breadcrumbTitle = '';
    if ($role === 'mentor') {
        $breadcrumbTitle = match(true) {
            request()->routeIs('mentor.dashboard') => 'Dashboard Utama',
            request()->routeIs('mentor.tickets.create') => 'Lapor Kendala Baru',
            request()->routeIs('mentor.tickets.index', 'mentor.tickets.show') => 'Tiket Saya',
            request()->routeIs('mentor.notifications.*') => 'Notifikasi',
            request()->routeIs('profile.*') => 'Edit Profil',
            default => 'Helpdesk'
        };
    } elseif ($role === 'it_support') {
        $breadcrumbTitle = match(true) {
            request()->routeIs('it.dashboard') => 'Dashboard & Antrean',
            request()->routeIs('it.tickets.incoming') => 'Tiket Masuk',
            request()->routeIs('it.tickets.my') => 'Tiket Ditangani Saya',
            request()->routeIs('it.tickets.history') => 'Riwayat & Arsip',
            request()->routeIs('it.password-reset.*') => 'Reset Password',
            request()->routeIs('it.notifications.*') => 'Notifikasi',
            request()->routeIs('profile.*') => 'Edit Profil',
            default => 'Helpdesk'
        };
    } elseif ($role === 'kepala_it') {
        $breadcrumbTitle = match(true) {
            request()->routeIs('kepala.dashboard') => 'Dashboard',
            request()->routeIs('kepala.monitoring*') => 'SLA IT Support',
            request()->routeIs('kepala.statistik') => 'Statistik Kendala',
            request()->routeIs('kepala.tickets.index', 'kepala.tickets.show') => 'Semua Tiket',
            request()->routeIs('kepala.tickets.late') => 'Tiket Lambat',
            request()->routeIs('kepala.eskalasi') => 'Eskalasi',
            request()->routeIs('kepala.notifications.*') => 'Notifikasi',
            request()->routeIs('kepala.users.*') => 'Manajemen User',
            request()->routeIs('profile.*') => 'Edit Profil',
            default => 'Helpdesk'
        };
    }
@endphp

<div class="p-3">
    <div class="d-flex justify-content-between align-items-center gap-3">

        {{-- KIRI: Breadcrumb Dinamis --}}
        <div class="flex-shrink-0">
            <small class="text-muted">
                HELPDESK / <span class="fw-bold text-dark">{{ $breadcrumbTitle }}</span>
            </small>
        </div>

        {{-- TENGAH: Search Bar --}}
        <form action="{{ $searchAction }}" method="GET" class="flex-grow-1" style="max-width: 500px;">
            <div class="input-group">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control bg-light border-0"
                       placeholder="Cari tiket, ID kendala, merchant, atau fasilitas...">
            </div>
        </form>

        {{-- KANAN: Notif + User --}}
        <div class="d-flex align-items-center gap-3 flex-shrink-0">

            {{-- Bell Notifikasi --}}
            @if($notifUrl !== '#')
                <a href="{{ $notifUrl }}" class="position-relative text-decoration-none">
                    <i class="bi bi-bell fs-5 text-secondary"></i>
                    @if($notifCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size: 0.6rem;">{{ $notifCount }}</span>
                    @endif
                </a>
            @endif

            {{-- Tombol Toggle Light/Dark Mode --}}
            <button class="btn btn-link text-secondary p-0 me-2" id="themeToggle" title="Ubah Tema">
                <i class="bi bi-moon-fill fs-5" id="themeIcon"></i>
            </button>

            {{-- User Profile Dropdown --}}
            <div class="dropdown border-start ps-3">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 35px; height: 35px; background-color: var(--inamikro-red);">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="lh-1">
                        <div class="fw-bold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-pencil-square me-2"></i> Edit Profil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>