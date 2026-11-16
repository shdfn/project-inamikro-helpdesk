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
    <!-- KOLOM KIRI: TIKET TERBARU -->
    <div class="col-md-8 mb-4">
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
                                <td style="width: 70%;">
                                    <div class="fw-bold text-dark">#{{ $ticket->ticket_number }}</div>
                                    <div class="fw-bold">{{ $ticket->title }}</div>
                                    <small class="text-muted">{{ $ticket->category->name ?? '-' }}</small>
                                </td>
                                
                                <td class="text-end" style="width: 30%;">
                                    <div class="mb-1">
                                        <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-3">
                                            <i class="bi bi-{{ $ticket->status_icon }}"></i>
                                            {{ $ticket->status_short }}
                                        </span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">Belum ada tiket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- KOLOM KANAN: TIM IT SUPPORT -->
    <div class="col-md-4 mb-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3 text-center">
                <i class="bi bi-people-fill text-danger"></i> Tim IT Support
            </h5>
            <p class="text-muted small text-center mb-4">Hubungi teknisi jika darurat (di luar sistem tiket)</p>
            
            <div class="d-flex flex-column gap-2">
                @forelse($itSupports as $it)
                    <div class="d-flex align-items-center border rounded p-2">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                            style="width: 36px; height: 36px; font-size: 0.75rem; font-weight: 600;">
                            {{ strtoupper(substr($it->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.85rem;">{{ $it->name }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">IT Support</small>
                        </div>
                        <div class="text-success" title="Online" style="font-size: 0.6rem;">
                            <i class="bi bi-circle-fill"></i>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted small py-3">
                        Belum ada data IT Support.
                    </div>
                @endforelse
            </div>
            
            <div class="alert alert-warning mt-3 mb-0 text-start" style="font-size: 0.75rem;">
                <i class="bi bi-info-circle"></i> <strong>Catatan:</strong> Tetap laporkan kendala Anda melalui menu <strong>"Lapor Kendala"</strong> agar tiket dapat dilacak dan diukur SLA-nya.
            </div>
        </div>
    </div>
</div>

@endsection