<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notes')) {
            return;
        }

        Schema::table('notes', function (Blueprint $table) {
            if (!Schema::hasColumn('notes', 'execution_slots')) {
                $table->json('execution_slots')->nullable()->after('drex_reel');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notes')) {
            return;
        }

        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'execution_slots')) {
                $table->dropColumn('execution_slots');
            }
        });
    }
};

