<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Identifiants
            $table->string('matricule')->unique()->nullable()->after('email');
            $table->string('ldap_username')->nullable()->after('matricule');
            $table->string('ldap_guid')->nullable()->after('ldap_username');
            
            // Informations personnelles
            $table->string('nom')->nullable()->after('ldap_guid');
            $table->string('prenom')->nullable()->after('nom');
            $table->string('poste')->nullable()->after('prenom');
            $table->string('telephone')->nullable()->after('poste');
            $table->string('photo')->nullable()->after('telephone');
            
            // Organisation
            $table->string('organisation')->nullable()->after('photo');
            $table->string('entreprise')->nullable()->after('organisation');
            $table->string('service')->nullable()->after('entreprise');
            $table->string('direction')->nullable()->after('service');
            $table->string('departement')->nullable()->after('direction');
            
            // Oracle HR
            $table->unsignedBigInteger('oracle_person_id')->nullable()->after('departement');
            $table->string('fonction_oracle')->nullable()->after('oracle_person_id');
            $table->timestamp('oracle_synced_at')->nullable()->after('fonction_oracle');
            
            // Signatures et images
            $table->string('signature')->nullable()->after('oracle_synced_at');
            $table->string('stamp')->nullable()->after('signature');
            
            // Groupe et statut
            $table->foreignId('groupe_id')->nullable()->after('stamp')->constrained('groupes')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('groupe_id');
            
            // Timestamps sync
            $table->timestamp('last_sync_at')->nullable()->after('is_active');
            $table->timestamp('last_activity_at')->nullable()->after('last_sync_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['groupe_id']);
            $table->dropColumn([
                'matricule', 'ldap_username', 'ldap_guid',
                'nom', 'prenom', 'poste', 'telephone', 'photo',
                'organisation', 'entreprise', 'service', 'direction', 'departement',
                'oracle_person_id', 'fonction_oracle', 'oracle_synced_at',
                'signature', 'stamp', 'groupe_id', 'is_active',
                'last_sync_at', 'last_activity_at'
            ]);
        });
    }
};
