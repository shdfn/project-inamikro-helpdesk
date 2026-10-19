@php
    $role = auth()->user()->role ?? 'guest';
    $notifCount = 0;
    $notifUrl = '#';

    // ⭐ SEMUA ROLE menghitung dari tabel notifications (SAMA dengan Sidebar & Controller)
    if ($role === 'mentor') {
        $notifCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)->count();
        $notifUrl = route('mentor.notifications.index');
    } elseif ($role === 'it_support') {
        $notifCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)->count();
        $notifUrl = route('it.notifications.index');
    } elseif ($role === 'kepala_it') {
        // FIX: Jangan hitung tiket lambat, hitung dari tabel notifications
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
@endphp

<div class="bg-white border-bottom p-3">
    <div class="d-flex justify-content-between align-items-center gap-3">

        {{-- KIRI: Breadcrumb (khusus Kepala IT) --}}
        <div class="flex-shrink-0">
            @if($role === 'kepala_it')
                <small class="text-muted">
                    HELPDESK / <span class="fw-bold text-dark">@yield('breadcrumb', 'Monitoring Eksekutif')</span>
                </small>
            @endif
        </div>

        {{-- TENGAH: Search Bar (semua role) --}}
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

            {{-- User Profile --}}
            <div class="d-flex align-items-center gap-2 border-start ps-3">
                <div class="text-white rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 35px; height: 35px; background-color: var(--inamikro-red);">
                    <i class="bi bi-person"></i>
                </div>
                <div class="lh-1">
                    <div class="fw-bold small">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        {{ $role === 'kepala_it' ? 'Kepala IT' : ucfirst(str_replace('_', ' ', $role)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>