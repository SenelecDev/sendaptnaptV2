<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'utilisateur admin
        $admin = User::firstOrCreate(
            ['matricule' => 'ADMIN'],
            [
                'name' => 'Administrateur Système',
                'nom' => 'Administrateur',
                'prenom' => 'Système',
                'email' => 'admin@senelec.sn',
                'password' => Hash::make('password'),
                'poste' => 'Administrateur Système',
                'service' => 'Direction des Systèmes d\'Information',
                'organisation' => 'SENELEC',
                'entreprise' => 'SENELEC',
                'telephone' => '',
                'is_active' => true,
            ]
        );

        // Attribuer le rôle admin
        $admin->syncRoles(['admin']);

        $this->command->info('Utilisateur admin créé avec succès !');
        $this->command->info('Matricule: ADMIN');
        $this->command->info('Mot de passe: password');
    }
}
