<?php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Traits\SearchableTrait;
use App\Models\Demande;
use App\Models\Note;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Observation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DirecteurController extends Controller
{
    use SearchableTrait;
    /**
     * Dashboard avec statistiques complètes pour le directeur
     */
    public function dashboard(Request $request)
    {
        // Récupération des filtres
        $filtre = $request->get('filtre', 'tout'); // semaine, mois, annee, tout
        $semaine = $request->get('semaine');
        $annee = $request->get('annee', date('Y'));
        
        // Calcul des dates selon le filtre
        $dateDebut = null;
        $dateFin = null;
        
        switch ($filtre) {
            case 'semaine':
                if ($semaine) {
                    $dateDebut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
                    $dateFin = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();
                } else {
                    $dateDebut = Carbon::now()->startOfWeek();
                    $dateFin = Carbon::now()->endOfWeek();
                }
                break;
            case 'mois':
                $mois = $request->get('mois', date('m'));
                $dateDebut = Carbon::createFromDate($annee, $mois, 1)->startOfMonth();
                $dateFin = Carbon::createFromDate($annee, $mois, 1)->endOfMonth();
                break;
            case 'annee':
                $dateDebut = Carbon::createFromDate($annee, 1, 1)->startOfYear();
                $dateFin = Carbon::createFromDate($annee, 12, 31)->endOfYear();
                break;
            default:
                // Tout - pas de filtre de date
                break;
        }

        // ===== STATISTIQUES DAPT =====
        $statsDapt = [
            'total' => $this->countWithDateFilter(Demande::query(), $dateDebut, $dateFin),
            'creees' => $this->countWithDateFilter(Demande::where('statut', Demande::STATUT_CREEE), $dateDebut, $dateFin),
            'brouillon' => $this->countWithDateFilter(Demande::where('statut', Demande::STATUT_BROUILLON), $dateDebut, $dateFin),
            'en_cours' => $this->countWithDateFilter(Demande::where('statut', Demande::STATUT_EN_COURS), $dateDebut, $dateFin),
            'acceptees' => $this->countWithDateFilter(Demande::where('statut', Demande::STATUT_ACCEPTEE), $dateDebut, $dateFin),
            'retournees' => $this->countWithDateFilter(Demande::where('statut', Demande::STATUT_RETOURNEE), $dateDebut, $dateFin),
        ];

        // ===== STATISTIQUES NAPT =====
        $statsNapt = [
            'total' => $this->countWithDateFilter(Note::query(), $dateDebut, $dateFin),
            'brouillon' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_BROUILLON), $dateDebut, $dateFin),
            'en_etude' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_EN_ETUDE), $dateDebut, $dateFin),
            'en_verification' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION), $dateDebut, $dateFin),
            'verifiees' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_VERIFIEE), $dateDebut, $dateFin),
            'en_validation' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_EN_ATTENTE_VALIDATION), $dateDebut, $dateFin),
            'validees' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_VALIDEE), $dateDebut, $dateFin),
            'en_execution' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_EN_COURS_EXECUTION), $dateDebut, $dateFin),
            'executees' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_EXECUTEE), $dateDebut, $dateFin),
            'retournees' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_RETOURNEE), $dateDebut, $dateFin),
            'annulees' => $this->countWithDateFilter(Note::where('statut', Note::STATUT_ANNULEE), $dateDebut, $dateFin),
        ];

        // ===== STATISTIQUES UTILISATEURS =====
        $statsUsers = [
            'total' => User::count(),
            'actifs' => User::where('is_active', true)->count(),
            'par_role' => [
                'admin' => User::role('admin')->count(),
                'demandeur' => User::role('demandeur')->count(),
                'desa' => User::role('desa')->count(),
                'verificateur' => User::role('verificateur')->count(),
                'valideur' => User::role('valideur')->count(),
                'operateurchef' => User::role('operateurchef')->count(),
                'operateur' => User::role('operateur')->count(),
                'directeur' => User::role('directeur')->count(),
            ],
        ];

        // ===== STATISTIQUES PAR GROUPE =====
        $statsGroupes = Groupe::withCount('users')
            ->get()
            ->map(function ($groupe) use ($dateDebut, $dateFin) {
                $demandesQuery = Demande::whereHas('demandeur', fn($q) => $q->where('groupe_id', $groupe->id));
                
                return [
                    'groupe' => $groupe,
                    'users_count' => $groupe->users_count,
                    'demandes_count' => $this->countWithDateFilter($demandesQuery, $dateDebut, $dateFin),
                ];
            })
            ->filter(fn($item) => $item['demandes_count'] > 0 || $item['users_count'] > 0)
            ->sortByDesc('demandes_count')
            ->take(10);

        // ===== CALCUL DES TAUX =====
        $tauxDapt = $statsDapt['total'] > 0 
            ? round(($statsDapt['acceptees'] / $statsDapt['total']) * 100, 1) 
            : 0;
        
        $tauxNapt = $statsNapt['total'] > 0 
            ? round(($statsNapt['executees'] / $statsNapt['total']) * 100, 1) 
            : 0;

        // ===== DONNÉES POUR LES FILTRES =====
        $semainesDisponibles = [];
        for ($i = 1; $i <= 53; $i++) {
            $semainesDisponibles[$i] = 'Semaine ' . $i;
        }
        
        $anneesDisponibles = range(date('Y') - 2, date('Y'));

        return view('directeur.dashboard', compact(
            'statsDapt',
            'statsNapt',
            'statsUsers',
            'statsGroupes',
            'tauxDapt',
            'tauxNapt',
            'filtre',
            'semaine',
            'annee',
            'dateDebut',
            'dateFin',
            'semainesDisponibles',
            'anneesDisponibles'
        ));
    }

    /**
     * Liste des DAPT en lecture seule
     */
    public function dapt(Request $request)
    {
        $query = Demande::with(['demandeur.groupe', 'chargeTravaux', 'traite']);
        
        // Recherche
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['numero_demande', 'designation', 'lieu_execution'], []);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par dates
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $annee = $request->get('annee', date('Y'));
            $dateDebut = Carbon::now()->setISODate($annee, $request->semaine)->startOfWeek();
            $dateFin = Carbon::now()->setISODate($annee, $request->semaine)->endOfWeek();
            $query->whereBetween('date', [$dateDebut, $dateFin]);
        }
        
        $demandes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistiques rapides
        $stats = [
            'total' => Demande::count(),
            'creees' => Demande::where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => Demande::where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => Demande::where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => Demande::where('statut', Demande::STATUT_RETOURNEE)->count(),
        ];
        
        return view('directeur.dapt.index', compact('demandes', 'stats'));
    }

    /**
     * Détail d'une DAPT
     */
    public function showDapt(Demande $demande)
    {
        $demande->load(['demandeur.groupe', 'chargeTravaux', 'traite', 'note', 'histories.user']);
        return view('directeur.dapt.show', compact('demande'));
    }

    /**
     * Liste des NAPT en lecture seule
     */
    public function napt(Request $request)
    {
        $query = Note::with(['demande', 'etabliPar', 'verifiePar', 'validePar']);
        
        // Recherche
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['numero_note'], ['demande' => ['numero_demande']]);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par dates
        if ($request->filled('date_debut')) {
            $query->whereDate('ddt', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('dft', '<=', $request->date_fin);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->where('numero_semaine', $request->semaine);
            if (!$request->filled('annee')) {
                $query->whereYear('ddt', date('Y'));
            }
        }
        
        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('ddt', $request->annee);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistiques rapides
        $stats = [
            'total' => Note::count(),
            'brouillon' => Note::where('statut', Note::STATUT_BROUILLON)->count(),
            'en_etude' => Note::where('statut', Note::STATUT_EN_ETUDE)->count(),
            'en_attente_verification' => Note::where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
            'verifiee' => Note::where('statut', Note::STATUT_VERIFIEE)->count(),
            'en_attente_validation' => Note::where('statut', Note::STATUT_EN_ATTENTE_VALIDATION)->count(),
            'validee' => Note::where('statut', Note::STATUT_VALIDEE)->count(),
            'en_cours_execution' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'executee' => Note::where('statut', Note::STATUT_EXECUTEE)->count(),
            'annulee' => Note::where('statut', Note::STATUT_ANNULEE)->count(),
        ];
        
        return view('directeur.napt.index', compact('notes', 'stats'));
    }

    /**
     * Détail d'une NAPT
     */
    public function showNapt(Note $note)
    {
        $note->load(['demande.demandeur', 'demande.chargeTravaux', 'etabliPar', 'verifiePar', 'validePar', 'execute', 'chargesCons', 'correspondants', 'services', 'histories.user']);
        return view('directeur.napt.show', compact('note'));
    }

    /**
     * Formulaire de feedback
     */
    public function feedback()
    {
        // Récupérer les observations envoyées par le directeur
        $observations = Observation::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('directeur.feedback.index', compact('observations'));
    }

    /**
     * Envoyer un feedback
     */
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string|min:10',
            'type' => 'required|in:suggestion,bug,question,remarque',
        ]);
        
        // Créer l'observation
        $observation = Observation::create([
            'user_id' => auth()->id(),
            'sujet' => $request->sujet,
            'description' => $request->contenu,
            'type' => $request->type,
            'priorite' => Observation::PRIORITE_NORMALE,
            'statut' => Observation::STATUT_OUVERT,
        ]);
        
        // Notifier les admins
        app(NotificationService::class)->notifyNewFeedback($observation);
        
        // Envoyer email aux membres du groupe "Experts Previsions"
        $groupeExperts = Groupe::where('nom', 'like', '%Experts%Previsions%')
            ->orWhere('nom', 'like', '%Expert%Prévision%')
            ->first();
        
        if ($groupeExperts) {
            $experts = $groupeExperts->users()->pluck('email')->toArray();
            // TODO: Envoyer l'email - Mail::to($experts)->send(new FeedbackDirecteur($observation));
        }
        
        return redirect()->route('directeur.feedback')
            ->with('success', 'Votre feedback a été envoyé avec succès.');
    }

    /**
     * Statistiques DAPT
     */
    public function statistiquesDapt(Request $request)
    {
        // Stats par mois
        $parMois = Demande::select(
            DB::raw('EXTRACT(MONTH FROM created_at)::integer as mois'),
            DB::raw('EXTRACT(YEAR FROM created_at)::integer as annee'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN statut = 'acceptée' THEN 1 ELSE 0 END) as acceptees"),
            DB::raw("SUM(CASE WHEN statut = 'retournée' THEN 1 ELSE 0 END) as retournees")
        )
        ->whereYear('created_at', $request->get('annee', now()->year))
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), DB::raw('EXTRACT(YEAR FROM created_at)'))
        ->orderBy('annee')
        ->orderBy('mois')
        ->get();
        
        // Stats par demandeur
        $parDemandeur = Demande::select(
            'demandeur_id',
            DB::raw('COUNT(*) as total')
        )
        ->with('demandeur')
        ->groupBy('demandeur_id')
        ->orderByDesc('total')
        ->limit(10)
        ->get();
        
        // Délai moyen de traitement
        $delaiRaw = DB::getDriverName() === 'pgsql'
            ? 'AVG((date_traitement::date - created_at::date)) as delai_moyen'
            : 'AVG(DATEDIFF(date_traitement, created_at)) as delai_moyen';
        $delaiMoyen = Demande::whereIn('statut', ['acceptée', 'retournée'])
            ->whereNotNull('date_traitement')
            ->selectRaw($delaiRaw)
            ->first();
        
        return view('directeur.dapt.statistiques', compact('parMois', 'parDemandeur', 'delaiMoyen'));
    }

    /**
     * Statistiques NAPT
     */
    public function statistiquesNapt(Request $request)
    {
        $annee = $request->get('annee', now()->year);
        
        // Stats par mois
        $parMois = Note::select(
            DB::raw('EXTRACT(MONTH FROM created_at)::integer as mois'),
            DB::raw('EXTRACT(YEAR FROM created_at)::integer as annee'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN statut = 'exécutée' THEN 1 ELSE 0 END) as executees"),
            DB::raw("SUM(CASE WHEN statut = 'retournée' THEN 1 ELSE 0 END) as retournees")
        )
        ->whereYear('created_at', $annee)
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), DB::raw('EXTRACT(YEAR FROM created_at)'))
        ->orderBy('annee')
        ->orderBy('mois')
        ->get();
        
        // Stats par vérificateur
        $parVerificateur = Note::select(
            'verifie_id',
            DB::raw('COUNT(*) as total')
        )
        ->with('verifie')
        ->whereNotNull('verifie_id')
        ->groupBy('verifie_id')
        ->orderByDesc('total')
        ->limit(10)
        ->get();
        
        // Stats par valideur
        $parValideur = Note::select(
            'valide_id',
            DB::raw('COUNT(*) as total')
        )
        ->with('valide')
        ->whereNotNull('valide_id')
        ->groupBy('valide_id')
        ->orderByDesc('total')
        ->limit(10)
        ->get();
        
        // Stats par opérateur
        $parOperateur = Note::select(
            'execute_id',
            DB::raw('COUNT(*) as total')
        )
        ->with('execute')
        ->whereNotNull('execute_id')
        ->groupBy('execute_id')
        ->orderByDesc('total')
        ->limit(10)
        ->get();
        
        // Délai moyen de traitement complet (de création à exécution)
        $delaiRaw = DB::getDriverName() === 'pgsql'
            ? 'AVG((drex::date - created_at::date)) as delai_moyen'
            : 'AVG(DATEDIFF(drex, created_at)) as delai_moyen';
        $delaiMoyen = Note::where('statut', 'exécutée')
            ->whereNotNull('drex')
            ->selectRaw($delaiRaw)
            ->first();
        
        // Taux de réussite (exécutées / total soumises)
        $totalSoumises = Note::whereNotIn('statut', ['brouillon', 'en étude'])->count();
        $totalExecutees = Note::where('statut', 'exécutée')->count();
        $tauxReussite = $totalSoumises > 0 ? round(($totalExecutees / $totalSoumises) * 100, 1) : 0;
        
        // GMAO vs Manuel (depuis les demandes associées)
        $parMode = Note::join('demandes', 'notes.demande_id', '=', 'demandes.id')
            ->select('demandes.mode_saisie as mode', DB::raw('COUNT(*) as total'))
            ->whereNotNull('demandes.mode_saisie')
            ->groupBy('demandes.mode_saisie')
            ->get()
            ->pluck('total', 'mode');
        
        return view('directeur.napt.statistiques', compact(
            'parMois', 
            'parVerificateur', 
            'parValideur', 
            'parOperateur',
            'delaiMoyen',
            'tauxReussite',
            'parMode',
            'annee'
        ));
    }

    /**
     * Helper pour compter avec filtre de date optionnel
     */
    private function countWithDateFilter($query, $dateDebut = null, $dateFin = null)
    {
        if ($dateDebut && $dateFin) {
            return $query->whereBetween('created_at', [$dateDebut, $dateFin])->count();
        }
        return $query->count();
    }
}
