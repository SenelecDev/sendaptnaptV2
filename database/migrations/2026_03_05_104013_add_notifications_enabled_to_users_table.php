<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'notifications_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('notifications_enabled')->default(true)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'notifications_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notifications_enabled');
            });
        }
    }
};
