<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tables des contacts
        Schema::create('charges_cons', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('fonction')->nullable();
            $table->string('adresse')->nullable();
            $table->string('matricule')->nullable();
            $table->string('telephone')->nullable();
            $table->timestamps();
        });

        Schema::create('correspondants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('fonction')->nullable();
            $table->string('adresse')->nullable();
            $table->string('matricule')->nullable();
            $table->string('telephone')->nullable();
            $table->timestamps();
        });

        Schema::create('services_dest', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('responsable')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges_cons');
        Schema::dropIfExists('correspondants');
        Schema::dropIfExists('services_dest');
    }
};
