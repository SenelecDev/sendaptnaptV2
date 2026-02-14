<?php

namespace App\Ldap;

use App\Models\User;
use App\Services\RoleAssignmentService;
use Illuminate\Support\Facades\Log;
use LdapRecord\Models\Model as LdapUser;

class LdapAttributeHandler
{
    /**
     * Synchronise les attributs LDAP vers l'utilisateur local
     * Basé sur le code fonctionnel du projet précédent
     */
    public function handle(LdapUser $ldap, User $database): void
    {
        // Login / SAMAccountName
        $database->ldap_username = $ldap->getFirstAttribute('samaccountname');
        
        // Nom complet - Assurer que name n'est jamais null
        $name = $ldap->getFirstAttribute('name') ?: $ldap->getFirstAttribute('displayname');
        if (empty($name)) {
            // Construire le nom à partir de givenname et sn
            $givenName = $ldap->getFirstAttribute('givenname');
            $surname = $ldap->getFirstAttribute('sn');
            $name = trim(($givenName ?: '') . ' ' . ($surname ?: ''));
            
            // Si toujours vide, utiliser le login
            if (empty($name)) {
                $name = $ldap->getFirstAttribute('samaccountname') ?: 'Utilisateur';
            }
        }
        
        // Nom et Prénom séparés
        $database->nom = $ldap->getFirstAttribute('sn') ?? '';
        $database->prenom = $ldap->getFirstAttribute('givenname') ?? '';
        
        // Si nom/prenom vides, essayer de parser le name complet
        if (empty($database->nom) && !empty($name)) {
            $nameParts = explode(' ', $name, 2);
            $database->prenom = $nameParts[0] ?? '';
            $database->nom = $nameParts[1] ?? '';
        }
        
        // Poste / Titre / Fonction
        $database->poste = $ldap->getFirstAttribute('title');
        
        // Email
        $email = $ldap->getFirstAttribute('mail');
        if (empty($email)) {
            $samaccountname = $ldap->getFirstAttribute('samaccountname');
            $email = $samaccountname ? $samaccountname . '@senelec.sn' : null;
        }
        $database->email = $email;
        
        // Service / Département
        $database->service = $ldap->getFirstAttribute('department');
        
        // Téléphone
        $database->telephone = $ldap->getFirstAttribute('mobile') 
            ?? $ldap->getFirstAttribute('telephonenumber');
        
        // Organisation / Company
        $company = $ldap->getFirstAttribute('company');
        $database->organisation = $company;
        
        // DEBUG: Log pour diagnostiquer le problème du matricule
        $split = explode(' ', $company ?? '');
        
        Log::info('LDAP AttributeHandler Debug:', [
            'company_field' => $company,
            'split_result' => $split,
            'split_count' => count($split),
            'user_email' => $email,
            'user_name' => $name
        ]);

        // Gérer le matricule avec une valeur par défaut unique si vide
        $matricule = array_key_exists(1, $split) ? trim($split[1]) : null;

        // DEBUG: Log le matricule extrait
        Log::info('Matricule extrait:', [
            'matricule_brut' => $matricule,
            'is_empty' => empty($matricule),
            'user_email' => $email
        ]);

        // Si le matricule est vide ou null, générer un identifiant unique basé sur l'email
        if (empty($matricule)) {
            $login = $ldap->getFirstAttribute('samaccountname') ?: '';
            
            // Utiliser l'email s'il existe, sinon le login
            $baseForMatricule = !empty($email) ? $email : $login;
            
            if (!empty($baseForMatricule)) {
                $matricule = 'AUTO_' . strtoupper(substr(md5($baseForMatricule . time()), 0, 8));
            } else {
                $matricule = 'AUTO_' . strtoupper(substr(md5(uniqid() . time()), 0, 8));
            }
        }

        $database->matricule = $matricule;
        
        // Entreprise (première partie du champ company)
        $database->entreprise = array_key_exists(0, $split) ? $split[0] : null;

        // LDAP GUID pour la synchronisation
        $database->ldap_guid = $ldap->getConvertedGuid();

        // Synchroniser l'image de profil
        $this->syncProfilePhoto($ldap, $database);

        // Mot de passe par défaut pour les nouveaux utilisateurs
        if (!$database->exists) {
            $database->password = bcrypt('password');
        }

        // Date de synchronisation
        $database->last_sync_at = now();

        // Sauvegarder l'utilisateur
        $database->save();

        // Attribuer le rôle automatiquement basé sur la fonction
        $this->assignRoleBasedOnFunction($database);

        // Log de synchronisation réussie
        Log::info('LDAP User synced successfully', [
            'matricule' => $matricule,
            'email' => $database->email,
            'nom' => $database->nom,
            'prenom' => $database->prenom,
            'poste' => $database->poste,
            'service' => $database->service,
        ]);
    }

    /**
     * Synchronise la photo de profil depuis LDAP
     */
    protected function syncProfilePhoto(LdapUser $ldap, User $database): void
    {
        $ldapImage = $ldap->getFirstAttribute('thumbnailPhoto');

        if ($ldapImage) {
            // Option 1: Enregistrer l'image en tant que fichier sur le serveur
            if (!empty($database->matricule)) {
                $imagePath = 'profil/' . $database->matricule . '.jpg';

                // Créer le dossier s'il n'existe pas
                $profileDir = public_path('profil');
                if (!file_exists($profileDir)) {
                    mkdir($profileDir, 0755, true);
                }

                file_put_contents(public_path($imagePath), $ldapImage);
                $database->photo = $imagePath;
            } else {
                // Option 2: Si pas de matricule, sauvegarder en base64
                $imageBase64 = base64_encode($ldapImage);
                $database->photo = 'data:image/jpeg;base64,' . $imageBase64;
            }
        }
    }

    /**
     * Attribue automatiquement un rôle basé sur la fonction/poste de l'utilisateur
     * Peut être modifié par l'admin par la suite
     */
    protected function assignRoleBasedOnFunction(User $user): void
    {
        // Ne pas écraser les rôles si déjà attribués par un admin (sauf demandeur qui est le défaut)
        $currentRoles = $user->getRoleNames()->toArray();
        
        // Si l'utilisateur a déjà un rôle autre que 'demandeur', on conserve
        $nonDefaultRoles = array_filter($currentRoles, fn($role) => !in_array($role, ['demandeur', 'agent']));
        if (!empty($nonDefaultRoles)) {
            Log::info('Role already assigned by admin, skipping auto-assignment', [
                'matricule' => $user->matricule,
                'current_roles' => $currentRoles,
            ]);
            return;
        }

        $roleService = app(RoleAssignmentService::class);
        $suggestedRole = $roleService->suggestRoleFromFunction($user->poste);

        // Attribuer le nouveau rôle (demandeur par défaut si aucune correspondance)
        $user->syncRoles([$suggestedRole]);

        Log::info('Role auto-assigned based on function', [
            'user_id' => $user->id,
            'matricule' => $user->matricule,
            'poste' => $user->poste,
            'assigned_role' => $suggestedRole,
        ]);
    }
}
