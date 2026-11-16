<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Mengubah kolom ticket_id agar boleh NULL
            $table->foreignId('ticket_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Mengembalikan ke NOT NULL jika di-rollback (opsional)
            $table->foreignId('ticket_id')->nullable(false)->change();
        });
    }
};