<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Supprimer la contrainte CHECK sur ouvrage_type qui n'accepte que 'ligne' et 'poste'.
     * En mode manuel, on stocke 'manuel' dans ouvrage_type et ouvrage_type_installer.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE demandes DROP CONSTRAINT IF EXISTS demandes_ouvrage_type_check');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE demandes ADD CONSTRAINT demandes_ouvrage_type_check CHECK (ouvrage_type::text = ANY (ARRAY['ligne'::text, 'poste'::text]))");
        }
    }
};
