<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class TicketDummySeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan role Mentor, IT Support, dan Kepala IT tersedia
        $mentor = User::firstOrCreate(
            ['email' => 'budi@inamikro.com'],
            ['name' => 'Budi Santoso', 'username' => 'budisantoso', 'password' => bcrypt('password'), 'role' => 'mentor']
        );

        $itSupport = User::firstOrCreate(
            ['email' => 'it@inamikro.com'],
            ['name' => 'Tim IT Support', 'username' => 'itsupport', 'password' => bcrypt('password'), 'role' => 'it_support']
        );

        $kepalaIt = User::firstOrCreate(
            ['email' => 'kepala@inamikro.com'],
            ['name' => 'Siti Kepala', 'username' => 'sitikepala', 'password' => bcrypt('password'), 'role' => 'kepala_it']
        );

        // 2. Ambil atau buat kategori dasar
        $categoryIds = Category::pluck('id')->toArray();
        if (empty($categoryIds)) {
            $categoryIds = [
                Category::create(['name' => 'Login/Akses'])->id,
                Category::create(['name' => 'Performa Sistem'])->id,
                Category::create(['name' => 'File & Dokumen'])->id,
                Category::create(['name' => 'Jaringan & Server'])->id,
            ];
        }

        // 3. Nama staf personal IT Support (akun bersama)
        $personalStaffNames = ['Andi Pratama', 'Rian Hidayat', 'Fajar Nugraha', 'Dewi Lestari'];

        // 4. Variasi 6 data dummy tiket dengan kendala berbeda & status berbeda
        $ticketsData = [
            [
                'title' => 'Tidak bisa login akun mentor di browser HP',
                'description' => 'Mentor melaporkan error saat mencoba masuk aplikasi via mobile browser.',
                'status' => 'Baru',
                'has_response' => false
            ],
            [
                'title' => 'Password akun gagal direset via tautan email',
                'description' => 'Tombol reset password tidak merespon saat diklik oleh pengguna.',
                'status' => 'Ditangani IT',
                'has_response' => true
            ],
            [
                'title' => 'Server aplikasi sangat lambat saat jam sibuk',
                'description' => 'Proses loading halaman dashboard memakan waktu lebih dari 10 detik.',
                'status' => 'Dibalas IT',
                'has_response' => true
            ],
            [
                'title' => 'Gagal upload dokumen persyaratan verifikasi merchant',
                'description' => 'Format file PDF sudah sesuai tapi selalu muncul pesan gagal saat diunggah.',
                'status' => 'Selesai',
                'has_response' => true
            ],
            [
                'title' => 'Koneksi internet kantor terputus tiba-tiba',
                'description' => 'Jaringan lokal mengalami gangguan total sejak pagi hari.',
                'status' => 'Ditutup',
                'has_response' => true
            ],
            [
                'title' => 'Menu rekapitulasi kehadiran tidak muncul di hak akses',
                'description' => 'Kendala tampilan UI pada modul pelaporan bulanan.',
                'status' => 'Baru',
                'has_response' => false
            ],
        ];

        foreach ($ticketsData as $index => $data) {
            $i = $index + 1;
            
            // Rentang waktu pembuatan tiket tersebar acak dalam 2 bulan terakhir (0 s.d 60 hari lalu)
            $createdAt = Carbon::now()->subDays(rand(0, 60))->subHours(rand(1, 12));
            
            $firstResponseAt = null;
            $resolvedAt = null;
            $itSupportId = null;
            $activeStaffName = $personalStaffNames[$index % count($personalStaffNames)];

            // Atur waktu respons dan penanganan berdasarkan status
            if ($data['has_response']) {
                $itSupportId = $itSupport->id;
                $firstResponseAt = (clone $createdAt)->addMinutes(rand(10, 45));
            }

            if (in_array($data['status'], ['Selesai', 'Ditutup'])) {
                $resolvedAt = (clone $createdAt)->addHours(rand(2, 24));
            }

            $ticket = Ticket::create([
                'ticket_number' => 'TK-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'priority' => ['Tinggi', 'Sedang', 'Rendah'][array_rand(['Tinggi', 'Sedang', 'Rendah'])],
                'status' => $data['status'],
                'mentor_id' => $mentor->id,
                'it_support_id' => $itSupportId,
                'first_response_at' => $firstResponseAt,
                'resolved_at' => $resolvedAt,
                'created_at' => $createdAt,
                'updated_at' => $resolvedAt ?? ($firstResponseAt ?? $createdAt),
            ]);

            // Jika ada respons, buat catatan pesan diskusi dengan sender_name staf personal
            if ($data['has_response']) {
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $itSupport->id,
                    'sender_name' => $activeStaffName, // Nama personal staf IT yang bertugas
                    'message' => 'Halo, laporan kendala ini sedang kami periksa dan tangani.',
                    'created_at' => $firstResponseAt,
                    'updated_at' => $firstResponseAt,
                ]);
            }
        }
    }
}