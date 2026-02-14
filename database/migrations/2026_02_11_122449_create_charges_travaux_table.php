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
        // Table pour les chargés de travaux externes (qui ne se connectent pas)
        Schema::create('charges_travaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom complet
            $table->string('telephone')->nullable();
            $table->string('entreprise')->nullable();
            $table->string('service')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Ajouter colonne pour CT externe dans demandes
        Schema::table('demandes', function (Blueprint $table) {
            $table->foreignId('charge_travaux_externe_id')
                  ->nullable()
                  ->after('charge_travaux_id')
                  ->constrained('charges_travaux')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['charge_travaux_externe_id']);
            $table->dropColumn('charge_travaux_externe_id');
        });
        
        Schema::dropIfExists('charges_travaux');
    }
};
