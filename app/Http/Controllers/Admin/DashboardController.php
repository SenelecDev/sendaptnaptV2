<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Note;
use App\Models\User;
use App\Models\Groupe;
use App\Models\ChargeCons;
use App\Models\Correspondant;
use App\Models\ServiceDest;
use App\Models\Observation;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with all statistics and KPIs
     */
    public function index(Request $request)
    {
        // Filtres de dates
        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateFin = $request->get('date_fin', Carbon::now()->format('Y-m-d'));
        $semaine = $request->get('semaine');
        
        // Si semaine spécifiée, calculer les dates
        if ($semaine) {
            $dateDebut = Carbon::now()->setISODate(Carbon::now()->year, $semaine)->startOfWeek()->format('Y-m-d');
            $dateFin = Carbon::now()->setISODate(Carbon::now()->year, $semaine)->endOfWeek()->format('Y-m-d');
        }

        // ===== STATISTIQUES GLOBALES =====
        $stats = [
            // Utilisateurs
            'users_total' => User::count(),
            'users_actifs' => User::where('is_active', true)->count(),
            'groupes_count' => Groupe::count(),
            
            // DAPT
            'dapt_total' => Demande::count(),
            'dapt_periode' => Demande::whereBetween('date', [$dateDebut, $dateFin])->count(),
            'dapt_creees' => Demande::where('statut', Demande::STATUT_CREEE)->count(),
            'dapt_en_cours' => Demande::where('statut', Demande::STATUT_EN_COURS)->count(),
            'dapt_acceptees' => Demande::where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'dapt_retournees' => Demande::where('statut', Demande::STATUT_RETOURNEE)->count(),
            
            // NAPT
            'napt_total' => Note::count(),
            'napt_periode' => Note::whereBetween('date', [$dateDebut, $dateFin])->count(),
            'napt_brouillon' => Note::where('statut', Note::STATUT_BROUILLON)->count(),
            'napt_en_etude' => Note::where('statut', Note::STATUT_EN_ETUDE)->count(),
            'napt_en_attente_verif' => Note::where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
            'napt_verifiees' => Note::where('statut', Note::STATUT_VERIFIEE)->count(),
            'napt_en_attente_valid' => Note::where('statut', Note::STATUT_EN_ATTENTE_VALIDATION)->count(),
            'napt_validees' => Note::where('statut', Note::STATUT_VALIDEE)->count(),
            'napt_en_execution' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'napt_executees' => Note::where('statut', Note::STATUT_EXECUTEE)->count(),
            'napt_annulees' => Note::where('statut', Note::STATUT_ANNULEE)->count(),
            
            // Référentiels
            'chargecons_count' => ChargeCons::count(),
            'correspondants_count' => Correspondant::count(),
            'servicedests_count' => ServiceDest::count(),
            
            // Observations/Feedback
            'observations_total' => Observation::count(),
            'observations_non_traitees' => Observation::where('statut', 'nouveau')->count(),
            
            // Intérims
            'interims_actifs' => Absence::where('date_debut', '<=', now())
                                        ->where('date_fin', '>=', now())
                                        ->count(),
        ];

        // ===== STATISTIQUES PAR PÉRIODE =====
        $statsPeriode = [
            'dapt' => [
                'creees' => Demande::whereBetween('date', [$dateDebut, $dateFin])
                                   ->where('statut', Demande::STATUT_CREEE)->count(),
                'acceptees' => Demande::whereBetween('date', [$dateDebut, $dateFin])
                                      ->where('statut', Demande::STATUT_ACCEPTEE)->count(),
                'retournees' => Demande::whereBetween('date', [$dateDebut, $dateFin])
                                       ->where('statut', Demande::STATUT_RETOURNEE)->count(),
            ],
            'napt' => [
                'creees' => Note::whereBetween('date', [$dateDebut, $dateFin])->count(),
                'executees' => Note::whereBetween('date', [$dateDebut, $dateFin])
                                   ->where('statut', Note::STATUT_EXECUTEE)->count(),
                'annulees' => Note::whereBetween('date', [$dateDebut, $dateFin])
                                  ->where('statut', Note::STATUT_ANNULEE)->count(),
            ],
        ];

        // ===== DERNIÈRES ACTIVITÉS =====
        $dernieresDapt = Demande::with(['demandeur.groupe'])
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();

        $dernieresNapt = Note::with(['demande', 'etabliPar'])
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        // ===== OBSERVATIONS NON TRAITÉES =====
        $observationsRecentes = Observation::with('user')
                                           ->where('statut', 'nouveau')
                                           ->orderBy('created_at', 'desc')
                                           ->take(5)
                                           ->get();

        // ===== GRAPHIQUES - Évolution mensuelle =====
        $evolutionMensuelle = $this->getEvolutionMensuelle();

        // ===== RÉPARTITION PAR GROUPE (via demandeur) =====
        $repartitionGroupes = Groupe::withCount(['users as demandes_count' => function ($query) {
            $query->whereHas('demandes');
        }])->get()->map(function ($groupe) {
            return [
                'groupe' => $groupe,
                'total' => Demande::whereHas('demandeur', function ($q) use ($groupe) {
                    $q->where('groupe_id', $groupe->id);
                })->count()
            ];
        })->filter(fn($item) => $item['total'] > 0);

        // ===== TAUX DE TRAITEMENT =====
        $tauxTraitement = [
            'dapt' => $stats['dapt_total'] > 0 
                ? round(($stats['dapt_acceptees'] / $stats['dapt_total']) * 100, 1) 
                : 0,
            'napt' => $stats['napt_total'] > 0 
                ? round((($stats['napt_executees'] + $stats['napt_annulees']) / $stats['napt_total']) * 100, 1)
                : 0,
        ];

        // ===== SEMAINES DISPONIBLES =====
        $semainesDisponibles = [];
        for ($i = 1; $i <= Carbon::now()->weekOfYear; $i++) {
            $semainesDisponibles[$i] = 'Semaine ' . $i;
        }

        return view('admin.dashboard', compact(
            'stats',
            'statsPeriode',
            'dernieresDapt',
            'dernieresNapt',
            'observationsRecentes',
            'evolutionMensuelle',
            'repartitionGroupes',
            'tauxTraitement',
            'dateDebut',
            'dateFin',
            'semaine',
            'semainesDisponibles'
        ));
    }

    /**
     * Get monthly evolution data for charts
     */
    private function getEvolutionMensuelle()
    {
        $mois = [];
        $daptData = [];
        $naptData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois[] = $date->translatedFormat('M Y');
            
            $daptData[] = Demande::whereYear('date', $date->year)
                                 ->whereMonth('date', $date->month)
                                 ->count();
            
            $naptData[] = Note::whereYear('date', $date->year)
                              ->whereMonth('date', $date->month)
                              ->count();
        }

        return [
            'labels' => $mois,
            'dapt' => $daptData,
            'napt' => $naptData,
        ];
    }

    /**
     * Export dashboard data
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel/PDF export
        return back()->with('info', 'Export en cours de développement');
    }
}
