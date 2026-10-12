@extends('template')

@section('judul', 'Performa IT Support')

@section('konten')

<div class="card card-custom p-4 mb-4">
    <h3 class="fw-bold mb-1">Performa IT Support</h3>
    <p class="text-muted mb-0">Ranking berdasarkan jumlah tiket yang ditangani</p>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Nama IT Support</th>
                    <th>Email</th>
                    <th>Total Ditangani</th>
                    <th>Sedang Aktif</th>
                    <th>Avg Penyelesaian</th>
                    <th>SLA %</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itSupports as $i => $it)
                    <tr>
                        <td>
                            @if($i == 0) <i class="bi bi-trophy-fill text-warning"></i>
                            @elseif($i == 1) <i class="bi bi-trophy-fill text-secondary"></i>
                            @elseif($i == 2) <i class="bi bi-trophy-fill" style="color: #cd7f32;"></i>
                            @else {{ $i + 1 }}
                            @endif
                        </td>
                        <td class="fw-bold">{{ $it->name }}</td>
                        <td>{{ $it->email }}</td>
                        <td><strong>{{ $it->total_handled }}</strong> tiket</td>
                        <td>
                            @if($it->total_active > 0)
                                <span class="badge badge-soft-warning rounded-pill px-3">
                                    {{ $it->total_active }} aktif
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $it->avg_resolution_hours }} jam</td>
                        <td>
                            @if($it->sla_percentage >= 80)
                                <span class="badge badge-soft-success rounded-pill px-3">{{ $it->sla_percentage }}%</span>
                            @elseif($it->sla_percentage >= 50)
                                <span class="badge badge-soft-warning rounded-pill px-3">{{ $it->sla_percentage }}%</span>
                            @else
                                <span class="badge badge-soft-danger rounded-pill px-3">{{ $it->sla_percentage }}%</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada IT Support.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection