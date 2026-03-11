<?php

namespace App\Http\Controllers\Verificateur;

use App\Http\Controllers\Controller;
use App\Traits\SearchableTrait;
use App\Models\Note;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NoteController extends Controller
{
    use SearchableTrait;
    /**
     * Display the verificateur dashboard.
     */
    public function dashboard(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        
        $stats = [
            'en_attente_verification' => Note::where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
            'verifiees' => Note::where('statut', Note::STATUT_VERIFIEE)->count(),
            'validees' => Note::where('statut', Note::STATUT_VALIDEE)->count(),
            'en_cours_execution' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'retournees' => Note::where('statut', Note::STATUT_RETOURNEE)->count(),
            'annulees' => Note::where('statut', Note::STATUT_ANNULEE)->count(),
            'executees' => Note::where('statut', Note::STATUT_EXECUTEE)->count(),
        ];
        
        // Graphique data
        $graphData = $this->getNotesGraphData($periode);
        
        // Dernières notes à vérifier
        $dernieresNotes = Note::with(['demande', 'etabliPar'])
            ->where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('verificateur.dashboard', compact('stats', 'graphData', 'periode', 'dernieresNotes'));
    }

    /**
     * Display a listing of notes for verificateur.
     */
    public function index(Request $request)
    {
        $query = Note::with(['demande.demandeur.groupe', 'etabliPar'])
                     ->whereIn('statut', [
                         Note::STATUT_EN_ATTENTE_VERIFICATION,
                         Note::STATUT_VERIFIEE,
                         Note::STATUT_EN_ATTENTE_VALIDATION,
                         Note::STATUT_VALIDEE,
                         Note::STATUT_EN_COURS_EXECUTION,
                         Note::STATUT_EXECUTEE,
                         Note::STATUT_RETOURNEE,
                         Note::STATUT_ANNULEE,
                     ]);
        
        // Recherche globale: NAPT, DAPT, lieu, établi par + ouvrages à consigner (manuel + GMAO)
        if ($request->filled('search')) {
            $term = mb_strtolower(trim($request->search));
            if ($term !== '') {
                $pattern = '%' . $term . '%';
                $driver = DB::connection()->getDriverName();

                $query->where(function ($q) use ($pattern, $driver) {
                    // N° NAPT
                    $q->whereRaw('LOWER(numero_note) LIKE ?', [$pattern]);

                    // DAPT + lieu + ouvrages manuels
                    $q->orWhereHas('demande', function ($dq) use ($pattern, $driver) {
                        $dq->whereRaw('LOWER(COALESCE(numero_demande, \'\')) LIKE ?', [$pattern])
                           ->orWhereRaw('LOWER(COALESCE(lieu_execution, \'\')) LIKE ?', [$pattern])
                           ->orWhereRaw('LOWER(COALESCE(ouvrages_consigner_manuel, \'\')) LIKE ?', [$pattern]);

                        // Ouvrages GMAO JSON
                        if ($driver === 'mysql') {
                            $dq->orWhereRaw('LOWER(CAST(COALESCE(ouvrages_consigner_gmao, "[]") AS CHAR)) LIKE ?', [$pattern]);
                        } elseif ($driver === 'pgsql') {
                            $dq->orWhereRaw('LOWER(COALESCE(ouvrages_consigner_gmao::text, \'[]\')) LIKE ?', [$pattern]);
                        }
                    });

                    // Etabli par (nom / prénom / matricule)
                    $q->orWhereHas('etabliPar', function ($uq) use ($pattern) {
                        $uq->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', [$pattern])
                           ->orWhereRaw('LOWER(COALESCE(prenom, \'\')) LIKE ?', [$pattern])
                           ->orWhereRaw('LOWER(COALESCE(matricule, \'\')) LIKE ?', [$pattern]);
                    });
                });
            }
        }
        
        // Filtre par statut (par défaut: en attente de vérification)
        $statut = $request->get('statut', Note::STATUT_EN_ATTENTE_VERIFICATION);
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
        
        return view('verificateur.notes.index', compact('notes', 'statut'));
    }
    
    /**
     * Get graph data for Notes based on period.
     */
    private function getNotesGraphData($periode)
    {
        $labels = [];
        $data = [
            'en_attente_verification' => [],
            'verifiees' => [],
            'validees' => [],
            'en_cours_execution' => [],
            'executees' => [],
            'retournees' => [],
            'annulees' => [],
        ];
        
        $statutMap = [
            'en_attente_verification' => Note::STATUT_EN_ATTENTE_VERIFICATION,
            'verifiees' => Note::STATUT_VERIFIEE,
            'validees' => Note::STATUT_VALIDEE,
            'en_cours_execution' => Note::STATUT_EN_COURS_EXECUTION,
            'executees' => Note::STATUT_EXECUTEE,
            'retournees' => Note::STATUT_RETOURNEE,
            'annulees' => Note::STATUT_ANNULEE,
        ];
        
        switch ($periode) {
            case 'semaine':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->locale('fr')->isoFormat('ddd DD/MM');
                    
                    foreach (array_keys($data) as $key) {
                        $data[$key][] = Note::where('statut', $statutMap[$key])
                            ->whereDate('created_at', $date->toDateString())
                            ->count();
                    }
                }
                break;
                
            case 'mois':
                for ($i = 3; $i >= 0; $i--) {
                    $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                    $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
                    $labels[] = 'Sem. ' . $startOfWeek->weekOfYear;
                    
                    foreach (array_keys($data) as $key) {
                        $data[$key][] = Note::where('statut', $statutMap[$key])
                            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                            ->count();
                    }
                }
                break;
                
            case 'annee':
                for ($i = 11; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $labels[] = $month->locale('fr')->isoFormat('MMM YYYY');
                    
                    foreach (array_keys($data) as $key) {
                        $data[$key][] = Note::where('statut', $statutMap[$key])
                            ->whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month)
                            ->count();
                    }
                }
                break;
        }
        
        return [
            'labels' => $labels,
            'datasets' => $data,
        ];
    }

    /**
     * Display the specified note (PDF format).
     */
    public function show(Note $note)
    {
        $note->load([
            'demande.demandeur',
            'demande.chargeTravaux',
            'etabliPar',
            'verifiePar',
            'validePar',
            'chargesConsignation',
            'correspondants',
            'services'
        ]);
        
        return view('verificateur.notes.show', compact('note'));
    }

    /**
     * Show the form for editing (verifying) the specified note.
     * Redirects to PDF view with action buttons.
     */
    public function edit(Note $note)
    {
        // Redirect to show which displays the PDF with action buttons
        return redirect()->route('verificateur.notes.show', $note);
    }

    /**
     * Update the specified note (verification action).
     */
    public function update(Request $request, Note $note)
    {
        // Allow verification of notes that are either waiting for verification or returned
        if (!in_array($note->statut, [Note::STATUT_EN_ATTENTE_VERIFICATION, Note::STATUT_RETOURNEE])) {
            return redirect()->route('verificateur.notes.show', $note)
                             ->with('error', 'Cette note ne peut pas être vérifiée.');
        }
        
        $action = $request->input('action');
        
        if ($action === 'verifier') {
            // Garde backend: impossible de verifier une note d'etude sans document
            if ($note->etude === 'oui' && !$note->document) {
                return redirect()->route('verificateur.notes.show', $note)
                    ->with('error', 'Cette NAPT ne peut pas etre verifiee: document d\'etude manquant.');
            }

            $note->statut = Note::STATUT_VERIFIEE;
            $note->verifie_id = Auth::id();
            // Reset return fields when verified again
            $note->motif = null;
            $note->motifbis = null;
            $note->retourne1_id = null;
            $note->retourne2_id = null;
            $note->save();
            
            // Notification aux valideurs
            app(NotificationService::class)->notifyNaptVerified($note);
            
            return redirect()->route('verificateur.notes.index')
                             ->with('success', 'Note vérifiée avec succès.');
        }
        
        if ($action === 'retourner') {
            $request->validate([
                'motif' => 'required|string|min:2',
            ]);
            
            $note->statut = Note::STATUT_RETOURNEE;
            $note->retourne1_id = Auth::id();
            $note->motif = $request->motif;
            $note->save();
            
            // Notification au DESA
            app(NotificationService::class)->notifyNaptReturned($note, 'vérificateur', $request->motif);
            
            return redirect()->route('verificateur.notes.index')
                             ->with('success', 'Note retournée avec succès.');
        }
        
        return redirect()->back()->with('error', 'Action non reconnue.');
    }
}
