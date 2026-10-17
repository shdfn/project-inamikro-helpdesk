@extends('template')

@section('judul', 'Pengawasan Tiket')

@section('konten')

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Pengawasan Tiket</h4>
            <p class="text-muted mb-0">Semua tiket dari seluruh divisi</p>
        </div>
        <a href="{{ route('kepala.tickets.export', request()->query()) }}"
           class="btn btn-outline-danger">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('kepala.tickets.index') }}" class="row g-2 mb-3">
        {{-- Search --}}
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control bg-light border-0"
                       placeholder="Cari tiket, judul, kategori...">
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="Baru" {{ request('status') == 'Baru' ? 'selected' : '' }}>Baru</option>
                <option value="Ditangani IT" {{ request('status') == 'Ditangani IT' ? 'selected' : '' }}>Ditangani IT</option>
                <option value="Dibalas IT" {{ request('status') == 'Dibalas IT' ? 'selected' : '' }}>Dibalas IT</option>
                <option value="Dibalas Mentor" {{ request('status') == 'Dibalas Mentor' ? 'selected' : '' }}>Dibalas Mentor</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Ditutup" {{ request('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
            </select>
        </div>

        {{-- Filter Kategori --}}
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

        {{-- Filter Prioritas --}}
        <div class="col-md-2">
            <select name="priority" class="form-select">
                <option value="">Semua Prioritas</option>
                <option value="Tinggi" {{ request('priority') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="Sedang" {{ request('priority') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="Rendah" {{ request('priority') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
            </select>
        </div>

        {{-- Tombol Filter --}}
        <div class="col-md-1">
            <button type="submit" class="btn btn-inamikro w-100" title="Filter">
                <i class="bi bi-funnel"></i>
            </button>
        </div>

        {{-- Tombol Reset --}}
        <div class="col-md-2">
            <a href="{{ route('kepala.tickets.index') }}"
               class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>

    {{-- INFO JUMLAH --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">
            Total: <strong>{{ $tickets->total() }}</strong> tiket
            @if(request('q') || request('status') || request('category_id') || request('priority'))
                (terfilter)
            @endif
        </small>
        @if(request()->has('q') || request()->has('status') || request()->has('category_id') || request()->has('priority'))
            <a href="{{ route('kepala.tickets.index') }}"
               class="text-decoration-none small text-danger">
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
                    <th>IT Support</th>
                    <th>Status</th>
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

                        {{-- KOLOM PRIORITAS --}}
                        <td>
                            @php
                                $pc = match($ticket->priority) {
                                    'Tinggi' => 'badge-soft-danger',
                                    'Sedang' => 'badge-soft-warning',
                                    default => 'badge-soft-success',
                                };
                            @endphp
                            <span class="badge {{ $pc }} rounded-pill px-3">
                                {{ $ticket->priority }}
                            </span>
                        </td>

                        <td>
                            <i class="bi bi-person-circle me-1"></i>
                            {{ $ticket->mentor->name ?? '-' }}
                        </td>

                        <td>
                            @if($ticket->itSupport)
                                <i class="bi bi-person-badge me-1"></i>
                                {{ $ticket->itSupport->name }}
                            @else
                                <span class="text-muted small">Belum ditugaskan</span>
                            @endif
                        </td>

                        {{-- KOLOM STATUS --}}
                        <td>
                            <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-3"
                                  title="{{ $ticket->status_label }}">
                                <i class="bi bi-{{ $ticket->status_icon }}"></i>
                                {{ $ticket->status_label }}
                            </span>
                        </td>

                        <td>{{ $ticket->created_at->format('n/j/y H:i') }}</td>

                        <td>
                            <a href="{{ route('kepala.tickets.show', $ticket->id) }}"
                               class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            @if(request('q') || request('status') || request('category_id') || request('priority'))
                                Tidak ada tiket yang cocok dengan filter.
                            @else
                                Belum ada tiket.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($tickets->hasPages())
        <div class="mt-3">
            {{ $tickets->withQueryString()->links() }}
        </div>
    @endif
</div>

@endsection