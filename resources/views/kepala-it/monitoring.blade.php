@extends('template')

@section('judul', 'Monitoring Eksekutif')

@section('konten')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Monitoring Eksekutif</h4>
        <p class="text-muted mb-0">Pemantauan SLA & performa tim IT</p>
    </div>

    {{-- Filter Bulan & Tahun --}}
    <form method="GET" class="d-flex gap-2">
        <select name="month" class="form-select" style="width:auto;">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
        <select name="year" class="form-select" style="width:auto;">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button class="btn btn-inamikro">Terapkan</button>
    </form>
</div>

{{-- 4 KARTU METRIK --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">RATA-RATA RESPON PERTAMA</small>
            <h2 class="fw-bold mt-2">{{ round($avgRespon) }} <small class="fs-6">menit</small></h2>
            <small class="text-success"><i class="bi bi-check-circle"></i> Target &lt; 30 menit</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">PENYELESAIAN SLA</small>
            <h2 class="fw-bold mt-2">{{ $persenSLA }}%</h2>
            <small class="text-muted">{{ $dalamSLA }} dari {{ $totalSelesai }} tiket</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TEKNISI ON-DUTY</small>
            <h2 class="fw-bold mt-2">{{ $onDuty->count() }}</h2>
            <small class="text-muted">IT Support aktif</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">KEPUASAN PENGGUNA</small>
            <h2 class="fw-bold mt-2">{{ $kepuasan }} <small class="fs-6">/ 5.0</small></h2>
            <small class="text-success">⭐⭐⭐⭐⭐</small>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- KINERJA TEKNISI ON-DUTY --}}
    <div class="col-md-7">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">Kinerja Teknisi On-Duty</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Teknisi</th>
                            <th>Tiket Selesai</th>
                            <th>Sedang Ditangani</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($onDuty as $t)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 fs-5"></i>
                                        <strong>{{ $t->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $t->total_ditangani ?? 0 }}</td>
                                <td>
                                    <span class="badge badge-soft-warning rounded-pill px-3">
                                        {{ $t->sedang_ditangani }} tiket
                                    </span>
                                </td>
                                <td>
                                    @if($t->sedang_ditangani > 0)
                                        <span class="text-warning">● Sibuk</span>
                                    @else
                                        <span class="text-success">● Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Belum ada data IT Support.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 5 KATEGORI TERATAS --}}
    <div class="col-md-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">Kategori Kendala Teratas</h5>
            @forelse($kategoriTeratas as $kat)
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span><i class="bi bi-dot"></i> {{ $kat->name }}</span>
                    <strong>{{ $kat->jumlah }} tiket</strong>
                </div>
            @empty
                <p class="text-muted">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection