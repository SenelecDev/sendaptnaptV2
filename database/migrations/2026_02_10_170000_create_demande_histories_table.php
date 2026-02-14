<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, status_changed
            $table->string('field')->nullable(); // champ modifié
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description')->nullable(); // description lisible
            $table->timestamps();
            
            $table->index(['demande_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_histories');
    }
};
