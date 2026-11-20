<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INAmikro - @yield('judul', 'Helpdesk')</title>

    {{-- ⭐ SCRIPT ANTI-FLICKER: Diletakkan paling atas agar tema langsung diterapkan --}}
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    {{-- Bootstrap CSS via CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons via CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --inamikro-red: #d81b21;
            --inamikro-bg: #f4f6f9;
            --sidebar-width: 230px;
        }

        /* ═══════════════════════════════════════════ */
        /* GLOBAL & SCROLL LAYOUT                      */
        /* ═══════════════════════════════════════════ */
        html {
            font-size: 14px;
        }

        body {
            background-color: var(--inamikro-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.9rem;
            line-height: 1.45;
            color: #333;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        /* ═══════════════════════════════════════════ */
        /* SIDEBAR                                     */
        /* ═══════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: white;
            border-right: 1px solid #e0e0e0;
            z-index: 1000;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            transition: background-color 0.3s, border-color 0.3s;
        }

        /* ═══════════════════════════════════════════ */
        /* MAIN WRAPPER                                */
        /* ═══════════════════════════════════════════ */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        /* ═══════════════════════════════════════════ */
        /* TOPBAR                                      */
        /* ═══════════════════════════════════════════ */
        .topbar-wrapper {
            position: sticky;
            top: 0;
            z-index: 999;
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.3s, border-color 0.3s;
        }

        /* ═══════════════════════════════════════════ */
        /* CONTENT AREA                                */
        /* ═══════════════════════════════════════════ */
        .content-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        /* ═══════════════════════════════════════════ */
        /* HEADING                                     */
        /* ═══════════════════════════════════════════ */
        h1 { font-size: 1.6rem; }
        h2 { font-size: 1.35rem; }
        h3 { font-size: 1.15rem; }
        h4 { font-size: 1.05rem; }
        h5 { font-size: 1rem; }
        h6 { font-size: 0.9rem; }

        /* ═══════════════════════════════════════════ */
        /* CARD                                        */
        /* ═══════════════════════════════════════════ */
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.04);
            transition: background-color 0.3s, color 0.3s;
        }
        .card-custom.p-4 { padding: 1.1rem !important; }
        .card-custom.p-3 { padding: 0.9rem !important; }

        /* ═══════════════════════════════════════════ */
        /* NAVIGASI SIDEBAR                            */
        /* ═══════════════════════════════════════════ */
        .sidebar .nav-link {
            color: #333;
            font-weight: 500;
            padding: 9px 14px;
            border-radius: 8px;
            margin-bottom: 3px;
            font-size: 0.875rem;
        }
        .sidebar .nav-link i { margin-right: 6px; }
        .sidebar .nav-link.active {
            background-color: var(--inamikro-red);
            color: white;
        }
        .sidebar .nav-link:hover:not(.active) {
            background-color: #f0f0f0;
        }

        /* ═══════════════════════════════════════════ */
        /* BUTTON & FORM                               */
        /* ═══════════════════════════════════════════ */
        .btn-inamikro {
            background-color: var(--inamikro-red);
            color: white;
            border: none;
        }
        .btn-inamikro:hover {
            background-color: #b0151a;
            color: white;
        }
        .btn { font-size: 0.85rem; padding: 6px 14px; }
        .btn-sm { font-size: 0.78rem; padding: 4px 10px; }

        .form-control, .form-select {
            font-size: 0.85rem;
            padding: 6px 12px;
        }
        .form-control-sm, .form-select-sm {
            font-size: 0.8rem;
            padding: 4px 10px;
        }
        .input-group-text { font-size: 0.85rem; }

        /* ═══════════════════════════════════════════ */
        /* TABLE & BADGE                               */
        /* ═══════════════════════════════════════════ */
        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
            color: #333;
        }
        .table th {
            padding: 9px 12px;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .table td {
            padding: 10px 12px;
            vertical-align: middle;
        }

        .badge {
            font-size: 0.72rem;
            font-weight: 500;
            padding: 4px 9px;
        }
        .badge-soft-danger    { background-color: #f8d7da; color: #dc3545; }
        .badge-soft-warning   { background-color: #fff3cd; color: #856404; }
        .badge-soft-success   { background-color: #d1e7dd; color: #198754; }
        .badge-soft-primary   { background-color: #cfe2ff; color: #0d6efd; }
        .badge-soft-info      { background-color: #cff4fc; color: #055160; }
        .badge-soft-secondary { background-color: #e2e3e5; color: #41464b; }

        /* ═══════════════════════════════════════════ */
        /* SPACING                                     */
        /* ═══════════════════════════════════════════ */
        .mb-4 { margin-bottom: 1rem !important; }
        .mb-3 { margin-bottom: 0.75rem !important; }
        .mt-4 { margin-top: 1rem !important; }
        .mt-3 { margin-top: 0.75rem !important; }

        /* ═══════════════════════════════════════════ */
        /* ALERT (Notifikasi)                          */
        /* ═══════════════════════════════════════════ */
        .alert {
            font-size: 0.85rem;
            padding: 10px 14px;
            border-radius: 8px;
        }
        .alert-dismissible .btn-close {
            padding: 0.75rem 1rem;
        }

        /* ═══════════════════════════════════════════ */
        /* PROGRESS BAR                                */
        /* ═══════════════════════════════════════════ */
        .progress {
            background-color: #e9ecef;
            border-radius: 8px;
        }

        /* ═══════════════════════════════════════════ */
        /* SCROLLBAR CUSTOM                            */
        /* ═══════════════════════════════════════════ */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background-color: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-track { background-color: #f1f1f1; }

        /* ═══════════════════════════════════════════ */
        /* TIMELINE SLA                                */
        /* ═══════════════════════════════════════════ */
        .timeline-container {
            position: relative;
            margin-left: 8px;
        }
        .timeline-item {
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            top: 15px;
            bottom: -5px;
            left: 5px;
            width: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        .timeline-item.is-active::before {
            background-color: #d81b21;
        }
        .timeline-item:last-child::before {
            display: none;
        }
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            left: 0;
            top: 5px;
            z-index: 2;
            background-color: #6c757d;
        }
        .timeline-dot.is-active {
            background-color: #d81b21;
        }

        /* ═══════════════════════════════════════════ */
        /* RESPONSIVE (Mobile)                         */
        /* ═══════════════════════════════════════════ */
        @media (max-width: 768px) {
            :root { --sidebar-width: 0px; }
            .sidebar { display: none; }
            .main-wrapper { margin-left: 0; width: 100%; }
        }

        /* ═══════════════════════════════════════════ */
        /* CUSTOM PAGINATION — Tema INAmikro (Merah)   */
        /* ═══════════════════════════════════════════ */
        .pagination .page-item.active .page-link {
            background-color: var(--inamikro-red) !important;
            border-color: var(--inamikro-red) !important;
            color: white !important;
        }
        .pagination .page-link {
            color: var(--inamikro-red) !important;
        }
        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.25rem rgba(216, 27, 33, 0.25) !important;
        }
        .pagination .page-item .page-link {
            border-radius: 6px;
            margin: 0 2px;
        }

        /* ═══════════════════════════════════════════ */
        /* DARK MODE OVERRIDES                         */
        /* ═══════════════════════════════════════════ */
        [data-bs-theme="dark"] body {
            background-color: #121212;
            color: #e0e0e0;
        }
        
        [data-bs-theme="dark"] .card-custom,
        [data-bs-theme="dark"] .sidebar,
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .topbar-wrapper {
            background-color: #1e1e1e !important;
            color: #e0e0e0;
            border-color: #333 !important;
        }

        [data-bs-theme="dark"] .sidebar .nav-link {
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .sidebar .nav-link:hover:not(.active) {
            background-color: #2c2c2c;
        }

        [data-bs-theme="dark"] .text-dark,
        [data-bs-theme="dark"] .table {
            color: #e0e0e0 !important;
        }

        [data-bs-theme="dark"] .table-hover tbody tr:hover {
            background-color: #2c2c2c;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #2c2c2c !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text {
            background-color: #2c2c2c;
            border-color: #444;
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .border-bottom,
        [data-bs-theme="dark"] .border-top,
        [data-bs-theme="dark"] .border-start,
        [data-bs-theme="dark"] .border-end {
            border-color: #444 !important;
        }

        /* ═══════════════════════════════════════════ */
        /* WARNA & UKURAN LOGO INAMIKRO DINAMIS        */
        /* ═══════════════════════════════════════════ */
        .brand-text {
            font-size: 1.90rem;
            font-family: 'Segoe UI', sans-serif;
            letter-spacing: -0.5px;
        }

        .text-ina {
            color: #000000;
            transition: color 0.3s ease;
        }

        [data-bs-theme="dark"] .text-ina {
            color: #ffffff !important;
        }

        .sidebar-profile-box {
            background-color: #f8f9fa;
            transition: background-color 0.3s;
        }
        [data-bs-theme="dark"] .sidebar-profile-box {
            background-color: #2c2c2c !important;
        }

        /* ═══════════════════════════════════════════ */
        /* CUSTOM CHAT BUBBLE DI DARK MODE             */
        /* ═══════════════════════════════════════════ */
        .chat-bubble-right {
            background-color: #f4c7c9;
            color: #000;
        }
        .chat-bubble-left {
            background-color: #f8f9fa;
            color: #000;
        }

        [data-bs-theme="dark"] .chat-bubble-right {
            background-color: #8b1e23;
            color: #ffffff;
        }
        [data-bs-theme="dark"] .chat-bubble-left {
            background-color: #2c2c2c;
            color: #ffffff;
        }
        [data-bs-theme="dark"] .chat-bubble-right a,
        [data-bs-theme="dark"] .chat-bubble-left a {
            color: #90caf9 !important;
        }
        
    </style>
</head>
<body>

    {{-- ═══ SIDEBAR (Fixed) ═══ --}}
    @include('partials.sidebar')

    {{-- ═══ MAIN WRAPPER ═══ --}}
    <div class="main-wrapper">

        {{-- TOPBAR (Sticky) --}}
        <div class="topbar-wrapper">
            @include('partials.topbar')
        </div>

        {{-- CONTENT AREA (Scroll Only Here) --}}
        <div class="content-area">

            {{-- ═══ NOTIFIKASI ═══ --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- KONTEN UTAMA --}}
            @yield('konten')

        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto dismiss notifikasi setelah 5 detik
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    });

    // === LOGIKA LIGHT/DARK MODE ===
    document.addEventListener('DOMContentLoaded', function () {
        const htmlElement = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        if (themeToggle) {
            // Cek tema saat ini dari atribut yang sudah di-set di head
            const currentTheme = htmlElement.getAttribute('data-bs-theme') || 'light';
            updateIcon(currentTheme);

            // Fungsi klik tombol toggle
            themeToggle.addEventListener('click', function () {
                const newTheme = htmlElement.getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light';
                
                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });

            // Fungsi untuk mengganti ikon (Matahari/Bulan)
            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.classList.remove('bi-moon-fill');
                    themeIcon.classList.add('bi-sun-fill');
                } else {
                    themeIcon.classList.remove('bi-sun-fill');
                    themeIcon.classList.add('bi-moon-fill');
                }
            }
        }
    });
</script>

{{-- ⭐ MODAL PAKSA GANTI PASSWORD ⭐ --}}
@auth
    @if(auth()->user()->is_temporary_password)
        <div class="modal fade show" id="modalGantiPassword" data-bs-backdrop="static" data-bs-keyboard="false" style="display:block; background:rgba(0,0,0,0.7);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-shield-exclamation"></i> Keamanan Akun</h5>
                    </div>
                    <!-- Form mengarah ke route profile.update dengan method PUT -->
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p class="fw-bold">Anda menggunakan kata sandi sementara.</p>
                            <p class="text-muted small">Demi keamanan, Anda <strong>wajib</strong> mengganti kata sandi Anda sekarang.</p>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kata Sandi Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi baru" required>
                            </div>
                            {{-- Hidden fields agar validasi di controller tidak error --}}
                            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            
                            {{-- ⭐ INPUT INI YANG MEMBUAT POP-UP HILANG ⭐ --}}
                            <input type="hidden" name="clear_temporary" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-inamikro w-100">Simpan Sandi Baru</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

</body>
</html>