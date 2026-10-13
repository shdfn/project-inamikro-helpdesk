@extends('template')
@section('judul', 'Semua Tiket')
@section('breadcrumb', 'Pengawasan Tiket Masuk')

@section('konten')

{{-- Header --}}
<div class="d-flex justify-content-between mb-3">
    <div>
        <h3 class="fw-bold mb-0">Pengawasan Tiket Masuk</h3>
        <p class="text-muted mb-0">Ringkasan seluruh tiket masuk, status penanganan, dan teknisi PIC.</p>
    </div>
</div>

{{-- Kartu Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TOTAL TIKET</small>
            <h2 class="fw-bold my-1">{{ $counts['semua'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">MENUNGGU RESPON</small>
            <h2 class="fw-bold my-1 text-danger">{{ $counts['baru'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">SEDANG DIKERJAKAN</small>
            <h2 class="fw-bold my-1">{{ $counts['diproses'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TELAH DITUTUP</small>
            <h2 class="fw-bold my-1">{{ $counts['selesai'] }}</h2>
        </div>
    </div>
</div>

{{-- Card Utama --}}
<div class="card card-custom p-4">

    {{-- TAB FILTER (dengan q) --}}
    <ul class="nav nav-tabs mb-3 border-0">
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ !request('status') ? 'active fw-bold' : 'text-muted' }}"
               style="{{ !request('status') ? 'color:#d81b21;' : '' }}"
               href="{{ route('kepala.tickets.index', ['q' => request('q'), 'category_id' => request('category_id')]) }}">
                Semua ({{ $counts['semua'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ request('status') == 'Baru' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ request('status') == 'Baru' ? 'color:#d81b21;' : '' }}"
               href="{{ route('kepala.tickets.index', ['status' => 'Baru', 'q' => request('q'), 'category_id' => request('category_id')]) }}">
                Baru ({{ $counts['baru'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ request('status') == 'Diproses' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ request('status') == 'Diproses' ? 'color:#d81b21;' : '' }}"
               href="{{ route('kepala.tickets.index', ['status' => 'Diproses', 'q' => request('q'), 'category_id' => request('category_id')]) }}">
                Diproses ({{ $counts['diproses'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ request('status') == 'Selesai' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ request('status') == 'Selesai' ? 'color:#d81b21;' : '' }}"
               href="{{ route('kepala.tickets.index', ['status' => 'Selesai', 'q' => request('q'), 'category_id' => request('category_id')]) }}">
                Selesai ({{ $counts['selesai'] }})
            </a>
        </li>
    </ul>

    {{-- Form Filter Tambahan (Kategori) --}}
    <form method="GET" class="row g-2 mb-3">
        {{-- Pertahankan q & status saat filter kategori --}}
        <input type="hidden" name="q" value="{{ request('q') }}">
        <input type="hidden" name="status" value="{{ request('status') }}">

        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari ID tiket, judul...">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-inamikro w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('kepala.tickets.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    {{-- Info Jumlah --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">
            Menampilkan <strong>{{ $tickets->count() }}</strong> dari
            <strong>{{ $tickets->total() }}</strong> tiket
        </small>
        @if(request('q') || request('status') || request('category_id'))
            <a href="{{ route('kepala.tickets.index') }}" class="text-decoration-none small text-danger">
                <i class="bi bi-x-circle"></i> Reset Filter
            </a>
        @endif
    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID TIKET</th>
                    <th>JUDUL</th>
                    <th>KATEGORI</th>
                    <th>PIC</th>
                    <th>DURASI</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>{{ $t->itSupport->name ?? '-' }}</td>
                        <td>{{ $t->created_at->diffForHumans(null, true) }}</td>
                        <td>
                            @php
                                $sc = match($t->status) {
                                    'Baru' => 'danger',
                                    'Diproses' => 'warning',
                                    'Selesai' => 'success',
                                    'Ditutup' => 'primary',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge badge-soft-{{ $sc }} rounded-pill px-3">{{ $t->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('kepala.tickets.show', $t->id) }}" class="text-danger small">
                                Detail <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada tiket yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">{{ $tickets->links() }}</div>

</div>
@endsection