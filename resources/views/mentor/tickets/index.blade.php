@extends('template')

@section('judul', 'Tiket Saya')

@section('konten')

<div class="card card-custom p-4">
    <h4 class="fw-bold">Tiket Saya</h4>
    <p class="text-muted">Pantau dan kelola seluruh tiket yang Anda buat</p>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('mentor.tickets.index') }}" class="row g-2 mb-3">
        {{-- Search --}}
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control bg-light border-0"
                       placeholder="Cari tiket, judul, atau deskripsi...">
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>
                    Baru — Menunggu IT Support
                </option>
                <option value="ditangani" {{ request('status') == 'ditangani' ? 'selected' : '' }}>
                    Ditangani IT — Belum Dibalas
                </option>
                <option value="dibalas_it" {{ request('status') == 'dibalas_it' ? 'selected' : '' }}>
                    Dibalas IT — Menunggu Mentor
                </option>
                <option value="dibalas_mentor" {{ request('status') == 'dibalas_mentor' ? 'selected' : '' }}>
                    Dibalas Mentor — Menunggu IT
                </option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                    Selesai — Menunggu Konfirmasi
                </option>
                <option value="ditutup" {{ request('status') == 'ditutup' ? 'selected' : '' }}>
                    Ditutup — Dikonfirmasi Mentor
                </option>
            </select>
        </div>

        {{-- Tombol Filter --}}
        <div class="col-md-2">
            <button type="submit" class="btn btn-inamikro w-100">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>

        {{-- Tombol Reset --}}
        <div class="col-md-2">
            <a href="{{ route('mentor.tickets.index') }}"
               class="btn btn-outline-secondary w-100" title="Reset Filter">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>

    {{-- INFO JUMLAH --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">
            Total: <strong>{{ $tickets->count() }}</strong> tiket
            @if(request('q') || request('status'))
                (terfilter)
            @endif
        </small>
        @if(request()->has('q') || request()->has('status'))
            <a href="{{ route('mentor.tickets.index') }}"
               class="text-decoration-none small text-danger">
                <i class="bi bi-x-circle"></i> Reset Filter
            </a>
        @endif
    </div>

    {{-- TABEL TIKET --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th>
                    <th>Judul Kendala</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    {{-- ⭐ Baris bisa diklik ke detail tiket --}}
                    <tr onclick="window.location='{{ route('mentor.tickets.show', $ticket->id) }}'"
                        style="cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#f8f9fa';"
                        onmouseout="this.style.backgroundColor='';">
                        <td class="fw-bold">#{{ $ticket->ticket_number }}</td>
                        <td>
                            <div class="fw-bold">{{ $ticket->title }}</div>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($ticket->description, 60) }}
                            </small>
                        </td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
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
                            <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-3"
                                  title="{{ $ticket->status_label }}">
                                <i class="bi bi-{{ $ticket->status_icon }}"></i>
                                {{ $ticket->status_short }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted" title="{{ $ticket->created_at->format('d M Y H:i') }}">
                                {{ $ticket->created_at->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            {{-- ⭐ onclick stopPropagation() supaya klik tombol tidak dobel trigger baris --}}
                            <a href="{{ route('mentor.tickets.show', $ticket->id) }}"
                               class="btn btn-outline-danger btn-sm"
                               onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            @if(request('q') || request('status'))
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
</div>

@endsection