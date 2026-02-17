<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixGroupesSequence extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->command->warn('Cette commande est uniquement pour PostgreSQL.');
            return;
        }
        DB::statement("SELECT setval(pg_get_serial_sequence('groupes', 'id'), COALESCE((SELECT MAX(id) FROM groupes), 1))");
        $this->command->info('Séquence groupes réinitialisée.');
    }
}
