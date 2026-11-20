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
            <select name="month" class="form-select" style="width: 130px; cursor: pointer;">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month((int) $i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
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
{{-- 3 KARTU METRIK                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- 1. Rata-rata Respon --}}
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">RATA-RATA RESPON PERTAMA</small>
            <h2 class="fw-bold my-2">{{ $avgRespon }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">
                Target < 30 menit | {{ \Carbon\Carbon::create()->month((int) $month)->translatedFormat('F') }} {{ $year }}
            </small>
        </div>
    </div>

    {{-- 2. Penyelesaian SLA --}}
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">PENYELESAIAN SLA</small>
            <h2 class="fw-bold my-2 text-success">{{ $persenSLA }}%</h2>
            <small class="text-muted" style="font-size: 0.75rem;">
                {{ $dalamSLA }} dari {{ $totalRespon }} tiket
            </small>
        </div>
    </div>

    {{-- 3. Teknisi On-Duty --}}
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <small class="text-muted fw-bold" style="font-size: 0.7rem;">TEKNISI ON-DUTY</small>
            <h2 class="fw-bold my-2">{{ $onDuty->count() }}</h2>
            <small class="text-muted" style="font-size: 0.75rem;">IT Support aktif</small>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- TABEL + GRAFIK METRIK PER IT SUPPORT --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-person-badge text-danger"></i> Metrik Per IT Support
            </h5>
            <small class="text-muted">
                Performa individu IT Support — Bulan
                {{ \Carbon\Carbon::create()->month((int) $month)->translatedFormat('F') }} {{ $year }}
            </small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-start">IT Support</th>
                    <th class="text-center">Total Tiket</th>
                    <th class="text-center">Tiket Selesai</th>
                    <th class="text-center">Tiket Menggantung</th>
                    <th class="text-center">Avg Response</th>
                    <th class="text-center">SLA %</th>
                    <th class="text-start">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($onDuty as $it)
                    <tr>
                        {{-- Nama + Avatar --}}
                        <td class="text-start">
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 36px; height: 36px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; flex-shrink: 0;">
                                    {{ strtoupper(substr($it->name, 0, 2)) }}
                                </div>
                                <span class="fw-bold">{{ $it->name }}</span>
                            </div>
                        </td>

                        {{-- Total Tiket --}}
                        <td class="text-center">
                            <span class="badge badge-soft-secondary rounded-pill px-3">
                                {{ $it->total_tiket }}
                            </span>
                        </td>

                        {{-- Tiket Selesai --}}
                        <td class="text-center">
                            <span class="badge badge-soft-success rounded-pill px-3">
                                <i class="bi bi-check-circle"></i> {{ $it->tiket_selesai }}
                            </span>
                        </td>

                        {{-- Tiket Menggantung --}}
                        <td class="text-center">
                            @if($it->tiket_menggantung > 0)
                                <span class="badge badge-soft-danger rounded-pill px-3">
                                    <i class="bi bi-hourglass-split"></i> {{ $it->tiket_menggantung }}
                                </span>
                            @else
                                <span class="badge badge-soft-success rounded-pill px-3">
                                    <i class="bi bi-check2-all"></i> 0
                                </span>
                            @endif
                        </td>

                        {{-- Avg Response --}}
                        <td class="text-center">
                            <small class="fw-bold">{{ $it->avg_response }}</small>
                        </td>

                        {{-- SLA % --}}
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-bold" style="color: var(--bs-{{ $it->status_class }});">
                                    {{ $it->sla_percentage }}%
                                </span>
                                <div class="progress mt-1" style="height: 4px; width: 60px;">
                                    <div class="progress-bar bg-{{ $it->status_class }}"
                                         role="progressbar"
                                         style="width: {{ $it->sla_percentage }}%"
                                         aria-valuenow="{{ $it->sla_percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Status Label --}}
                        <td class="text-start">
                            <span class="badge badge-soft-{{ $it->status_class }} rounded-pill px-3">
                                {{ $it->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            Belum ada data IT Support.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Grafik Perbandingan Per IT Support --}}
    <div class="mt-4 pt-3 border-top" style="position: relative; height: 260px; width: 100%;">
        <canvas id="chartPerIT"></canvas>
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

{{-- ═══════════════════════════════════════════ --}}
{{-- SCRIPT CHART.JS --}}
{{-- ═══════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ═══ Fungsi untuk mendapatkan warna tema saat ini ═══
        function getThemeColors() {
            const styles = getComputedStyle(document.documentElement);
            return {
                grid: styles.getPropertyValue('--bs-border-color').trim() || 'rgba(0,0,0,0.1)',
                text: styles.getPropertyValue('--bs-secondary-color').trim() || '#6c757d'
            };
        }

        // ═══ Inisialisasi Chart Perbandingan Per IT ═══
        const ctxPerIT = document.getElementById('chartPerIT');
        let chartPerIT = null;
        
        if (ctxPerIT) {
            let colors = getThemeColors();
            chartPerIT = new Chart(ctxPerIT, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($onDuty as $it)
                            '{{ $it->name }}',
                        @endforeach
                    ],
                    datasets: [
                        {
                            label: 'Total Tiket',
                            data: [
                                @foreach($onDuty as $it)
                                    {{ $it->total_tiket }},
                                @endforeach
                            ],
                            backgroundColor: '#6c757d',
                            borderRadius: 5
                        },
                        {
                            label: 'Selesai',
                            data: [
                                @foreach($onDuty as $it)
                                    {{ $it->tiket_selesai }},
                                @endforeach
                            ],
                            backgroundColor: '#198754',
                            borderRadius: 5
                        },
                        {
                            label: 'Menggantung',
                            data: [
                                @foreach($onDuty as $it)
                                    {{ $it->tiket_menggantung }},
                                @endforeach
                            ],
                            backgroundColor: '#dc3545',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { usePointStyle: true, padding: 15, color: colors.text }
                        }
                    },
                    scales: {
                        x: { grid: { color: colors.grid }, ticks: { color: colors.text } },
                        y: { beginAtZero: true, ticks: { stepSize: 1, color: colors.text }, grid: { color: colors.grid } }
                    }
                }
            });
        }

        // ═══ EVENT LISTENER: Deteksi Klik Ganti Mode ═══
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'data-bs-theme' || mutation.attributeName === 'class') {
                    setTimeout(() => {
                        let newColors = getThemeColors();

                        if (chartPerIT) {
                            // Update Warna Teks Legend
                            chartPerIT.options.plugins.legend.labels.color = newColors.text;
                            
                            // Update Warna Grid & Teks Sumbu X
                            chartPerIT.options.scales.x.grid.color = newColors.grid;
                            chartPerIT.options.scales.x.ticks.color = newColors.text;
                            
                            // Update Warna Grid & Teks Sumbu Y
                            chartPerIT.options.scales.y.grid.color = newColors.grid;
                            chartPerIT.options.scales.y.ticks.color = newColors.text;
                            
                            // Render ulang
                            chartPerIT.update();
                        }
                    }, 100);
                }
            });
        });

        // Mulai pantau perubahan tema
        observer.observe(document.documentElement, { attributes: true });
        observer.observe(document.body, { attributes: true });
    });
</script>

@endsection