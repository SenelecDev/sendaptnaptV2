<?php

namespace App\Http\Controllers\OperateurChef;

use App\Http\Controllers\Controller;
use App\Traits\SearchableTrait;
use App\Models\Note;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class NoteController extends Controller
{
    use SearchableTrait;
    /**
     * Display the dashboard for operateur chef.
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
        
        // Dernières NAPT de la semaine en cours
        $dernieresNapt = Note::with(['demande', 'etabliPar'])
            ->whereIn('statut', [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION, Note::STATUT_EXECUTEE, Note::STATUT_ANNULEE])
            ->whereBetween('ddt', [$debutSemaine, $finSemaine->copy()->endOfDay()])
            ->orderBy('ddt', 'asc')
            ->take(10)
            ->get();
        
        // NAPT semaine S+1
        $naptS1 = Note::with(['demande', 'etabliPar'])
            ->whereIn('statut', [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION, Note::STATUT_EXECUTEE, Note::STATUT_ANNULEE])
            ->whereBetween('ddt', [$debutSemaineS1, $finSemaineS1->copy()->endOfDay()])
            ->orderBy('ddt', 'asc')
            ->take(10)
            ->get();
        
        return view('operateurchef.dashboard', compact(
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
     * Get stats for a specific week.
     */
    private function getWeekStats($debut, $fin)
    {
        $baseQuery = Note::whereIn('statut', [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION, Note::STATUT_EXECUTEE, Note::STATUT_ANNULEE])
            ->whereBetween('ddt', [$debut, $fin->copy()->endOfDay()]);
        
        $total = (clone $baseQuery)->count();
        $validees = Note::where('statut', Note::STATUT_VALIDEE)
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
            'validees' => $validees,
            'en_cours' => $enCours,
            'executees' => $executees,
            'annulees' => $annulees,
            'taux_execution' => $tauxExecution,
        ];
    }

    /**
     * Display a listing of notes for operateur chef.
     * Affiche les notes validées pour ajouter la fiche manœuvre.
     */
    public function index(Request $request)
    {
        $query = Note::with(['demande', 'etabliPar', 'verifiePar', 'validePar'])
                     ->whereIn('statut', [
                         Note::STATUT_VALIDEE,
                         Note::STATUT_EN_COURS_EXECUTION,
                         Note::STATUT_EXECUTEE,
                         Note::STATUT_ANNULEE,
                     ]);
        
        // Recherche
        if ($request->filled('search')) {
            $driver = DB::connection()->getDriverName();
            $this->applySimpleSearch(
                $query,
                $request->search,
                ['numero_note'],
                ['demande' => ['numero_demande', 'lieu_execution', 'ouvrages_consigner_manuel']],
                function ($q, $pattern) use ($driver) {
                    if ($driver === 'mysql') {
                        $q->orWhereHas('demande', function ($dq) use ($pattern) {
                            $dq->whereRaw('LOWER(CAST(COALESCE(ouvrages_consigner_gmao, "[]") AS CHAR)) LIKE ?', [$pattern]);
                        });
                    } elseif ($driver === 'pgsql') {
                        $q->orWhereHas('demande', function ($dq) use ($pattern) {
                            $dq->whereRaw('LOWER(COALESCE(ouvrages_consigner_gmao::text, \'[]\')) LIKE ?', [$pattern]);
                        });
                    }
                }
            );
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre fiche manœuvre
        if ($request->filled('fiche')) {
            if ($request->fiche === 'avec') {
                $query->whereNotNull('fiche_manoeuvre');
            } elseif ($request->fiche === 'sans') {
                $query->whereNull('fiche_manoeuvre');
            }
        }
        
        // Filtre par date début
        if ($request->filled('date_debut')) {
            $query->whereDate('ddt', '>=', $request->date_debut);
        }
        
        // Filtre par date fin
        if ($request->filled('date_fin')) {
            $query->whereDate('ddt', '<=', $request->date_fin);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->where('numero_semaine', $request->semaine);
        }
        
        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('ddt', $request->annee);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'sans_fiche' => Note::where('statut', Note::STATUT_VALIDEE)->whereNull('fiche_manoeuvre')->count(),
            'avec_fiche' => Note::whereIn('statut', [Note::STATUT_VALIDEE, Note::STATUT_EN_COURS_EXECUTION, Note::STATUT_EXECUTEE])->whereNotNull('fiche_manoeuvre')->count(),
            'en_cours' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'executees' => Note::where('statut', Note::STATUT_EXECUTEE)->count(),
        ];
        
        return view('operateurchef.notes.index', compact('notes', 'stats'));
    }

    /**
     * Display the specified note.
     */
    public function show(Note $note)
    {
        $note->load(['demande.demandeur', 'demande.chargeTravaux', 'etabliPar', 'verifiePar', 'validePar', 'chargesCons', 'correspondants', 'services']);
        return view('operateurchef.notes.show', compact('note'));
    }

    /**
     * Show the form for editing the note (ajouter fiche manœuvre).
     */
    public function edit(Note $note)
    {
        if ($note->statut !== Note::STATUT_VALIDEE) {
            return redirect()->route('operateurchef.notes.show', $note)
                             ->with('error', 'La fiche de manœuvre ne peut être modifiée que sur une note validée.');
        }
        
        $note->load(['demande.demandeur', 'demande.chargeTravaux', 'etabliPar', 'verifiePar', 'validePar']);
        return view('operateurchef.notes.edit', compact('note'));
    }

    /**
     * Update the specified note (ajouter/modifier fiche manœuvre).
     */
    public function update(Request $request, Note $note)
    {
        // Vérifier que la note est bien validée
        if ($note->statut !== Note::STATUT_VALIDEE) {
            return redirect()->route('operateurchef.notes.show', $note)
                             ->with('error', 'La fiche de manœuvre ne peut être ajoutée que sur une note validée.');
        }

        $request->validate([
            'fiche_manoeuvre' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'fiche_manoeuvre.required' => 'La fiche de manœuvre est obligatoire.',
            'fiche_manoeuvre.mimes' => 'Le fichier doit être au format PDF, JPG ou PNG.',
            'fiche_manoeuvre.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        // Supprimer l'ancienne fiche si elle existe
        if ($note->fiche_manoeuvre) {
            Storage::disk('public')->delete($note->fiche_manoeuvre);
        }

        // Enregistrer la nouvelle fiche
        $note->fiche_manoeuvre = $request->file('fiche_manoeuvre')->store('fiches_manoeuvre', 'public');
        $note->save();

        return redirect()->route('operateurchef.notes.show', $note)
                         ->with('success', 'Fiche de manœuvre ajoutée avec succès.');
    }

    /**
     * Supprimer la fiche manœuvre (si erreur).
     */
    public function destroyFiche(Note $note)
    {
        if ($note->statut !== Note::STATUT_VALIDEE) {
            return redirect()->route('operateurchef.notes.show', $note)
                             ->with('error', 'La fiche ne peut être supprimée que sur une note validée.');
        }

        if ($note->fiche_manoeuvre) {
            Storage::disk('public')->delete($note->fiche_manoeuvre);
            $note->fiche_manoeuvre = null;
            $note->save();
        }

        return redirect()->route('operateurchef.notes.edit', $note)
                         ->with('success', 'Fiche de manœuvre supprimée.');
    }

    /**
     * Annuler une note (uniquement si validée, pas encore en cours d'exécution).
     */
    public function annuler(Request $request, Note $note)
    {
        // Seules les notes validées peuvent être annulées par l'opérateur chef
        if ($note->statut !== Note::STATUT_VALIDEE) {
            return redirect()->route('operateurchef.notes.show', $note)
                             ->with('error', 'Seule une note validée peut être annulée.');
        }

        $request->validate([
            'commentanul' => 'required|string|min:10|max:1000',
        ], [
            'commentanul.required' => 'Le motif d\'annulation est obligatoire.',
            'commentanul.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $note->statut = Note::STATUT_ANNULEE;
        $note->annule_id = Auth::id();
        $note->commentanul = $request->commentanul;
        $note->save();

        // Notification au demandeur et DESA
        app(NotificationService::class)->notifyNaptCancelled($note, 'operateurchef', $request->commentanul);

        return redirect()->route('operateurchef.notes.index')
                         ->with('success', 'Note annulée avec succès.');
    }
}
