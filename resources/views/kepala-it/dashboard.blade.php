@extends('template')

@section('judul', 'Dashboard Eksekutif')

@section('konten')

{{-- Header --}}
<div class="card card-custom p-4 mb-4">
    <h3 class="fw-bold mb-1">Selamat datang, {{ auth()->user()->name }} 👋</h3>
    <p class="text-muted mb-0">Ringkasan performa sistem helpdesk INAmikro</p>
</div>

{{-- Kartu Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">TOTAL TIKET</small>
                <i class="bi bi-ticket text-primary"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $totalTickets }}</h2>
            <small class="text-muted">Semua periode</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">TIKET SELESAI</small>
                <i class="bi bi-check-circle text-success"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $resolvedTickets }}</h2>
            <small class="text-success">
                {{ $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100) : 0 }}% completion rate
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">SEDANG DIPROSES</small>
                <i class="bi bi-hourglass-split text-warning"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $pendingTickets }}</h2>
            <small class="text-muted">Belum selesai</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">SLA COMPLIANCE</small>
                <i class="bi bi-speedometer2 text-success"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $slaPercentage }}%</h2>
            <small class="text-success">Target: 24 jam</small>
        </div>
    </div>
</div>

{{-- Rata-rata & Top Kategori --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-clock-history"></i> Rata-rata Penyelesaian
            </h5>
            <h1 class="fw-bold" style="color: var(--inamikro-red);">
                {{ round($avgResolutionTime, 1) }}<small class="fs-6 text-muted"> jam</small>
            </h1>
            <p class="text-muted small mb-0">Dari tiket dibuat hingga diselesaikan</p>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-bar-chart"></i> Top 5 Kategori Kendala
            </h5>
            @forelse($topCategories as $cat)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $cat->category->name ?? '-' }}</span>
                    <div class="flex-grow-1 mx-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" 
                                 style="width: {{ $totalTickets > 0 ? ($cat->total / $totalTickets) * 100 : 0 }}%; 
                                        background-color: var(--inamikro-red);">
                            </div>
                        </div>
                    </div>
                    <strong>{{ $cat->total }}</strong>
                </div>
            @empty
                <p class="text-muted">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Tiket per Bulan --}}
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-graph-up"></i> Tren Tiket 6 Bulan Terakhir
    </h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="bg-light">
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Jumlah Tiket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticketsPerMonth as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::create()->month($t->month)->format('F') }}</td>
                        <td>{{ $t->year }}</td>
                        <td><strong>{{ $t->total }}</strong> tiket</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection