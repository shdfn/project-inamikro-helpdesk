@extends('template')

@section('judul', 'Manajemen User')

@section('konten')

{{-- ═══════════════════════════════════════════ --}}
{{-- TABEL PENGAJUAN PERUBAHAN PROFIL --}}
{{-- ═══════════════════════════════════════════ --}}
@if(isset($profileRequests) && $profileRequests->count() > 0)
<div class="card card-custom p-4 mb-4 border-start border-4 border-warning">
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-circle-fill text-warning fs-4 me-2"></i>
        <h5 class="fw-bold mb-0">Pengajuan Perubahan Profil</h5>
        <span class="badge bg-warning text-dark ms-2">{{ $profileRequests->count() }} Menunggu</span>
    </div>
    <p class="text-muted small mb-3">Pengguna berikut mengajukan perubahan data profil. Silakan tinjau dan setujui.</p>
    
    <div class="table-responsive">
        <table class="table table-bordered align-middle bg-white">
            <thead class="bg-light">
                <tr>
                    <th style="width: 20%;">Pengguna</th>
                    <th style="width: 30%;">Data Lama</th>
                    <th style="width: 30%;">Data Baru (Pengajuan)</th>
                    <th style="width: 20%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($profileRequests as $req)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $req->user->name ?? 'User Terhapus' }}</div>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $req->user->role ?? '-')) }}</small>
                        </td>
                        <td class="text-muted small">
                            <div>Nama: {{ $req->user->name ?? '-' }}</div>
                            <div>Username: {{ $req->user->username ?? '-' }}</div>
                            <div>Email: {{ $req->user->email ?? '-' }}</div>
                        </td>
                        <td class="small fw-bold text-success">
                            <div>Nama: {{ $req->new_name }}</div>
                            <div>Username: {{ $req->new_username }}</div>
                            <div>Email: {{ $req->new_email }}</div>
                            
                            @if($req->new_password)
                                <div class="text-danger mt-1">
                                    <i class="bi bi-lock-fill"></i> Password Juga Diubah
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('kepala.users.approve', $req->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Setujui perubahan data ini?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success mb-1 w-100">
                                    <i class="bi bi-check-circle"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('kepala.users.reject', $req->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Tolak pengajuan ini?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════ --}}
{{-- TABEL MANAJEMEN USER UTAMA --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Manajemen User</h4>
            <p class="text-muted mb-0">Kelola akun Mentor, IT Support, dan Kepala IT</p>
        </div>
        <a href="{{ route('kepala.users.create') }}" class="btn btn-inamikro">
            <i class="bi bi-plus-circle"></i> Tambah User
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control bg-light border-0"
                       placeholder="Cari nama, email, username...">
            </div>
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select">
                <option value="">Semua Role</option>
                <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>Mentor</option>
                <option value="it_support" {{ request('role') == 'it_support' ? 'selected' : '' }}>IT Support</option>
                <option value="kepala_it" {{ request('role') == 'kepala_it' ? 'selected' : '' }}>Kepala IT</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-inamikro w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('kepala.users.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    {{-- Tabel User --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->username ?? '-' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleClass = match($user->role) {
                                    'mentor' => 'badge-soft-primary',
                                    'it_support' => 'badge-soft-warning',
                                    'kepala_it' => 'badge-soft-danger',
                                    default => 'badge-soft-secondary',
                                };
                            @endphp
                            <span class="badge {{ $roleClass }} rounded-pill px-3">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('kepala.users.edit', $user->id) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('kepala.users.destroy', $user->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection