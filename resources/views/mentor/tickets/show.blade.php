@extends('template')

@section('judul', 'Detail Tiket')

@section('konten')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="mb-3">
    <a href="{{ route('mentor.tickets.index') }}" class="text-decoration-none text-muted">
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
    <div class="col-md-7">
        {{-- INFORMASI TIKET --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Informasi Tiket</h5>
            <table class="table table-borderless">
                <tr><td class="text-muted" width="35%">Pelapor</td><td class="fw-bold">{{ $ticket->mentor->name }} (Mentor)</td></tr>
                <tr><td class="text-muted">Kategori</td><td>{{ $ticket->category->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Dibuat</td><td>{{ $ticket->created_at->format('d M Y, H:i') }}</td></tr>
                <tr><td class="text-muted">IT Support</td><td class="fw-bold">{{ $ticket->itSupport->name ?? 'Belum ditugaskan' }}</td></tr>
                <tr><td class="text-muted">Status</td><td>{{ $ticket->status }}</td></tr>
            </table>

            @if($ticket->attachment)
                <div class="mt-3">
                    <label class="fw-bold small">Lampiran:</label>
                    <div class="border rounded p-2 mt-1 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark"></i> {{ basename($ticket->attachment) }}</span>
                        <a href="{{ asset('storage/' . $ticket->attachment) }}" download class="text-decoration-none">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- DISKUSI --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text"></i> Diskusi</h5>

            {{-- Pesan Awal (Deskripsi Tiket) --}}
            <div class="d-flex mb-3 justify-content-end">
                <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                    <small class="d-block text-end mb-1 fw-bold">
                        (Mentor) {{ $ticket->mentor->name }} - {{ $ticket->created_at->format('d M Y H:i') }}
                    </small>
                    {{ $ticket->description }}
                </div>
                <div class="ms-2"><i class="bi bi-person-circle fs-2"></i></div>
            </div>

            {{-- Pesan-pesan dari ticket_messages --}}
            @foreach($ticket->messages as $msg)
                @if($msg->user_id === auth()->id())
                    {{-- Pesan dari Mentor (kanan) --}}
                    <div class="d-flex mb-3 justify-content-end">
                        <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                            <small class="d-block text-end mb-1 fw-bold">
                                (Mentor) {{ $msg->user->name }} - {{ $msg->created_at->format('d M Y H:i') }}
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
                    {{-- Pesan dari IT Support (kiri) --}}
                    <div class="d-flex mb-3">
                        <div class="me-2"><i class="bi bi-person-circle fs-2"></i></div>
                        <div class="p-3 rounded-3 bg-light" style="max-width: 80%;">
                            <small class="d-block mb-1 fw-bold">
                                {{ $msg->user->name }} (IT Support) - {{ $msg->created_at->format('d M Y H:i') }}
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

            {{-- FORM KIRIM PESAN --}}
            @if($ticket->status !== 'Ditutup')
            <form action="{{ route('mentor.tickets.message', $ticket->id) }}" method="POST"
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
            @else
                <div class="alert alert-info mt-4">Tiket sudah ditutup. Diskusi tidak dapat dilanjutkan.</div>
            @endif
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="col-md-5">
        {{-- TIMELINE SLA - Perbaikan #8: warna bullet seragam --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4">Timeline SLA</h5>
            <div class="position-relative ps-4 border-start border-2" style="border-color: #0d6efd !important; margin-left: 10px;">

                {{-- Step 1: Tiket Dibuat --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute bg-primary rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;"></div>
                    <div class="fw-bold text-primary">Tiket Dibuat</div>
                    <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                </div>

                {{-- Step 2: Respon Pertama --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->first_response_at ? '#0d6efd' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->first_response_at ? 'fw-bold text-primary' : 'text-muted' }}">Respon Pertama</div>
                    @if($ticket->first_response_at)
                        <small class="text-muted">{{ $ticket->first_response_at->format('d M Y, H:i') }}</small>
                    @endif
                </div>

                {{-- Step 3: Penanganan --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->it_support_id ? '#0d6efd' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->it_support_id ? 'fw-bold text-primary' : 'text-muted' }}">Penanganan</div>
                </div>

                {{-- Step 4: Solusi Diberikan --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->status == 'Selesai' || $ticket->status == 'Ditutup' ? '#0d6efd' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->status == 'Selesai' || $ticket->status == 'Ditutup' ? 'fw-bold text-primary' : 'text-muted' }}">Solusi Diberikan</div>
                </div>

                {{-- Step 5: Konfirmasi Mentor --}}
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->status == 'Ditutup' ? '#0d6efd' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->status == 'Ditutup' ? 'fw-bold text-primary' : 'text-muted' }}">Konfirmasi Mentor</div>
                </div>

                {{-- Step 6: Ditutup --}}
                <div class="position-relative">
                    <div class="position-absolute rounded-circle"
                         style="width: 15px; height: 15px; left: -31px; top: 5px;
                                background-color: {{ $ticket->status == 'Ditutup' ? '#0d6efd' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->status == 'Ditutup' ? 'fw-bold text-primary' : 'text-muted' }}">Ditutup</div>
                </div>

            </div>
        </div>

        {{-- KONFIRMASI --}}
        @if($ticket->status == 'Selesai')
        <div class="card card-custom p-4 text-center">
            <h5 class="fw-bold">Konfirmasi Penyelesaian</h5>
            <p class="text-muted small">Jika kendala sudah teratasi, klik tombol di bawah ini untuk mengonfirmasi penyelesaian tiket.</p>
            <form action="{{ route('mentor.tickets.confirm', $ticket->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-inamikro w-100">Konfirmasi Selesai</button>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
    // Preview nama file saat dipilih
    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        document.getElementById('fileName').textContent = e.target.files[0]?.name ?? '';
    });
</script>
@endsection