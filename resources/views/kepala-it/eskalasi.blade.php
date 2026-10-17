@extends('template')

@section('judul', 'Eskalasi & Pantauan')

@section('konten')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Eskalasi & Pantauan</h4>
        <p class="text-muted mb-0">
            Tiket yang sudah melewati SLA (> 24 jam) & membutuhkan perhatian khusus
        </p>
    </div>
    <a href="{{ route('kepala.tickets.export') }}" class="btn btn-outline-danger">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div>

{{-- KARTU METRIK --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">TOTAL ESKALASI</small>
                <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
            </div>
            <h2 class="fw-bold mt-2 mb-0">{{ $totalEskalasi }}</h2>
            <small class="text-danger">
                <i class="bi bi-arrow-up"></i> Butuh tindakan segera
            </small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">SLA TERCAPAI</small>
                <i class="bi bi-check-circle text-success fs-4"></i>
            </div>
            <h2 class="fw-bold mt-2 mb-0">{{ $persenSLA }}%</h2>
            <small class="text-muted">Target ≥ 90%</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted fw-bold">STATUS</small>
                @if($totalEskalasi > 5)
                    <i class="bi bi-x-circle text-danger fs-4"></i>
                @elseif($totalEskalasi > 0)
                    <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                @else
                    <i class="bi bi-check-circle text-success fs-4"></i>
                @endif
            </div>
            <h2 class="fw-bold mt-2 mb-0">
                @if($totalEskalasi > 5)
                    <span class="text-danger">Perlu Tindakan</span>
                @elseif($totalEskalasi > 0)
                    <span class="text-warning">Pantau</span>
                @else
                    <span class="text-success">Aman</span>
                @endif
            </h2>
        </div>
    </div>
</div>

{{-- DAFTAR TIKET ESKALASI --}}
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-exclamation-triangle text-danger"></i> Daftar Tiket Eskalasi
        </h5>
        <small class="text-muted">{{ $tiketEskalasi->count() }} tiket</small>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Pelapor</th>
                    <th>IT Support</th>
                    <th>Umur</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tiketEskalasi as $t)
                    <tr onclick="window.location='{{ route('kepala.tickets.show', $t->id) }}'"
                        style="cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#fff5f5';"
                        onmouseout="this.style.backgroundColor='';">
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td>
                            <div class="fw-bold">{{ $t->title }}</div>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($t->description, 50) }}
                            </small>
                        </td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>
                            @php
                                $pc = match($t->priority) {
                                    'Tinggi' => 'badge-soft-danger',
                                    'Sedang' => 'badge-soft-warning',
                                    default => 'badge-soft-success',
                                };
                            @endphp
                            <span class="badge {{ $pc }} rounded-pill px-3">{{ $t->priority }}</span>
                        </td>
                        <td>
                            <i class="bi bi-person-circle me-1"></i>
                            {{ $t->mentor->name ?? '-' }}
                        </td>
                        <td>
                            @if($t->itSupport)
                                <i class="bi bi-person-badge me-1"></i>
                                {{ $t->itSupport->name }}
                            @else
                                <span class="badge badge-soft-danger rounded-pill px-2"
                                      style="font-size: 0.7rem;">Belum ditangani</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $hours = $t->created_at->diffInHours(now());
                                $days = floor($hours / 24);
                            @endphp
                            <span class="badge badge-soft-danger rounded-pill px-3">
                                <i class="bi bi-clock"></i>
                                @if($days > 0)
                                    {{ $days }} hari
                                @else
                                    {{ $hours }} jam
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $t->status_badge_class }} rounded-pill px-3"
                                  title="{{ $t->status_label }}">
                                <i class="bi bi-{{ $t->status_icon }}"></i>
                                {{ $t->status_short }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kepala.tickets.show', $t->id) }}"
                               class="btn btn-sm btn-outline-danger"
                               onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                            <p class="mb-0">Tidak ada tiket eskalasi. Semua tiket dalam SLA. 🎉</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection