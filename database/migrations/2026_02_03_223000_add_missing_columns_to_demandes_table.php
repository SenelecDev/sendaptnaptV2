<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Ajouter user_id si non existant
            if (!Schema::hasColumn('demandes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('charge_travaux_id')->constrained('users')->nullOnDelete();
            }
            
            // Ajouter lieu_execution_manuel si non existant
            if (!Schema::hasColumn('demandes', 'lieu_execution_manuel')) {
                $table->string('lieu_execution_manuel')->nullable()->after('lieu_execution');
            }
            
            // Ajouter ouvrage_type_installer si non existant
            if (!Schema::hasColumn('demandes', 'ouvrage_type_installer')) {
                $table->string('ouvrage_type_installer')->nullable()->after('ouvrage_type');
            }
            
            // Ajouter equipements_oracle si non existant (JSON pour stocker les équipements à consigner)
            if (!Schema::hasColumn('demandes', 'equipements_oracle')) {
                $table->json('equipements_oracle')->nullable()->after('ouvrages_installer_manuel');
            }
            
            // Ajouter equipements_installer_oracle si non existant (JSON pour stocker les équipements travaux)
            if (!Schema::hasColumn('demandes', 'equipements_installer_oracle')) {
                $table->json('equipements_installer_oracle')->nullable()->after('equipements_oracle');
            }
            
            // Modifier dmrp pour accepter une heure (string) au lieu de boolean
            // Note: Cette modification nécessite de recréer la colonne
        });
        
        // Modifier le type de dmrp de boolean à string pour stocker l'heure
        Schema::table('demandes', function (Blueprint $table) {
            $table->string('dmrp')->nullable()->change();
        });
        
        // Modifier ouvrage_type pour accepter plus de valeurs
        Schema::table('demandes', function (Blueprint $table) {
            $table->string('ouvrage_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'lieu_execution_manuel',
                'ouvrage_type_installer',
                'equipements_oracle',
                'equipements_installer_oracle'
            ]);
        });
    }
};
