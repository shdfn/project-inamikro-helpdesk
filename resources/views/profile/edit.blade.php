@extends('template')

@section('judul', 'Edit Profil')

@section('konten')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-custom p-4">
            <h4 class="fw-bold mb-1">Edit Profil</h4>
            <p class="text-muted small mb-4">Perubahan data Anda akan dikirim ke Kepala IT untuk disetujui.</p>

            {{-- Notifikasi --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                
                {{-- DATA DIRI --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', auth()->user()->username) }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                </div>

                {{-- BAGIAN UBAH PASSWORD --}}
                <div class="border-top pt-3 mt-3 mb-3">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-shield-lock"></i> Ubah Kata Sandi (Opsional)</h6>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kata Sandi Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti sandi">
                        <small class="text-muted" style="font-size: 0.75rem;">Minimal 6 karakter. Sandi baru akan aktif setelah disetujui Kepala IT.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi baru">
                    </div>
                </div>
                {{-- END BAGIAN UBAH PASSWORD --}}

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-inamikro">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection