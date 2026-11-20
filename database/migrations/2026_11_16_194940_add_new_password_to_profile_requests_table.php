<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        if (!Schema::hasColumn('profile_requests', 'new_password')) {
            Schema::table('profile_requests', function (Blueprint $table) {
                $table->string('new_password')->nullable()->after('new_email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('profile_requests', 'new_password')) {
            Schema::table('profile_requests', function (Blueprint $table) {
                $table->dropColumn('new_password');
            });
        }
    }
};