<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixServiceDestSequence extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->command->warn('Cette commande est uniquement pour PostgreSQL.');
            return;
        }
        DB::statement("SELECT setval(pg_get_serial_sequence('services_dest', 'id'), COALESCE((SELECT MAX(id) FROM services_dest), 1))");
        $this->command->info('Séquence services_dest réinitialisée.');
    }
}
