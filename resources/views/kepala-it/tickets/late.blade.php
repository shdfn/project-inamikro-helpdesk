@extends('template')

@section('judul', 'Tiket Lambat')

@section('konten')

<h3 class="fw-bold">Eskalasi Prioritas L2/Vendor</h3>
<p class="text-muted">Tiket aktif yang membutuhkan perhatian pimpinan dan tindakan intervensi eskalasi.</p>

<div class="row g-3 mb-4">

    {{-- Kartu 1: Jumlah tiket lambat --}}
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold">TIKET LEWAT BATAS SLA</small>
            <h2 class="fw-bold text-danger my-2">{{ $jumlahTiket }} Tiket</h2>
            <small>Perlu Eskalasi Segera</small>
        </div>
    </div>

    {{-- Kartu 2: Rata-rata keterlambatan --}}
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold">RATA-RATA KETERLAMBATAN</small>
            <h2 class="fw-bold my-2">+{{ $avgKeterlambatan }}</h2>
            <small>Melebihi standar respon 120 menit</small>
        </div>
    </div>

    {{-- Kartu 3: Estimasi Risiko SLA Bulanan --}}
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
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
        <table class="table table-hover align-middle" style="table-layout: fixed; width: 100%;">
            <thead class="bg-light">
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 28%;">DESKRIPSI INSIDEN</th>
                    <th style="width: 12%;">KATEGORI</th>
                    <th style="width: 15%;">TEKNISI (PIC)</th>
                    <th style="width: 15%;">WAKTU TUNGGU</th>
                    <th style="width: 10%;">URGENSI</th>
                    <th style="width: 12%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    @php
                        // Hitung total menit keterlambatan
                        $totalMenit = $t->created_at->diffInMinutes(now()) - 120;
                        
                        // Konversi ke Hari, Jam, Menit
                        $hari = floor($totalMenit / 1440);
                        $jam = floor(($totalMenit % 1440) / 60);
                        $sisaMenit = $totalMenit % 60;

                        // Susun teks waktu tunggu
                        $parts = [];
                        if ($hari > 0) $parts[] = $hari . ' Hari';
                        if ($jam > 0) $parts[] = $jam . ' Jam';
                        if ($sisaMenit > 0 || empty($parts)) $parts[] = $sisaMenit . ' Menit';
                        $waktuTunggu = implode(' ', $parts);

                        // Tentukan urgensi
                        $urgensi = $t->priority === 'Tinggi' ? 'Kritis' : ($t->priority === 'Sedang' ? 'Tinggi' : 'Sedang');
                    @endphp
                    <tr>
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td style="word-wrap: break-word;">
                            <div class="fw-bold">{{ $t->title }}</div>
                            <small class="text-muted">{{ Str::limit($t->description, 50) }}</small>
                        </td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>
                            <div>{{ $t->itSupport?->name ?? 'Belum ditugaskan' }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-danger">{{ $waktuTunggu }}</span><br>
                            <small class="text-muted">Melebihi SLA</small>
                        </td>
                        <td>
                            <span class="badge badge-soft-{{ $urgensi === 'Kritis' ? 'danger' : 'warning' }} rounded-pill px-3">
                                {{ $urgensi }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kepala.tickets.show', $t->id) }}" class="btn btn-sm btn-danger">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                            Tidak ada tiket lambat. 🎉
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection