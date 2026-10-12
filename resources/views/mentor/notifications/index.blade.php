@extends('template')

@section('judul', 'Notifikasi')

@section('konten')
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-4">Notifikasi</h4>

    @forelse($notifications as $notif)
        <a href="{{ route('mentor.tickets.show', $notif->id) }}"
           class="d-flex align-items-start p-3 border-bottom text-decoration-none text-dark">
            <div class="me-3 rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 45px; height: 45px; background-color: #f8d7da;">
                <i class="bi bi-ticket-detailed" style="color: #d81b21;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold">Tiket #{{ $notif->ticket_number }} diperbarui</div>
                <div class="text-muted small">
                    Status: <strong>{{ $notif->status }}</strong> &mdash; {{ $notif->title }}
                </div>
            </div>
            <small class="text-muted">{{ $notif->updated_at->diffForHumans() }}</small>
        </a>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1"></i>
            <p class="mt-2">Belum ada notifikasi.</p>
        </div>
    @endforelse
</div>
@endsection