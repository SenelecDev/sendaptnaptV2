<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OracleHRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class UserSyncController extends Controller
{
    protected OracleHRService $oracleService;

    public function __construct(OracleHRService $oracleService)
    {
        $this->oracleService = $oracleService;
        $this->middleware(function ($request, $next) {
            if (!$request->user()->isSuperAdmin()) {
                abort(403, 'Accès réservé au super administrateur.');
            }
            return $next($request);
        });
    }

    /**
     * Page de synchronisation Oracle
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'users_with_matricule' => User::whereNotNull('matricule')->where('matricule', '!=', '')->count(),
            'synced_users' => User::whereNotNull('oracle_synced_at')->count(),
            'never_synced' => User::whereNull('oracle_synced_at')->whereNotNull('matricule')->count(),
        ];

        $recentlySynced = User::whereNotNull('oracle_synced_at')
            ->orderBy('oracle_synced_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.users.sync', compact('stats', 'recentlySynced'));
    }

    /**
     * Synchroniser un utilisateur spécifique
     */
    public function syncUser(Request $request, User $user)
    {
        if (!$user->matricule) {
            return back()->with('error', 'Cet utilisateur n\'a pas de matricule.');
        }

        try {
            $oracleData = $this->oracleService->getEmployeeByMatricule($user->matricule);

            if (!$oracleData) {
                return back()->with('warning', 'Aucune donnée trouvée dans Oracle pour ce matricule.');
            }

            $user->update([
                'nom' => $oracleData['nom'] ?? $user->nom,
                'prenom' => $oracleData['prenom'] ?? $user->prenom,
                'name' => trim(($oracleData['prenom'] ?? $user->prenom ?? '') . ' ' . ($oracleData['nom'] ?? $user->nom ?? '')),
                'email' => $oracleData['email'] ?? $user->email,
                'poste' => $oracleData['poste'] ?? $oracleData['fonction'] ?? $user->poste,
                'fonction_oracle' => $oracleData['fonction'] ?? $user->fonction_oracle,
                'organisation' => $oracleData['organisation'] ?? $user->organisation,
                'service' => $oracleData['service'] ?? $user->service,
                'direction' => $oracleData['direction'] ?? $oracleData['direction_principale'] ?? $user->direction,
                'departement' => $oracleData['departement'] ?? $user->departement,
                'oracle_person_id' => $oracleData['person_id'] ?? $user->oracle_person_id,
                'oracle_synced_at' => now(),
            ]);

            return back()->with('success', "Utilisateur {$user->full_name} synchronisé avec succès.");

        } catch (\Exception $e) {
            Log::error("Sync error for user {$user->matricule}: " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la synchronisation: ' . $e->getMessage());
        }
    }

    /**
     * Synchroniser tous les utilisateurs (en arrière-plan)
     */
    public function syncAll(Request $request)
    {
        $usersCount = User::whereNotNull('matricule')
            ->where('matricule', '!=', '')
            ->count();

        $message = "Synchronisation lancée en arrière-plan pour {$usersCount} utilisateurs. Consultez les logs pour suivre la progression.";
        return $this->runSyncInBackground(['php', 'artisan', 'users:sync-oracle', '--all'], $message);
    }

    /**
     * Rechercher un employé Oracle par matricule
     */
    public function searchOracle(Request $request)
    {
        $matricule = $request->input('matricule');

        if (!$matricule) {
            return response()->json(['error' => 'Matricule requis'], 400);
        }

        try {
            $oracleData = $this->oracleService->getEmployeeByMatricule($matricule);

            if (!$oracleData) {
                return response()->json(['error' => 'Aucun employé trouvé'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $oracleData
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Importer un nouvel utilisateur depuis Oracle
     */
    public function importUser(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string',
        ]);

        $matricule = $request->input('matricule');

        // Vérifier si l'utilisateur existe déjà
        if (User::where('matricule', $matricule)->exists()) {
            return back()->with('warning', 'Un utilisateur avec ce matricule existe déjà.');
        }

        try {
            $oracleData = $this->oracleService->getEmployeeByMatricule($matricule);

            if (!$oracleData) {
                return back()->with('error', 'Aucun employé trouvé dans Oracle avec ce matricule.');
            }

            $user = User::create([
                'matricule' => $oracleData['matricule'],
                'name' => trim(($oracleData['prenom'] ?? '') . ' ' . ($oracleData['nom'] ?? '')),
                'nom' => $oracleData['nom'] ?? null,
                'prenom' => $oracleData['prenom'] ?? null,
                'email' => $oracleData['email'] ?? ($oracleData['matricule'] . '@senelec.sn'),
                'password' => bcrypt('Senelec@' . $oracleData['matricule']),
                'poste' => $oracleData['poste'] ?? $oracleData['fonction'] ?? null,
                'fonction_oracle' => $oracleData['fonction'] ?? null,
                'organisation' => $oracleData['organisation'] ?? null,
                'service' => $oracleData['service'] ?? null,
                'direction' => $oracleData['direction'] ?? $oracleData['direction_principale'] ?? null,
                'departement' => $oracleData['departement'] ?? null,
                'oracle_person_id' => $oracleData['person_id'] ?? null,
                'oracle_synced_at' => now(),
                'is_active' => true,
            ]);

            return back()->with('success', "Utilisateur {$user->full_name} importé avec succès.");

        } catch (\Exception $e) {
            Log::error("Import error for matricule {$matricule}: " . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Synchroniser les données LDAP (photos, téléphone, etc.) en arrière-plan
     */
    public function syncLdap(Request $request)
    {
        $usersCount = User::whereNotNull('matricule')
            ->where('matricule', '!=', '')
            ->count();

        $message = "Synchronisation LDAP lancée en arrière-plan pour {$usersCount} utilisateurs. Les photos de profil seront mises à jour.";
        return $this->runSyncInBackground(['php', 'artisan', 'users:sync-oracle', '--ldap'], $message);
    }

    /**
     * Synchroniser uniquement les photos LDAP (rapide) en arrière-plan
     */
    public function syncPhotos(Request $request)
    {
        $usersCount = User::whereNotNull('matricule')
            ->where('matricule', '!=', '')
            ->where(function($q) {
                $q->whereNull('photo')->orWhere('photo', '');
            })
            ->count();

        $message = "Synchronisation des photos LDAP lancée en arrière-plan pour {$usersCount} utilisateurs sans photo.";
        return $this->runSyncInBackground(['php', 'artisan', 'users:sync-oracle', '--photos'], $message);
    }

    /**
     * Importer TOUS les utilisateurs depuis Oracle + LDAP en arrière-plan
     * - Matricule d'Oracle (plus fiable)
     * - Complète avec LDAP si champ manquant
     * - Importe les photos depuis LDAP
     */
    public function importAll(Request $request)
    {
        $message = "Importation massive lancée en arrière-plan depuis Oracle et LDAP. Les matricules viennent d'Oracle, les champs manquants et photos de LDAP. Consultez les logs pour suivre la progression.";
        return $this->runSyncInBackground(['php', 'artisan', 'users:sync-oracle', '--import-all'], $message);
    }

    /**
     * Lancer la sync : envoie la réponse au client puis exécute la commande.
     * Process::start() tue l'enfant à la fin de la requête, donc on envoie
     * la réponse d'abord puis run() dans le même worker.
     */
    protected function runSyncInBackground(array $command, string $message)
    {
        session()->flash('success', $message);
        session()->save();

        $response = redirect()->back();
        $response->send();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        ignore_user_abort(true);

        Process::path(base_path())->forever()->quietly()->run($command);

        exit;
    }

    /**
     * Récupérer les logs de synchronisation en temps réel (API)
     * Lit depuis les fichiers écrits par la commande SyncOracleUsers
     */
    public function getLogs()
    {
        $logFile = storage_path('logs/sync_oracle.log');
        $statusFile = storage_path('logs/sync_oracle_status.json');

        $logs = [];
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines ?: [] as $line) {
                $decoded = json_decode($line, true);
                if ($decoded) {
                    $logs[] = $decoded;
                }
            }
        }

        $status = [
            'running' => false,
            'operation' => null,
            'progress' => 0,
            'total' => 0,
        ];
        if (file_exists($statusFile)) {
            $content = file_get_contents($statusFile);
            $decoded = json_decode($content, true);
            if ($decoded) {
                $status = array_merge($status, $decoded);
            }
        }

        return response()->json([
            'logs' => $logs,
            'status' => $status,
        ]);
    }

    /**
     * Effacer les logs de synchronisation
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/sync_oracle.log');
        $statusFile = storage_path('logs/sync_oracle_status.json');

        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }
        if (file_exists($statusFile)) {
            unlink($statusFile);
        }

        Cache::forget('sync_oracle_log');
        Cache::forget('sync_oracle_status');

        return response()->json(['success' => true]);
    }
}
