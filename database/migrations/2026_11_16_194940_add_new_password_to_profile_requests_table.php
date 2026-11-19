<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_requests', function (Blueprint $table) {
            // Tambahkan kolom new_password tanpa ->after()
            $table->string('new_password')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profile_requests', function (Blueprint $table) {
            $table->dropColumn('new_password');
        });
    }
};
