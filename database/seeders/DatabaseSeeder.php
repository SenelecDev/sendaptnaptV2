<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Create admin user (idempotent) - matricule ADMIN pour connexion
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@senelec.sn'],
            [
                'name' => 'Administrateur',
                'matricule' => 'ADMIN',
                'password' => bcrypt('password'),
            ]
        );
        if (!$admin->matricule) {
            $admin->matricule = 'ADMIN';
            $admin->save();
        }
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}

