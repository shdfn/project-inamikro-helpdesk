<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'attachment')) {
                $table->string('attachment')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tickets', 'first_response_at')) {
                $table->timestamp('first_response_at')->nullable()->after('it_support_id');
            }
            if (!Schema::hasColumn('tickets', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('first_response_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'first_response_at', 'resolved_at']);
        });
    }
};
