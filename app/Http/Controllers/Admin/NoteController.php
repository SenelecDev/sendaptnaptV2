<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    use SearchableTrait;

    public function index(Request $request)
    {
        $query = Note::with(['demande', 'etabli', 'verifie', 'valide', 'execute']);

        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['numero_note', 'motif', 'renseignementN'], []);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par date de début
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        // Filtre par date de fin
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $semaine = $request->semaine;
            $debut = now()->setISODate(now()->year, $semaine)->startOfWeek();
            $fin = now()->setISODate(now()->year, $semaine)->endOfWeek();
            $query->whereBetween('created_at', [$debut, $fin]);
        }
        
        // Filtre par mode
        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Note::count(),
            'brouillon' => Note::where('statut', 'brouillon')->count(),
            'en_etude' => Note::where('statut', 'en étude')->count(),
            'en_attente_verification' => Note::where('statut', 'en attente de vérification')->count(),
            'verifiees' => Note::where('statut', 'vérifiée')->count(),
            'validees' => Note::where('statut', 'validée')->count(),
            'en_execution' => Note::where('statut', 'en cours d\'exécution')->count(),
            'executees' => Note::where('statut', 'exécutée')->count(),
            'retournees' => Note::where('statut', 'retournée')->count(),
        ];
        
        return view('admin.notes.index', compact('notes', 'stats'));
    }

    public function show(Note $note)
    {
        $note->load([
            'demande', 
            'etabli', 
            'verifie', 
            'valide', 
            'execute',
            'chargesCons',
            'correspondants',
            'servicesDest',
            'histories.user'
        ]);
        
        return view('admin.notes.show', compact('note'));
    }

    public function destroy(Note $note)
    {
        // Ne supprimer que si en brouillon ou en étude
        if (!in_array($note->statut, ['brouillon', 'en étude'])) {
            return redirect()->back()
                             ->with('error', 'Impossible de supprimer cette note car elle est déjà en cours de traitement.');
        }
        
        $note->delete();
        
        return redirect()->route('admin.notes.index')
                         ->with('success', 'Note supprimée avec succès.');
    }
    
    public function export(Request $request)
    {
        $query = Note::with(['demande', 'createur']);
        
        // Appliquer les mêmes filtres que l'index
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->get();
        
        // Export Excel via Maatwebsite
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\NotesExport($notes),
            'notes_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function statistiques(Request $request)
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
        
        return view('admin.notes.statistiques', compact(
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
    
    public function timeline(Note $note)
    {
        $note->load([
            'etabli',
            'verifie',
            'valide',
            'execute',
            'retourne1',
            'retourne2',
            'annule'
        ]);
        
        $events = [];
        
        // Création
        $events[] = [
            'date' => $note->created_at,
            'action' => 'Création',
            'user' => $note->etabli?->name ?? 'Système',
            'statut' => 'brouillon',
            'icon' => 'plus',
            'color' => 'gray'
        ];
        
        // Date remise étude (dre) - soumission pour vérification
        if ($note->dre) {
            $events[] = [
                'date' => $note->dre,
                'action' => 'Remise de l\'étude',
                'user' => $note->etabli?->name ?? 'Système',
                'statut' => 'en attente de vérification',
                'icon' => 'paper-airplane',
                'color' => 'blue'
            ];
        }
        
        // Vérification
        if ($note->verifie_id) {
            $events[] = [
                'date' => $note->updated_at,
                'action' => 'Vérification',
                'user' => $note->verifie?->name ?? 'Système',
                'statut' => 'vérifiée',
                'icon' => 'check',
                'color' => 'teal'
            ];
        }
        
        // Validation
        if ($note->valide_id) {
            $events[] = [
                'date' => $note->updated_at,
                'action' => 'Validation',
                'user' => $note->valide?->name ?? 'Système',
                'statut' => 'validée',
                'icon' => 'badge-check',
                'color' => 'green'
            ];
        }
        
        // Date début travaux
        if ($note->ddt) {
            $events[] = [
                'date' => $note->ddt,
                'action' => 'Début des travaux',
                'user' => $note->execute?->name ?? 'Opérateur',
                'statut' => 'en cours d\'exécution',
                'icon' => 'play',
                'color' => 'orange'
            ];
        }
        
        // Date fin travaux / exécution réelle
        if ($note->drex) {
            $events[] = [
                'date' => $note->drex,
                'action' => 'Exécution terminée',
                'user' => $note->execute?->name ?? 'Opérateur',
                'statut' => 'exécutée',
                'icon' => 'flag',
                'color' => 'purple'
            ];
        }
        
        // Retour 1
        if ($note->retourne1_id) {
            $events[] = [
                'date' => $note->updated_at,
                'action' => 'Retournée (1)',
                'user' => $note->retourne1?->name ?? 'Système',
                'statut' => 'retournée',
                'icon' => 'undo',
                'color' => 'orange'
            ];
        }
        
        // Retour 2
        if ($note->retourne2_id) {
            $events[] = [
                'date' => $note->updated_at,
                'action' => 'Retournée (2)',
                'user' => $note->retourne2?->name ?? 'Système',
                'statut' => 'retournée',
                'icon' => 'undo',
                'color' => 'orange'
            ];
        }
        
        // Annulation
        if ($note->annule_id) {
            $events[] = [
                'date' => $note->updated_at,
                'action' => 'Annulation',
                'user' => $note->annule?->name ?? 'Système',
                'statut' => 'annulée',
                'icon' => 'times',
                'color' => 'red'
            ];
        }
        
        // Trier par date
        usort($events, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });
        
        return view('admin.notes.timeline', compact('note', 'events'));
    }
}
