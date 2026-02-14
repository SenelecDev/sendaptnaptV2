<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChargesConsSeeder extends Seeder
{
    public function run(): void
    {
        // Copier les correspondants vers charges_cons
        $correspondants = DB::table('correspondants')->get();
        $count = 0;
        $now = now();

        foreach ($correspondants as $c) {
            $exists = DB::table('charges_cons')->where('matricule', $c->matricule)->exists();
            
            if (!$exists) {
                DB::table('charges_cons')->insert([
                    'nom' => $c->nom,
                    'fonction' => $c->fonction,
                    'matricule' => $c->matricule,
                    'telephone' => $c->telephone,
                    'adresse' => $c->adresse,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ {$count} chargés de consignation insérés.");
    }
}
