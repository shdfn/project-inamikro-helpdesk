@extends('template')

@section('judul', 'Monitoring Eksekutif')

@section('konten')

{{-- ═══════════════════════════════════════════ --}}
{{-- HEADER: Judul KIRI, Filter KANAN --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-0">Monitoring Eksekutif</h4>
        <p class="text-muted mb-0 small">Pemantauan SLA & performa tim IT</p>
    </div>

    {{-- Filter Periode --}}
    <form method="GET" class="d-flex align-items-center gap-2">
        <div class="input-group input-group-sm shadow-sm" style="width: auto;">
            {{-- Bulan --}}
            <select name="month" class="form-select" style="width: 130px; cursor: pointer;">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            {{-- Tahun --}}
            <select name="year" class="form-select" style="width: 95px; cursor: pointer;">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-inamikro btn-sm px-3 shadow-sm">
            Terapkan
        </button>
    </form>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- 4 KARTU METRIK --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">RATA-RATA RESPON PERTAMA</small>
            <h2 class="fw-bold my-2">
                {{ $avgRespon }}
            </h2>
            <small class="text-success" style="font-size: 0.75rem;">
                <i class="bi bi-check-circle"></i> Target &lt; 30 menit
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">PENYELESAIAN SLA</small>
            <h2 class="fw-bold my-2 text-success">{{ $persenSLA }}%</h2>
            <small class="text-muted" style="font-size: 0.75rem;">
                {{ $dalamSLA }} dari {{ $totalSelesai }} tiket
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">TEKNISI ON-DUTY</small>
            <h2 class="fw-bold my-2">{{ $onDuty->count() }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">IT Support aktif</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">KEPUASAN PENGGUNA</small>
            <h2 class="fw-bold my-2">{{ $kepuasan }} <small class="fs-6 text-muted">/ 5.0</small></h2>
            <div class="text-warning">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- KINERJA TEKNISI & KATEGORI TERATAS --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3">
    {{-- Kinerja Teknisi On-Duty --}}
    <div class="col-md-7">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-people text-danger"></i> Kinerja Teknisi On-Duty
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-start">Teknisi</th>
                            <th class="text-center">Tiket Selesai</th>
                            <th class="text-center">Sedang Ditangani</th>
                            <th class="text-start">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($onDuty as $t)
                            <tr>
                                <td class="text-start">
                                    <div class="d-flex align-items-center justify-content-start">
                                        {{-- Avatar Inisial --}}
                                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3"
                                            style="width: 36px; height: 36px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; flex-shrink: 0;">
                                            {{ strtoupper(substr($t->name, 0, 2)) }}
                                        </div>
                                        <span class="fw-bold">{{ $t->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">{{ $t->total_ditangani ?? 0 }}</td>
                                <td class="text-center">
                                    <span class="badge badge-soft-warning rounded-pill px-3">
                                        {{ $t->sedang_ditangani }} tiket
                                    </span>
                                </td>
                                <td class="text-start">
                                    @if($t->sedang_ditangani > 0)
                                        <span class="text-warning"><i class="bi bi-circle-fill"></i> Sibuk</span>
                                    @else
                                        <span class="text-success"><i class="bi bi-circle-fill"></i> Tersedia</span>
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
            <h5 class="fw-bold mb-3">
                <i class="bi bi-pie-chart text-danger"></i> Kategori Kendala Teratas
            </h5>
            @forelse($kategoriTeratas as $kat)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                        <i class="bi bi-dot text-danger"></i>
                        {{ $kat->name }}
                    </span>
                    <strong>{{ $kat->jumlah }} tiket</strong>
                </div>
            @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-pie-chart fs-2 d-block mb-2"></i>
                    Belum ada data.
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection