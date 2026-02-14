<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Ajouter lignes_oracle si non existant
            if (!Schema::hasColumn('demandes', 'lignes_oracle')) {
                $table->json('lignes_oracle')->nullable()->after('equipements_installer_oracle');
            }
            
            // Ajouter lignes_installer_oracle si non existant
            if (!Schema::hasColumn('demandes', 'lignes_installer_oracle')) {
                $table->json('lignes_installer_oracle')->nullable()->after('lignes_oracle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropColumn(['lignes_oracle', 'lignes_installer_oracle']);
        });
    }
};
