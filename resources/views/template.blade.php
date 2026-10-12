<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INAmikro - @yield('judul')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --inamikro-red: #d81b21;
            --inamikro-bg: #f4f6f9;
        }
        body { background-color: var(--inamikro-bg); font-family: 'Segoe UI', sans-serif; }
        .btn-inamikro { background-color: var(--inamikro-red); color: white; border: none; }
        .btn-inamikro:hover { background-color: #b0151a; color: white; }
        .sidebar { background-color: white; min-height: 100vh; border-right: 1px solid #e0e0e0; }
        .sidebar .nav-link { color: #333; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; }
        .sidebar .nav-link.active { background-color: var(--inamikro-red); color: white; }
        .sidebar .nav-link:hover:not(.active) { background-color: #f0f0f0; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .badge-soft-danger { background-color: #f8d7da; color: #dc3545; }
        .badge-soft-warning { background-color: #fff3cd; color: #856404; }
        .badge-soft-success { background-color: #d1e7dd; color: #198754; }
        .badge-soft-primary { background-color: #cfe2ff; color: #0d6efd; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-0">
            <!-- TOPBAR -->
            @include('partials.topbar')

            <!-- CONTENT -->
            <div class="p-4">
                @yield('konten')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>