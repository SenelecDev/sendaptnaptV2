<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $user = auth()->user();
        
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
        
        // Récupérer les NAPT du mois (ddt = date début travaux, dft = date fin travaux)
        $query = Note::with(['demande.demandeur', 'etabliPar'])
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('ddt', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('dft', [$startOfMonth, $endOfMonth])
                      ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('ddt', '<=', $startOfMonth)
                            ->where('dft', '>=', $endOfMonth);
                      });
            })
            ->whereNotIn('statut', ['brouillon', 'annulée']);
        
        // Filtrer par groupe pour les demandeurs
        if ($user->hasRole('demandeur') && !$user->hasAnyRole(['admin', 'desa', 'operateur', 'operateurchef'])) {
            $query->whereHas('demande.demandeur', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            });
        }
        
        $notes = $query->orderBy('ddt')->get();
        
        // Organiser les NAPT par jour
        $eventsByDay = [];
        foreach ($notes as $note) {
            $start = Carbon::parse($note->ddt);
            $end = Carbon::parse($note->dft ?? $note->ddt);
            
            $current = $start->copy();
            while ($current <= $end) {
                if ($current->month == $month && $current->year == $year) {
                    $day = $current->day;
                    if (!isset($eventsByDay[$day])) {
                        $eventsByDay[$day] = [];
                    }
                    // Éviter les doublons
                    if (!collect($eventsByDay[$day])->contains('id', $note->id)) {
                        $lieu = $note->demande->lieu_execution ?? $note->demande->lieu_execution_manuel ?? 'N/A';
                        $installations = $this->getInstallationsConsignees($note->demande);
                        $eventsByDay[$day][] = [
                            'id' => $note->id,
                            'numero' => $note->numero_note,
                            'lieu' => $lieu,
                            'installations' => !empty($installations) ? implode(', ', $installations) : '',
                            'statut' => $note->statut,
                            'isStart' => $current->isSameDay($start),
                            'isEnd' => $current->isSameDay($end),
                        ];
                    }
                }
                $current->addDay();
            }
        }
        
        return view('calendrier.index', compact('notes', 'eventsByDay', 'month', 'year', 'startOfMonth', 'endOfMonth'));
    }
    
    /**
     * API pour FullCalendar
     */
    public function events(Request $request)
    {
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));
        $user = auth()->user();
        
        $query = Note::with(['demande.demandeur'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('ddt', [$start, $end])
                      ->orWhereBetween('dft', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('ddt', '<=', $start)
                            ->where('dft', '>=', $end);
                      });
            })
            ->whereNotIn('statut', ['brouillon', 'annulée']);
        
        // Filtrer par groupe pour les demandeurs
        if ($user->hasRole('demandeur') && !$user->hasAnyRole(['admin', 'desa', 'operateur', 'operateurchef'])) {
            $query->whereHas('demande.demandeur', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            });
        }
        
        $notes = $query->get();
        
        $events = $notes->map(function ($note) {
            $color = match($note->statut) {
                'établie', 'en attente de vérification', 'vérifiée' => '#F59E0B',
                'validée' => '#10B981',
                'en cours d\'exécution' => '#3B82F6',
                'executée' => '#6B7280',
                default => '#E85D04',
            };
            
            $lieu = $note->demande->lieu_execution ?? $note->demande->lieu_execution_manuel ?? 'N/A';
            $installations = $this->getInstallationsConsignees($note->demande);
            
            $titleParts = [$note->numero_note];
            if ($lieu !== 'N/A') {
                $titleParts[] = $lieu;
            }
            if (!empty($installations)) {
                $titleParts[] = implode(', ', array_slice($installations, 0, 3));
            }
            
            return [
                'id' => $note->id,
                'title' => implode(' - ', $titleParts),
                'start' => $note->ddt,
                'end' => Carbon::parse($note->dft ?? $note->ddt)->addDay()->format('Y-m-d'),
                'color' => $color,
                'url' => route('desa.notes.show', $note->id),
                'extendedProps' => [
                    'lieu' => $lieu,
                    'installations' => !empty($installations) ? implode(', ', $installations) : 'N/A',
                    'statut' => $note->statut,
                    'demandeur' => $note->demande->demandeur->name ?? 'N/A',
                ],
            ];
        });
        
        return response()->json($events);
    }

    private function getInstallationsConsignees($demande): array
    {
        $installations = [];

        if ($demande->mode_saisie === 'manuelle' || $demande->ouvrage_type === 'manuel') {
            if (!empty($demande->ouvrages_consigner_manuel)) {
                $installations[] = $demande->ouvrages_consigner_manuel;
            }
            return $installations;
        }

        if (!empty($demande->ouvrages_consigner_gmao)) {
            $gmaoData = is_array($demande->ouvrages_consigner_gmao)
                ? $demande->ouvrages_consigner_gmao
                : json_decode($demande->ouvrages_consigner_gmao, true);

            if (is_array($gmaoData)) {
                foreach ($gmaoData as $item) {
                    if (is_array($item)) {
                        $desc = $item['description'] ?? $item['EQUIPMENT_DES'] ?? $item['nom'] ?? $item['code'] ?? null;
                        if ($desc) $installations[] = $desc;
                    } elseif (is_string($item)) {
                        $installations[] = $item;
                    }
                }
            }
        }

        if (empty($installations) && !empty($demande->equipements_oracle)) {
            $equipementsData = is_array($demande->equipements_oracle)
                ? $demande->equipements_oracle
                : json_decode($demande->equipements_oracle, true);

            if (is_array($equipementsData)) {
                $niveauxAvecData = [];
                foreach ($equipementsData as $levelKey => $levelData) {
                    if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                        $niveauxAvecData[$m[1]] = $levelData;
                    }
                }
                if (!empty($niveauxAvecData)) {
                    $dernierNiveau = $niveauxAvecData[max(array_keys($niveauxAvecData))];
                    foreach ($dernierNiveau as $equip) {
                        $desc = is_array($equip) ? ($equip['description'] ?? $equip['nom'] ?? $equip['code'] ?? null) : $equip;
                        if ($desc) $installations[] = $desc;
                    }
                }
            }
        }

        return $installations;
    }
}
