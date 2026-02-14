<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_demande')->unique();
            $table->date('date')->nullable();
            
            // Acteurs
            $table->foreignId('demandeur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('charge_travaux_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('traite_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Localisation
            $table->string('destinataire')->nullable();
            $table->string('lieu_execution')->nullable();
            $table->string('lieu_code')->nullable();
            $table->enum('ouvrage_type', ['ligne', 'poste'])->default('ligne');
            
            // Dates prévues
            $table->date('ddp')->nullable();
            $table->time('hdp')->nullable();
            $table->date('dfp')->nullable();
            $table->time('hfp')->nullable();
            $table->boolean('dmrp')->default(false);
            $table->boolean('dmrp_restitution')->default(false);
            
            // Dates acceptées
            $table->date('dda')->nullable();
            $table->time('hda')->nullable();
            $table->date('dfa')->nullable();
            $table->time('hfa')->nullable();
            $table->boolean('dmra')->default(false);
            
            // Informations
            $table->text('designation')->nullable();
            $table->text('renseignement')->nullable();
            $table->boolean('mte')->default(false);
            $table->boolean('mcce')->default(false);
            $table->enum('etape', ['ue', 'de'])->default('ue');
            $table->string('schema')->nullable();
            $table->string('pdf_path')->nullable();
            
            // Téléphones
            $table->string('telephone_demandeur')->nullable();
            $table->string('telephone_charge')->nullable();
            
            // Statut
            $table->enum('statut', ['créée', 'en cours de traitement', 'acceptée', 'retournée', 'brouillon'])
                  ->default('créée');
            
            // Mode saisie : GMAO (données depuis SQL Server) ou Manuel (texte libre)
            $table->enum('mode_saisie', ['gmao', 'manuel'])->default('gmao');
            
            // Mode GMAO : données JSON récupérées depuis SQL Server GMAO
            $table->json('ouvrages_consigner_gmao')->nullable();
            $table->json('ouvrages_installer_gmao')->nullable();
            
            // Mode Manuel : texte libre
            $table->text('ouvrages_consigner_manuel')->nullable();
            $table->text('ouvrages_installer_manuel')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
