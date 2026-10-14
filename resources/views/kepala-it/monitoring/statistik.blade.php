@extends('template')
@section('judul', 'Statistik Kendala')
@section('breadcrumb', 'Statistik Kendala')

@section('konten')
<h3 class="fw-bold">Statistik & Analisis Kendala</h3>
<p class="text-muted">Pemetaan tren insiden dan modul sistem yang paling sering dilaporkan.</p>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">TOTAL INSIDEN</small>
            <h2 class="fw-bold my-2">{{ $total }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted fw-bold">RATA-RATA MTTR</small>
            <h2 class="fw-bold my-2">{{ round($avgMttr) }} <small class="fs-6 text-muted">menit</small></h2>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3">5 Kategori Kendala Teratas</h5>
    @foreach($topCategories as $cat)
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span class="fw-bold">{{ $cat->category->name ?? '-' }}</span>
                <span>{{ $cat->total }} tiket <strong class="text-danger">{{ $total > 0 ? round(($cat->total/$total)*100) : 0 }}%</strong></span>
            </div>
            <div class="progress mt-1" style="height:6px;">
                <div class="progress-bar" style="width: {{ $total > 0 ? ($cat->total/$total)*100 : 0 }}%; background-color:var(--inamikro-red);"></div>
            </div>
        </div>
    @endforeach
</div>
@endsection