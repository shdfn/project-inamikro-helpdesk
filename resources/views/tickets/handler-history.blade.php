{{-- ═══════════════════════════════════════════ --}}
{{-- RIWAYAT PENANGANAN (IT SUPPORT & KEPALA IT) --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Riwayat Penanganan</h5>
    
    @php
        $handlers = $ticket->messages
            ->filter(function($msg) {
                return $msg->user && in_array($msg->user->role, ['it_support', 'kepala_it']);
            })
            ->unique('user_id');
    @endphp

    @if($handlers->count() > 0)
        <ul class="list-group list-group-flush">
            @foreach($handlers as $handler)
                <li class="list-group-item px-0 d-flex align-items-center bg-transparent border-bottom">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width: 38px; height: 38px; font-size: 0.75rem; font-weight: 600;">
                        {{ strtoupper(substr($handler->user->name, 0, 2)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold" style="font-size: 0.9rem;">{{ $handler->user->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $handler->user->role === 'kepala_it' ? 'Kepala IT' : 'IT Support' }}
                        </div>
                    </div>
                    <i class="bi bi-check-circle-fill text-success" title="Pernah membalas"></i>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center text-muted py-4">
            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
            <small>Belum ada IT Support yang merespon tiket ini.</small>
        </div>
    @endif
</div>