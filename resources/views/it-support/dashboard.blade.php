@extends('template')

@section('judul', 'Dashboard & Antrean')

@section('konten')

<div class="card card-custom p-4 mb-4">
    <h3 class="fw-bold mb-1">Selamat datang, {{ auth()->user()->name }} 👋</h3>
    <p class="text-muted mb-0">Berikut tiket yang perlu Anda tangani</p>
</div>

{{-- STATISTIK --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">TIKET MASUK</small>
                <i class="bi bi-inbox text-primary"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['masuk'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 2 dari kemarin</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">SEDANG DITANGANI</small>
                <i class="bi bi-hand-index text-danger"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['ditangani'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 1 dari kemarin</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">MENUNGGU</small>
                <i class="bi bi-hourglass text-primary"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['menunggu'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 1 dari kemarin</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">SELESAI</small>
                <i class="bi bi-check-circle text-success"></i>
            </div>
            <h2 class="fw-bold mt-2">{{ $stats['selesai'] }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 5 dari kemarin</small>
        </div>
    </div>
</div>

{{-- TABEL TIKET MASUK --}}
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0"><i class="bi bi-ticket-detailed me-2"></i> Tiket Masuk</h5>
        <a href="{{ route('it.tickets.incoming') }}" class="text-decoration-none" style="color: #d81b21;">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th>
                    <th>Judul Kendala</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Pelapor</th>
                    <th>Status</th>
                    <th>Waktu Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="fw-bold">#{{ $ticket->ticket_number }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td>
                            @php
                                $pc = match($ticket->priority) {
                                    'Tinggi' => 'badge-soft-danger',
                                    'Sedang' => 'badge-soft-warning',
                                    default => 'badge-soft-success',
                                };
                            @endphp
                            <span class="badge {{ $pc }} rounded-pill px-3">{{ $ticket->priority }}</span>
                        </td>
                        <td>
                            <i class="bi bi-person-circle me-1"></i> {{ $ticket->mentor->name ?? '-' }}
                        </td>
                        <td>
                            @if($ticket->status === 'Baru')
                                <span class="badge badge-soft-danger rounded-pill px-3">Baru</span>
                            @else
                                <span class="badge badge-soft-warning rounded-pill px-3">Ditangani</span>
                            @endif
                        </td>
                        <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                        <td>
                            @if($ticket->status === 'Baru')
                                <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-inamikro">Tangani</button>
                                </form>
                            @else
                                <a href="{{ route('it.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-dark">
                                    Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada tiket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection