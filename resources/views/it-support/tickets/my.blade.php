@extends('template')

@section('judul', 'Tiket Ditangani Saya')

@section('konten')
<div class="card card-custom p-4">
    <h4 class="fw-bold">Tiket Ditangani</h4>
    <p class="text-muted">Tiket yang sedang dalam proses penanganan Anda.</p>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0"
                       placeholder="Cari tiket, judul, kategori, atau nama...">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-inamikro w-100">Cari</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th>
                    <th>Judul Kendala</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Pelapor</th>
                    <th>Waktu</th>
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
                        <td>{{ $ticket->mentor->name ?? '-' }}</td>
                        <td>{{ $ticket->created_at->format('n/j/y H:i') }}</td>
                        <td>
                            <a href="{{ route('it.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-dark">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada tiket ditangani.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection