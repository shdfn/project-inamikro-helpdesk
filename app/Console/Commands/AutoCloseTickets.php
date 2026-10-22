<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Helpers\NotificationHelper;

class AutoCloseTickets extends Command
{
    // Nama perintah yang akan dipanggil oleh scheduler
    protected $signature = 'tickets:auto-close';
    protected $description = 'Tutup otomatis tiket yang berstatus Selesai lebih dari 24 jam tanpa konfirmasi Mentor';

    public function handle()
    {
        $this->info('Memulai proses auto-close tiket...');

        // Cari tiket berstatus 'Selesai' yang di-resolve lebih dari 24 jam yang lalu
        $tickets = Ticket::where('status', 'Selesai')
            ->whereNotNull('resolved_at')
            ->where('resolved_at', '<=', now()->subHours(24))
            ->get();

        $count = 0;

        foreach ($tickets as $ticket) {
            // Ubah status menjadi Ditutup
            $ticket->update([
                'status' => 'Ditutup',
                // Kita biarkan resolved_at tetap sama (waktu IT menandai selesai)
            ]);

            // Kirim notifikasi ke Mentor bahwa tiket ditutup otomatis
            if ($ticket->mentor_id) {
                NotificationHelper::send(
                    $ticket->mentor_id,
                    $ticket->id,
                    null, // System (tidak ada user yang mengirim)
                    'auto_closed',
                    'Tiket Ditutup Otomatis',
                    'Tiket #' . $ticket->ticket_number . ' telah ditutup otomatis oleh sistem karena tidak ada konfirmasi dalam 24 jam.'
                );
            }

            // (Opsional) Kirim notifikasi ke IT Support
            if ($ticket->it_support_id) {
                NotificationHelper::send(
                    $ticket->it_support_id,
                    $ticket->id,
                    null,
                    'auto_closed',
                    'Tiket Ditutup Otomatis',
                    'Tiket #' . $ticket->ticket_number . ' ditutup otomatis karena Mentor tidak merespons dalam 24 jam.'
                );
            }

            $count++;
        }

        $this->info("Selesai! {$count} tiket berhasil ditutup otomatis.");
    }
}