@extends('template')

@section('judul', 'Dashboard Kepala IT')

@section('konten')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Selamat datang, {{ auth()->user()->name }} 👋</h3>
        <p class="text-muted mb-0">Pantau performa IT Support dan perkembangan tiket operasional.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('kepala.tickets.export') }}" class="btn btn-dark btn-sm">
            <i class="bi bi-download"></i> Unduh Laporan
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- KARTU STATISTIK --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TOTAL TIKET</small>
            <h2 class="fw-bold my-1">{{ $total }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up"></i> 12% dari periode lalu</small>
        </div>
    </div>
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TIKET TERBUKA</small>
            <h2 class="fw-bold my-1">{{ $open }}</h2>
            <small class="text-danger">
                <i class="bi bi-exclamation-circle"></i> {{ $open }} butuh penanganan
            </small>
        </div>
    </div>
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TIKET DITUTUP</small>
            <h2 class="fw-bold my-1">{{ $closed }}</h2>
            <small class="text-muted">
                {{ $total > 0 ? round(($closed/$total)*100, 2) : 0 }}% resolusi sukses
            </small>
        </div>
    </div>
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">AVG RESPONSE</small>
            <h2 class="fw-bold my-1">
                {{ $avgResponse }}
            </h2>
            <small class="text-success"><i class="bi bi-arrow-down"></i> lebih cepat dari target</small>
        </div>
    </div>
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">SLA KESELURUHAN</small>
            <h2 class="fw-bold my-1" style="color:var(--inamikro-red);">{{ $slaPercent }}%</h2>
            <small class="text-muted">Target ≥ 90%</small>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- CHART SECTION 1: Performa SLA + Kategori --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-bar-chart-line text-danger"></i> Performa SLA IT Support
            </h5>
            <small class="text-muted d-block mb-3">Persentase respon sesuai target SLA (≤ 30 menit)</small>
            <canvas id="chartPerformaIT" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-exclamation-circle text-danger"></i> Kendala Paling Sering
            </h5>
            <small class="text-muted d-block mb-3">Top 5 kategori dengan tiket terbanyak</small>
            <canvas id="chartKategori" height="200"></canvas>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- CHART SECTION 2: Distribusi Status --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-pie-chart text-danger"></i> Distribusi Status Tiket
            </h5>
            <div class="row align-items-center">
                <div class="col-md-5">
                    <canvas id="chartStatus" height="200"></canvas>
                </div>
                <div class="col-md-7">
                    <div class="row g-2">
                        @php
                            $statusData = [
                                ['Baru', 'Baru', 'danger'],
                                ['Ditangani IT', 'Ditangani IT', 'warning'],
                                ['Dibalas IT', 'Dibalas IT', 'primary'],
                                ['Dibalas Mentor', 'Dibalas Mentor', 'info'],
                                ['Selesai', 'Selesai', 'success'],
                                ['Ditutup', 'Ditutup', 'secondary'],
                            ];
                        @endphp
                        @foreach($statusData as $s)
                            <div class="col-md-6">
                                <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-circle-fill" style="color: var(--bs-{{ $s[2] }}); font-size: 0.7rem;"></i>
                                        <small>{{ $s[1] }}</small>
                                    </span>
                                    <strong>{{ \App\Models\Ticket::where('status', $s[0])->count() }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- TIKET LAMBAT --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-exclamation-triangle text-danger"></i>
            Tiket Membutuhkan Perhatian (Tiket Lambat)
        </h5>
        <a href="{{ route('kepala.tickets.index') }}" class="text-decoration-none text-danger small">
            Lihat Detail <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th>
                    <th>Kendala</th>
                    <th>Kategori</th>
                    <th>PIC</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lateTickets as $t)
                    @php
                        $durasi = $t->created_at->diffForHumans(null, true);
                    @endphp
                    <tr onclick="window.location='{{ route('kepala.tickets.show', $t->id) }}'"
                        style="cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#fff5f5';"
                        onmouseout="this.style.backgroundColor='';">
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td>
                            <div class="fw-bold">{{ $t->title }}</div>
                        </td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>{{ $t->itSupport->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-soft-danger rounded-pill px-3">
                                <i class="bi bi-clock"></i> {{ $durasi }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $t->status_badge_class }} rounded-pill px-3"
                                  title="{{ $t->status_label }}">
                                <i class="bi bi-{{ $t->status_icon }}"></i>
                                {{ $t->status_short }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kepala.tickets.show', $t->id) }}"
                               class="btn btn-sm btn-outline-danger"
                               onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="mt-2 mb-0">Tidak ada tiket lambat. 🎉</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SCRIPT CHART.JS --}}
{{-- ═══════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ═══ Chart 1: Performa SLA IT Support ═══
        const ctxPerformaIT = document.getElementById('chartPerformaIT');
        if (ctxPerformaIT) {
            new Chart(ctxPerformaIT, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($itSupports as $it)
                            '{{ $it->name }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'SLA (%)',
                        data: [
                            @foreach($itSupports as $it)
                                {{ $it->sla_percentage }},
                            @endforeach
                        ],
                        backgroundColor: [
                            @foreach($itSupports as $it)
                                '{{ $it->sla_percentage >= 90 ? "#198754" : ($it->sla_percentage >= 75 ? "#ffc107" : "#dc3545") }}',
                            @endforeach
                        ],
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'SLA: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function (value) { return value + '%'; }
                            }
                        }
                    }
                }
            });
        }

        // ═══ Chart 2: Kategori Kendala Teratas ═══
        const ctxKategori = document.getElementById('chartKategori');
        if (ctxKategori) {
            new Chart(ctxKategori, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($kategoriPopuler as $kat)
                            '{{ $kat->name }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [
                            @foreach($kategoriPopuler as $kat)
                                {{ $kat->jumlah }},
                            @endforeach
                        ],
                        backgroundColor: '#d81b21',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // ═══ Chart 3: Distribusi Status ═══
        const ctxStatus = document.getElementById('chartStatus');
        if (ctxStatus) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Baru', 'Ditangani IT', 'Dibalas IT', 'Dibalas Mentor', 'Selesai', 'Ditutup'],
                    datasets: [{
                        data: [
                            {{ \App\Models\Ticket::where('status', 'Baru')->count() }},
                            {{ \App\Models\Ticket::where('status', 'Ditangani IT')->count() }},
                            {{ \App\Models\Ticket::where('status', 'Dibalas IT')->count() }},
                            {{ \App\Models\Ticket::where('status', 'Dibalas Mentor')->count() }},
                            {{ \App\Models\Ticket::where('status', 'Selesai')->count() }},
                            {{ \App\Models\Ticket::where('status', 'Ditutup')->count() }},
                        ],
                        backgroundColor: ['#dc3545', '#ffc107', '#0d6efd', '#0dcaf0', '#198754', '#6c757d'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(2) : 0;
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