@extends('template')

@section('judul', 'Pengawasan Tiket')

@section('konten')

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Pengawasan Tiket</h4>
            <p class="text-muted mb-0">Semua tiket dari seluruh divisi</p>
        </div>
        <a href="{{ route('kepala.tickets.export', request()->query()) }}" class="btn btn-outline-danger">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('kepala.tickets.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Cari tiket...">
            </div>
        </div>
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
        <div class="col-md-2">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
        <div class="col-md-1">
            <button type="submit" class="btn btn-inamikro w-100" title="Filter"><i class="bi bi-funnel"></i></button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('kepala.tickets.index') }}" class="btn btn-outline-secondary w-100" title="Reset"><i class="bi bi-arrow-clockwise"></i> Reset</a>
        </div>
    </form>

    {{-- INFO JUMLAH --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">Total: <strong>{{ $tickets->total() }}</strong> tiket</small>
    </div>

    {{-- TABEL --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle" style="table-layout: fixed; width: 100%;">
            <thead class="bg-light">
                <tr>
                    <th style="width: 8%;">ID Tiket</th>
                    <th style="width: 22%;">Judul Kendala</th>
                    <th style="width: 10%;">Kategori</th>
                    <th style="width: 8%;">Prioritas</th>
                    <th style="width: 10%;">Pelapor</th>
                    <th style="width: 10%;">IT Support</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 12%;">Waktu Dibuat</th>
                    <th style="width: 12%;">Balasan Terakhir</th>
                    <th style="width: 11%;">Waktu Balasan</th>
                    <th style="width: 5%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr onclick="window.location='{{ route('kepala.tickets.show', $ticket->id) }}'" style="cursor: pointer;">
                        <td class="fw-bold">#{{ $ticket->ticket_number }}</td>
                        
                        <td style="word-wrap: break-word;">
                            <div class="fw-bold">{{ $ticket->title }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($ticket->description, 30) }}</small>
                        </td>
                        
                        {{-- Gunakan ?-> agar aman jika kategori kosong --}}
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

                        {{-- Gunakan ?-> agar aman jika mentor kosong --}}
                        <td><i class="bi bi-person-circle me-1"></i> {{ $ticket->mentor?->name ?? '-' }}</td>

                        {{-- Gunakan ?-> agar aman jika itSupport kosong --}}
                        <td>
                            @if($ticket->itSupport)
                                <i class="bi bi-person-badge me-1"></i> {{ $ticket->itSupport->name }}
                            @else
                                <span class="text-muted small">Belum ditugaskan</span>
                            @endif
                        </td>

                        {{-- STATUS TIDAK DIUBAH --}}
                        <td>
                            <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-2" title="{{ $ticket->status_short }}">
                                <i class="bi bi-{{ $ticket->status_icon }}"></i> {{ $ticket->status_short }}
                            </span>
                        </td>

                        <td>
                            <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i:s') }}</small>
                        </td>

                        {{-- Balasan Terakhir --}}
                        <td>
                            @php
                                $lastMessage = $ticket->messages->first(); 
                            @endphp
                            
                            @if($lastMessage && $lastMessage->user)
                                <div class="d-flex flex-column">
                                    <span class="fw-bold" style="font-size: 0.8rem;">
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

                        {{-- Waktu Balasan --}}
                        <td>
                            @if($lastMessage)
                                <small class="text-muted">{{ $lastMessage->created_at->format('d/m/y H:i') }}</small>
                            @else
                                <span class="text-muted fst-italic">-</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('kepala.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-dark" onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada tiket.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($tickets->hasPages())
        <div class="mt-4 d-flex justify-content-end">
            {{ $tickets->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection