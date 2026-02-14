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

        // Create admin user
        $admin = \App\Models\User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin@senelec.sn',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');
    }
}

