@extends('template')

@section('judul', 'Edit Profil')

@section('konten')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <h4 class="fw-bold mb-1">Edit Profil</h4>
            <p class="text-muted small mb-4">Perbarui informasi data diri Anda di sini.</p>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nama Lengkap --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" 
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small">Username</label>
                    <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" 
                           class="form-control @error('username') is-invalid @enderror" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                {{-- Ubah Kata Sandi --}}
                <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i> Ubah Kata Sandi (Opsional)</h6>
                <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengganti kata sandi.</p>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Kata Sandi Baru</label>
                    <input type="password" name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" 
                           class="form-control" placeholder="Ulangi sandi baru">
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-inamikro">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection