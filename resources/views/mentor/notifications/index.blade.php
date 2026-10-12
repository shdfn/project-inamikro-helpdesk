@extends('template')

@section('judul', 'Notifikasi')

@section('konten')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Notifikasi</h4>
        <small class="text-muted">{{ $notifications->count() }} notifikasi</small>
    </div>

    @forelse($notifications as $notif)
        <a href="{{ $notif->ticket_id ? route('mentor.tickets.show', $notif->ticket_id) : '#' }}"
           class="d-flex align-items-start p-3 border-bottom text-decoration-none text-dark
                  {{ $notif->is_read ? '' : 'bg-light' }}">
            <div class="me-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 45px; height: 45px; background-color: #f8d7da;">
                <i class="bi bi-chat-dots" style="color: #d81b21;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold">
                    {{ $notif->title }}
                    @if(!$notif->is_read)
                        <span class="badge bg-danger ms-2" style="font-size: 0.65rem;">Baru</span>
                    @endif
                </div>
                <div class="text-muted small">{{ $notif->body }}</div>
            </div>
            <small class="text-muted flex-shrink-0">{{ $notif->created_at->diffForHumans() }}</small>
        </a>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1"></i>
            <p class="mt-2">Belum ada notifikasi.</p>
        </div>
    @endforelse
</div>
@endsection