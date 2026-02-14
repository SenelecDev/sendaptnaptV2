<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Modifier mte et mcce de boolean à string pour accepter 'oui'/'non'
            $table->string('mte')->nullable()->default(null)->change();
            $table->string('mcce')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->boolean('mte')->default(false)->change();
            $table->boolean('mcce')->default(false)->change();
        });
    }
};
