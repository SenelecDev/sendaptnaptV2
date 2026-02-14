<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Ldap\LdapAttributeHandler;
use App\Models\User;
use App\Services\OracleHRService;
use App\Services\RoleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class LoginController extends Controller
{
    protected OracleHRService $oracleService;
    protected RoleAssignmentService $roleService;

    public function __construct(OracleHRService $oracleService, RoleAssignmentService $roleService)
    {
        $this->oracleService = $oracleService;
        $this->roleService = $roleService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->getRedirectRouteForUser(Auth::user()));
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string',
            'password' => 'required|string',
        ], [
            'matricule.required' => 'Le matricule est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $matricule = strtoupper(trim($request->input('matricule')));
        $password = $request->input('password');

        Log::info('Login attempt', ['matricule' => $matricule]);

        // 1. Fallback to local authentication FIRST (for dev/testing and admin)
        $user = User::whereRaw('UPPER(matricule) = ?', [$matricule])->first();
        if ($user && Hash::check($password, $user->password)) {
            Log::info('Local authentication successful', ['matricule' => $matricule]);
            return $this->handleSuccessfulLogin($request, $matricule);
        }

        // 2. Try LDAP authentication (if enabled)
        if (env('LDAP_ENABLED', false)) {
            if ($this->authenticateViaLdap($matricule, $password)) {
                return $this->handleSuccessfulLogin($request, $matricule);
            }
        }

        Log::warning('Login failed', ['matricule' => $matricule]);

        return back()
            ->withInput($request->only('matricule'))
            ->withErrors([
                'matricule' => 'Identifiants incorrects. Veuillez vérifier votre matricule et mot de passe.',
            ]);
    }

    /**
     * Authenticate via LDAP
     */
    protected function authenticateViaLdap(string $matricule, string $password): bool
    {
        try {
            $connection = Container::getDefaultConnection();
            
            // 1. Rechercher l'utilisateur par matricule (samaccountname, employeeNumber ou company)
            $ldapUser = LdapUser::where('samaccountname', '=', $matricule)
                ->orWhere('employeenumber', '=', $matricule)
                ->first();

            // 2. Si non trouvé, essayer dans le champ company (format: "SENELEC XXXXX")
            if (!$ldapUser) {
                $ldapUser = LdapUser::whereContains('company', $matricule)->first();
            }

            // 3. Si toujours pas trouvé, chercher l'email dans Oracle HR puis dans LDAP
            if (!$ldapUser && env('ORACLE_ENABLED', false)) {
                $ldapUser = $this->findLdapUserViaOracle($matricule);
            }

            if (!$ldapUser) {
                Log::info('LDAP user not found', ['matricule' => $matricule]);
                return false;
            }

            Log::info('LDAP user found', [
                'matricule' => $matricule,
                'dn' => $ldapUser->getDn(),
                'samaccountname' => $ldapUser->getFirstAttribute('samaccountname'),
                'company' => $ldapUser->getFirstAttribute('company'),
                'mail' => $ldapUser->getFirstAttribute('mail'),
            ]);

            // Tentative d'authentification avec le DN de l'utilisateur trouvé
            if (!$connection->auth()->attempt($ldapUser->getDn(), $password)) {
                Log::info('LDAP bind failed - wrong password', [
                    'matricule' => $matricule,
                    'dn' => $ldapUser->getDn(),
                ]);
                return false;
            }

            // Synchroniser l'utilisateur depuis LDAP
            $this->syncUserFromLdap($ldapUser, $matricule);

            Log::info('LDAP authentication successful', ['matricule' => $matricule]);
            return true;

        } catch (\Exception $e) {
            Log::error('LDAP authentication error', [
                'matricule' => $matricule,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Find LDAP user via Oracle HR email
     * Pour les utilisateurs qui n'ont pas de matricule dans LDAP mais ont un email dans Oracle
     */
    protected function findLdapUserViaOracle(string $matricule): ?LdapUser
    {
        try {
            // Chercher l'employé dans Oracle HR par matricule
            $oracleData = $this->oracleService->getEmployeeByMatricule($matricule);

            if (!$oracleData) {
                Log::info('User not found in Oracle HR', ['matricule' => $matricule]);
                return null;
            }

            $email = $oracleData['email'] ?? null;

            if (empty($email)) {
                Log::info('No email found in Oracle HR for matricule', ['matricule' => $matricule]);
                return null;
            }

            Log::info('Found email in Oracle HR, searching in LDAP', [
                'matricule' => $matricule,
                'email' => $email,
            ]);

            // Chercher l'utilisateur LDAP par email
            $ldapUser = LdapUser::where('mail', '=', $email)->first();

            if ($ldapUser) {
                Log::info('LDAP user found via Oracle email', [
                    'matricule' => $matricule,
                    'email' => $email,
                    'dn' => $ldapUser->getDn(),
                ]);
            }

            return $ldapUser;

        } catch (\Exception $e) {
            Log::warning('Oracle lookup failed', [
                'matricule' => $matricule,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Sync user from LDAP to local database
     */
    protected function syncUserFromLdap(LdapUser $ldapUser, string $matricule): User
    {
        // Extraire le matricule depuis LDAP
        $actualMatricule = $this->extractMatriculeFromLdap($ldapUser, $matricule);

        // Chercher l'utilisateur existant
        $user = User::where('matricule', $actualMatricule)
            ->orWhere('ldap_guid', $ldapUser->getConvertedGuid())
            ->first();

        if (!$user) {
            $user = new User();
            $user->matricule = $actualMatricule;
        }

        // Synchroniser les attributs LDAP
        $user->nom = $ldapUser->getFirstAttribute('sn') ?? $user->nom ?? '';
        $user->prenom = $ldapUser->getFirstAttribute('givenname') ?? $user->prenom ?? '';
        $user->name = trim(($user->prenom ?? '') . ' ' . ($user->nom ?? ''));  // Champ name requis
        $user->email = $ldapUser->getFirstAttribute('mail') ?? $actualMatricule . '@senelec.sn';
        $user->poste = $ldapUser->getFirstAttribute('title') ?? $user->poste;
        $user->service = $ldapUser->getFirstAttribute('department') ?? $user->service;
        $user->organisation = $ldapUser->getFirstAttribute('company') ?? $user->organisation;
        $user->telephone = $ldapUser->getFirstAttribute('telephonenumber') ?? $user->telephone;
        $user->ldap_username = $ldapUser->getFirstAttribute('samaccountname');
        $user->ldap_guid = $ldapUser->getConvertedGuid();
        $user->entreprise = 'SENELEC';
        $user->last_sync_at = now();
        
        // Synchroniser la photo si disponible (sauvegarde en fichier)
        $thumbnailPhoto = $ldapUser->getFirstAttribute('thumbnailphoto');
        if ($thumbnailPhoto) {
            $photoPath = $this->saveProfilePhoto($thumbnailPhoto, $actualMatricule);
            if ($photoPath) {
                $user->photo = $photoPath;
            }
        }

        // Mot de passe par défaut pour les nouveaux utilisateurs
        if (!$user->exists) {
            $user->password = bcrypt('password');
            $user->is_active = true;
        }

        $user->save();

        // Synchroniser avec Oracle si activé
        if (env('ORACLE_ENABLED', false)) {
            $this->syncWithOracle($user);
        }

        // Attribuer le rôle automatiquement basé sur la fonction
        $this->roleService->autoAssignRole($user);

        return $user;
    }

    /**
     * Extract matricule from LDAP user
     */
    protected function extractMatriculeFromLdap(LdapUser $ldapUser, string $fallback): string
    {
        // Priorité: employeeNumber > company (split) > samaccountname > fallback
        $employeeNumber = $ldapUser->getFirstAttribute('employeenumber');
        if (!empty($employeeNumber)) {
            return strtoupper($employeeNumber);
        }

        $company = $ldapUser->getFirstAttribute('company');
        if (!empty($company)) {
            $split = explode(' ', $company);
            if (isset($split[1]) && !empty(trim($split[1]))) {
                return strtoupper(trim($split[1]));
            }
        }

        $samaccountname = $ldapUser->getFirstAttribute('samaccountname');
        if (!empty($samaccountname)) {
            return strtoupper($samaccountname);
        }

        return strtoupper($fallback);
    }

    /**
     * Sync user data with Oracle HR
     */
    protected function syncWithOracle(User $user): void
    {
        try {
            $oracleData = $this->oracleService->getEmployeeByMatricule($user->matricule);

            if ($oracleData) {
                // Stocker la fonction Oracle séparément (source de vérité pour les rôles)
                $fonctionOracle = $oracleData['fonction'] ?? null;
                
                $user->update([
                    'oracle_person_id' => $oracleData['person_id'] ?? null,
                    'nom' => $oracleData['nom'] ?? $user->nom,
                    'prenom' => $oracleData['prenom'] ?? $user->prenom,
                    'fonction_oracle' => $fonctionOracle,  // Fonction Oracle (PER_JOBS.NAME)
                    'poste' => $fonctionOracle ?? $user->poste,  // Mettre à jour poste aussi
                    'direction' => $oracleData['direction'] ?? $user->direction,
                    'departement' => $oracleData['departement'] ?? $user->departement,
                    'service' => $oracleData['service'] ?? $user->service,
                    'telephone' => $oracleData['telephone'] ?? $user->telephone,
                    'oracle_synced_at' => now(),
                ]);

                Log::info('User synced with Oracle HR', [
                    'matricule' => $user->matricule,
                    'fonction_oracle' => $fonctionOracle,
                ]);

                // Attribuer le rôle basé sur la fonction Oracle
                $this->roleService->syncRoleFromOracle($user, $oracleData);
            }
        } catch (\Exception $e) {
            Log::warning('Oracle sync failed', [
                'matricule' => $user->matricule,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle successful login
     */
    protected function handleSuccessfulLogin(Request $request, string $matricule)
    {
        $user = User::whereRaw('UPPER(matricule) = ?', [strtoupper($matricule)])->first();

        if (!$user) {
            Log::error('User not found after authentication', ['matricule' => $matricule]);
            return back()->withErrors([
                'matricule' => 'Erreur lors de la récupération du profil utilisateur.',
            ]);
        }

        // S'assurer que l'utilisateur a au moins le rôle demandeur
        if ($user->roles->isEmpty()) {
            $user->assignRole('demandeur');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Update last activity
        $user->update(['last_activity_at' => now()]);

        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'matricule' => $user->matricule,
            'roles' => $user->getRoleNames()->toArray(),
        ]);

        // Redirect based on user role
        $redirectRoute = $this->getRedirectRouteForUser($user);

        return redirect()->intended($redirectRoute);
    }

    /**
     * Get redirect route based on user's primary role
     */
    protected function getRedirectRouteForUser(User $user): string
    {
        // Priority order for dashboard redirection
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        if ($user->hasRole('desa')) {
            return route('desa.dashboard');
        }
        if ($user->hasRole('verificateur')) {
            return route('verificateur.dashboard');
        }
        if ($user->hasRole('valideur')) {
            return route('valideur.notes.index');
        }
        if ($user->hasRole('operateur') || $user->hasRole('operateurchef')) {
            return route('operateur.notes.index');
        }
        if ($user->hasRole('directeur')) {
            return route('directeur.dashboard');
        }
        if ($user->hasRole('demandeur')) {
            return route('demandeur.dashboard');
        }
        
        // Default fallback
        return route('dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId]);

        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Save profile photo from LDAP to file
     * 
     * @param string $photoData Binary photo data from LDAP thumbnailPhoto
     * @param string $matricule User matricule for filename
     * @return string|null Path to saved photo or null on failure
     */
    protected function saveProfilePhoto(string $photoData, string $matricule): ?string
    {
        try {
            // Créer le dossier profil s'il n'existe pas
            $profilPath = public_path('profil');
            if (!is_dir($profilPath)) {
                mkdir($profilPath, 0755, true);
            }

            // Déterminer l'extension du fichier (jpg par défaut)
            $extension = 'jpg';
            
            // Vérifier le type d'image
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($photoData);
            
            if ($mimeType === 'image/png') {
                $extension = 'png';
            } elseif ($mimeType === 'image/gif') {
                $extension = 'gif';
            }

            // Nom du fichier: matricule.extension
            $filename = strtoupper($matricule) . '.' . $extension;
            $fullPath = $profilPath . DIRECTORY_SEPARATOR . $filename;

            // Sauvegarder le fichier
            file_put_contents($fullPath, $photoData);

            Log::info('Profile photo saved', [
                'matricule' => $matricule,
                'path' => 'profil/' . $filename,
                'size' => strlen($photoData),
            ]);

            // Retourner le chemin relatif pour stockage en BDD
            return 'profil/' . $filename;

        } catch (\Exception $e) {
            Log::warning('Failed to save profile photo', [
                'matricule' => $matricule,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
