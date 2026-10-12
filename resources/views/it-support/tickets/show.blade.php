@extends('template')

@section('judul', 'Detail Tiket')

@section('konten')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="mb-3">
    <a href="{{ url()->previous() }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> kembali ke tiket
    </a>
    <h2 class="fw-bold mt-2">#{{ $ticket->ticket_number }}</h2>
    <h5 class="text-muted">{{ $ticket->title }}</h5>
    <div class="mt-2">
        <span class="badge badge-soft-warning px-3 py-2 rounded-pill">{{ $ticket->status }}</span>
        <span class="badge badge-soft-danger px-3 py-2 rounded-pill">{{ $ticket->priority }}</span>
    </div>
</div>

<div class="row g-4">
    {{-- KIRI: Info + Diskusi --}}
    <div class="col-md-7">

        {{-- INFORMASI TIKET --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Informasi Tiket</h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><td class="text-muted" width="40%">Pelapor</td><td class="fw-bold">{{ $ticket->mentor->name ?? '-' }} (Mentor)</td></tr>
                        <tr><td class="text-muted">Kategori</td><td>{{ $ticket->category->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Prioritas</td><td class="text-danger fw-bold">{{ $ticket->priority }}</td></tr>
                        <tr><td class="text-muted">Waktu Masuk</td><td>{{ $ticket->created_at->format('d M Y, H:i') }}</td></tr>
                        <tr><td class="text-muted">Respon Pertama</td><td>{{ $ticket->first_response_at ? $ticket->first_response_at->format('d M Y, H:i') : '-' }}</td></tr>
                        <tr><td class="text-muted">IT Support</td><td class="fw-bold">{{ $ticket->itSupport->name ?? 'Belum ditugaskan' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><td class="text-muted" width="40%">Status</td><td class="text-warning fw-bold">{{ $ticket->status }}</td></tr>
                        <tr><td class="text-muted">Judul Kendala</td><td>{{ $ticket->title }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- DESKRIPSI KENDALA --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Deskripsi Kendala</h5>
            <div class="border rounded p-3 bg-light">
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

        {{-- DISKUSI --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text"></i> Diskusi</h5>

            {{-- Pesan Awal (Deskripsi) --}}
            <div class="d-flex mb-3">
                <div class="me-2"><i class="bi bi-person-circle fs-2"></i></div>
                <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                    <small class="d-block mb-1 fw-bold">
                        {{ $ticket->mentor->name ?? 'Mentor' }} (Mentor) - {{ $ticket->created_at->format('d M Y H:i') }}
                    </small>
                    {{ $ticket->description }}
                </div>
            </div>

            {{-- Pesan-pesan selanjutnya --}}
            @foreach($ticket->messages as $msg)
                @if($msg->user_id === auth()->id())
                    {{-- Pesan dari IT Support (kanan) --}}
                    <div class="d-flex mb-3 justify-content-end">
                        <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                            <small class="d-block text-end mb-1 fw-bold">
                                (IT Support) {{ $msg->user->name }} - {{ $msg->created_at->format('d M Y H:i') }}
                            </small>
                            {{ $msg->message }}
                            @if($msg->attachment)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" download class="text-dark small">
                                        <i class="bi bi-paperclip"></i> {{ basename($msg->attachment) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="ms-2"><i class="bi bi-person-circle fs-2"></i></div>
                    </div>
                @else
                    {{-- Pesan dari Mentor (kiri) --}}
                    <div class="d-flex mb-3">
                        <div class="me-2"><i class="bi bi-person-circle fs-2"></i></div>
                        <div class="p-3 rounded-3 bg-light" style="max-width: 80%;">
                            <small class="d-block mb-1 fw-bold">
                                {{ $msg->user->name }} (Mentor) - {{ $msg->created_at->format('d M Y H:i') }}
                            </small>
                            {{ $msg->message }}
                            @if($msg->attachment)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" download class="text-dark small">
                                        <i class="bi bi-paperclip"></i> {{ basename($msg->attachment) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- FORM KIRIM PESAN (hanya jika ditangani oleh IT ini) --}}
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
            @elseif($ticket->it_support_id !== auth()->id() && $ticket->status === 'Baru')
                <div class="alert alert-info mt-4">
                    <form action="{{ route('it.tickets.assign', $ticket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-inamikro">Tangani Tiket Ini</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- KANAN: Timeline SLA + Konfirmasi --}}
    <div class="col-md-5">

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4">Timeline SLA</h5>
            <div class="position-relative ps-4 border-start border-2" style="border-color: #d81b21 !important; margin-left: 10px;">
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px; background-color: #d81b21;"></div>
                    <div class="fw-bold text-danger">Tiket Masuk</div>
                    <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                </div>
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->first_response_at ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->first_response_at ? 'fw-bold text-danger' : 'text-muted' }}">Respon Pertama</div>
                    @if($ticket->first_response_at)
                        <small class="text-muted">{{ $ticket->first_response_at->format('d M Y, H:i') }}</small>
                    @endif
                </div>
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->status === 'Selesai' || $ticket->status === 'Ditutup' ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->status === 'Selesai' || $ticket->status === 'Ditutup' ? 'fw-bold text-danger' : 'text-muted' }}">Penanganan</div>
                </div>
                <div class="position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ in_array($ticket->status, ['Selesai', 'Ditutup']) ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ in_array($ticket->status, ['Selesai', 'Ditutup']) ? 'fw-bold text-danger' : 'text-muted' }}">Selesai</div>
                </div>
            </div>
        </div>

        {{-- KONFIRMASI PENYELESAIAN --}}
        @if($ticket->it_support_id === auth()->id() && $ticket->status === 'Diproses')
            <div class="card card-custom p-4 text-center">
                <h5 class="fw-bold">Konfirmasi Penyelesaian</h5>
                <p class="text-muted small">Jika kendala sudah teratasi, klik tombol di bawah ini untuk mengirim konfirmasi ke Mentor.</p>
                <form action="{{ route('it.tickets.resolve', $ticket->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-inamikro w-100">Konfirmasi Selesai</button>
                </form>
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