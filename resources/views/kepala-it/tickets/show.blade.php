@extends('template')

@section('judul', 'Detail Tiket — Kepala IT')

@section('konten')

{{-- ═══════════════════════════════════════════ --}}
{{-- HEADER TIKET --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="mb-3">
    <a href="{{ route('kepala.tickets.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Kembali ke Semua Tiket
    </a>
    <h2 class="fw-bold mt-2">#{{ $ticket->ticket_number }}</h2>
    <h5 class="text-muted">{{ $ticket->title }}</h5>
    <div class="mt-2">
        <span class="badge {{ $ticket->status_badge_class }} px-3 py-2 rounded-pill">
            <i class="bi bi-{{ $ticket->status_icon }}"></i> {{ $ticket->status_label }}
        </span>
        <span class="badge badge-soft-danger px-3 py-2 rounded-pill">{{ $ticket->priority }}</span>
        <span class="badge badge-soft-secondary px-3 py-2 rounded-pill">
            <i class="bi bi-eye"></i> Mode Kepala IT (Read-Only)
        </span>
    </div>
</div>

<div class="row g-4">

    {{-- ═══════════════════════════════════════════ --}}
    {{-- KOLOM KIRI: Informasi + Diskusi --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="col-md-7">

        {{-- INFORMASI TIKET --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Informasi Tiket</h5>
            <table class="table table-borderless table-sm mb-0">
                <tbody>
                    <tr>
                        <td class="text-muted" style="width: 20%;">Pelapor</td>
                        <td style="width: 30%;" class="fw-bold">{{ $ticket->mentor->name ?? '-' }}</td>
                        <td class="text-muted" style="width: 20%;">IT Support</td>
                        <td style="width: 30%;" class="fw-bold">{{ $ticket->itSupport->name ?? 'Belum ditugaskan' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td class="text-muted">Respon Pertama</td>
                        <td>{{ $ticket->first_response_at ? $ticket->first_response_at->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Prioritas</td>
                        <td class="text-danger fw-bold">{{ $ticket->priority }}</td>
                        <td class="text-muted">Selesai</td>
                        <td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu Masuk</td>
                        <td>{{ $ticket->created_at->format('d M Y, H:i') }}</td>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge {{ $ticket->status_badge_class }} rounded-pill px-3">
                                <i class="bi bi-{{ $ticket->status_icon }}"></i>
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- DISKUSI (Read-Only) --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-chat-left-text"></i> Diskusi
                <small class="text-muted fw-normal">(Read-Only)</small>
            </h5>

            {{-- ⭐ AREA CHAT (SCROLLABLE) — Max Height 450px --}}
            <div class="chat-area" style="max-height: 450px; overflow-y: auto; padding-right: 8px;">

                {{-- DESKRIPSI KENDALA AWAL --}}
                <div class="d-flex mb-3">
                    <div class="me-2">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-file-text"></i>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 bg-light border-start border-4 border-danger flex-grow-1"
                        style="max-width: 85%;">
                        <small class="d-block mb-1 fw-bold text-danger">
                            <i class="bi bi-pin-angle-fill"></i> Deskripsi Kendala Awal
                        </small>
                        <small class="d-block mb-2 text-muted">
                            {{ $ticket->mentor->name ?? 'Mentor' }} — {{ $ticket->created_at->format('d M Y, H:i') }}
                        </small>
                        <div class="mt-2">
                            {{ $ticket->description }}
                        </div>

                        @if($ticket->attachment)
                            <div class="mt-2 pt-2 border-top border-danger">
                                <a href="{{ asset('storage/' . $ticket->attachment) }}" download
                                class="text-dark small text-decoration-none">
                                    <i class="bi bi-paperclip"></i> {{ basename($ticket->attachment) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PEMISAH --}}
                @if($ticket->messages->count() > 0)
                    <hr class="my-3">
                    <div class="text-center mb-3">
                        <small class="text-muted">
                            <i class="bi bi-chat-dots"></i>
                            {{ $ticket->messages->count() }} pesan diskusi
                        </small>
                    </div>
                @endif

                {{-- PESAN DISKUSI (TERBARU DI ATAS) --}}
                @forelse($ticket->messages as $msg)
                    @php
                        $isItSupport = $msg->user && $msg->user->role === 'it_support';
                    @endphp

                    @if($isItSupport)
                        {{-- Pesan dari IT Support (kanan) --}}
                        <div class="d-flex mb-3 justify-content-end">
                            <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                                <small class="d-block text-end mb-1 fw-bold">
                                    <i class="bi bi-person-badge"></i>
                                    (IT Support) {{ $msg->user->name }}
                                </small>
                                <small class="d-block text-end mb-2 text-muted" style="font-size: 0.7rem;">
                                    {{ $msg->created_at->format('d M Y H:i') }}
                                </small>
                                {{ $msg->message }}
                                @if($msg->attachment)
                                    <div class="mt-2 pt-2 border-top">
                                        <a href="{{ asset('storage/' . $msg->attachment) }}" download
                                           class="text-dark small text-decoration-none">
                                            <i class="bi bi-paperclip"></i> {{ basename($msg->attachment) }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-2">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Pesan dari Mentor (kiri) --}}
                        <div class="d-flex mb-3">
                            <div class="me-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>
                            <div class="p-3 rounded-3 bg-light" style="max-width: 80%;">
                                <small class="d-block mb-1 fw-bold">
                                    <i class="bi bi-person"></i>
                                    {{ $msg->user->name ?? 'User' }} (Mentor)
                                </small>
                                <small class="d-block mb-2 text-muted" style="font-size: 0.7rem;">
                                    {{ $msg->created_at->format('d M Y H:i') }}
                                </small>
                                {{ $msg->message }}
                                @if($msg->attachment)
                                    <div class="mt-2 pt-2 border-top">
                                        <a href="{{ asset('storage/' . $msg->attachment) }}" download
                                           class="text-dark small text-decoration-none">
                                            <i class="bi bi-paperclip"></i> {{ basename($msg->attachment) }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center text-muted py-3 mt-3">
                        <i class="bi bi-chat-dots fs-2"></i>
                        <p class="mb-0 mt-2 small">Belum ada balasan diskusi.</p>
                    </div>
                @endforelse
            </div> {{-- END AREA CHAT --}}

            {{-- INFO READ-ONLY (Sticky Bottom) --}}
            <div class="mt-3 pt-3 border-top">
                <div class="alert alert-secondary mb-0">
                    <i class="bi bi-eye"></i>
                    <small>
                        <strong>Mode Kepala IT (Read-Only).</strong>
                        Anda hanya dapat melihat diskusi. Untuk ikut berkomunikasi, hubungi IT Support atau Mentor terkait.
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- KOLOM KANAN: Timeline SLA + Metrik + Aksi --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="col-md-5">

        {{-- TIMELINE SLA --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4">Timeline SLA</h5>
            
            <div class="timeline-container ps-3">
                
                {{-- 1. Tiket Masuk (Selalu Merah) --}}
                <div class="timeline-item is-active position-relative pb-4">
                    <div class="timeline-dot is-active position-absolute"></div>
                    <div class="fw-bold text-danger ms-3">Tiket Masuk</div>
                    <div class="text-muted ms-3" style="font-size: 0.8rem;">
                        {{ $ticket->created_at->format('d M Y, H:i') }}
                    </div>
                </div>

                {{-- 2. Respon Pertama --}}
                @php $isRespon = $ticket->first_response_at ? true : false; @endphp
                <div class="timeline-item {{ $isRespon ? 'is-active' : '' }} position-relative pb-4">
                    <div class="timeline-dot {{ $isRespon ? 'is-active' : '' }} position-absolute"></div>
                    <div class="{{ $isRespon ? 'fw-bold text-danger' : 'text-muted' }} ms-3">
                        Respon Pertama
                    </div>
                    <div class="text-muted ms-3" style="font-size: 0.8rem;">
                        {{ $isRespon ? $ticket->first_response_at->format('d M Y, H:i') : 'Belum ada respon' }}
                    </div>
                </div>

                {{-- 3. Penanganan Selesai --}}
                @php $isSelesai = in_array($ticket->status, ['Selesai', 'Ditutup']) ? true : false; @endphp
                <div class="timeline-item {{ $isSelesai ? 'is-active' : '' }} position-relative pb-4">
                    <div class="timeline-dot {{ $isSelesai ? 'is-active' : '' }} position-absolute"></div>
                    <div class="{{ $isSelesai ? 'fw-bold text-danger' : 'text-muted' }} ms-3">
                        Penanganan Selesai
                    </div>
                    <div class="text-muted ms-3" style="font-size: 0.8rem;">
                        {{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : 'Belum selesai' }}
                    </div>
                </div>

                {{-- 4. Ditutup --}}
                @php $isTutup = $ticket->status === 'Ditutup' ? true : false; @endphp
                <div class="timeline-item {{ $isTutup ? 'is-active' : '' }} position-relative">
                    <div class="timeline-dot {{ $isTutup ? 'is-active' : '' }} position-absolute"></div>
                    <div class="{{ $isTutup ? 'fw-bold text-danger' : 'text-muted' }} ms-3">
                        Ditutup
                    </div>
                    <div class="text-muted ms-3" style="font-size: 0.8rem;">
                        {{ $isTutup ? 'Dikonfirmasi Mentor' : 'Belum ditutup' }}
                    </div>
                </div>

            </div>
        </div>

        {{-- METRIK TIKET --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Metrik</h5>

            {{-- Umur Tiket --}}
            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                <span class="text-muted">Umur Tiket</span>
                <strong>{{ round($ticket->created_at->diffInHours(now()), 2) }} jam</strong>
            </div>

            {{-- Waktu Respon Pertama --}}
            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                <span class="text-muted">Waktu Respon Pertama</span>
                <strong>
                    @if($ticket->first_response_at)
                        {{ round($ticket->created_at->diffInMinutes($ticket->first_response_at), 2) }} menit
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </strong>
            </div>

            {{-- Waktu Penyelesaian --}}
            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                <span class="text-muted">Waktu Penyelesaian</span>
                <strong>
                    @if($ticket->resolved_at)
                        {{ round($ticket->created_at->diffInHours($ticket->resolved_at), 2) }} jam
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </strong>
            </div>

            {{-- SLA STATUS (SESUAI PRD) --}}
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted">SLA Status</span>
                @php
                    // Kategori kecepatan respons sesuai PRD
                    $slaText = 'Belum Direspon';
                    $slaStatus = 'secondary';
                    $slaKeterangan = '-';

                    if ($ticket->first_response_at) {
                        $minutes = $ticket->created_at->diffInMinutes($ticket->first_response_at);

                        if ($minutes <= 15) {
                            $slaText = 'Sangat Cepat';
                            $slaStatus = 'success';
                            $slaKeterangan = '≤ 15 menit';
                        } elseif ($minutes <= 30) {
                            $slaText = 'Cepat';
                            $slaStatus = 'success';
                            $slaKeterangan = '16 – 30 menit';
                        } elseif ($minutes <= 60) {
                            $slaText = 'Sedang';
                            $slaStatus = 'info';
                            $slaKeterangan = '31 – 60 menit';
                        } elseif ($minutes <= 120) {
                            $slaText = 'Lambat';
                            $slaStatus = 'warning';
                            $slaKeterangan = '61 – 120 menit';
                        } else {
                            $slaText = 'Sangat Lambat';
                            $slaStatus = 'danger';
                            $slaKeterangan = '> 120 menit';
                        }
                    } else {
                        // Belum ada respon pertama
                        $hours = $ticket->created_at->diffInHours(now());
                        if ($hours > 24) {
                            $slaText = 'Belum Direspon';
                            $slaStatus = 'danger';
                            $slaKeterangan = '> 24 jam';
                        } else {
                            $slaText = 'Menunggu Respon';
                            $slaStatus = 'warning';
                            $slaKeterangan = '< 24 jam';
                        }
                    }
                @endphp

                <div class="text-end">
                    <span class="badge badge-soft-{{ $slaStatus }} rounded-pill px-3"
                          title="{{ $slaKeterangan }}">
                        <i class="bi bi-speedometer2"></i> {{ $slaText }}
                    </span>
                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                        {{ $slaKeterangan }}
                    </div>
                </div>
            </div>
        </div>

        {{-- AKSI CEPAT --}}
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Aksi Cepat</h6>
            <a href="{{ route('kepala.tickets.index') }}" class="btn btn-outline-dark w-100 mb-2">
                <i class="bi bi-list"></i> Semua Tiket
            </a>
            @if($ticket->it_support_id)
                <div class="border rounded p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-2"
                         style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Ditangani oleh</small>
                        <strong>{{ $ticket->itSupport->name ?? '-' }}</strong>
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>

<script>
    // Auto-scroll ke atas saat chat dibuka (pesan terbaru)
    document.addEventListener('DOMContentLoaded', function () {
        const chatArea = document.querySelector('.chat-area');
        if (chatArea) {
            chatArea.scrollTop = 0;
        }
    });
</script>

@endsection