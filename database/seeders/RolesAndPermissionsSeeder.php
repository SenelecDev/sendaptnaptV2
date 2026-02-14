<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Demandes
            'demande.create',
            'demande.view',
            'demande.edit',
            'demande.delete',
            'demande.traiter',
            'demande.accepter',
            'demande.retourner',
            
            // Notes
            'note.create',
            'note.view',
            'note.edit',
            'note.delete',
            'note.verifier',
            'note.valider',
            'note.executer',
            'note.annuler',
            'note.fiche_manoeuvre',
            
            // Admin
            'user.manage',
            'role.manage',
            'groupe.manage',
            'import.manage',
            'observation.manage',
            'absence.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - Full access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Demandeur - Create and view own demandes
        $demandeur = Role::create(['name' => 'demandeur']);
        $demandeur->givePermissionTo([
            'demande.create',
            'demande.view',
            'demande.edit',
        ]);

        // DESA (Éditeur) - Traiter demandes, créer/gérer notes
        $desa = Role::create(['name' => 'desa']);
        $desa->givePermissionTo([
            'demande.view',
            'demande.traiter',
            'demande.accepter',
            'demande.retourner',
            'note.create',
            'note.view',
            'note.edit',
        ]);

        // Vérificateur - Vérifier notes
        $verificateur = Role::create(['name' => 'verificateur']);
        $verificateur->givePermissionTo([
            'demande.view',
            'note.view',
            'note.verifier',
        ]);

        // Valideur - Valider notes
        $valideur = Role::create(['name' => 'valideur']);
        $valideur->givePermissionTo([
            'demande.view',
            'note.view',
            'note.valider',
        ]);

        // Opérateur - Exécuter notes
        $operateur = Role::create(['name' => 'operateur']);
        $operateur->givePermissionTo([
            'demande.view',
            'note.view',
            'note.executer',
        ]);

        // Opérateur Chef - Exécuter notes + fiche manoeuvre
        $operateurChef = Role::create(['name' => 'operateurchef']);
        $operateurChef->givePermissionTo([
            'demande.view',
            'note.view',
            'note.executer',
            'note.fiche_manoeuvre',
        ]);

        // Directeur - Consultation only
        $directeur = Role::create(['name' => 'directeur']);
        $directeur->givePermissionTo([
            'demande.view',
            'note.view',
        ]);
    }
}
