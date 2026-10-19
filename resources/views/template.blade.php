<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INAmikro - @yield('judul', 'Helpdesk')</title>

    {{-- Bootstrap CSS via CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons via CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --inamikro-red: #d81b21;
            --inamikro-bg: #f4f6f9;
        }

        /* ═══════════════════════════════════════════ */
        /* GLOBAL — Perkecil font-size dasar           */
        /* ═══════════════════════════════════════════ */
        html {
            font-size: 14px;              /* Default 16px → 14px (12.5% lebih kecil) */
        }

        body {
            background-color: var(--inamikro-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.9rem;
            line-height: 1.45;
            color: #333;
            margin: 0;
        }

        /* ═══════════════════════════════════════════ */
        /* HEADING — Proporsional dengan font-size baru */
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
        }

        .card-custom.p-4 {
            padding: 1.1rem !important;   /* default 1.5rem → 1.1rem */
        }

        .card-custom.p-3 {
            padding: 0.9rem !important;
        }

        /* ═══════════════════════════════════════════ */
        /* SIDEBAR                                     */
        /* ═══════════════════════════════════════════ */
        .sidebar {
            background-color: white;
            min-height: 100vh;
            border-right: 1px solid #e0e0e0;
        }

        .sidebar .nav-link {
            color: #333;
            font-weight: 500;
            padding: 9px 14px;
            border-radius: 8px;
            margin-bottom: 3px;
            font-size: 0.875rem;
        }

        .sidebar .nav-link i {
            margin-right: 6px;
        }

        .sidebar .nav-link.active {
            background-color: var(--inamikro-red);
            color: white;
        }

        .sidebar .nav-link:hover:not(.active) {
            background-color: #f0f0f0;
        }

        /* ═══════════════════════════════════════════ */
        /* BUTTON                                      */
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

        .btn {
            font-size: 0.85rem;
            padding: 6px 14px;
        }

        .btn-sm {
            font-size: 0.78rem;
            padding: 4px 10px;
        }

        /* ═══════════════════════════════════════════ */
        /* TABLE                                       */
        /* ═══════════════════════════════════════════ */
        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
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

        /* ═══════════════════════════════════════════ */
        /* BADGE                                       */
        /* ═══════════════════════════════════════════ */
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
        /* FORM                                        */
        /* ═══════════════════════════════════════════ */
        .form-control, .form-select {
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        .form-control-sm, .form-select-sm {
            font-size: 0.8rem;
            padding: 4px 10px;
        }

        .input-group-text {
            font-size: 0.85rem;
        }

        /* ═══════════════════════════════════════════ */
        /* SPACING — Kecilkan margin                   */
        /* ═══════════════════════════════════════════ */
        .mb-4 { margin-bottom: 1rem !important; }
        .mb-3 { margin-bottom: 0.75rem !important; }
        .mt-4 { margin-top: 1rem !important; }
        .mt-3 { margin-top: 0.75rem !important; }

        /* ═══════════════════════════════════════════ */
        /* ALERT (Notifikasi) — RAPI                   */
        /* ═══════════════════════════════════════════ */
        .alert {
            font-size: 0.85rem;
            padding: 10px 14px;
            border-radius: 8px;
        }
        /* Menyeimbangkan tombol close agar tidak terlalu menempel */
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
        /* SCROLLBAR (opsional, biar rapi)             */
        /* ═══════════════════════════════════════════ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }

        /* ═══════════════════════════════════════════ */
        /* TIMELINE SLA — Custom                       */
        /* ═══════════════════════════════════════════ */
        .timeline-container {
            position: relative;
            margin-left: 8px;
        }
        .timeline-item {
            position: relative;
        }
        /* Garis default (abu-abu) */
        .timeline-item::before {
            content: '';
            position: absolute;
            top: 15px;
            bottom: -5px;
            left: 5px;
            width: 2px;
            background-color: #e0e0e0; /* Abu-abu */
            z-index: 1;
        }
        /* ⭐ KUNCI: Jika item ini punya class 'is-active', garisnya jadi merah */
        .timeline-item.is-active::before {
            background-color: #d81b21;
        }
        /* Hilangkan garis di item terakhir */
        .timeline-item:last-child::before {
            display: none;
        }
        /* Bulatan default (abu-abu) */
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            left: 0;
            top: 5px;
            z-index: 2;
            background-color: #6c757d; /* Abu-abu */
        }
        /* Bulatan jika aktif (merah) */
        .timeline-dot.is-active {
            background-color: #d81b21;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row g-0">

        {{-- ═══ SIDEBAR ═══ --}}
        @include('partials.sidebar')

        {{-- ═══ MAIN CONTENT ═══ --}}
        <div class="col-md-9 col-lg-10 p-0">

            {{-- TOPBAR --}}
            @include('partials.topbar')

            {{-- CONTENT --}}
            <div class="p-3">

                {{-- ═══ NOTIFIKASI (Diperbaiki) ═══ --}}
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

                @yield('konten')

            </div>
        </div>
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
</script>

</body>
</html>