<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Demande::with(['demandeur', 'notes']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(numero) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(objet) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) like ?', ["%{$search}%"]);
            });
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
        
        $demandes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Demande::count(),
            'creees' => Demande::where('statut', 'créée')->count(),
            'en_cours' => Demande::where('statut', 'en cours de traitement')->count(),
            'acceptees' => Demande::where('statut', 'acceptée')->count(),
            'retournees' => Demande::where('statut', 'retournée')->count(),
        ];
        
        return view('admin.demandes.index', compact('demandes', 'stats'));
    }

    public function show(Demande $demande)
    {
        $demande->load(['demandeur', 'chargeTravaux', 'traite', 'note', 'histories.user']);
        return view('admin.demandes.show', compact('demande'));
    }

    public function destroy(Demande $demande)
    {
        // Supprimer uniquement si pas de notes liées
        if ($demande->notes()->count() > 0) {
            return redirect()->back()
                             ->with('error', 'Impossible de supprimer cette demande car elle a des notes associées.');
        }
        
        $demande->delete();
        
        return redirect()->route('admin.demandes.index')
                         ->with('success', 'Demande supprimée avec succès.');
    }
    
    public function export(Request $request)
    {
        $query = Demande::with(['demandeur']);
        
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
        
        $demandes = $query->orderBy('created_at', 'desc')->get();
        
        // Export Excel via Maatwebsite
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DemandesExport($demandes),
            'demandes_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function statistiques(Request $request)
    {
        // Stats par mois
        $parMois = Demande::select(
            DB::raw('MONTH(created_at) as mois'),
            DB::raw('YEAR(created_at) as annee'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN statut = 'acceptée' THEN 1 ELSE 0 END) as acceptees"),
            DB::raw("SUM(CASE WHEN statut = 'retournée' THEN 1 ELSE 0 END) as retournees")
        )
        ->whereYear('created_at', $request->get('annee', now()->year))
        ->groupBy(DB::raw('MONTH(created_at)'), DB::raw('YEAR(created_at)'))
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
        $delaiMoyen = Demande::whereIn('statut', ['acceptée', 'retournée'])
            ->whereNotNull('date_traitement')
            ->selectRaw('AVG(DATEDIFF(date_traitement, created_at)) as delai_moyen')
            ->first();
        
        return view('admin.demandes.statistiques', compact('parMois', 'parDemandeur', 'delaiMoyen'));
    }
}
