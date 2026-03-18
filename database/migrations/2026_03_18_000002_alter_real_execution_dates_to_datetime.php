<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notes')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // Convertir en timestamp sans perdre les valeurs existantes
            DB::statement("ALTER TABLE notes ALTER COLUMN dre_reel TYPE timestamp(0) without time zone USING dre_reel::timestamp");
            DB::statement("ALTER TABLE notes ALTER COLUMN ddt_reel TYPE timestamp(0) without time zone USING ddt_reel::timestamp");
            DB::statement("ALTER TABLE notes ALTER COLUMN dft_reel TYPE timestamp(0) without time zone USING dft_reel::timestamp");
            DB::statement("ALTER TABLE notes ALTER COLUMN drex_reel TYPE timestamp(0) without time zone USING drex_reel::timestamp");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE notes MODIFY dre_reel DATETIME NULL");
            DB::statement("ALTER TABLE notes MODIFY ddt_reel DATETIME NULL");
            DB::statement("ALTER TABLE notes MODIFY dft_reel DATETIME NULL");
            DB::statement("ALTER TABLE notes MODIFY drex_reel DATETIME NULL");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('notes')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        // Revenir au schéma initial: dre_reel en date, les autres en datetime
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE notes ALTER COLUMN dre_reel TYPE date USING dre_reel::date");
            DB::statement("ALTER TABLE notes ALTER COLUMN ddt_reel TYPE timestamp(0) without time zone USING ddt_reel::timestamp");
            DB::statement("ALTER TABLE notes ALTER COLUMN dft_reel TYPE timestamp(0) without time zone USING dft_reel::timestamp");
            DB::statement("ALTER TABLE notes ALTER COLUMN drex_reel TYPE timestamp(0) without time zone USING drex_reel::timestamp");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE notes MODIFY dre_reel DATE NULL");
            DB::statement("ALTER TABLE notes MODIFY ddt_reel DATETIME NULL");
            DB::statement("ALTER TABLE notes MODIFY dft_reel DATETIME NULL");
            DB::statement("ALTER TABLE notes MODIFY drex_reel DATETIME NULL");
        }
    }
};

