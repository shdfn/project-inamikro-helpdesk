@extends('template')

@section('judul', 'Edit User')

@section('konten')

<div class="card card-custom p-4">
    <h3 class="fw-bold mb-1">Edit User</h3>
    <p class="text-muted">Perbarui data user: <strong>{{ $user->name }}</strong></p>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('kepala.users.update', $user->id) }}" class="mt-3">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="mentor" {{ $user->role == 'mentor' ? 'selected' : '' }}>Mentor</option>
                    <option value="it_support" {{ $user->role == 'it_support' ? 'selected' : '' }}>IT Support</option>
                    <option value="kepala_it" {{ $user->role == 'kepala_it' ? 'selected' : '' }}>Kepala IT</option>
                </select>
            </div>

            <div class="col-12 mb-3">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i> Kosongkan field password kalau tidak ingin mengganti.
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Password Baru (opsional)</label>
                <input type="password" name="password" class="form-control" minlength="6">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="6">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('kepala.users.index') }}" class="btn btn-outline-dark">Batal</a>
            <button type="submit" class="btn btn-inamikro px-4">Perbarui</button>
        </div>
    </form>
</div>

@endsection