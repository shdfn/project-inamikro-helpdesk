<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah data lama dulu agar tidak error
        DB::statement("UPDATE tickets SET status = 'Ditangani IT' WHERE status = 'Diproses'");

        // Ubah enum
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
        DB::statement("UPDATE tickets SET status = 'Diproses' WHERE status IN ('Ditangani IT', 'Dibalas IT', 'Dibalas Mentor')");

        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
            'Baru',
            'Diproses',
            'Selesai',
            'Ditutup'
        ) NOT NULL DEFAULT 'Baru'");
    }
};
