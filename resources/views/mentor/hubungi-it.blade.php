@extends('template')

@section('judul', 'Hubungi IT Support')

@section('konten')

<div class="card card-custom p-4">
    <h4 class="fw-bold mb-2">Hubungi IT Support</h4>
    <p class="text-muted">Kontak IT Support yang siap membantu Anda</p>

    @forelse($itSupports as $it)
        <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3"
                     style="width: 50px; height: 50px;">
                    <i class="bi bi-person-fill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold">{{ $it->name }}</div>
                    <small class="text-muted">IT Support</small>
                </div>
            </div>
            <div>
                <a href="mailto:{{ $it->email }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-envelope"></i> Email
                </a>
            </div>
        </div>
    @empty
        <p class="text-muted">Belum ada IT Support terdaftar.</p>
    @endforelse

    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i>
        <small>Untuk kendala spesifik, silakan buat tiket baru agar dapat dilacak oleh sistem.</small>
    </div>

    <a href="{{ route('mentor.dashboard') }}" class="btn btn-outline-secondary mt-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

@endsection