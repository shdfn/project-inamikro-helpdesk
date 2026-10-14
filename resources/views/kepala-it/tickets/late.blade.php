@extends('template')
@section('judul', 'Tiket Lambat')
@section('breadcrumb', 'Eskalasi Tiket Lambat')

@section('konten')
<h3 class="fw-bold">Eskalasi Prioritas L2/Vendor</h3>
<p class="text-muted">Tiket aktif yang membutuhkan perhatian pimpinan dan tindakan intervensi eskalasi.</p>

<div class="row g-3 mb-4">

    {{-- Kartu 1: Jumlah tiket lambat --}}
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TIKET LEWAT BATAS SLA</small>
            <h2 class="fw-bold text-danger my-2">{{ $jumlahTiket }} Tiket</h2>
            <small>Perlu Eskalasi Segera</small>
        </div>
    </div>

    {{-- Kartu 2: Rata-rata keterlambatan --}}
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">RATA-RATA KETERLAMBATAN</small>
            @php
                $jam = floor($avgKeterlambatan / 60);
                $menit = $avgKeterlambatan % 60;
            @endphp
            <h2 class="fw-bold my-2">+{{ $jam }}j {{ $menit }}m</h2>
            <small>Melebihi standar respon 60 menit</small>
        </div>
    </div>

    {{-- Kartu 3: Estimasi Risiko SLA Bulanan (PENGGANTI Dampak Merchant) --}}
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">ESTIMASI RISIKO SLA BULANAN</small>

            @php
                $trend = $slaProjected < $slaSekarang ? 'down' : 'flat';
            @endphp

            <h2 class="fw-bold my-2 {{ $trend === 'down' ? 'text-danger' : 'text-success' }}">
                @if($trend === 'down')
                    <i class="bi bi-arrow-down-short"></i>
                @endif
                {{ $slaProjected }}%
            </h2>

            <small class="text-muted">
                dari {{ $slaSekarang }}% (SLA bulan ini)
            </small>
        </div>
    </div>

</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr><th>ID</th><th>DESKRIPSI INSIDEN</th><th>KATEGORI</th><th>TEKNISI (PIC)</th><th>WAKTU TUNGGU</th><th>URGENSI</th><th>AKSI</th></tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    @php
                        $menit = $t->created_at->diffInMinutes(now());
                        $jam   = floor($menit / 60);
                        $sisa  = $menit % 60;
                        $urgensi = $t->priority === 'Tinggi' ? 'Kritis' : ($t->priority === 'Sedang' ? 'Tinggi' : 'Sedang');
                    @endphp
                    <tr>
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td>
                            <div class="fw-bold">{{ $t->title }}</div>
                            <small class="text-muted">{{ Str::limit($t->description, 60) }}</small>
                        </td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>
                            <div>{{ $t->itSupport->name ?? 'Belum ditugaskan' }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-danger">{{ $jam }}j {{ $sisa }}m</span><br>
                            <small class="text-muted">Lewat 1j 15m</small>
                        </td>
                        <td>
                            <span class="badge badge-soft-{{ $urgensi === 'Kritis' ? 'danger' : 'warning' }} rounded-pill px-3">{{ $urgensi }}</span>
                        </td>
                        <td>
                            <a href="{{ route('kepala.tickets.show', $t->id) }}" class="btn btn-sm btn-danger">Eskalasi</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada tiket lambat. 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection