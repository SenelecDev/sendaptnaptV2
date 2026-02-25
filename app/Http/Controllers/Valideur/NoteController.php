<?php

namespace App\Http\Controllers\Valideur;

use App\Http\Controllers\Controller;
use App\Traits\SearchableTrait;
use App\Models\Note;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NoteController extends Controller
{
    use SearchableTrait;
    /**
     * Display the valideur dashboard.
     */
    public function dashboard(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        
        $stats = [
            'en_attente_validation' => Note::whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])->count(),
            'validees' => Note::where('statut', Note::STATUT_VALIDEE)->count(),
            'en_cours_execution' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'retournees' => Note::where('statut', Note::STATUT_RETOURNEE)->count(),
            'annulees' => Note::where('statut', Note::STATUT_ANNULEE)->count(),
            'executees' => Note::where('statut', Note::STATUT_EXECUTEE)->count(),
        ];
        
        // Graphique data
        $graphData = $this->getNotesGraphData($periode);
        
        // Dernières notes à valider
        $dernieresNotes = Note::with(['demande', 'etabliPar', 'verifiePar'])
            ->whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('valideur.dashboard', compact('stats', 'graphData', 'periode', 'dernieresNotes'));
    }

    /**
     * Get graph data for Notes based on period.
     */
    private function getNotesGraphData($periode)
    {
        $labels = [];
        $data = [
            'en_attente_validation' => [],
            'validees' => [],
            'en_cours_execution' => [],
            'executees' => [],
            'retournees' => [],
            'annulees' => [],
        ];
        
        switch ($periode) {
            case 'semaine':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->locale('fr')->isoFormat('ddd DD/MM');
                    
                    $data['en_attente_validation'][] = Note::whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])
                        ->whereDate('created_at', $date->toDateString())->count();
                    $data['validees'][] = Note::where('statut', Note::STATUT_VALIDEE)
                        ->whereDate('created_at', $date->toDateString())->count();
                    $data['en_cours_execution'][] = Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)
                        ->whereDate('created_at', $date->toDateString())->count();
                    $data['executees'][] = Note::where('statut', Note::STATUT_EXECUTEE)
                        ->whereDate('created_at', $date->toDateString())->count();
                    $data['retournees'][] = Note::where('statut', Note::STATUT_RETOURNEE)
                        ->whereDate('created_at', $date->toDateString())->count();
                    $data['annulees'][] = Note::where('statut', Note::STATUT_ANNULEE)
                        ->whereDate('created_at', $date->toDateString())->count();
                }
                break;
                
            case 'mois':
                for ($i = 3; $i >= 0; $i--) {
                    $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                    $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
                    $labels[] = 'Sem. ' . $startOfWeek->weekOfYear;
                    
                    $data['en_attente_validation'][] = Note::whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                    $data['validees'][] = Note::where('statut', Note::STATUT_VALIDEE)
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                    $data['en_cours_execution'][] = Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                    $data['executees'][] = Note::where('statut', Note::STATUT_EXECUTEE)
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                    $data['retournees'][] = Note::where('statut', Note::STATUT_RETOURNEE)
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                    $data['annulees'][] = Note::where('statut', Note::STATUT_ANNULEE)
                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                }
                break;
                
            case 'annee':
                for ($i = 11; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $labels[] = $month->locale('fr')->isoFormat('MMM YYYY');
                    
                    $data['en_attente_validation'][] = Note::whereIn('statut', [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])
                        ->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                    $data['validees'][] = Note::where('statut', Note::STATUT_VALIDEE)
                        ->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                    $data['en_cours_execution'][] = Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)
                        ->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                    $data['executees'][] = Note::where('statut', Note::STATUT_EXECUTEE)
                        ->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                    $data['retournees'][] = Note::where('statut', Note::STATUT_RETOURNEE)
                        ->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                    $data['annulees'][] = Note::where('statut', Note::STATUT_ANNULEE)
                        ->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                }
                break;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Display a listing of notes for valideur.
     */
    public function index(Request $request)
    {
        $query = Note::with(['demande.demandeur.groupe', 'etabliPar', 'verifiePar'])
                     ->whereIn('statut', [
                         Note::STATUT_VERIFIEE,
                         Note::STATUT_EN_ATTENTE_VALIDATION,
                         Note::STATUT_VALIDEE,
                         Note::STATUT_EN_COURS_EXECUTION,
                         Note::STATUT_EXECUTEE,
                         Note::STATUT_RETOURNEE,
                         Note::STATUT_ANNULEE,
                     ]);
        
        // Recherche
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['numero_note'], []);
        }
        
        // Filtre par statut (par défaut: vérifiée / à valider)
        $statut = $request->get('statut', Note::STATUT_VERIFIEE);
        if ($statut !== 'tous') {
            $query->where('statut', $statut);
        }
        
        // Filtre par date début (sur ddt de la note)
        if ($request->filled('date_debut')) {
            $query->whereDate('ddt', '=', $request->date_debut);
        }
        
        // Filtre par date fin (sur dft de la note)
        if ($request->filled('date_fin')) {
            $query->whereDate('dft', '=', $request->date_fin);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->where('numero_semaine', $request->semaine);
        }
        
        // Filtre par année (sur la date des travaux de la note)
        if ($request->filled('annee')) {
            $query->whereYear('ddt', $request->annee);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('valideur.notes.index', compact('notes', 'statut'));
    }

    /**
     * Display the specified note.
     */
    public function show(Note $note)
    {
        $note->load(['demande.demandeur', 'etabliPar', 'verifiePar', 'chargecons', 'correspondants', 'services']);
        return view('valideur.notes.show', compact('note'));
    }

    /**
     * Show the form for editing (validating) the specified note.
     */
    public function edit(Note $note)
    {
        if (!in_array($note->statut, [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])) {
            return redirect()->route('valideur.notes.show', $note)
                             ->with('error', 'Cette note n\'est pas en attente de validation.');
        }
        
        $note->load(['demande', 'etabliPar', 'verifiePar', 'chargecons', 'correspondants', 'services']);
        return view('valideur.notes.edit', compact('note'));
    }

    /**
     * Update the specified note (validation action).
     */
    public function update(Request $request, Note $note)
    {
        if (!in_array($note->statut, [Note::STATUT_VERIFIEE, Note::STATUT_EN_ATTENTE_VALIDATION])) {
            return redirect()->route('valideur.notes.show', $note)
                             ->with('error', 'Cette note n\'est pas en attente de validation.');
        }
        
        $action = $request->input('action');
        
        if ($action === 'valider') {
            // Garde backend: impossible de valider une note d'etude sans document
            if ($note->etude === 'oui' && !$note->document) {
                return redirect()->route('valideur.notes.show', $note)
                    ->with('error', 'Cette NAPT ne peut pas etre validee: document d\'etude manquant.');
            }

            $note->statut = Note::STATUT_VALIDEE;
            $note->valide_id = Auth::id();
            $note->save();
            
            // Mettre à jour le statut de la demande à "acceptée"
            if ($note->demande) {
                $note->demande->statut = \App\Models\Demande::STATUT_ACCEPTEE;
                $note->demande->date_traitement = $note->demande->date_traitement ?? now();
                $note->demande->save();
            }
            
            // Notification aux opérateurs et au DESA/demandeur
            app(NotificationService::class)->notifyNaptValidated($note);
            
            return redirect()->route('valideur.notes.index')
                             ->with('success', 'Note validée avec succès.');
        }
        
        if ($action === 'retourner') {
            $validator = Validator::make($request->all(), [
                'motifbis' => 'required|string|min:2',
            ], [
                'motifbis.required' => 'Le motif du retour est obligatoire.',
                'motifbis.min' => 'Le motif du retour doit contenir au moins 2 caractères.',
            ]);
            
            if ($validator->fails()) {
                return redirect()->route('valideur.notes.index')
                                 ->with('error', $validator->errors()->first());
            }
            
            $note->statut = Note::STATUT_RETOURNEE;
            $note->retourne2_id = Auth::id();
            $note->motifbis = $request->motifbis;
            $note->save();
            
            // Notification au DESA
            app(NotificationService::class)->notifyNaptReturned($note, 'valideur', $request->motifbis);
            
            return redirect()->route('valideur.notes.index')
                             ->with('success', 'Note retournée au vérificateur.');
        }
        
        return redirect()->route('valideur.notes.index')->with('error', 'Action non reconnue.');
    }
}
