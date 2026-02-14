<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RoleAssignmentService
{
    /**
     * Mapping des fonctions/postes vers les rôles
     * Basé sur les titres LDAP et Oracle
     */
    protected array $roleMapping = [
        // Directeurs et Chefs - rôle directeur ou valideur
        'directeur' => ['directeur', 'valideur'],
        'chef de département' => ['valideur'],
        'chef département' => ['valideur'],
        'chef de service' => ['verificateur'],
        'chef service' => ['verificateur'],
        'responsable' => ['verificateur'],
        
        // Opérateurs électriques
        'chef de quart' => ['operateurchef'],
        'chef quart' => ['operateurchef'],
        'opérateur' => ['operateur'],
        'operateur' => ['operateur'],
        'agent de conduite' => ['operateur'],
        'conducteur' => ['operateur'],
        
        // DESA - Département Études et Sécurité d'Approvisionnement
        'desa' => ['desa'],
        'ingénieur desa' => ['desa'],
        'ingenieur desa' => ['desa'],
        'chargé d\'études desa' => ['desa'],
        'charge d\'etudes desa' => ['desa'],
        
        // Vérificateurs
        'vérificateur' => ['verificateur'],
        'verificateur' => ['verificateur'],
        'contrôleur' => ['verificateur'],
        'controleur' => ['verificateur'],
        
        // Demandeurs (ingénieurs, techniciens, agents)
        'ingénieur' => ['demandeur'],
        'ingenieur' => ['demandeur'],
        'technicien' => ['demandeur'],
        'agent' => ['demandeur'],
        'chargé' => ['demandeur'],
        'charge' => ['demandeur'],
    ];

    /**
     * Suggère un rôle basé sur la fonction/poste de l'utilisateur
     */
    public function suggestRoleFromFunction(?string $fonction): string
    {
        if (empty($fonction)) {
            return 'demandeur'; // Rôle par défaut
        }

        $fonctionLower = mb_strtolower($fonction, 'UTF-8');

        // Parcourir le mapping pour trouver une correspondance
        foreach ($this->roleMapping as $keyword => $roles) {
            if (str_contains($fonctionLower, $keyword)) {
                Log::debug("RoleAssignment: Matched '{$keyword}' in '{$fonction}', suggesting: {$roles[0]}");
                return $roles[0];
            }
        }

        // Vérifier des patterns spécifiques pour SENELEC
        if ($this->isDirectionRole($fonctionLower)) {
            return 'directeur';
        }

        if ($this->isOperateurRole($fonctionLower)) {
            return 'operateur';
        }

        if ($this->isDesaRole($fonctionLower)) {
            return 'desa';
        }

        // Par défaut, attribuer le rôle demandeur
        Log::debug("RoleAssignment: No match for '{$fonction}', defaulting to 'demandeur'");
        return 'demandeur';
    }

    /**
     * Vérifie si c'est un rôle de direction
     */
    protected function isDirectionRole(string $fonction): bool
    {
        $patterns = [
            'directeur',
            'dg',
            'pdg',
            'président',
            'president',
            'secrétaire général',
            'secretaire general',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($fonction, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si c'est un rôle d'opérateur
     */
    protected function isOperateurRole(string $fonction): bool
    {
        $patterns = [
            'opérateur',
            'operateur',
            'conduite',
            'dispatching',
            'exploitation',
            'quart',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($fonction, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si c'est un rôle DESA
     */
    protected function isDesaRole(string $fonction): bool
    {
        $patterns = [
            'desa',
            'études réseau',
            'etudes reseau',
            'sécurité approvisionnement',
            'securite approvisionnement',
            'planification réseau',
            'planification reseau',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($fonction, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne tous les rôles disponibles
     */
    public function getAvailableRoles(): array
    {
        return [
            'admin' => 'Administrateur',
            'demandeur' => 'Demandeur',
            'desa' => 'DESA',
            'verificateur' => 'Vérificateur',
            'valideur' => 'Valideur',
            'operateur' => 'Opérateur',
            'operateurchef' => 'Opérateur Chef',
            'directeur' => 'Directeur',
        ];
    }

    /**
     * Retourne la description d'un rôle
     */
    public function getRoleDescription(string $role): string
    {
        $descriptions = [
            'admin' => 'Accès complet à toutes les fonctionnalités',
            'demandeur' => 'Peut créer et suivre ses demandes DAPT',
            'desa' => 'Traite les demandes et crée les notes NAPT',
            'verificateur' => 'Vérifie les notes NAPT avant validation',
            'valideur' => 'Valide les notes NAPT',
            'operateur' => 'Exécute les manœuvres sur le réseau',
            'operateurchef' => 'Exécute les manœuvres et gère les fiches de manœuvre',
            'directeur' => 'Consultation et supervision',
        ];

        return $descriptions[$role] ?? 'Aucune description disponible';
    }

    /**
     * Auto-assign role based on user's function/position
     * Called during login/sync
     */
    public function autoAssignRole(\App\Models\User $user): void
    {
        // Ne pas écraser les rôles si déjà attribués par un admin
        $currentRoles = $user->getRoleNames()->toArray();
        
        // Si l'utilisateur a déjà un rôle autre que 'demandeur', on conserve
        $nonDefaultRoles = array_filter($currentRoles, fn($role) => !in_array($role, ['demandeur', 'agent']));
        if (!empty($nonDefaultRoles)) {
            \Illuminate\Support\Facades\Log::info('Role already assigned, skipping auto-assignment', [
                'matricule' => $user->matricule,
                'current_roles' => $currentRoles,
            ]);
            return;
        }

        $suggestedRole = $this->suggestRoleFromFunction($user->poste);

        // Attribuer le nouveau rôle
        $user->syncRoles([$suggestedRole]);

        \Illuminate\Support\Facades\Log::info('Role auto-assigned based on function', [
            'user_id' => $user->id,
            'matricule' => $user->matricule,
            'poste' => $user->poste,
            'assigned_role' => $suggestedRole,
        ]);
    }

    /**
     * Sync role from Oracle HR data
     * Called when Oracle data is available
     */
    public function syncRoleFromOracle(\App\Models\User $user, array $oracleData): void
    {
        // Ne pas écraser les rôles si déjà attribués par un admin
        $currentRoles = $user->getRoleNames()->toArray();
        
        // Si l'utilisateur a déjà un rôle admin, on conserve
        if (in_array('admin', $currentRoles)) {
            return;
        }

        // Utiliser la fonction Oracle pour déterminer le rôle
        $fonction = $oracleData['fonction'] ?? $oracleData['poste'] ?? null;
        
        if (empty($fonction)) {
            return;
        }

        $suggestedRole = $this->suggestRoleFromFunction($fonction);

        // Vérifier si le rôle suggéré est différent et plus spécifique
        $nonDefaultRoles = array_filter($currentRoles, fn($role) => !in_array($role, ['demandeur', 'agent']));
        
        // Si pas de rôle spécifique ou si le rôle Oracle est plus pertinent
        if (empty($nonDefaultRoles) || $suggestedRole !== 'demandeur') {
            $user->syncRoles([$suggestedRole]);

            \Illuminate\Support\Facades\Log::info('Role synced from Oracle function', [
                'user_id' => $user->id,
                'matricule' => $user->matricule,
                'fonction_oracle' => $fonction,
                'assigned_role' => $suggestedRole,
            ]);
        }
    }
}
