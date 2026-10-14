@extends('template')
@section('judul', 'Notifikasi')
@section('breadcrumb', 'Notifikasi')

@section('konten')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Notifikasi Eskalasi</h4>
            <p class="text-muted mb-0">Tiket yang membutuhkan perhatian Kepala IT</p>
        </div>
        <span class="badge bg-danger rounded-pill px-3 py-2">
            {{ $lateTickets->count() }} tiket lambat
        </span>
    </div>

    @forelse($lateTickets as $t)
        @php
            $menit = $t->created_at->diffInMinutes(now());
            $jam   = floor($menit / 60);
            $sisa  = $menit % 60;
        @endphp
        <a href="{{ route('kepala.tickets.show', $t->id) }}"
           class="d-flex align-items-start p-3 border-bottom text-decoration-none text-dark">
            <div class="me-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 45px; height: 45px; background-color: #f8d7da;">
                <i class="bi bi-exclamation-triangle" style="color: #d81b21;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold">
                    Tiket #{{ $t->ticket_number }} — {{ $t->title }}
                </div>
                <div class="text-muted small">
                    Pelapor: {{ $t->mentor->name ?? '-' }} &middot;
                    PIC: {{ $t->itSupport->name ?? 'Belum ditugaskan' }} &middot;
                    Status: {{ $t->status }} &middot;
                    Prioritas: {{ $t->priority }}
                </div>
                <div class="mt-1">
                    <span class="badge badge-soft-danger rounded-pill px-3">
                        <i class="bi bi-clock-history"></i> Lewat SLA {{ $jam }}j {{ $sisa }}m
                    </span>
                </div>
            </div>
            <small class="text-muted flex-shrink-0">
                {{ $t->created_at->diffForHumans() }}
            </small>
        </a>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-check-circle fs-1 text-success"></i>
            <p class="mt-2">Tidak ada notifikasi. Semua tiket dalam kondisi baik. 🎉</p>
        </div>
    @endforelse
</div>
@endsection