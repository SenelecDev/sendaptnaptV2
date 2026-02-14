<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_note')->unique();
            $table->integer('numero_semaine')->nullable();
            $table->date('date')->nullable();
            $table->foreignId('demande_id')->constrained('demandes')->cascadeOnDelete();
            
            // Acteurs avec signatures
            $table->foreignId('etabli_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verifie_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('retourne1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('retourne2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('execute_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('en_cours_execution_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('annule_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Dates
            $table->date('dre')->nullable();
            $table->datetime('ddt')->nullable();
            $table->datetime('dft')->nullable();
            $table->datetime('drex')->nullable();
            
            // Destinataires (anciens champs single - gardés pour compatibilité)
            $table->foreignId('chargecon_id')->nullable()->constrained('charges_cons')->nullOnDelete();
            $table->foreignId('correspondant_id')->nullable()->constrained('correspondants')->nullOnDelete();
            $table->foreignId('servicedest_id')->nullable()->constrained('services_dest')->nullOnDelete();
            $table->text('adresse_charges_consignation')->nullable();
            $table->text('adresse_correspondants')->nullable();
            
            // Documents
            $table->string('document')->nullable();
            $table->string('etude')->nullable();
            $table->string('fiche_manoeuvre')->nullable();
            
            // Informations
            $table->text('renseignementN')->nullable();
            $table->text('motif')->nullable();
            $table->text('motifbis')->nullable();
            $table->text('commentanul')->nullable();
            
            // Statut
            $table->string('statut')->default('brouillon');
            
            $table->timestamps();
        });

        // Tables pivot pour les relations many-to-many
        Schema::create('note_charge_consignation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('notes')->cascadeOnDelete();
            $table->foreignId('charge_cons_id')->constrained('charges_cons')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('note_correspondant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('notes')->cascadeOnDelete();
            $table->foreignId('correspondant_id')->constrained('correspondants')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('note_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('notes')->cascadeOnDelete();
            $table->foreignId('service_dest_id')->constrained('services_dest')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_service');
        Schema::dropIfExists('note_correspondant');
        Schema::dropIfExists('note_charge_consignation');
        Schema::dropIfExists('notes');
    }
};
