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
                        $eventsByDay[$day][] = [
                            'id' => $note->id,
                            'numero' => $note->numero_note,
                            'designation' => $note->demande->designation ?? 'N/A',
                            'lieu' => $note->demande->lieu_execution ?? 'N/A',
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
                'établie', 'en attente de vérification', 'vérifiée' => '#F59E0B', // Jaune
                'validée' => '#10B981', // Vert
                'en cours d\'exécution' => '#3B82F6', // Bleu
                'executée' => '#6B7280', // Gris
                default => '#E85D04', // Orange Senelec
            };
            
            return [
                'id' => $note->id,
                'title' => $note->numero_note . ' - ' . \Illuminate\Support\Str::limit($note->demande->designation ?? 'N/A', 30),
                'start' => $note->ddt,
                'end' => Carbon::parse($note->dft ?? $note->ddt)->addDay()->format('Y-m-d'), // FullCalendar end est exclusif
                'color' => $color,
                'url' => route('desa.notes.show', $note->id),
                'extendedProps' => [
                    'lieu' => $note->demande->lieu_execution ?? 'N/A',
                    'statut' => $note->statut,
                    'demandeur' => $note->demande->demandeur->name ?? 'N/A',
                ],
            ];
        });
        
        return response()->json($events);
    }
}
