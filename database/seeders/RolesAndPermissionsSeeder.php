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
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        
        // Admin - Full access
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Demandeur - Create and view own demandes
        $demandeur = Role::firstOrCreate(['name' => 'demandeur', 'guard_name' => 'web']);
        $demandeur->givePermissionTo([
            'demande.create',
            'demande.view',
            'demande.edit',
        ]);

        // DESA (Éditeur) - Traiter demandes, créer/gérer notes
        $desa = Role::firstOrCreate(['name' => 'desa', 'guard_name' => 'web']);
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
        $verificateur = Role::firstOrCreate(['name' => 'verificateur', 'guard_name' => 'web']);
        $verificateur->givePermissionTo([
            'demande.view',
            'note.view',
            'note.verifier',
        ]);

        // Valideur - Valider notes
        $valideur = Role::firstOrCreate(['name' => 'valideur', 'guard_name' => 'web']);
        $valideur->givePermissionTo([
            'demande.view',
            'note.view',
            'note.valider',
        ]);

        // Opérateur - Exécuter notes
        $operateur = Role::firstOrCreate(['name' => 'operateur', 'guard_name' => 'web']);
        $operateur->givePermissionTo([
            'demande.view',
            'note.view',
            'note.executer',
        ]);

        // Opérateur Chef - Exécuter notes + fiche manoeuvre
        $operateurChef = Role::firstOrCreate(['name' => 'operateurchef', 'guard_name' => 'web']);
        $operateurChef->givePermissionTo([
            'demande.view',
            'note.view',
            'note.executer',
            'note.fiche_manoeuvre',
        ]);

        // Directeur - Consultation only
        $directeur = Role::firstOrCreate(['name' => 'directeur', 'guard_name' => 'web']);
        $directeur->givePermissionTo([
            'demande.view',
            'note.view',
        ]);
    }
}
