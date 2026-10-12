@extends('template')

@section('judul', 'Laporkan Kendala Aplikasi')

@section('konten')
<div class="card card-custom p-4">
    <h3 class="fw-bold">Laporkan Kendala Aplikasi</h3>
    <p class="text-muted">Sampaikan kendala aplikasi yang Anda alami agar dapat ditangani segera oleh IT Support</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mentor.tickets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">

                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Kendala <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="Contoh: Aplikasi kasir POS gagal cetak struk bluetooth">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kategori Kendala <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select">
                            <option value="">Pilih kategori yang paling sesuai...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Prioritas <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="priority" value="Rendah">
                                <label class="form-check-label">Rendah</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="priority" value="Sedang" checked>
                                <label class="form-check-label">Sedang</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="priority" value="Tinggi">
                                <label class="form-check-label text-danger fw-bold">Tinggi</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Merchant/Mitra Binaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Pilih nama mitra merchant di Cluster Jabar-03...">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi Kendala <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan kendala yang terjadi secara detail">{{ old('description') }}</textarea>
                </div>

                <div class="mb-4">
                <label class="form-label fw-bold">Lampiran (optional)</label>
                <div class="border border-2 border-dashed rounded p-4 text-center bg-light">
                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                    <p class="mb-0 text-muted">Klik untuk memilih file</p>
                    <small class="text-muted">Format: JPG, PNG, PDF, LOG, TXT (Maks. 10MB)</small>
                    <input type="file" name="attachment" class="form-control mt-3">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('mentor.tickets.index') }}" class="btn btn-outline-dark px-4">Batal</a>
                <button type="submit" class="btn btn-inamikro px-4">Kirim Tiket</button>
            </div>
    </form>
</div>
@endsection