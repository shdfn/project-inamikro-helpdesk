<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status — tambah nilai baru
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
            'Baru',
            'Ditangani IT',
            'Dibalas IT',
            'Dibalas Mentor',
            'Selesai',
            'Ditutup'
        ) NOT NULL DEFAULT 'Baru'");
    }

    public function down(): void
    {
        // Balik ke enum lama
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
            'Baru',
            'Diproses',
            'Selesai',
            'Ditutup'
        ) NOT NULL DEFAULT 'Baru'");
    }
};
