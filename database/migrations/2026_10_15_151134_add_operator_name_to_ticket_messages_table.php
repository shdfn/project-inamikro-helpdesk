<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function run(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->string('operator_name')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn('operator_name');
        });
    }
};