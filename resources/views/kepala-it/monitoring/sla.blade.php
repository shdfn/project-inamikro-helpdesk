@extends('template')
@section('judul', 'SLA IT Support')
@section('breadcrumb', 'SLA IT Support')

@section('konten')
<h3 class="fw-bold">Pemantauan SLA & Performa Tim IT</h3>
<p class="text-muted">Evaluasi kecepatan respon dan tingkat keberhasilan penanganan insiden tiap teknisi.</p>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">RATA-RATA RESPON PERTAMA</small>
            <h2 class="fw-bold my-2">{{ round($avgResponse) }} <small class="fs-6 text-muted">Menit</small></h2>
            <small class="text-success">Target &lt; 30m • Optimal</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">PENYELESAIAN SESUAI SLA</small>
            <h2 class="fw-bold my-2">{{ $slaPercent }}%</h2>
            <small class="text-success">Target ≥ 90% • Tercapai</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TEKNISI ON-DUTY</small>
            <h2 class="fw-bold my-2">{{ $onDuty }} <small class="fs-6 text-muted">Personel</small></h2>
            <small class="text-muted">Kapasitas shift aktif</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">KEPUASAN PENGGUNA</small>
            <h2 class="fw-bold my-2">4.8 <small class="fs-6 text-muted">/ 5.0</small></h2>
            <small class="text-success">98% Positif</small>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3">Kinerja Teknisi On-Duty</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>TEKNISI</th>
                    <th>TIKET DITANGANI</th>
                    <th>TIKET SELESAI</th>
                    <th>RATA-RATA RESPON</th>
                    <th>KEPATUHAN SLA</th>
                    <th>STATUS KINERJA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itSupports as $it)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center"
                                     style="width:35px;height:35px;font-size:0.75rem;">{{ $it->initial }}</div>
                                <div>
                                    <div class="fw-bold">{{ $it->name }}</div>
                                    <small class="text-muted">{{ $it->role_label }}</small>
                                </div>
                            </div>
                        </td>
                        <td><strong>{{ $it->handled }}</strong></td>
                        <td><strong>{{ $it->resolved }}</strong></td>
                        <td>{{ $it->avg_response }}</td>
                        <td>{{ $it->sla_percentage }}% <small class="text-muted">Target 90%</small></td>
                        <td>
                            <span class="badge badge-soft-{{ $it->status_class }} rounded-pill px-3">{{ $it->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada IT Support.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection