@extends('template')

@section('judul', 'Tiket Saya')

@section('konten')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card card-custom p-4">
    <h4 class="fw-bold">Tiket Saya</h4>
    <p class="text-muted">Pantau dan kelola seluruh tiket yang Anda buat</p>

    {{-- TAB FILTER --}}
    @php
        $currentStatus = request('status', 'semua');
    @endphp
    <ul class="nav nav-tabs mb-3 border-0">
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ $currentStatus == 'semua' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ $currentStatus == 'semua' ? 'color:#d81b21;' : '' }}"
               href="{{ route('mentor.tickets.index', ['status' => 'semua', 'q' => request('q')]) }}">
                Semua ({{ $counts['semua'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ $currentStatus == 'baru' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ $currentStatus == 'baru' ? 'color:#d81b21;' : '' }}"
               href="{{ route('mentor.tickets.index', ['status' => 'baru', 'q' => request('q')]) }}">
                Baru ({{ $counts['baru'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ $currentStatus == 'diproses' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ $currentStatus == 'diproses' ? 'color:#d81b21;' : '' }}"
               href="{{ route('mentor.tickets.index', ['status' => 'diproses', 'q' => request('q')]) }}">
                Diproses ({{ $counts['diproses'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link border-0 bg-transparent {{ $currentStatus == 'selesai' ? 'active fw-bold' : 'text-muted' }}"
               style="{{ $currentStatus == 'selesai' ? 'color:#d81b21;' : '' }}"
               href="{{ route('mentor.tickets.index', ['status' => 'selesai', 'q' => request('q')]) }}">
                Selesai ({{ $counts['selesai'] }})
            </a>
        </li>
    </ul>

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
                @forelse ($tickets as $ticket)
                <tr>
                    <td class="fw-bold">#{{ $ticket->ticket_number }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>{{ $ticket->category->name ?? '-' }}</td>
                    <td>
                        @if($ticket->priority == 'Tinggi')
                            <span class="badge badge-soft-danger rounded-pill px-3">Tinggi</span>
                        @elseif($ticket->priority == 'Sedang')
                            <span class="badge badge-soft-warning rounded-pill px-3">Sedang</span>
                        @else
                            <span class="badge badge-soft-success rounded-pill px-3">Rendah</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->status == 'Diproses')
                            <span class="badge badge-soft-warning rounded-pill px-3">Diproses</span>
                        @elseif($ticket->status == 'Selesai')
                            <span class="badge badge-soft-success rounded-pill px-3">Selesai</span>
                        @elseif($ticket->status == 'Ditutup')
                            <span class="badge badge-soft-primary rounded-pill px-3">Ditutup</span>
                        @else
                            <span class="badge badge-soft-danger rounded-pill px-3">Baru</span>
                        @endif
                    </td>
                    <td>{{ $ticket->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('mentor.tickets.show', $ticket->id) }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-chat"></i> Diskusi
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada tiket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection