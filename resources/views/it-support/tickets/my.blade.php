@extends('template')

@section('judul', 'Tiket Ditangani Saya')

@section('konten')

<div class="card card-custom p-4">
    <h4 class="fw-bold">Tiket Ditangani</h4>
    <p class="text-muted">Tiket yang sedang dalam proses penanganan Anda.</p>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('it.tickets.my') }}" class="row g-2 mb-3">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Cari tiket...">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-inamikro w-100">Cari</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('it.tickets.my') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    {{-- INFO JUMLAH --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">Total: <strong>{{ $tickets->count() }}</strong> tiket</small>
    </div>

    {{-- TABEL --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="table-layout: fixed; width: 100%;">
            <thead class="bg-light">
                <tr>
                    <th style="width: 8%;">ID Tiket</th>
                    <th style="width: 20%;">Judul Kendala</th>
                    <th style="width: 9%;">Kategori</th>
                    <th style="width: 8%;">Prioritas</th>
                    <th style="width: 11%;">Pelapor</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 11%;">Dibuat</th> {{-- Dipersingkat dari Waktu Dibuat --}}
                    <th style="width: 12%;">Balasan Terakhir</th>
                    <th style="width: 11%;">Waktu Balasan</th>
                    <th style="width: 5%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr onclick="window.location='{{ route('it.tickets.show', $ticket->id) }}'" style="cursor: pointer;">
                        <td class="fw-bold">#{{ $ticket->ticket_number }}</td>
                        <td style="word-wrap: break-word;">
                            <div class="fw-bold">{{ $ticket->title }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($ticket->description, 30) }}</small>
                        </td>
                        <td>{{ $ticket->category?->name ?? '-' }}</td>
                        <td>
                            @php
                                $pc = match($ticket->priority) {
                                    'Tinggi' => 'badge-soft-danger',
                                    'Sedang' => 'badge-soft-warning',
                                    default => 'badge-soft-success',
                                };
                            @endphp
                            <span class="badge {{ $pc }} rounded-pill px-2">{{ $ticket->priority }}</span>
                        </td>
                        <td><i class="bi bi-person-circle me-1"></i> {{ $ticket->mentor?->name ?? '-' }}</td>
                        
                        {{-- STATUS RINGKAS --}}
                        <td>
                            <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-2" title="{{ $ticket->status_label }}">
                                <i class="bi bi-{{ $ticket->status_icon }}"></i> {{ $ticket->status_short }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $ticket->created_at->format('d/m/y H:i') }}</small></td>
                        
                        {{-- BALASAN TERAKHIR --}}
                        <td>
                            @php $lastMessage = $ticket->messages->first(); @endphp
                            @if($lastMessage && $lastMessage->user)
                                <div class="d-flex flex-column">
                                    <span class="fw-bold" style="font-size: 0.8rem;" title="{{ $lastMessage->user->name }}">
                                        <i class="bi bi-person-circle"></i> {{ Str::limit($lastMessage->user->name, 12) }}
                                    </span>
                                    <span class="badge {{ $lastMessage->user->role === 'mentor' ? 'bg-secondary' : 'bg-danger' }}" style="font-size: 0.6rem; width: fit-content;">
                                        {{ $lastMessage->user->role === 'mentor' ? 'Mentor' : 'IT Support' }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted fst-italic">-</span>
                            @endif
                        </td>

                        {{-- WAKTU BALASAN --}}
                        <td>
                            @if($lastMessage)
                                <small class="text-muted">{{ $lastMessage->created_at->format('d/m/y H:i') }}</small>
                            @else
                                <span class="text-muted fst-italic">-</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('it.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-dark" onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Belum ada tiket ditangani.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection