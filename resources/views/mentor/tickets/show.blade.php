@extends('template')

@section('judul', 'Detail Tiket')

@section('konten')

{{-- HEADER TIKET --}}
<div class="mb-3">
    <a href="{{ route('mentor.tickets.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Kembali ke Tiket Saya
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

<!-- ⚠️ PENTING: ROW DIMULAI DI SINI -->
<div class="row g-4">

    <!-- ⚠️ KOLOM KIRI DIMULAI -->
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

            {{-- Deskripsi di atas --}}
            <div class="d-flex mb-3">
                <div class="me-2">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px;">
                        <i class="bi bi-file-text"></i>
                    </div>
                </div>
                <div class="p-3 rounded-3 bg-light border-start border-4 border-danger flex-grow-1" style="max-width: 85%;">
                    <small class="d-block mb-1 fw-bold text-danger">
                        <i class="bi bi-pin-angle-fill"></i> Deskripsi Kendala Awal
                    </small>
                    <small class="d-block mb-2 text-muted">
                        {{ $ticket->mentor->name ?? 'Mentor' }} — {{ $ticket->created_at->format('d M Y, H:i') }}
                    </small>
                    <div class="mt-2">{{ $ticket->description }}</div>
                </div>
            </div>

            {{-- Pesan diskusi --}}
            @foreach($ticket->messages as $msg)
                @if($msg->user_id === auth()->id())
                    <div class="d-flex mb-3 justify-content-end">
                        <div class="p-3 rounded-3" style="max-width: 80%; background-color: #f4c7c9;">
                            <small class="d-block text-end mb-1 fw-bold">(Anda) {{ $msg->user->name }}</small>
                            <small class="d-block text-end mb-2 text-muted" style="font-size: 0.7rem;">{{ $msg->created_at->format('d M Y H:i') }}</small>
                            {{ $msg->message }}
                            @if($msg->attachment)
                                <div class="mt-2 pt-2 border-top">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" download class="text-dark small text-decoration-none">
                                        <i class="bi bi-paperclip"></i> {{ basename($msg->attachment) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="ms-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="d-flex mb-3">
                        <div class="me-2">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 bg-light" style="max-width: 80%;">
                            <small class="d-block mb-1 fw-bold">{{ $msg->user->name ?? 'User' }} (IT Support)</small>
                            <small class="d-block mb-2 text-muted" style="font-size: 0.7rem;">{{ $msg->created_at->format('d M Y H:i') }}</small>
                            {{ $msg->message }}
                            @if($msg->attachment)
                                <div class="mt-2 pt-2 border-top">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" download class="text-dark small text-decoration-none">
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
            @endif
        </div>

    </div>
    <!-- ⚠️ PENTING: KOLOM KIRI DITUTUP DI SINI -->

    <!-- ⚠️ KOLOM KANAN DIMULAI -->
    <div class="col-md-5">

        {{-- TIMELINE SLA --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4">Timeline SLA</h5>
            <div class="position-relative ps-4 border-start border-2" style="border-color: #d81b21 !important; margin-left: 10px;">
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle" style="width: 15px; height: 15px; left: -31px; top: 5px; background-color: #d81b21;"></div>
                    <div class="fw-bold text-danger">Tiket Masuk</div>
                    <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                </div>
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle" style="width: 15px; height: 15px; left: -31px; top: 5px; background-color: {{ $ticket->first_response_at ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->first_response_at ? 'fw-bold text-danger' : 'text-muted' }}">Respon Pertama</div>
                    @if($ticket->first_response_at)
                        <small class="text-muted">{{ $ticket->first_response_at->format('d M Y, H:i') }}</small>
                    @else
                        <small class="text-muted">Menunggu IT Support</small>
                    @endif
                </div>
                <div class="mb-4 position-relative">
                    <div class="position-absolute rounded-circle" style="width: 15px; height: 15px; left: -31px; top: 5px; background-color: {{ in_array($ticket->status, ['Selesai', 'Ditutup']) ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ in_array($ticket->status, ['Selesai', 'Ditutup']) ? 'fw-bold text-danger' : 'text-muted' }}">Penanganan Selesai</div>
                    @if($ticket->resolved_at)
                        <small class="text-muted">{{ $ticket->resolved_at->format('d M Y, H:i') }}</small>
                    @else
                        <small class="text-muted">Belum selesai</small>
                    @endif
                </div>
                <div class="position-relative">
                    <div class="position-absolute rounded-circle" style="width: 15px; height: 15px; left: -31px; top: 5px; background-color: {{ $ticket->status === 'Ditutup' ? '#d81b21' : '#dee2e6' }};"></div>
                    <div class="{{ $ticket->status === 'Ditutup' ? 'fw-bold text-danger' : 'text-muted' }}">Ditutup</div>
                    @if($ticket->status === 'Ditutup')
                        <small class="text-muted">Dikonfirmasi Mentor</small>
                    @else
                        <small class="text-muted">Belum ditutup</small>
                    @endif
                </div>
            </div>
        </div>

        {{-- KONFIRMASI PENYELESAIAN --}}
        @if($ticket->status === 'Selesai')
            <div class="card card-custom p-4 border-start border-4 border-success">
                <div class="text-center">
                    <i class="bi bi-patch-question text-success fs-1"></i>
                    <h5 class="fw-bold mt-2">Konfirmasi Penyelesaian</h5>
                    <p class="text-muted small mb-3">
                        IT Support sudah menandai tiket ini <strong>selesai</strong>.
                        Apakah kendala Anda sudah teratasi?
                    </p>

                    <form action="{{ route('mentor.tickets.confirm', $ticket->id) }}" method="POST"
                          onsubmit="return confirm('Yakin kendala sudah teratasi? Tiket akan ditutup.')">
                        @csrf
                        <button type="submit" class="btn btn-inamikro w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Ya, Kendala Sudah Teratasi
                        </button>
                    </form>

                    <button type="button" class="btn btn-outline-warning w-100"
                            data-bs-toggle="collapse" data-bs-target="#formBelumSelesai">
                        <i class="bi bi-exclamation-circle"></i> Belum, Masih Ada Kendala
                    </button>

                    <div class="collapse mt-3" id="formBelumSelesai">
                        <div class="alert alert-warning text-start small">
                            <strong>Kendala belum teratasi?</strong><br>
                            Kirim pesan ke IT Support untuk melanjutkan penanganan.
                        </div>
                        <form action="{{ route('mentor.tickets.message', $ticket->id) }}" method="POST">
                            @csrf
                            <textarea name="message" class="form-control form-control-sm mb-2"
                                      rows="3" placeholder="Jelaskan kendala yang masih terjadi..." required></textarea>
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="bi bi-send"></i> Kirim Pesan ke IT Support
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- INFO STATUS --}}
        @if($ticket->status === 'Ditutup')
            <div class="card card-custom p-4 border-start border-4 border-secondary">
                <div class="text-center">
                    <i class="bi bi-check-circle-fill text-secondary fs-1"></i>
                    <h6 class="fw-bold mt-2 mb-1">Tiket Sudah Ditutup</h6>
                    <small class="text-muted">Anda sudah mengonfirmasi kendala selesai.</small>
                </div>
            </div>
        @elseif(in_array($ticket->status, ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor']))
            <div class="card card-custom p-4 border-start border-4 border-info">
                <div class="text-center">
                    <i class="bi bi-hourglass-split text-info fs-1"></i>
                    <h6 class="fw-bold mt-2 mb-1">
                        @if($ticket->status === 'Baru') Menunggu IT Support Mengambil Tiket
                        @elseif($ticket->status === 'Ditangani IT') IT Support Sedang Menangani
                        @elseif($ticket->status === 'Dibalas IT') Menunggu Balasan Anda
                        @elseif($ticket->status === 'Dibalas Mentor') Menunggu IT Support Menindaklanjuti
                        @endif
                    </h6>
                    <small class="text-muted">
                        Tombol konfirmasi akan muncul setelah IT Support menandai tiket selesai.
                    </small>
                </div>
            </div>
        @endif

    </div>
    <!-- ⚠️ PENTING: KOLOM KANAN DITUTUP DI SINI -->

</div>
<!-- ⚠️ PENTING: ROW DITUTUP DI SINI -->

<script>
    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        document.getElementById('fileName').textContent = e.target.files[0]?.name ?? '';
    });
</script>

@endsection