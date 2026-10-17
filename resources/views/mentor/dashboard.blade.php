@extends('template')

@section('judul', 'Dashboard Utama')

@section('konten')

<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Selamat datang, {{ auth()->user()->name }} 👋</h3>
            <p class="text-muted mb-0">Ada kendala aplikasi? Laporakan melalui helpdesk</p>
        </div>
        <a href="{{ route('mentor.tickets.create') }}" class="btn btn-inamikro px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Lapor Kendala
        </a>
    </div>
</div>

<!-- STATISTIK -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">TOTAL TIKET</small>
                <i class="bi bi-ticket text-primary"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['total'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 2 dari bulan lalu</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">MENUNGGU RESPON</small>
                <i class="bi bi-envelope text-danger"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['menunggu'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 1 hari ini</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">DIPROSES</small>
                <i class="bi bi-gear text-primary"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['diproses'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 1 hari ini</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">SELESAI</small>
                <i class="bi bi-check-circle text-success"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['selesai'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 1 hari ini</small>
        </div>
    </div>
</div>

<div class="row">
    <!-- TIKET TERBARU -->
    <div class="col-md-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i> Tiket Terbaru</h5>
                <a href="{{ route('mentor.tickets.index') }}" class="text-decoration-none small text-danger">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @forelse ($recentTickets as $ticket)
                            <tr onclick="window.location='{{ route('mentor.tickets.show', $ticket->id) }}'" style="cursor: pointer;">
                                <!-- Kolom ID & Judul -->
                                <td style="width: 70%;">
                                    <div class="fw-bold text-dark">#{{ $ticket->ticket_number }}</div>
                                    <div class="fw-bold">{{ $ticket->title }}</div>
                                    <small class="text-muted">{{ $ticket->category->name ?? '-' }}</small>
                                </td>
                                
                                <!-- Kolom Status & Waktu (Ditumpuk) -->
                                <td class="text-end" style="width: 30%;">
                                    <!-- Badge Status di atas -->
                                    <div class="mb-1">
                                        <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-3">
                                            <i class="bi bi-{{ $ticket->status_icon }}"></i>
                                            {{ $ticket->status_short }}
                                        </span>
                                    </div>
                                    <!-- Waktu di bawah badge -->
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    Belum ada tiket.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- BUTUH BANTUAN -->
    <div class="col-md-4">
        <div class="card card-custom p-4 text-center h-100 d-flex flex-column justify-content-center">
            <i class="bi bi-file-earmark-plus text-secondary" style="font-size: 4rem;"></i>
            <h5 class="fw-bold mt-3">Butuh Bantuan?</h5>
            <p class="text-muted small">Jika ada kendala yang belum bisa ditangani, silakan hubungi Kepala IT</p>
            <a href="{{ route('mentor.hubungi-it') }}" class="btn btn-inamikro w-100 mt-2">
                <i class="bi bi-headset"></i> Hubungi IT Support
            </a>
        </div>
    </div>
</div>

@endsection