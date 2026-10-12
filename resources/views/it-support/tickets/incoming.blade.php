@extends('template')

@section('judul', 'Seluruh Tiket Masuk')

@section('konten')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card card-custom p-4">
    <h4 class="fw-bold">Seluruh Tiket Masuk</h4>
    <p class="text-muted">Daftar tiket yang membutuhkan penanganan.</p>

    {{-- FILTER --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0"
                       placeholder="Cari tiket, judul, kategori, atau nama...">
            </div>
        </div>
        <div class="col-md-2">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="priority" class="form-select">
                <option value="">Semua Prioritas</option>
                <option value="Tinggi" {{ request('priority') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="Sedang" {{ request('priority') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="Rendah" {{ request('priority') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-inamikro w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('it.tickets.incoming') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    {{-- TABEL --}}
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
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada tiket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection