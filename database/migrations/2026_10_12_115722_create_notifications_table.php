<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');      // penerima
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');    // tiket terkait
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade'); // pengirim
            $table->string('type');          // 'message', 'assigned', 'resolved', 'closed'
            $table->string('title');
            $table->text('body')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
