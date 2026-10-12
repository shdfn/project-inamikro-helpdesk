<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // TK-104
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['Rendah', 'Sedang', 'Tinggi'])->default('Sedang');
            $table->enum('status', ['Baru', 'Diproses', 'Selesai', 'Ditutup'])->default('Baru');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('mentor_id')->constrained('users');
            $table->foreignId('it_support_id')->nullable()->constrained('users');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
