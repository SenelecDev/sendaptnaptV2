<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Dates réelles d'exécution (ne pas écraser les dates acceptées)
            $table->date('dre_reel')->nullable()->after('dre');
            $table->datetime('ddt_reel')->nullable()->after('ddt');
            $table->datetime('dft_reel')->nullable()->after('dft');
            $table->datetime('drex_reel')->nullable()->after('drex');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn(['dre_reel', 'ddt_reel', 'dft_reel', 'drex_reel']);
        });
    }
};

