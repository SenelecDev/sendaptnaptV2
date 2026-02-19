<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Traits\SearchableTrait;
use App\Models\Note;
use App\Models\Observation;
use App\Models\Groupe;
use App\Exports\Desa\DashboardExport;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class DemandeController extends Controller
{
    use SearchableTrait;
    /**
     * Display the DESA dashboard.
     */
    public function dashboard(Request $request)
    {
        // Filtres
        $filtre = $request->get('filtre', 'tout'); // tout, semaine, mois, annee
        $periode = $request->get('periode', 'mois'); // Pour les graphiques: semaine, mois, annee
        $groupeIds = $request->get('groupe_ids', []); // Support multiple groupes
        $groupeIds = is_array($groupeIds) ? array_filter($groupeIds) : [];
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $mois = $request->get('mois', Carbon::now()->month);
        $annee = $request->get('annee', Carbon::now()->year);
        
        // Calculer les dates de début et fin selon le filtre
        $dateDebut = null;
        $dateFin = null;
        
        switch ($filtre) {
            case 'semaine':
                $dateDebut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
                $dateFin = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();
                break;
            case 'mois':
                $dateDebut = Carbon::createFromDate($annee, $mois, 1)->startOfMonth();
                $dateFin = Carbon::createFromDate($annee, $mois, 1)->endOfMonth();
                break;
            case 'annee':
                $dateDebut = Carbon::createFromDate($annee, 1, 1)->startOfYear();
                $dateFin = Carbon::createFromDate($annee, 12, 31)->endOfYear();
                break;
        }
        
        // Builder de base pour les demandes avec filtres
        $demandesQuery = Demande::query();
        $notesQuery = Note::query();
        
        if ($dateDebut && $dateFin) {
            $demandesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
            $notesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }
        
        if (!empty($groupeIds)) {
            $demandesQuery->whereHas('demandeur', function($q) use ($groupeIds) {
                $q->whereIn('groupe_id', $groupeIds);
            });
            $notesQuery->whereHas('demande.demandeur', function($q) use ($groupeIds) {
                $q->whereIn('groupe_id', $groupeIds);
            });
        }
        
        // Statistiques des demandes
        $demandesStats = [
            'total' => (clone $demandesQuery)->count(),
            'recues' => (clone $demandesQuery)->where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => (clone $demandesQuery)->where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => (clone $demandesQuery)->where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => (clone $demandesQuery)->where('statut', Demande::STATUT_RETOURNEE)->count(),
        ];
        
        // Statistiques des notes
        $notesStats = [
            'total' => (clone $notesQuery)->count(),
            'brouillon' => (clone $notesQuery)->where('statut', Note::STATUT_BROUILLON)->count(),
            'en_etude' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ETUDE)->count(),
            'en_attente_verification' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
            'verifiees' => (clone $notesQuery)->where('statut', Note::STATUT_VERIFIEE)->count(),
            'en_attente_validation' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ATTENTE_VALIDATION)->count(),
            'validees' => (clone $notesQuery)->where('statut', Note::STATUT_VALIDEE)->count(),
            'en_cours_execution' => (clone $notesQuery)->where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'executees' => (clone $notesQuery)->where('statut', Note::STATUT_EXECUTEE)->count(),
            'retournees' => (clone $notesQuery)->where('statut', Note::STATUT_RETOURNEE)->count(),
            'annulees' => (clone $notesQuery)->where('statut', Note::STATUT_ANNULEE)->count(),
        ];
        
        // Statistiques des observations
        $observationsStats = [
            'total' => Observation::count(),
            'non_lues' => Observation::where('lu', false)->count(),
        ];
        
        // Graph data for Notes (by period) - adapté au filtre
        $graphData = $this->getNotesGraphData($periode, $groupeIds, $dateDebut, $dateFin);
        
        // Si plusieurs groupes sélectionnés, préparer les données de comparaison
        $compareData = null;
        $compareGraphData = null;
        if (count($groupeIds) > 1) {
            $compareData = $this->getGroupesCompareData($groupeIds, $dateDebut, $dateFin);
            $compareGraphData = $this->getGroupesCompareGraphData($groupeIds, $dateDebut, $dateFin);
        }
        
        // Top groups creating DAPTs (avec filtres de période)
        $topGroupesQuery = Groupe::withCount(['demandes' => function($q) use ($dateDebut, $dateFin) {
            if ($dateDebut && $dateFin) {
                $q->whereBetween('demandes.created_at', [$dateDebut, $dateFin]);
            }
        }]);
        
        $topGroupes = $topGroupesQuery
            ->whereHas('demandes')
            ->orderByDesc('demandes_count')
            ->take(5)
            ->get();
        
        // Top groupes avec DAPT retournées (filtrable par période) + nb renvois cumulés
        $topGroupesRetourneesQuery = Groupe::query()
            ->select('groupes.*')
            ->selectSub(function ($q) use ($dateDebut, $dateFin) {
                $q->from('demandes')
                    ->join('users', 'demandes.demandeur_id', '=', 'users.id')
                    ->whereColumn('users.groupe_id', 'groupes.id')
                    ->where('demandes.statut', Demande::STATUT_RETOURNEE);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('demandes.created_at', [$dateDebut, $dateFin]);
                }
                $q->selectRaw('COUNT(*)');
            }, 'demandes_retournees_count')
            ->selectSub(function ($q) use ($dateDebut, $dateFin) {
                $q->from('demandes')
                    ->join('users', 'demandes.demandeur_id', '=', 'users.id')
                    ->whereColumn('users.groupe_id', 'groupes.id');
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('demandes.created_at', [$dateDebut, $dateFin]);
                }
                $q->selectRaw('COALESCE(SUM(demandes.nb_retours), 0)');
            }, 'total_renvois');

        $topGroupesRetournees = $topGroupesRetourneesQuery
            ->whereHas('demandes', function($q) use ($dateDebut, $dateFin) {
                $q->where(function($sub) {
                    $sub->where('statut', Demande::STATUT_RETOURNEE)
                        ->orWhere('nb_retours', '>', 0);
                });
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('demandes.created_at', [$dateDebut, $dateFin]);
                }
            })
            ->orderByDesc('total_renvois')
            ->take(10)
            ->get();
        
        // All groupes for filter
        $groupes = Groupe::orderBy('nom')->get();
        
        // Semaines disponibles pour le filtre
        $semainesDisponibles = [];
        for ($i = 1; $i <= 52; $i++) {
            $startOfWeek = Carbon::now()->setISODate($annee, $i)->startOfWeek();
            $endOfWeek = Carbon::now()->setISODate($annee, $i)->endOfWeek();
            $semainesDisponibles[$i] = "Semaine $i ({$startOfWeek->format('d/m')} - {$endOfWeek->format('d/m')})";
        }
        
        // Années disponibles
        $anneesDisponibles = range(Carbon::now()->year - 2, Carbon::now()->year + 1);
        
        // Dernières demandes reçues (avec filtre groupe)
        $dernieresDemandesQuery = Demande::where('statut', Demande::STATUT_CREEE)->with('demandeur');
        if (!empty($groupeIds)) {
            $dernieresDemandesQuery->whereHas('demandeur', function($q) use ($groupeIds) {
                $q->whereIn('groupe_id', $groupeIds);
            });
        }
        $dernieresDemandes = $dernieresDemandesQuery->orderBy('created_at', 'desc')->take(5)->get();
        
        // Dernières notes (avec filtre groupe)
        $dernieresNotesQuery = Note::with(['demande.demandeur', 'etabliPar']);
        if (!empty($groupeIds)) {
            $dernieresNotesQuery->whereHas('demande.demandeur', function($q) use ($groupeIds) {
                $q->whereIn('groupe_id', $groupeIds);
            });
        }
        $dernieresNotes = $dernieresNotesQuery->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('desa.dashboard', compact(
            'demandesStats', 
            'notesStats', 
            'observationsStats',
            'dernieresDemandes',
            'dernieresNotes',
            'graphData',
            'compareData',
            'compareGraphData',
            'periode',
            'filtre',
            'semaine',
            'mois',
            'annee',
            'groupeIds',
            'dateDebut',
            'dateFin',
            'topGroupes',
            'topGroupesRetournees',
            'groupes',
            'semainesDisponibles',
            'anneesDisponibles'
        ));
    }

    /**
     * Export dashboard statistics to Excel.
     */
    public function exportDashboard(Request $request)
    {
        $data = $this->getDashboardExportData($request);
        $periodeLabel = $data['dateDebut'] && $data['dateFin']
            ? $data['dateDebut']->format('d/m/Y') . ' - ' . $data['dateFin']->format('d/m/Y')
            : 'Toutes les données';

        $filename = 'Statistiques_Dashboard_DESA_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new DashboardExport(
                $data['demandesStats'],
                $data['notesStats'],
                $data['topGroupesRetournees'],
                $data['graphData'],
                $data['compareData'],
                $periodeLabel
            ),
            $filename
        );
    }

    /**
     * Get dashboard data for export (same logic as dashboard, filtered subset).
     */
    protected function getDashboardExportData(Request $request): array
    {
        $filtre = $request->get('filtre', 'tout');
        $periode = $request->get('periode', 'mois');
        $groupeIds = $request->get('groupe_ids', []);
        $groupeIds = is_array($groupeIds) ? array_filter($groupeIds) : [];
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $mois = $request->get('mois', Carbon::now()->month);
        $annee = $request->get('annee', Carbon::now()->year);

        $dateDebut = null;
        $dateFin = null;
        switch ($filtre) {
            case 'semaine':
                $dateDebut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
                $dateFin = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();
                break;
            case 'mois':
                $dateDebut = Carbon::createFromDate($annee, $mois, 1)->startOfMonth();
                $dateFin = Carbon::createFromDate($annee, $mois, 1)->endOfMonth();
                break;
            case 'annee':
                $dateDebut = Carbon::createFromDate($annee, 1, 1)->startOfYear();
                $dateFin = Carbon::createFromDate($annee, 12, 31)->endOfYear();
                break;
        }

        $demandesQuery = Demande::query();
        $notesQuery = Note::query();
        if ($dateDebut && $dateFin) {
            $demandesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
            $notesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
        }
        if (!empty($groupeIds)) {
            $demandesQuery->whereHas('demandeur', fn($q) => $q->whereIn('groupe_id', $groupeIds));
            $notesQuery->whereHas('demande.demandeur', fn($q) => $q->whereIn('groupe_id', $groupeIds));
        }

        $demandesStats = [
            'total' => (clone $demandesQuery)->count(),
            'recues' => (clone $demandesQuery)->where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => (clone $demandesQuery)->where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => (clone $demandesQuery)->where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => (clone $demandesQuery)->where('statut', Demande::STATUT_RETOURNEE)->count(),
        ];
        $notesStats = [
            'total' => (clone $notesQuery)->count(),
            'brouillon' => (clone $notesQuery)->where('statut', Note::STATUT_BROUILLON)->count(),
            'en_etude' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ETUDE)->count(),
            'en_attente_verification' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
            'verifiees' => (clone $notesQuery)->where('statut', Note::STATUT_VERIFIEE)->count(),
            'en_attente_validation' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ATTENTE_VALIDATION)->count(),
            'validees' => (clone $notesQuery)->where('statut', Note::STATUT_VALIDEE)->count(),
            'en_cours_execution' => (clone $notesQuery)->where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'executees' => (clone $notesQuery)->where('statut', Note::STATUT_EXECUTEE)->count(),
            'retournees' => (clone $notesQuery)->where('statut', Note::STATUT_RETOURNEE)->count(),
            'annulees' => (clone $notesQuery)->where('statut', Note::STATUT_ANNULEE)->count(),
        ];

        $graphData = $this->getNotesGraphData($periode, $groupeIds, $dateDebut, $dateFin);
        $compareData = count($groupeIds) > 1 ? $this->getGroupesCompareData($groupeIds, $dateDebut, $dateFin) : null;

        $topGroupesRetournees = Groupe::withCount(['demandes as demandes_retournees_count' => function($q) use ($dateDebut, $dateFin) {
            $q->where('statut', Demande::STATUT_RETOURNEE);
            if ($dateDebut && $dateFin) {
                $q->whereBetween('demandes.created_at', [$dateDebut, $dateFin]);
            }
        }])
        ->whereHas('demandes', function($q) use ($dateDebut, $dateFin) {
            $q->where('statut', Demande::STATUT_RETOURNEE);
            if ($dateDebut && $dateFin) {
                $q->whereBetween('demandes.created_at', [$dateDebut, $dateFin]);
            }
        })
        ->orderByDesc('demandes_retournees_count')
        ->take(10)
        ->get();

        return compact('demandesStats', 'notesStats', 'graphData', 'compareData', 'topGroupesRetournees', 'dateDebut', 'dateFin');
    }
    
    /**
     * Get graph data for Notes based on period and date range filter.
     */
    private function getNotesGraphData($periode, $groupeIds = [], $dateDebut = null, $dateFin = null)
    {
        $labels = [];
        $data = [
            'brouillon' => [],
            'en_etude' => [],
            'en_attente_verification' => [],
            'verifiees' => [],
            'en_attente_validation' => [],
            'validees' => [],
            'en_cours_execution' => [],
            'executees' => [],
            'retournees' => [],
            'annulees' => [],
        ];
        
        $statutMap = [
            'brouillon' => Note::STATUT_BROUILLON,
            'en_etude' => Note::STATUT_EN_ETUDE,
            'en_attente_verification' => Note::STATUT_EN_ATTENTE_VERIFICATION,
            'verifiees' => Note::STATUT_VERIFIEE,
            'en_attente_validation' => Note::STATUT_EN_ATTENTE_VALIDATION,
            'validees' => Note::STATUT_VALIDEE,
            'en_cours_execution' => Note::STATUT_EN_COURS_EXECUTION,
            'executees' => Note::STATUT_EXECUTEE,
            'retournees' => Note::STATUT_RETOURNEE,
            'annulees' => Note::STATUT_ANNULEE,
        ];
        
        // Helper function to add groupe filter
        $applyGroupeFilter = function($query) use ($groupeIds) {
            if (!empty($groupeIds)) {
                $query->whereHas('demande.demandeur', function($q) use ($groupeIds) {
                    $q->whereIn('groupe_id', $groupeIds);
                });
            }
            return $query;
        };
        
        // Si une période spécifique est filtrée, adapter les graphiques
        if ($dateDebut && $dateFin) {
            $diffInDays = $dateDebut->diffInDays($dateFin);
            
            if ($diffInDays <= 7) {
                // Afficher jour par jour pour une semaine
                $currentDate = $dateDebut->copy();
                while ($currentDate <= $dateFin) {
                    $labels[] = $currentDate->locale('fr')->isoFormat('ddd DD/MM');
                    
                    foreach (array_keys($data) as $key) {
                        $query = Note::where('statut', $statutMap[$key])
                            ->whereDate('created_at', $currentDate->toDateString());
                        $data[$key][] = $applyGroupeFilter($query)->count();
                    }
                    $currentDate->addDay();
                }
            } elseif ($diffInDays <= 31) {
                // Afficher par semaine pour un mois
                $currentDate = $dateDebut->copy()->startOfWeek();
                while ($currentDate <= $dateFin) {
                    $endOfWeek = $currentDate->copy()->endOfWeek();
                    $labels[] = 'Sem. ' . $currentDate->weekOfYear;
                    
                    foreach (array_keys($data) as $key) {
                        $query = Note::where('statut', $statutMap[$key])
                            ->whereBetween('created_at', [$currentDate, min($endOfWeek, $dateFin)]);
                        $data[$key][] = $applyGroupeFilter($query)->count();
                    }
                    $currentDate->addWeek();
                }
            } else {
                // Afficher par mois pour une année
                $currentDate = $dateDebut->copy()->startOfMonth();
                while ($currentDate <= $dateFin) {
                    $labels[] = $currentDate->locale('fr')->isoFormat('MMM YYYY');
                    
                    foreach (array_keys($data) as $key) {
                        $query = Note::where('statut', $statutMap[$key])
                            ->whereYear('created_at', $currentDate->year)
                            ->whereMonth('created_at', $currentDate->month);
                        $data[$key][] = $applyGroupeFilter($query)->count();
                    }
                    $currentDate->addMonth();
                }
            }
        } else {
            // Mode "tout" - utiliser le sélecteur de période graphique
            switch ($periode) {
                case 'semaine':
                    // Last 7 days
                    for ($i = 6; $i >= 0; $i--) {
                        $date = Carbon::now()->subDays($i);
                        $labels[] = $date->locale('fr')->isoFormat('ddd DD/MM');
                        
                        foreach (array_keys($data) as $key) {
                            $query = Note::where('statut', $statutMap[$key])
                                ->whereDate('created_at', $date->toDateString());
                            $data[$key][] = $applyGroupeFilter($query)->count();
                        }
                    }
                    break;
                    
                case 'mois':
                    // Last 4 weeks
                    for ($i = 3; $i >= 0; $i--) {
                        $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                        $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
                        $labels[] = 'Sem. ' . $startOfWeek->weekOfYear;
                        
                        foreach (array_keys($data) as $key) {
                            $query = Note::where('statut', $statutMap[$key])
                                ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                            $data[$key][] = $applyGroupeFilter($query)->count();
                        }
                    }
                    break;
                    
                case 'annee':
                    // Last 12 months
                    for ($i = 11; $i >= 0; $i--) {
                        $date = Carbon::now()->subMonths($i);
                        $labels[] = $date->locale('fr')->isoFormat('MMM YYYY');
                        
                        foreach (array_keys($data) as $key) {
                            $query = Note::where('statut', $statutMap[$key])
                                ->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month);
                            $data[$key][] = $applyGroupeFilter($query)->count();
                        }
                    }
                    break;
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => $data,
        ];
    }
    
    /**
     * Get comparison data for multiple groupes.
     */
    private function getGroupesCompareData($groupeIds, $dateDebut = null, $dateFin = null)
    {
        $compareData = [];
        $groupes = Groupe::whereIn('id', $groupeIds)->get();
        
        foreach ($groupes as $groupe) {
            $demandesQuery = Demande::whereHas('demandeur', function($q) use ($groupe) {
                $q->where('groupe_id', $groupe->id);
            });
            
            $notesQuery = Note::whereHas('demande.demandeur', function($q) use ($groupe) {
                $q->where('groupe_id', $groupe->id);
            });
            
            if ($dateDebut && $dateFin) {
                $demandesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
                $notesQuery->whereBetween('created_at', [$dateDebut, $dateFin]);
            }
            
            $compareData[] = [
                'groupe' => $groupe,
                'demandes' => [
                    'total' => (clone $demandesQuery)->count(),
                    'creees' => (clone $demandesQuery)->where('statut', Demande::STATUT_CREEE)->count(),
                    'en_cours' => (clone $demandesQuery)->where('statut', Demande::STATUT_EN_COURS)->count(),
                    'acceptees' => (clone $demandesQuery)->where('statut', Demande::STATUT_ACCEPTEE)->count(),
                    'retournees' => (clone $demandesQuery)->where('statut', Demande::STATUT_RETOURNEE)->count(),
                ],
                'notes' => [
                    'total' => (clone $notesQuery)->count(),
                    'en_etude' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ETUDE)->count(),
                    'en_verification' => (clone $notesQuery)->where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
                    'verifiees' => (clone $notesQuery)->where('statut', Note::STATUT_VERIFIEE)->count(),
                    'validees' => (clone $notesQuery)->where('statut', Note::STATUT_VALIDEE)->count(),
                    'executees' => (clone $notesQuery)->where('statut', Note::STATUT_EXECUTEE)->count(),
                    'retournees' => (clone $notesQuery)->where('statut', Note::STATUT_RETOURNEE)->count(),
                ],
            ];
        }
        
        return $compareData;
    }
    
    /**
     * Get graph data for comparing multiple groupes over time.
     */
    private function getGroupesCompareGraphData($groupeIds, $dateDebut = null, $dateFin = null)
    {
        $groupes = Groupe::whereIn('id', $groupeIds)->get();
        $labels = [];
        $datasets = [];
        
        // Couleurs pour chaque groupe
        $colors = ['#2B1444', '#B3006C', '#0A91A3', '#E87400', '#0D1CB0', '#10B981', '#EF4444', '#F59E0B'];
        
        // Déterminer les labels (dates) basé sur la période
        if ($dateDebut && $dateFin) {
            $diffInDays = $dateDebut->diffInDays($dateFin);
            
            if ($diffInDays <= 7) {
                // Jour par jour
                $currentDate = $dateDebut->copy();
                while ($currentDate <= $dateFin) {
                    $labels[] = $currentDate->locale('fr')->isoFormat('ddd DD/MM');
                    $currentDate->addDay();
                }
            } elseif ($diffInDays <= 31) {
                // Semaine par semaine
                $currentDate = $dateDebut->copy()->startOfWeek();
                while ($currentDate <= $dateFin) {
                    $labels[] = 'Sem. ' . $currentDate->weekOfYear;
                    $currentDate->addWeek();
                }
            } else {
                // Mois par mois
                $currentDate = $dateDebut->copy()->startOfMonth();
                while ($currentDate <= $dateFin) {
                    $labels[] = $currentDate->locale('fr')->isoFormat('MMM YYYY');
                    $currentDate->addMonth();
                }
            }
        } else {
            // Par défaut: 7 derniers jours
            for ($i = 6; $i >= 0; $i--) {
                $labels[] = Carbon::now()->subDays($i)->locale('fr')->isoFormat('ddd DD/MM');
            }
        }
        
        // Créer un dataset pour chaque groupe
        foreach ($groupes as $index => $groupe) {
            $data = [];
            $color = $colors[$index % count($colors)];
            
            if ($dateDebut && $dateFin) {
                $diffInDays = $dateDebut->diffInDays($dateFin);
                
                if ($diffInDays <= 7) {
                    $currentDate = $dateDebut->copy();
                    while ($currentDate <= $dateFin) {
                        $data[] = Note::whereHas('demande.demandeur', function($q) use ($groupe) {
                                $q->where('groupe_id', $groupe->id);
                            })
                            ->whereDate('created_at', $currentDate->toDateString())
                            ->count();
                        $currentDate->addDay();
                    }
                } elseif ($diffInDays <= 31) {
                    $currentDate = $dateDebut->copy()->startOfWeek();
                    while ($currentDate <= $dateFin) {
                        $endOfWeek = $currentDate->copy()->endOfWeek();
                        $data[] = Note::whereHas('demande.demandeur', function($q) use ($groupe) {
                                $q->where('groupe_id', $groupe->id);
                            })
                            ->whereBetween('created_at', [$currentDate, min($endOfWeek, $dateFin)])
                            ->count();
                        $currentDate->addWeek();
                    }
                } else {
                    $currentDate = $dateDebut->copy()->startOfMonth();
                    while ($currentDate <= $dateFin) {
                        $data[] = Note::whereHas('demande.demandeur', function($q) use ($groupe) {
                                $q->where('groupe_id', $groupe->id);
                            })
                            ->whereYear('created_at', $currentDate->year)
                            ->whereMonth('created_at', $currentDate->month)
                            ->count();
                        $currentDate->addMonth();
                    }
                }
            } else {
                // Par défaut: 7 derniers jours
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $data[] = Note::whereHas('demande.demandeur', function($q) use ($groupe) {
                            $q->where('groupe_id', $groupe->id);
                        })
                        ->whereDate('created_at', $date->toDateString())
                        ->count();
                }
            }
            
            $datasets[] = [
                'label' => $groupe->nom,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $color . '40',
                'tension' => 0.4,
                'fill' => false,
            ];
        }
        
        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    /**
     * Display a listing of demandes for DESA.
     */
    public function index(Request $request)
    {
        $query = Demande::with(['demandeur', 'note']);
        
        // Recherche (numéro, désignation, lieu, ouvrage à consigner)
        if ($request->filled('search')) {
            $driver = DB::connection()->getDriverName();
            $this->applySimpleSearch($query, $request->search,
                ['numero_demande', 'designation', 'lieu_execution', 'ouvrages_consigner_manuel'],
                [],
                function ($q, $pattern) use ($driver) {
                    if ($driver === 'mysql') {
                        $q->orWhereRaw('LOWER(CAST(COALESCE(ouvrages_consigner_gmao, "[]") AS CHAR)) LIKE ?', [$pattern]);
                    } elseif ($driver === 'pgsql') {
                        $q->orWhereRaw('LOWER(COALESCE(ouvrages_consigner_gmao::text, \'[]\')) LIKE ?', [$pattern]);
                    }
                });
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            // Mapping des clés URL vers les valeurs réelles en base de données
            $statutMapping = [
                'creee' => Demande::STATUT_CREEE,
                'en_cours' => Demande::STATUT_EN_COURS,
                'acceptee' => Demande::STATUT_ACCEPTEE,
                'retournee' => Demande::STATUT_RETOURNEE,
            ];
            $statutValue = $statutMapping[$request->statut] ?? $request->statut;
            $query->where('statut', $statutValue);
        }
        
        // Filtre par date de création
        if ($request->filled('date_creation')) {
            $query->whereDate('created_at', $request->date_creation);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->whereRaw('WEEK(created_at, 1) = ?', [$request->semaine]);
        }
        
        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('created_at', $request->annee);
        }
        
        // Filtre par groupe (via le demandeur)
        if ($request->filled('groupe')) {
            $query->whereHas('demandeur', function ($q) use ($request) {
                $q->where('groupe_id', $request->groupe);
            });
        }
        
        $demandes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Demande::count(),
            'creees' => Demande::where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => Demande::where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => Demande::where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => Demande::where('statut', Demande::STATUT_RETOURNEE)->count(),
        ];
        
        // Liste des groupes pour le filtre
        $groupes = Groupe::orderBy('nom')->get();
        
        return view('desa.demandes.index', compact('demandes', 'stats', 'groupes'));
    }

    /**
     * Display the specified demande.
     */
    public function show(Demande $demande)
    {
        $demande->load(['demandeur', 'note', 'chargeTravaux']);
        return view('desa.demandes.show', compact('demande'));
    }

    /**
     * Show the form for editing/treating the specified demande.
     */
    public function edit(Demande $demande)
    {
        $demande->load(['demandeur', 'note']);
        return view('desa.demandes.edit', compact('demande'));
    }

    /**
     * Update the specified demande (traitement DESA).
     */
    public function update(Request $request, Demande $demande)
    {
        $action = $request->input('action');
        
        if ($action === 'traiter') {
            // Mettre en cours de traitement
            $demande->statut = Demande::STATUT_EN_COURS;
            $demande->traite_id = Auth::id();
            $demande->save();
            
            return redirect()->route('desa.demandes.edit', $demande)
                             ->with('success', 'Demande mise en cours de traitement.');
        }
        
        if ($action === 'faire_napt') {
            // Valider les dates acceptées
            $validated = $request->validate([
                'dda' => 'required|date',
                'hda' => 'required',
                'dfa' => 'required|date|after_or_equal:dda',
                'hfa' => 'required',
            ]);
            
            // Mettre à jour la demande avec les dates acceptées
            $demande->dda = $validated['dda'];
            $demande->hda = $validated['hda'];
            $demande->dfa = $validated['dfa'];
            $demande->hfa = $validated['hfa'];
            $demande->statut = Demande::STATUT_ACCEPTEE;
            $demande->traite_id = Auth::id();
            $demande->date_traitement = now();
            $demande->save();
            
            // Notification
            app(NotificationService::class)->notifyDaptAccepted($demande);
            
            // Rediriger vers la création de la NAPT avec l'ID de la demande
            return redirect()->route('desa.notes.create', ['demande_id' => $demande->id])
                             ->with('success', 'Demande acceptée. Vous pouvez maintenant créer la NAPT.');
        }
        
        if ($action === 'retourner_napt') {
            $request->validate([
                'comment' => 'required|string|min:2',
            ]);
            
            $demande->statut = Demande::STATUT_RETOURNEE;
            $demande->comment = $request->input('comment');
            $demande->traite_id = Auth::id();
            $demande->date_traitement = now();
            $demande->save();
            
            // Notification
            app(NotificationService::class)->notifyDaptReturned($demande, $request->input('comment'));
            
            return redirect()->route('desa.demandes.index')
                             ->with('success', 'Demande retournée au demandeur.');
        }
        
        if ($action === 'accepter') {
            $validated = $request->validate([
                'dda' => 'required|date',
                'hda' => 'required',
                'dfa' => 'required|date|after_or_equal:dda',
                'hfa' => 'required',
                'dmra' => 'boolean',
            ]);
            
            $demande->fill($validated);
            $demande->dmra = $request->boolean('dmra');
            $demande->statut = Demande::STATUT_ACCEPTEE;
            $demande->traite_id = Auth::id();
            $demande->date_traitement = now();
            $demande->save();
            
            // Notification
            app(NotificationService::class)->notifyDaptAccepted($demande);
            
            return redirect()->route('desa.demandes.show', $demande)
                             ->with('success', 'Demande acceptée avec succès.');
        }
        
        if ($action === 'retourner') {
            $request->validate([
                'motif_retour' => 'required|string|min:2',
            ]);
            
            $demande->statut = Demande::STATUT_RETOURNEE;
            $demande->traite_id = Auth::id();
            $demande->date_traitement = now();
            // Stocker le motif dans un champ ou observation
            $demande->save();
            
            // Notification
            app(NotificationService::class)->notifyDaptReturned($demande, $request->input('motif_retour'));
            
            return redirect()->route('desa.demandes.index')
                             ->with('success', 'Demande retournée au demandeur.');
        }
        
        return redirect()->back()->with('error', 'Action non reconnue.');
    }

    /**
     * Enregistrer les dates et rediriger vers la création de la NAPT.
     * La demande passera à "acceptée" lors de la création de la NAPT.
     */
    public function faire_napt(Request $request, Demande $demande)
    {
        // Valider les dates acceptées
        $validated = $request->validate([
            'dda' => 'required|date',
            'hda' => 'required',
            'dfa' => 'required|date|after_or_equal:dda',
            'hfa' => 'required',
        ]);
        
        // Mettre à jour la demande avec les dates (sans changer le statut)
        $demande->dda = $validated['dda'];
        $demande->hda = $validated['hda'];
        $demande->dfa = $validated['dfa'];
        $demande->hfa = $validated['hfa'];
        // Le statut reste "en cours de traitement", il passera à "acceptée" lors de la création de la NAPT
        $demande->traite_id = Auth::id();
        $demande->save();
        
        // Rediriger vers la création de la NAPT avec l'ID de la demande
        return redirect()->route('desa.notes.create', ['demande_id' => $demande->id])
                         ->with('success', 'Dates enregistrées. Vous pouvez maintenant créer la NAPT.');
    }

    /**
     * Retourner la demande au demandeur.
     */
    public function retourner_napt(Request $request, Demande $demande)
    {
        $request->validate([
            'comment' => 'required|string|min:2',
        ], [
            'comment.required' => 'Le motif du retour est obligatoire.',
            'comment.min' => 'Le motif doit contenir au moins 2 caractères.',
        ]);
        
        $demande->statut = Demande::STATUT_RETOURNEE;
        $demande->motif_retour = $request->input('comment');
        $demande->traite_id = Auth::id();
        $demande->date_traitement = now();
        $demande->nb_retours = ($demande->nb_retours ?? 0) + 1;
        $demande->save();
        
        return redirect()->route('desa.demandes.index')
                         ->with('success', 'Demande retournée au demandeur avec succès.');
    }

    /**
     * Remove the specified demande from storage.
     */
    public function destroy(Demande $demande)
    {
        if ($demande->notes()->count() > 0) {
            return redirect()->back()
                             ->with('error', 'Impossible de supprimer cette demande car elle a des notes associées.');
        }
        
        $demande->delete();
        
        return redirect()->route('desa.demandes.index')
                         ->with('success', 'Demande supprimée avec succès.');
    }

    /**
     * Export filtered demandes to PDF (merge stored PDFs)
     */
    public function exportPdf(Request $request)
    {
        $query = Demande::with(['demandeur', 'chargeTravaux', 'note']);
        
        // Appliquer les mêmes filtres que l'index
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['numero_demande', 'designation', 'lieu_execution'], []);
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par date de création
        if ($request->filled('date_creation')) {
            $query->whereDate('created_at', $request->date_creation);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->whereRaw('WEEK(created_at, 1) = ?', [$request->semaine]);
        }
        
        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('created_at', $request->annee);
        }
        
        // Filtre par groupe
        if ($request->filled('groupe')) {
            $query->whereHas('demandeur', function ($q) use ($request) {
                $q->where('groupe_id', $request->groupe);
            });
        }
        
        $demandes = $query->orderBy('created_at', 'desc')->get();
        
        if ($demandes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune demande à exporter.');
        }
        
        // Filtrer les demandes qui ont un PDF stocké
        $demandesAvecPdf = $demandes->filter(function ($demande) {
            return $demande->pdf_path && Storage::disk('public')->exists($demande->pdf_path);
        });
        
        if ($demandesAvecPdf->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune demande avec un PDF généré trouvée.');
        }
        
        // Créer le PDF fusionné avec FPDI
        $merger = new \setasign\Fpdi\Fpdi();
        
        foreach ($demandesAvecPdf as $demande) {
            $pdfPath = storage_path('app/public/' . $demande->pdf_path);
            
            try {
                $pageCount = $merger->setSourceFile($pdfPath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $merger->importPage($i);
                    $size = $merger->getTemplateSize($templateId);
                    $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $merger->useTemplate($templateId);
                }
            } catch (\Exception $e) {
                // Si un PDF ne peut pas être lu, on continue avec les autres
                Log::warning("Impossible de lire le PDF de la demande {$demande->numero_demande}: " . $e->getMessage());
                continue;
            }
        }
        
        $filename = 'DAPT-Export-' . now()->format('Y-m-d_His') . '.pdf';
        
        return response($merger->Output('S', $filename), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
