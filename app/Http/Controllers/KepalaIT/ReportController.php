<?php

namespace App\Http\Controllers\KepalaIT;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;

class ReportController extends Controller
{
    // Laporan SLA
    public function sla()
    {
        // Semua tiket yang sudah selesai
        $tickets = Ticket::whereIn('status', ['Selesai', 'Ditutup'])
            ->with(['category', 'mentor', 'itSupport'])
            ->orderBy('resolved_at', 'desc')
            ->get();

        // Statistik
        $totalResolved = $tickets->count();

        $slaCompliant = $tickets->filter(function ($t) {
            return $t->resolved_at && $t->created_at->diffInHours($t->resolved_at) <= 24;
        })->count();

        $slaPercentage = $totalResolved > 0 ? round(($slaCompliant / $totalResolved) * 100) : 0;

        // Rata-rata waktu respon (dari created_at ke first_response_at)
        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        // Rata-rata waktu penyelesaian
        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        return view('kepala-it.reports.sla', compact(
            'tickets',
            'totalResolved',
            'slaCompliant',
            'slaPercentage',
            'avgResponseTime',
            'avgResolutionTime'
        ));
    }

    // Performa IT Support
    public function performance()
    {
        $itSupports = User::where('role', 'it_support')
            ->withCount([
                'ticketsAsSupport as total_handled' => function ($q) {
                    $q->whereIn('status', ['Selesai', 'Ditutup']);
                },
                'ticketsAsSupport as total_active' => function ($q) {
                    $q->where('status', 'Diproses');
                },
            ])
            ->get();

        // Hitung rata-rata waktu penyelesaian per IT Support
        foreach ($itSupports as $it) {
            $avg = Ticket::where('it_support_id', $it->id)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours') ?? 0;

            $it->avg_resolution_hours = round($avg, 1);

            $slaCount = Ticket::where('it_support_id', $it->id)
                ->whereNotNull('resolved_at')
                ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
                ->count();

            $totalCount = Ticket::where('it_support_id', $it->id)
                ->whereNotNull('resolved_at')
                ->count();

            $it->sla_percentage = $totalCount > 0 ? round(($slaCount / $totalCount) * 100) : 0;
        }

        return view('kepala-it.reports.performance', compact('itSupports'));
    }
}