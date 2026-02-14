<?php

namespace App\Http\Controllers\Operateur;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Display the dashboard for operateur.
     */
    public function dashboard(Request $request)
    {
        Carbon::setLocale('fr');
        
        // Semaine S-1 (précédente)
        $debutSemaineM1 = Carbon::now()->subWeek()->startOfWeek();
        $finSemaineM1 = Carbon::now()->subWeek()->endOfWeek();
        
        // Semaine en cours
        $debutSemaine = Carbon::now()->startOfWeek();
        $finSemaine = Carbon::now()->endOfWeek();
        
        // Semaine S+1
        $debutSemaineS1 = Carbon::now()->addWeek()->startOfWeek();
        $finSemaineS1 = Carbon::now()->addWeek()->endOfWeek();
        
        // Stats semaine S-1
        $statsSemaineM1 = $this->getWeekStats($debutSemaineM1, $finSemaineM1);
        
        // Stats semaine en cours
        $statsSemaineCourante = $this->getWeekStats($debutSemaine, $finSemaine);
        
        // Stats semaine S+1
        $statsSemaineS1 = $this->getWeekStats($debutSemaineS1, $finSemaineS1);
        
        // Dernières NAPT de la semaine en cours (prêtes à exécuter)
        $dernieresNapt = Note::with(['demande', 'etabliPar'])
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('statut', Note::STATUT_VALIDEE)
                         ->whereNotNull('fiche_manoeuvre');
                })
                ->orWhere('statut', Note::STATUT_EN_COURS_EXECUTION)
                ->orWhere('statut', Note::STATUT_EXECUTEE);
            })
            ->whereBetween('ddt', [$debutSemaine, $finSemaine->copy()->endOfDay()])
            ->orderBy('ddt', 'asc')
            ->take(10)
            ->get();
        
        // NAPT semaine S+1
        $naptS1 = Note::with(['demande', 'etabliPar'])
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('statut', Note::STATUT_VALIDEE)
                         ->whereNotNull('fiche_manoeuvre');
                })
                ->orWhere('statut', Note::STATUT_EN_COURS_EXECUTION)
                ->orWhere('statut', Note::STATUT_EXECUTEE);
            })
            ->whereBetween('ddt', [$debutSemaineS1, $finSemaineS1->copy()->endOfDay()])
            ->orderBy('ddt', 'asc')
            ->take(10)
            ->get();
        
        return view('operateur.dashboard', compact(
            'statsSemaineM1',
            'debutSemaineM1',
            'finSemaineM1',
            'statsSemaineCourante', 
            'statsSemaineS1', 
            'debutSemaine', 
            'finSemaine',
            'debutSemaineS1',
            'finSemaineS1',
            'dernieresNapt',
            'naptS1'
        ));
    }
    
    /**
     * Get stats for a specific week (notes with fiche manoeuvre for operateur).
     */
    private function getWeekStats($debut, $fin)
    {
        // Pour l'opérateur: seulement les notes avec fiche manœuvre ou en cours/exécutées
        $total = Note::where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('statut', Note::STATUT_VALIDEE)
                         ->whereNotNull('fiche_manoeuvre');
                })
                ->orWhere('statut', Note::STATUT_EN_COURS_EXECUTION)
                ->orWhere('statut', Note::STATUT_EXECUTEE)
                ->orWhere('statut', Note::STATUT_ANNULEE);
            })
            ->whereBetween('ddt', [$debut, $fin->copy()->endOfDay()])
            ->count();
        
        $aExecuter = Note::where('statut', Note::STATUT_VALIDEE)
            ->whereNotNull('fiche_manoeuvre')
            ->whereBetween('ddt', [$debut, $fin->copy()->endOfDay()])->count();
        $enCours = Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)
            ->whereBetween('ddt', [$debut, $fin->copy()->endOfDay()])->count();
        $executees = Note::where('statut', Note::STATUT_EXECUTEE)
            ->whereBetween('ddt', [$debut, $fin->copy()->endOfDay()])->count();
        $annulees = Note::where('statut', Note::STATUT_ANNULEE)
            ->whereBetween('ddt', [$debut, $fin->copy()->endOfDay()])->count();
        
        $tauxExecution = $total > 0 ? round(($executees / $total) * 100) : 0;
        
        return [
            'total' => $total,
            'a_executer' => $aExecuter,
            'en_cours' => $enCours,
            'executees' => $executees,
            'annulees' => $annulees,
            'taux_execution' => $tauxExecution,
        ];
    }

    /**
     * Display a listing of notes for operateur.
     * Notes avec fiche manœuvre jointe filtrables par statut.
     */
    public function index(Request $request)
    {
        // Stats pour les raccourcis (uniquement notes avec fiche manœuvre)
        $stats = [
            'validees' => Note::where('statut', Note::STATUT_VALIDEE)->whereNotNull('fiche_manoeuvre')->count(),
            'en_cours' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->whereNotNull('fiche_manoeuvre')->count(),
            'executees' => Note::where('statut', Note::STATUT_EXECUTEE)->whereNotNull('fiche_manoeuvre')->count(),
            'annulees' => Note::where('statut', Note::STATUT_ANNULEE)->whereNotNull('fiche_manoeuvre')->count(),
        ];
        
        // Query de base: notes avec fiche manœuvre
        $query = Note::with(['demande', 'etabliPar', 'verifiePar', 'validePar'])
                     ->whereNotNull('fiche_manoeuvre');
        
        // Mapping des slugs URL vers les constantes
        $statutMapping = [
            'validee' => Note::STATUT_VALIDEE,
            'en_cours_execution' => Note::STATUT_EN_COURS_EXECUTION,
            'executee' => Note::STATUT_EXECUTEE,
            'annulee' => Note::STATUT_ANNULEE,
        ];
        
        // Filtre par statut (défaut: validée)
        $statutSlug = $request->get('statut', 'validee');
        $statut = $statutMapping[$statutSlug] ?? Note::STATUT_VALIDEE;
        $query->where('statut', $statut);
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->where('numero_semaine', $request->semaine);
            // Si semaine spécifiée mais pas année, filtrer sur l'année en cours
            if (!$request->filled('annee')) {
                $query->whereYear('ddt', date('Y'));
            }
        }
        
        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('ddt', $request->annee);
        }
        
        // Filtre par dates (si spécifiées)
        if ($request->filled('date_debut')) {
            $query->whereDate('ddt', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('dft', '<=', $request->date_fin);
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(numero_note) like ?', ["%{$search}%"])
                  ->orWhereHas('demande', function ($q) use ($search) {
                      $q->whereRaw('LOWER(numero_demande) like ?', ["%{$search}%"]);
                  });
            });
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('operateur.notes.index', compact('notes', 'stats'));
    }

    /**
     * Display the specified note.
     */
    public function show(Note $note)
    {
        $note->load(['demande.demandeur', 'demande.chargeTravaux', 'etabliPar', 'verifiePar', 'validePar', 'chargesCons', 'correspondants', 'services']);
        return view('operateur.notes.show', compact('note'));
    }

    /**
     * Show the form for editing (executing) the specified note.
     */
    public function edit(Note $note)
    {
        // Vérifier que la fiche manœuvre est présente
        if (!$note->fiche_manoeuvre) {
            return redirect()->route('operateur.notes.index')
                             ->with('error', 'L\'opérateur chef doit d\'abord joindre la fiche de manœuvre.');
        }

        if (!in_array($note->statut, [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION])) {
            return redirect()->route('operateur.notes.show', $note)
                             ->with('error', 'Cette note ne peut pas être exécutée.');
        }
        
        $note->load(['demande', 'etabliPar', 'verifiePar', 'validePar', 'chargesCons', 'correspondants', 'services']);
        return view('operateur.notes.edit', compact('note'));
    }

    /**
     * Update the specified note (execution action).
     * L'opérateur ne peut que démarrer et terminer l'exécution.
     */
    public function update(Request $request, Note $note)
    {
        $action = $request->input('action');
        
        if ($action === 'demarrer') {
            if ($note->statut !== Note::STATUT_VALIDEE) {
                return redirect()->back()->with('error', 'Cette note ne peut pas être démarrée.');
            }
            
            // Vérifier que la fiche de manœuvre est jointe par l'opérateur chef
            if (!$note->fiche_manoeuvre) {
                return redirect()->back()->with('error', 'L\'opérateur chef doit d\'abord joindre la fiche de manœuvre.');
            }
            
            $note->statut = Note::STATUT_EN_COURS_EXECUTION;
            $note->en_cours_execution_id = Auth::id();
            $note->dre = now();
            $note->save();
            
            return redirect()->route('operateur.notes.show', $note)
                             ->with('success', 'Exécution démarrée avec succès.');
        }
        
        if ($action === 'terminer') {
            if ($note->statut !== Note::STATUT_EN_COURS_EXECUTION) {
                return redirect()->back()->with('error', 'Cette note n\'est pas en cours d\'exécution.');
            }
            
            $request->validate([
                'ddt' => 'required|date',
                'dft' => 'required|date|after_or_equal:ddt',
            ], [
                'ddt.required' => 'La date de début des travaux est obligatoire.',
                'dft.required' => 'La date de fin des travaux est obligatoire.',
                'dft.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            ]);
            
            $note->statut = Note::STATUT_EXECUTEE;
            $note->execute_id = Auth::id();
            $note->drex = now();
            $note->ddt = $request->ddt;
            $note->dft = $request->dft;
            $note->save();
            
            // Notification au DESA et au demandeur
            app(NotificationService::class)->notifyNaptExecuted($note);
            
            return redirect()->route('operateur.notes.index')
                             ->with('success', 'Note exécutée avec succès.');
        }

        if ($action === 'annuler') {
            if (!in_array($note->statut, [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION])) {
                return redirect()->back()->with('error', 'Seule une note validée ou en cours d\'exécution peut être annulée.');
            }

            $request->validate([
                'commentanul' => 'required|string|min:10|max:1000',
            ], [
                'commentanul.required' => 'Le motif d\'annulation est obligatoire.',
                'commentanul.min' => 'Le motif doit contenir au moins 10 caractères.',
            ]);

            $note->statut = Note::STATUT_ANNULEE;
            $note->commentanul = $request->commentanul;
            $note->annule_id = Auth::id();
            $note->save();

            return redirect()->route('operateur.notes.index')
                             ->with('success', 'La note a été annulée.');
        }
        
        return redirect()->back()->with('error', 'Action non reconnue.');
    }

    /**
     * Annuler une note validée.
     */
    public function annuler(Request $request, Note $note)
    {
        if (!in_array($note->statut, [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION])) {
            return redirect()->route('operateur.notes.show', $note)
                             ->with('error', 'Seule une note validée ou en cours d\'exécution peut être annulée.');
        }

        $request->validate([
            'commentanul' => 'required|string|min:10|max:1000',
        ], [
            'commentanul.required' => 'Le motif d\'annulation est obligatoire.',
            'commentanul.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $note->statut = Note::STATUT_ANNULEE;
        $note->commentanul = $request->commentanul;
        $note->annule_id = Auth::id();
        $note->save();

        // Notification au demandeur et DESA
        app(NotificationService::class)->notifyNaptCancelled($note, 'operateur', $request->commentanul);

        return redirect()->route('operateur.notes.index')
                         ->with('success', 'La note a été annulée.');
    }
}
