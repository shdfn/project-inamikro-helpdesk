@extends('template')

@section('judul', 'Seluruh Tiket Masuk')

@section('konten')

<div class="card card-custom p-4">
    <h4 class="fw-bold">Seluruh Tiket Masuk</h4>
    <p class="text-muted">Daftar tiket yang membutuhkan penanganan.</p>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('it.tickets.incoming') }}" class="row g-2 mb-3">
        {{-- Search --}}
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control bg-light border-0"
                       placeholder="Cari tiket, judul, pelapor...">
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="Baru" {{ request('status') == 'Baru' ? 'selected' : '' }}>Baru</option>
                <option value="Ditangani IT" {{ request('status') == 'Ditangani IT' ? 'selected' : '' }}>Ditangani IT</option>
                <option value="Dibalas IT" {{ request('status') == 'Dibalas IT' ? 'selected' : '' }}>Dibalas IT</option>
                <option value="Dibalas Mentor" {{ request('status') == 'Dibalas Mentor' ? 'selected' : '' }}>Dibalas Mentor</option>
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
        <div class="col-md-1">
            <a href="{{ route('it.tickets.incoming') }}"
               class="btn btn-outline-secondary w-100" title="Reset Filter">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    {{-- INFO JUMLAH --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">
            Total: <strong>{{ $tickets->count() }}</strong> tiket
            @if(request('q') || request('status') || request('category_id') || request('priority'))
                (terfilter)
            @endif
        </small>
        @if(request()->has('q') || request()->has('status') || request()->has('category_id') || request()->has('priority'))
            <a href="{{ route('it.tickets.incoming') }}"
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
                    {{-- ⭐ Baris bisa diklik ke detail --}}
                    <tr onclick="window.location='{{ route('it.tickets.show', $ticket->id) }}'"
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
                            <span class="badge {{ $pc }} rounded-pill px-3">{{ $ticket->priority }}</span>
                        </td>
                        <td>
                            <i class="bi bi-person-circle me-1"></i>
                            {{ $ticket->mentor->name ?? '-' }}
                        </td>
                        <td>
                            @if($ticket->itSupport)
                                <i class="bi bi-person-badge me-1"></i>
                                <small>{{ $ticket->itSupport->name }}</small>
                            @else
                                <span class="badge badge-soft-secondary rounded-pill px-2"
                                      style="font-size: 0.7rem;">Belum</span>
                            @endif
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
                            {{-- ⭐ Aksi kontekstual --}}
                            @if($ticket->it_support_id === auth()->id())
                                {{-- Milik sendiri → Detail --}}
                                <a href="{{ route('it.tickets.show', $ticket->id) }}"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="event.stopPropagation();">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            @elseif($ticket->it_support_id && $ticket->it_support_id !== auth()->id())
                                {{-- Ditangani IT lain → Ambil Alih --}}
                                <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="event.stopPropagation(); return confirm('Ambil alih tiket ini dari {{ $ticket->itSupport->name ?? 'IT lain' }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning"
                                            onclick="event.stopPropagation();">
                                        <i class="bi bi-arrow-left-right"></i> Ambil Alih
                                    </button>
                                </form>
                            @else
                                {{-- Belum ditangani → Tangani --}}
                                <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="event.stopPropagation(); return confirm('Tangani tiket ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-inamikro"
                                            onclick="event.stopPropagation();">
                                        <i class="bi bi-hand-index"></i> Tangani
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            @if(request('q') || request('status') || request('category_id') || request('priority'))
                                Tidak ada tiket yang cocok dengan filter.
                            @else
                                Belum ada tiket masuk.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection