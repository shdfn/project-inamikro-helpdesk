@extends('template')
@section('judul', 'Dashboard Kepala IT')
@section('breadcrumb', 'Monitoring Eksekutif')

@section('konten')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Selamat datang, {{ auth()->user()->name }} 👋</h3>
        <p class="text-muted mb-0">Pantau performa IT Support dan perkembangan tiket operasional.</p>
    </div>
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm"><option>Mingguan</option><option>Bulanan</option></select>
        <input type="text" class="form-control form-control-sm" placeholder="01-06 Okt 2026">
        <button class="btn btn-dark btn-sm"><i class="bi bi-download"></i> Unduh Laporan (CSV/PDF)</button>
    </div>
</div>

{{-- Kartu Statistik --}}
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
            <small class="text-danger">• {{ $open }} butuh penanganan</small>
        </div>
    </div>
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TIKET DITUTUP</small>
            <h2 class="fw-bold my-1">{{ $closed }}</h2>
            <small class="text-muted">{{ $total > 0 ? round(($closed/$total)*100) : 0 }}% resolusi sukses</small>
        </div>
    </div>
    <div class="col-md">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">AVG RESPONSE</small>
            <h2 class="fw-bold my-1">{{ round($avgResponse) }}<small class="fs-6 text-muted"> menit</small></h2>
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

<div class="row g-3 mb-4">
    {{-- Performa SLA IT Support --}}
    <div class="col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-line text-danger"></i> Performa SLA IT Support</h5>
            <small class="text-muted">Persentase respon sesuai target SLA (≤ 30 menit)</small>

            @foreach($itSupports as $it)
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center"
                                 style="width:35px;height:35px;font-size:0.75rem;">{{ $it->initial }}</div>
                            <div>
                                <div class="fw-bold">{{ $it->name }}</div>
                                <small class="text-muted">Rata-rata: {{ $it->avg_response }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-soft-{{ $it->status_class }} rounded-pill px-3">{{ $it->status }}</span>
                            <div class="fw-bold mt-1">{{ $it->sla_percentage }}%</div>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar bg-{{ $it->status_class }}" style="width: {{ $it->sla_percentage }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Kendala Paling Sering --}}
    <div class="col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-circle text-danger"></i> Kendala Paling Sering</h5>
            @foreach($topCategories as $cat)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">{{ $cat->category->name ?? '-' }}</span>
                        <span class="text-muted">{{ $cat->total }} tiket</span>
                    </div>
                    <div class="progress mt-1" style="height:6px;">
                        <div class="progress-bar" style="width: {{ $total > 0 ? ($cat->total/$total)*100 : 0 }}%; background-color: var(--inamikro-red);"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tiket Lambat --}}
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle text-danger"></i> Tiket Membutuhkan Perhatian (Tiket Lambat)</h5>
        <a href="{{ route('kepala.tickets.late') }}" class="text-decoration-none text-danger small">
            Lihat Detail <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>ID Tiket</th><th>Kendala</th><th>Kategori</th><th>PIC</th>
                    <th>Durasi</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lateTickets as $t)
                    @php $durasi = $t->created_at->diffForHumans(null, true); @endphp
                    <tr>
                        <td class="fw-bold">#{{ $t->ticket_number }}</td>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->category->name ?? '-' }}</td>
                        <td>{{ $t->itSupport->name ?? '-' }}</td>
                        <td><span class="badge badge-soft-danger rounded-pill px-3">{{ $durasi }}</span></td>
                        <td>{{ $t->status }}</td>
                        <td><a href="{{ route('kepala.tickets.show', $t->id) }}" class="btn btn-sm btn-danger">Eskalasi</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada tiket lambat. 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection