<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupesSeeder extends Seeder
{
    public function run(): void
    {
        $groupes = [
            ['id' => 1, 'nom' => 'DPE', 'email' => 'dpe@senelec.sn', 'description' => null],
            ['id' => 3, 'nom' => 'SME', 'email' => 'napt.sme@senelec.sn', 'description' => 'Réception des NAPTs'],
            ['id' => 4, 'nom' => 'UCC&UTC', 'email' => 'UCCUTCtravaux@senelec.sn', 'description' => 'UCC&UTC travaux'],
            ['id' => 5, 'nom' => 'SML', 'email' => 'napt.sml@senelec.sn', 'description' => 'SML_NAPT/NITST'],
            ['id' => 7, 'nom' => 'Experts Previsions', 'email' => 'Previsions@senelec.sn', 'description' => 'Groupe de diffusion des experts prévisions/DESE/DESA de Senelec'],
        ];

        $now = now();
        $inserted = 0;

        foreach ($groupes as $groupe) {
            // Vérifier si le groupe existe déjà (par nom ou email)
            $exists = DB::table('groupes')
                ->where('nom', $groupe['nom'])
                ->orWhere('email', $groupe['email'])
                ->exists();

            if (!$exists) {
                DB::table('groupes')->insert([
                    'id' => $groupe['id'],
                    'nom' => $groupe['nom'],
                    'email' => $groupe['email'],
                    'description' => $groupe['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }
        }

        // Réinitialiser la séquence PostgreSQL après insertion d'IDs explicites
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('groupes', 'id'), COALESCE((SELECT MAX(id) FROM groupes), 1))");
        }

        $this->command->info("✅ {$inserted} groupes insérés.");
    }
}
