@extends('template')

@section('judul', 'Detail Tiket')

@section('konten')

{{-- ═══════════════════════════════════════════ --}}
{{-- HEADER TIKET --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="mb-3">
    <a href="{{ route('it.tickets.my') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Kembali ke Tiket Ditangani
    </a>
    <h2 class="fw-bold mt-2">#{{ $ticket->ticket_number }}</h2>
    <h5 class="text-muted">{{ $ticket->title }}</h5>
    <div class="mt-2">
        <span class="badge {{ $ticket->status_badge_class }} px-3 py-2 rounded-pill">
            <i class="bi bi-{{ $ticket->status_icon }}"></i> {{ $ticket->status_label }}
        </span>
        <span class="badge badge-soft-danger px-3 py-2 rounded-pill">{{ $ticket->priority }}</span>
    </div>
</div>

<div class="row g-4">

    {{-- ═══════════════════════════════════════════ --}}
    {{-- KOLOM KIRI --}}
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

        {{-- DISKUSI (DENGAN AREA SCROLL) --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-chat-left-text"></i> Diskusi</h5>

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
                    @if($msg->user_id === auth()->id())
                        {{-- Pesan dari diri sendiri (IT Support) — kanan --}}
                        <div class="d-flex mb-3 justify-content-end">
                            <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                                <small class="d-block text-end mb-1 fw-bold">
                                    <i class="bi bi-person-badge"></i> (Anda) {{ $msg->user->name }}
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
                                    <i class="bi bi-person"></i> {{ $msg->user->name ?? 'User' }} (Mentor)
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
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-chat-dots fs-2"></i>
                        <p class="mb-0 mt-2 small">Belum ada diskusi. Mulai balas pesan dari Mentor.</p>
                    </div>
                @endforelse

            </div> {{-- END AREA CHAT --}}

            {{-- FORM KIRIM PESAN (STICKY BOTTOM) --}}
            @if($ticket->it_support_id === auth()->id() && $ticket->status !== 'Ditutup')
                <div class="mt-3 pt-3 border-top">
                    <form action="{{ route('it.tickets.message', $ticket->id) }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <label for="fileInput" class="input-group-text bg-white border-end-0" style="cursor: pointer;">
                                <i class="bi bi-paperclip"></i>
                            </label>
                            <input type="file" id="fileInput" name="attachment" class="d-none">
                            <input type="text" name="message" class="form-control border-start-0"
                                   placeholder="Tulis pesan..." required>
                            <button class="btn btn-inamikro" type="submit">
                                <i class="bi bi-send"></i> Kirim
                            </button>
                        </div>
                        <small id="fileName" class="text-muted d-block mt-1"></small>
                    </form>
                </div>

            {{-- Info kalau ditangani IT lain --}}
            @elseif($ticket->it_support_id && $ticket->it_support_id !== auth()->id())
                <div class="mt-3 pt-3 border-top">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle"></i>
                        Tiket ini sedang ditangani oleh <strong>{{ $ticket->itSupport->name ?? 'IT Support lain' }}</strong>.
                        <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST" class="mt-2"
                              onsubmit="return confirm('Ambil alih tiket ini dari {{ $ticket->itSupport->name ?? 'IT lain' }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="bi bi-arrow-left-right"></i> Ambil Alih
                            </button>
                        </form>
                    </div>
                </div>

            {{-- Tombol Tangani kalau tiket masih Baru --}}
            @elseif($ticket->status === 'Baru')
                <div class="mt-3 pt-3 border-top">
                    <div class="alert alert-info mb-0">
                        <p class="mb-2 small">Tiket ini belum ditangani siapa pun. Klik tombol untuk mulai menangani.</p>
                        <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-inamikro">
                                <i class="bi bi-hand-index"></i> Tangani Tiket Ini
                            </button>
                        </form>
                    </div>
                </div>

            {{-- Kalau status Ditutup --}}
            @elseif($ticket->status === 'Ditutup')
                <div class="mt-3 pt-3 border-top">
                    <div class="alert alert-secondary mb-0 text-center">
                        <i class="bi bi-lock-fill"></i>
                        <small>Tiket sudah ditutup. Diskusi tidak dapat dilanjutkan.</small>
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- KOLOM KANAN --}}
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

        {{-- KONFIRMASI PENYELESAIAN --}}
        @if($ticket->it_support_id === auth()->id() && in_array($ticket->status, ['Ditangani IT', 'Dibalas IT', 'Dibalas Mentor']))
            <div class="card card-custom p-4 text-center mb-4">
                <h5 class="fw-bold">Konfirmasi Penyelesaian</h5>
                <p class="text-muted small mb-3">
                    Jika kendala sudah teratasi, klik tombol di bawah ini untuk mengirim konfirmasi ke Mentor.
                </p>
                <form action="{{ route('it.tickets.resolve', $ticket->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menandai tiket ini selesai?')">
                    @csrf
                    <button type="submit" class="btn btn-inamikro w-100">
                        <i class="bi bi-check-circle"></i> Konfirmasi Selesai
                    </button>
                </form>
            </div>
        @endif

        {{-- INFO TIKET SELESAI --}}
        @if($ticket->status === 'Selesai')
            <div class="card card-custom p-4 text-center border-start border-4 border-success">
                <i class="bi bi-hourglass-split text-success fs-1"></i>
                <h6 class="fw-bold mt-2 mb-1">Menunggu Konfirmasi Mentor</h6>
                <small class="text-muted">
                    Anda sudah menandai tiket ini selesai. Menunggu Mentor mengonfirmasi.
                </small>
            </div>
        @elseif($ticket->status === 'Ditutup')
            <div class="card card-custom p-4 text-center border-start border-4 border-secondary">
                <i class="bi bi-check-circle-fill text-secondary fs-1"></i>
                <h6 class="fw-bold mt-2 mb-1">Tiket Ditutup</h6>
                <small class="text-muted">
                    Tiket ini sudah selesai dan dikonfirmasi oleh Mentor.
                </small>
            </div>
        @endif

    </div>

</div>

<script>
    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        document.getElementById('fileName').textContent = e.target.files[0]?.name ?? '';
    });

    // Auto-scroll ke atas saat chat dibuka (pesan terbaru)
    document.addEventListener('DOMContentLoaded', function () {
        const chatArea = document.querySelector('.chat-area');
        if (chatArea) {
            chatArea.scrollTop = 0;
        }
    });
</script>

@endsection