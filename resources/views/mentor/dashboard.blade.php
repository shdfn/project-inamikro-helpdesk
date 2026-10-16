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
        <div class="card card-custom p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i> Tiket Terbaru</h5>

            @forelse ($recentTickets as $ticket)
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div>
                        <div class="fw-bold">#{{ $ticket->ticket_number }}</div>
                        <div class="fw-bold">{{ $ticket->title }}</div>
                        <small class="text-muted">{{ $ticket->category->name ?? '-' }}</small>
                    </div>
                    <div>
                        @if($ticket->status == 'Diproses')
                            <span class="badge badge-soft-warning rounded-pill px-3 py-2">Diproses</span>
                        @elseif($ticket->status == 'Selesai')
                            <span class="badge badge-soft-success rounded-pill px-3 py-2">Selesai</span>
                        @else
                            <span class="badge badge-soft-danger rounded-pill px-3 py-2">Baru</span>
                        @endif
                    </div>
                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p class="text-muted">Belum ada tiket.</p>
            @endforelse
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