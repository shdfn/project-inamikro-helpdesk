@extends('template')

@section('judul', 'Detail Tiket')

@section('konten')

{{-- HEADER TIKET --}}
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
    {{-- KIRI: Info + Diskusi --}}
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

        {{-- DISKUSI --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text"></i> Diskusi</h5>

            {{-- ⭐ DESKRIPSI KENDALA — SELALU PALING ATAS --}}
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
                        <h5 class="fw-bold mt-4 mb-3">Lampiran</h5>
                        <div class="border rounded p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-image fs-3 me-2 text-secondary"></i>
                                <span>{{ basename($ticket->attachment) }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $ticket->attachment) }}" download class="text-decoration-none">
                                <i class="bi bi-download"></i>
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

            {{-- ⭐ PESAN DISKUSI — TERBARU DI ATAS --}}
            @forelse($ticket->messages as $msg)
                @if($msg->user_id === auth()->id())
                    {{-- Pesan dari diri sendiri (kanan, IT Support ini) --}}
                    <div class="d-flex mb-3 justify-content-end">
                        <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                            <small class="d-block text-end mb-1 fw-bold">
                                <i class="bi bi-person-badge"></i>
                                (Anda) {{ $msg->user->name }}
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
                    <p class="mb-0 mt-2 small">Belum ada balasan. Mulai diskusi dengan mengirim pesan.</p>
                </div>
            @endforelse

            {{-- FORM KIRIM PESAN (hanya jika ditangani oleh IT ini & bukan Ditutup) --}}
            @if($ticket->it_support_id === auth()->id() && $ticket->status !== 'Ditutup')
                <form action="{{ route('it.tickets.message', $ticket->id) }}" method="POST"
                      enctype="multipart/form-data" class="mt-4">
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
                    <small id="fileName" class="text-muted"></small>
                </form>

            {{-- Info kalau ditangani IT lain --}}
            @elseif($ticket->it_support_id && $ticket->it_support_id !== auth()->id())
                <div class="alert alert-warning mt-4 mb-0">
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

            {{-- Tombol Tangani kalau tiket masih Baru --}}
            @elseif($ticket->status === 'Baru')
                <div class="alert alert-info mt-4 mb-0">
                    <p class="mb-2 small">Tiket ini belum ditangani siapa pun. Klik tombol untuk mulai menangani.</p>
                    <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-inamikro">
                            <i class="bi bi-hand-index"></i> Tangani Tiket Ini
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- KANAN: Timeline SLA + Konfirmasi --}}
    <div class="col-md-5">

        {{-- TIMELINE SLA --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4">Timeline SLA</h5>
            <div class="position-relative ps-4 border-start border-2" style="border-color: #d81b21 !important; margin-left: 10px;">

                {{-- Tiket Masuk --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px; background-color: #d81b21;"></div>
                    <div class="fw-bold text-danger">Tiket Masuk</div>
                    <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                </div>

                {{-- Respon Pertama --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->first_response_at ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->first_response_at ? 'fw-bold text-danger' : 'text-muted' }}">Respon Pertama</div>
                    @if($ticket->first_response_at)
                        <small class="text-muted">{{ $ticket->first_response_at->format('d M Y, H:i') }}</small>
                    @else
                        <small class="text-muted">Belum ada respon</small>
                    @endif
                </div>

                {{-- Penanganan Selesai --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ in_array($ticket->status, ['Selesai', 'Ditutup']) ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ in_array($ticket->status, ['Selesai', 'Ditutup']) ? 'fw-bold text-danger' : 'text-muted' }}">
                        Penanganan Selesai
                    </div>
                    @if($ticket->resolved_at)
                        <small class="text-muted">{{ $ticket->resolved_at->format('d M Y, H:i') }}</small>
                    @endif
                </div>

                {{-- Ditutup --}}
                <div class="position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->status === 'Ditutup' ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->status === 'Ditutup' ? 'fw-bold text-danger' : 'text-muted' }}">Ditutup</div>
                    @if($ticket->status === 'Ditutup' && $ticket->resolved_at)
                        <small class="text-muted">{{ $ticket->resolved_at->format('d M Y, H:i') }}</small>
                    @endif
                </div>
            </div>
        </div>

        {{-- KONFIRMASI PENYELESAIAN --}}
        @if($ticket->it_support_id === auth()->id() && in_array($ticket->status, ['Ditangani IT', 'Dibalas IT', 'Dibalas Mentor']))
            <div class="card card-custom p-4 text-center">
                <h5 class="fw-bold">Konfirmasi Penyelesaian</h5>
                <p class="text-muted small">
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
                <small class="text-muted">Anda sudah menandai tiket ini selesai. Menunggu Mentor mengonfirmasi.</small>
            </div>
        @elseif($ticket->status === 'Ditutup')
            <div class="card card-custom p-4 text-center border-start border-4 border-secondary">
                <i class="bi bi-check-circle-fill text-secondary fs-1"></i>
                <h6 class="fw-bold mt-2 mb-1">Tiket Ditutup</h6>
                <small class="text-muted">Tiket ini sudah selesai dan dikonfirmasi oleh Mentor.</small>
            </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        document.getElementById('fileName').textContent = e.target.files[0]?.name ?? '';
    });
</script>

@endsection