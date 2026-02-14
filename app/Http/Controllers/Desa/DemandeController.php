<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Note;
use App\Models\Observation;
use App\Models\Groupe;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DemandeController extends Controller
{
    /**
     * Display the DESA dashboard.
     */
    public function dashboard(Request $request)
    {
        $periode = $request->get('periode', 'mois'); // semaine, mois, annee
        
        // Statistiques des demandes
        $demandesStats = [
            'total' => Demande::count(),
            'recues' => Demande::where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => Demande::where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => Demande::where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => Demande::where('statut', Demande::STATUT_RETOURNEE)->count(),
        ];
        
        // Statistiques des notes
        $notesStats = [
            'total' => Note::count(),
            'brouillon' => Note::where('statut', Note::STATUT_BROUILLON)->count(),
            'en_etude' => Note::where('statut', Note::STATUT_EN_ETUDE)->count(),
            'en_attente_verification' => Note::where('statut', Note::STATUT_EN_ATTENTE_VERIFICATION)->count(),
            'verifiees' => Note::where('statut', Note::STATUT_VERIFIEE)->count(),
            'en_attente_validation' => Note::where('statut', Note::STATUT_EN_ATTENTE_VALIDATION)->count(),
            'validees' => Note::where('statut', Note::STATUT_VALIDEE)->count(),
            'en_cours_execution' => Note::where('statut', Note::STATUT_EN_COURS_EXECUTION)->count(),
            'executees' => Note::where('statut', Note::STATUT_EXECUTEE)->count(),
            'retournees' => Note::where('statut', Note::STATUT_RETOURNEE)->count(),
            'annulees' => Note::where('statut', Note::STATUT_ANNULEE)->count(),
        ];
        
        // Statistiques des observations
        $observationsStats = [
            'total' => Observation::count(),
            'non_lues' => Observation::where('lu', false)->count(),
        ];
        
        // Graph data for Notes (by period)
        $graphData = $this->getNotesGraphData($periode);
        
        // Top groups creating DAPTs
        $topGroupes = Groupe::withCount('demandes')
            ->having('demandes_count', '>', 0)
            ->orderByDesc('demandes_count')
            ->take(5)
            ->get();
        
        // All groupes for filter
        $groupes = Groupe::orderBy('nom')->get();
        
        // Dernières demandes reçues
        $dernieresDemandes = Demande::where('statut', Demande::STATUT_CREEE)
            ->with('demandeur')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Dernières notes
        $dernieresNotes = Note::with(['demande.demandeur', 'etabliPar'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('desa.dashboard', compact(
            'demandesStats', 
            'notesStats', 
            'observationsStats',
            'dernieresDemandes',
            'dernieresNotes',
            'graphData',
            'periode',
            'topGroupes',
            'groupes'
        ));
    }
    
    /**
     * Get graph data for Notes based on period.
     */
    private function getNotesGraphData($periode)
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
        
        switch ($periode) {
            case 'semaine':
                // Last 7 days
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
                // Last 4 weeks
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
                // Last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->locale('fr')->isoFormat('MMM YYYY');
                    
                    foreach (array_keys($data) as $key) {
                        $data[$key][] = Note::where('statut', $statutMap[$key])
                            ->whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
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
     * Display a listing of demandes for DESA.
     */
    public function index(Request $request)
    {
        $query = Demande::with(['demandeur', 'note']);
        
        // Recherche
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(numero_demande) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(designation) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(lieu_execution) like ?', ["%{$search}%"]);
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
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(numero_demande) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(designation) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(lieu_execution) like ?', ["%{$search}%"]);
            });
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
