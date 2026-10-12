@extends('template')

@section('judul', 'Laporan SLA')

@section('konten')

<div class="card card-custom p-4 mb-4">
    <h3 class="fw-bold mb-1">Laporan SLA</h3>
    <p class="text-muted mb-0">Service Level Agreement — kepatuhan penyelesaian tiket</p>
</div>

{{-- Kartu SLA --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted fw-bold">SLA COMPLIANCE</small>
            <h1 class="fw-bold my-2" style="color: var(--inamikro-red);">{{ $slaPercentage }}%</h1>
            <small class="text-muted">{{ $slaCompliant }} dari {{ $totalResolved }} tiket</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted fw-bold">AVG RESPON PERTAMA</small>
            <h1 class="fw-bold my-2">{{ round($avgResponseTime) }}<small class="fs-6 text-muted"> mnt</small></h1>
            <small class="text-muted">Dari tiket dibuat</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted fw-bold">AVG PENYELESAIAN</small>
            <h1 class="fw-bold my-2">{{ round($avgResolutionTime, 1) }}<small class="fs-6 text-muted"> jam</small></h1>
            <small class="text-muted">Dari tiket dibuat</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted fw-bold">TOTAL SELESAI</small>
            <h1 class="fw-bold my-2">{{ $totalResolved }}</h1>
            <small class="text-muted">Sepanjang waktu</small>
        </div>
    </div>
</div>

{{-- Tabel Detail --}}
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-list-check"></i> Detail Kepatuhan SLA</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Pelapor</th>
                    <th>IT Support</th>
                    <th>Dibuat</th>
                    <th>Selesai</th>
                    <th>Durasi</th>
                    <th>Status SLA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    @php
                        $durasi = $t->resolved_at ? $t->created_at->diffInHours($t->resolved_at) : null;
                        $patuh = $durasi !== null && $durasi <= 24;
                    @endphp
                    <tr>
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>{{ $t->mentor->name ?? '-' }}</td>
                        <td>{{ $t->itSupport->name ?? '-' }}</td>
                        <td>{{ $t->created_at->format('d/m/y H:i') }}</td>
                        <td>{{ $t->resolved_at ? $t->resolved_at->format('d/m/y H:i') : '-' }}</td>
                        <td>{{ $durasi !== null ? $durasi . ' jam' : '-' }}</td>
                        <td>
                            @if($patuh)
                                <span class="badge badge-soft-success rounded-pill px-3">
                                    <i class="bi bi-check"></i> Patuh
                                </span>
                            @else
                                <span class="badge badge-soft-danger rounded-pill px-3">
                                    <i class="bi bi-x"></i> Tidak Patuh
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada tiket selesai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection