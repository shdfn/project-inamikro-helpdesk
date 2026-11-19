@extends('template')

@section('judul', 'Permintaan Reset Password')

@section('konten')

{{-- Popup Kode Acak --}}
@if(session('reset_code'))
    <div class="modal fade show" style="display:block; background:rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Password Berhasil Direset</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Berikan kode ini ke <strong>{{ session('reset_user') }}</strong>:</p>
                    <h1 class="fw-bold text-danger">{{ session('reset_code') }}</h1>
                    <small class="text-muted">Minta pengguna untuk login menggunakan kode ini dan segera ganti password.</small>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('it.password-reset.index') }}" class="btn btn-inamikro">Tutup</a>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Daftar Permintaan --}}
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-3">Permintaan Reset Password</h4>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Tanggal Permintaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->user->name }}</td>
                        <td>{{ $req->user->email }}</td>
                        <td>{{ $req->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <form action="{{ route('it.password-reset.process', $req->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-inamikro">
                                    <i class="bi bi-key"></i> Generate Kode
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Tidak ada permintaan reset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection