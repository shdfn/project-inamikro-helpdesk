@extends('template')

@section('judul', 'Statistik & Analitik')

@section('konten')

{{-- ═══════════════════════════════════════════ --}}
{{-- HEADER --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Statistik & Analitik</h4>
        <p class="text-muted mb-0">Analisis data tiket untuk mendukung pengambilan keputusan strategis</p>
    </div>
    <a href="{{ route('kepala.tickets.export') }}" class="btn btn-outline-danger">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- STATISTIK GLOBAL — 5 KOLOM 1 BARIS --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- 1. Total Tiket --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">TOTAL TIKET</small>
                <i class="bi bi-ticket-detailed text-secondary"></i>
            </div>
            <h2 class="fw-bold my-2">{{ $totalTiket }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">Semua tiket</small>
        </div>
    </div>

    {{-- 2. Selesai --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">SELESAI</small>
                <i class="bi bi-check-circle text-success"></i>
            </div>
            <h2 class="fw-bold my-2 text-success">{{ $totalSelesai }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">Selesai & ditutup</small>
        </div>
    </div>

    {{-- 3. Ditutup --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">DITUTUP</small>
                <i class="bi bi-check2-all text-primary"></i>
            </div>
            <h2 class="fw-bold my-2 text-primary">{{ $totalDitutup }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">Dikonfirmasi Mentor</small>
        </div>
    </div>

    {{-- 4. Sedang Ditangani --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">DITANGANI</small>
                <i class="bi bi-hourglass-split text-warning"></i>
            </div>
            <h2 class="fw-bold my-2 text-warning">{{ $sedangDitangani }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">Tiket aktif</small>
        </div>
    </div>

    {{-- 5. Avg Resolution --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <small class="text-muted fw-bold" style="font-size: 0.7rem;">AVG PENYELESAIAN</small>
                <i class="bi bi-clock-history text-danger"></i>
            </div>
            <h2 class="fw-bold my-2">
                {{ round($avgResolution, 2) }}
                <small class="fs-6 text-muted">jam</small>
            </h2>
            <small class="text-muted" style="font-size: 0.75rem;">Rata-rata selesai</small>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- CHART: TREN BULANAN (LINE) --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-graph-up text-danger"></i> Tren 6 Bulan Terakhir
            </h5>
            <small class="text-muted d-block mb-3">
                Perbandingan tiket masuk vs tiket selesai
            </small>

            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="chartTren"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- CHART: KATEGORI TERATAS (DOUGHNUT + TABEL) --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3">
    <div class="col-md-12">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-pie-chart text-danger"></i> Kategori Kendala Teratas
            </h5>
            <small class="text-muted d-block mb-3">Top 5 kategori dengan tiket terbanyak</small>

            @if($kategoriTop->count() > 0)
                <div class="row align-items-center g-3">

                    {{-- Chart Doughnut (KIRI) --}}
                    <div class="col-md-4">
                        <div style="position: relative; height: 240px; width: 100%;">
                            <canvas id="chartKategoriTop"></canvas>
                        </div>
                    </div>

                    {{-- Tabel (KANAN) --}}
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Kategori</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-end">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kategoriTop as $k)
                                        @php
                                            $persen = $totalTiket > 0
                                                ? round(($k->total / $totalTiket) * 100, 2)
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <i class="bi bi-dot text-danger"></i>
                                                {{ $k->name }}
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ $k->total }}</strong> tiket
                                            </td>
                                            <td class="text-end">
                                                <span class="badge badge-soft-danger rounded-pill px-3">
                                                    {{ $persen }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-pie-chart fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada data kategori.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SCRIPT CHART.JS --}}
{{-- ═══════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ═══ Chart 1: Tren 6 Bulan (Line) ═══
        const ctxTren = document.getElementById('chartTren');
        if (ctxTren) {
            new Chart(ctxTren, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($trenBulanan as $tren)
                            '{{ $tren["bulan"] }}',
                        @endforeach
                    ],
                    datasets: [
                        {
                            label: 'Total Masuk',
                            data: [
                                @foreach($trenBulanan as $tren)
                                    {{ $tren["total"] }},
                                @endforeach
                            ],
                            borderColor: '#d81b21',
                            backgroundColor: 'rgba(216, 27, 33, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#d81b21',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Selesai',
                            data: [
                                @foreach($trenBulanan as $tren)
                                    {{ $tren["selesai"] }},
                                @endforeach
                            ],
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#198754',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' tiket';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // ═══ Chart 2: Kategori Teratas (Doughnut) ═══
        const ctxKategori = document.getElementById('chartKategoriTop');
        if (ctxKategori) {
            new Chart(ctxKategori, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach($kategoriTop as $k)
                            '{{ $k->name }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach($kategoriTop as $k)
                                {{ $k->total }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#d81b21',
                            '#ffc107',
                            '#0d6efd',
                            '#0dcaf0',
                            '#198754'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 8,
                                boxWidth: 8,
                                boxHeight: 8,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
                            callbacks: {
                                label: function (context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0
                                        ? ((context.parsed / total) * 100).toFixed(2)
                                        : 0;
                                    return context.label + ': ' + context.parsed + ' tiket (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

@endsection