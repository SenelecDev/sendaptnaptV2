<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceDestSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['id' => 1, 'nom' => 'dispatching', 'responsable' => 'responsable dispatching', 'email' => 'nepasrepondre@senelec.sn'],
            ['id' => 2, 'nom' => 'sxp', 'responsable' => 'responsable sxp', 'email' => 'nepasrepondre@senelec.sn'],
            ['id' => 3, 'nom' => 'dt', 'responsable' => 'responsable dt', 'email' => 'nepasrepondre@senelec.sn'],
            ['id' => 4, 'nom' => 'dpp', 'responsable' => 'responsable dt', 'email' => 'nepasrepondre@senelec.sn'],
            ['id' => 5, 'nom' => 'dre', 'responsable' => 'responsable dre', 'email' => 'nepasrepondre@senelec.sn'],
            ['id' => 6, 'nom' => 'dpr', 'responsable' => 'responsable dpr', 'email' => 'nepasrepondre@senelec.sn'],
            ['id' => 7, 'nom' => 'dr', 'responsable' => 'responsable dre', 'email' => 'nepasrepondre@senelec.sn'],
        ];

        $now = now();
        $inserted = 0;

        foreach ($services as $service) {
            $exists = DB::table('services_dest')->where('nom', $service['nom'])->exists();

            if (!$exists) {
                DB::table('services_dest')->insert([
                    'id' => $service['id'],
                    'nom' => $service['nom'],
                    'responsable' => $service['responsable'],
                    'email' => $service['email'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }
        }

        // Réinitialiser la séquence PostgreSQL après insertion d'IDs explicites
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('services_dest', 'id'), COALESCE((SELECT MAX(id) FROM services_dest), 1))");
        }

        $this->command->info("✅ {$inserted} services destinataires insérés.");
    }
}
