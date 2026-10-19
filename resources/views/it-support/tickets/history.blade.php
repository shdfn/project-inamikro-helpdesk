@extends('template')

@section('judul', 'Riwayat & Arsip')

@section('konten')

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Riwayat & Arsip</h4>
            <p class="text-muted mb-0">Daftar tiket yang pernah Anda tangani</p>
        </div>
        {{-- TOMBOL EXPORT — bawa query filter yang aktif --}}
        <a href="{{ route('it.tickets.export', request()->query()) }}" class="btn btn-outline-danger">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    {{-- FORM FILTER --}}
    <form method="GET" action="{{ route('it.tickets.history') }}" class="row g-2 mb-3 mt-2">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control bg-light border-0"
                       placeholder="Cari tiket, judul...">
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
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="form-control" placeholder="Dari tanggal">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="form-control" placeholder="Sampai tanggal">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-inamikro w-100">
                <i class="bi bi-funnel"></i>
            </button>
        </div>
    </form>

    {{-- INFO --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">Total: <strong>{{ $tickets->count() }}</strong> tiket</small>
        @if(request()->has('q') || request()->has('category_id') || request()->has('priority') || request()->has('date_from') || request()->has('date_to'))
            <a href="{{ route('it.tickets.history') }}" class="text-decoration-none small text-danger">
                <i class="bi bi-x-circle"></i> Reset Filter
            </a>
        @endif
    </div>

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
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($tickets as $ticket)
                {{-- Baris bisa diklik ke halaman detail --}}
                <tr onclick="window.location='{{ route('it.tickets.show', $ticket->id) }}'" 
                    style="cursor: pointer;"
                    onmouseover="this.style.backgroundColor='#f8f9fa';"
                    onmouseout="this.style.backgroundColor='';">
                    
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
                        <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-3" title="{{ $ticket->status_label }}">
                            <i class="bi bi-{{ $ticket->status_icon }}"></i> {{ $ticket->status_short }}
                        </span>
                    </td>
                    
                    {{-- Tambahkan Kolom Aksi --}}
                    <td>
                        <a href="{{ route('it.tickets.show', $ticket->id) }}" 
                        class="btn btn-sm btn-outline-dark"
                        onclick="event.stopPropagation();">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada riwayat tiket.
                    </td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>
</div>
@endsection