<?php

namespace App\Http\Controllers\Demandeur;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use App\Models\Demande;
use App\Models\Groupe;
use App\Models\ChargeTravaux;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Mail\StatutDemandeMail;
use App\Http\Controllers\Controller;
use App\Traits\SearchableTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DemandeController extends Controller
{
    use SearchableTrait;
    /**
     * Display the demandeur dashboard with statistics and graphs.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $periode = $request->get('periode', 'mois'); // semaine, mois, annee
        
        // Base query for demandeur's demandes (including group)
        $baseQuery = function() use ($user) {
            return Demande::where(function($q) use ($user) {
                $q->where('demandeur_id', $user->id);
                if ($user->groupe_id) {
                    $q->orWhereHas('demandeur', function($subQ) use ($user) {
                        $subQ->where('groupe_id', $user->groupe_id);
                    });
                }
            });
        };
        
        // Statistics by status
        $stats = [
            'total' => $baseQuery()->count(),
            'creees' => $baseQuery()->where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => $baseQuery()->where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => $baseQuery()->where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => $baseQuery()->where('statut', Demande::STATUT_RETOURNEE)->count(),
            'brouillons' => $baseQuery()->where('statut', Demande::STATUT_BROUILLON)->count(),
        ];
        
        // Graph data based on period
        $graphData = $this->getGraphData($baseQuery, $periode);
        
        // Recent demandes
        $dernieresDemandes = $baseQuery()
            ->with(['demandeur', 'notes'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Top demandeurs du groupe
        $topDemandeurs = [];
        if ($user->groupe_id) {
            $topDemandeurs = User::where('groupe_id', $user->groupe_id)
                ->withCount('demandes')
                ->whereHas('demandes')
                ->orderByDesc('demandes_count')
                ->take(5)
                ->get();
        }
        
        // Groupe info
        $groupe = $user->groupe;
        
        return view('demandeur.dashboard', compact('stats', 'graphData', 'periode', 'dernieresDemandes', 'topDemandeurs', 'groupe'));
    }
    
    /**
     * Get graph data based on the selected period.
     */
    private function getGraphData($baseQuery, $periode)
    {
        $labels = [];
        $data = [
            'creees' => [],
            'en_cours' => [],
            'acceptees' => [],
            'retournees' => [],
            'brouillons' => [],
        ];
        
        switch ($periode) {
            case 'semaine':
                // Last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->locale('fr')->isoFormat('ddd DD/MM');
                    
                    foreach (array_keys($data) as $key) {
                        $statut = $this->getStatutFromKey($key);
                        $data[$key][] = $baseQuery()
                            ->where('statut', $statut)
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
                        $statut = $this->getStatutFromKey($key);
                        $data[$key][] = $baseQuery()
                            ->where('statut', $statut)
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
                        $statut = $this->getStatutFromKey($key);
                        $data[$key][] = $baseQuery()
                            ->where('statut', $statut)
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
     * Convert data key to Demande status constant.
     */
    private function getStatutFromKey($key)
    {
        return match($key) {
            'creees' => Demande::STATUT_CREEE,
            'en_cours' => Demande::STATUT_EN_COURS,
            'acceptees' => Demande::STATUT_ACCEPTEE,
            'retournees' => Demande::STATUT_RETOURNEE,
            'brouillons' => Demande::STATUT_BROUILLON,
            default => Demande::STATUT_CREEE,
        };
    }

    /**
     * Display a listing of the demandeur's demandes.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Les demandeurs voient leurs propres demandes ET celles des autres users de leur groupe
        $query = Demande::where(function($q) use ($user) {
            $q->where('demandeur_id', $user->id);
            if ($user->groupe_id) {
                $q->orWhereHas('demandeur', function($subQ) use ($user) {
                    $subQ->where('groupe_id', $user->groupe_id);
                });
            }
        })->with(['notes', 'demandeur', 'chargeTravaux', 'chargeTravauxExterne']);
        
        // Recherche
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search,
                ['numero_demande', 'designation', 'lieu_execution', 'statut'],
                ['demandeur' => ['name', 'matricule'], 'chargeTravaux' => ['name', 'matricule']]);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $demandes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistiques pour le groupe
        $baseQuery = Demande::where(function($q) use ($user) {
            $q->where('demandeur_id', $user->id);
            if ($user->groupe_id) {
                $q->orWhereHas('demandeur', function($subQ) use ($user) {
                    $subQ->where('groupe_id', $user->groupe_id);
                });
            }
        });
        
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'creees' => (clone $baseQuery)->where('statut', Demande::STATUT_CREEE)->count(),
            'en_cours' => (clone $baseQuery)->where('statut', Demande::STATUT_EN_COURS)->count(),
            'acceptees' => (clone $baseQuery)->where('statut', Demande::STATUT_ACCEPTEE)->count(),
            'retournees' => (clone $baseQuery)->where('statut', Demande::STATUT_RETOURNEE)->count(),
            'brouillons' => (clone $baseQuery)->where('statut', Demande::STATUT_BROUILLON)->count(),
        ];
        
        return view('demandeur.demandes.index', compact('demandes', 'stats'));
    }

    /**
     * Show the form for creating a new demande.
     */
    public function create()
    {
        // Récupérer les demandeurs (tous les utilisateurs avec rôle demandeur ou du même groupe)
        $user = Auth::user();
        $demandeurs = User::where('groupe_id', $user->groupe_id)
                          ->orWhereHas('roles', function($q) {
                              $q->where('name', 'demandeur');
                          })
                          ->orderBy('name')
                          ->get();
        
        // Chargés de travaux internes (tous les utilisateurs)
        $cts = User::orderBy('name')->get();
        
        // Chargés de travaux externes
        $ctsExternes = ChargeTravaux::actif()->orderBy('nom')->get();
        
        return view('demandeur.demandes.create', compact('demandeurs', 'cts', 'ctsExternes'));
    }

    /**
     * Store a newly created demande in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation selon le mode de saisie
            $rules = [
                'date' => 'required|date',
                'destinataire' => 'required|string|max:255|in:DESA,DD',
                'designation' => 'required|string',
                'ddp' => 'required|date',
                'hdp' => 'required',
                'dfp' => 'required|date|after_or_equal:ddp',
                'hfp' => 'required',
                'mode_saisie' => 'required|in:gmao,manuel',
                'mte' => 'required|in:oui,non',
                'mcce' => 'required|in:oui,non',
                'etape' => 'required|in:ue,de',
                'demandeur_id' => 'required|exists:users,id',
                // CT interne OU externe
                'charge_travaux_id' => 'nullable|exists:users,id',
                'charge_travaux_externe_id' => 'nullable|exists:charges_travaux,id',
                'ct_externe_nom' => 'nullable|string|max:255',
                'ct_externe_telephone' => 'nullable|string|max:50',
                'ct_externe_entreprise' => 'nullable|string|max:255',
                'ct_externe_service' => 'nullable|string|max:255',
                'renseignement' => 'required|string',
                'schema' => 'required|file|mimes:png,jpg,jpeg|max:10240',
                'telephone_demandeur' => 'nullable|string|max:50',
                'telephone_charge' => 'nullable|string|max:50',
            ];
            
            // Validation conditionnelle selon le mode
            if ($request->input('mode_saisie') === 'manuel') {
                $rules['lieu_execution_manuel'] = 'required|string';
                $rules['ouvrages_consigner_manuel'] = 'required|string';
                $rules['ouvrages_installer_manuel'] = 'nullable|string';
            } else {
                $rules['lieu_execution'] = 'required|string|max:255';
                $rules['ouvrage_type'] = 'required|in:ligne,poste';
            }
            
            $validated = $request->validate($rules);
            
            // Vérifier qu'on a soit un CT interne, soit un CT externe existant, soit un nouveau CT externe
            if (empty($validated['charge_travaux_id']) && empty($validated['charge_travaux_externe_id']) && empty($validated['ct_externe_nom'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Veuillez sélectionner ou ajouter un chargé de travaux.',
                        'errors' => ['charge_travaux_id' => ['Veuillez sélectionner ou ajouter un chargé de travaux.']]
                    ], 422);
                }
                return back()->withErrors(['charge_travaux_id' => 'Veuillez sélectionner ou ajouter un chargé de travaux.'])->withInput();
            }
            
            // Gérer le chargé de travaux externe
        $chargeTravauxExterneId = null;
        
        // Si un CT externe existant est sélectionné
        if (!empty($validated['charge_travaux_externe_id'])) {
            $chargeTravauxExterneId = $validated['charge_travaux_externe_id'];
        }
        // Si un nouveau CT externe est ajouté (via modal)
        elseif (!empty($validated['ct_externe_nom'])) {
            // Rechercher s'il existe déjà (même nom + même téléphone)
            $ctExterne = ChargeTravaux::where('nom', $validated['ct_externe_nom'])
                ->where('telephone', $validated['ct_externe_telephone'] ?? null)
                ->first();
            
            if (!$ctExterne) {
                // Créer le nouveau CT externe
                $ctExterne = ChargeTravaux::create([
                    'nom' => $validated['ct_externe_nom'],
                    'telephone' => $validated['ct_externe_telephone'] ?? null,
                    'entreprise' => $validated['ct_externe_entreprise'] ?? null,
                    'service' => $validated['ct_externe_service'] ?? null,
                    'actif' => true,
                ]);
            }
            $chargeTravauxExterneId = $ctExterne->id;
        }
        
        // Déterminer le statut
        $statut = ($request->input('statut') === 'brouillon') 
            ? Demande::STATUT_BROUILLON 
            : Demande::STATUT_CREEE;
        
        // Créer la demande (le numéro sera généré automatiquement par le modèle)
        $demande = new Demande();
        // numero_demande sera généré automatiquement dans le boot() du modèle
        $demande->date = $validated['date'];
        $demande->destinataire = $validated['destinataire'];
        $demande->designation = $validated['designation'];
        $demande->ddp = $validated['ddp'];
        $demande->hdp = $validated['hdp'];
        $demande->dfp = $validated['dfp'];
        $demande->hfp = $validated['hfp'];
        $demande->mode_saisie = $validated['mode_saisie'];
        $demande->mte = $validated['mte'];
        $demande->mcce = $validated['mcce'];
        $demande->etape = $validated['etape'];
        $demande->demandeur_id = $validated['demandeur_id'];
        $demande->charge_travaux_id = $validated['charge_travaux_id'] ?? null;
        $demande->charge_travaux_externe_id = $chargeTravauxExterneId;
        $demande->user_id = Auth::id();
        $demande->statut = $statut;
        $demande->renseignement = $validated['renseignement'] ?? null;
        $demande->telephone_demandeur = $validated['telephone_demandeur'] ?? null;
        $demande->telephone_charge = $validated['telephone_charge'] ?? null;
        
        // Gérer DMRP
        if ($request->input('dmrp_type') === 'non_applicable' || !$request->filled('dmrp')) {
            $demande->dmrp = null;
        } else {
            $demande->dmrp = $request->input('dmrp');
        }
        $demande->dmrp_restitution = $request->boolean('dmrp_restitution');
        
        // Mode de saisie
        if ($validated['mode_saisie'] === 'manuel') {
            $demande->lieu_execution = $request->input('lieu_execution_manuel');
            $demande->lieu_execution_manuel = $request->input('lieu_execution_manuel');
            $demande->ouvrages_consigner_manuel = $request->input('ouvrages_consigner_manuel');
            $demande->ouvrages_installer_manuel = $request->input('ouvrages_installer_manuel');
            $demande->ouvrage_type = 'manuel';
            $demande->ouvrage_type_installer = 'manuel';
        } else {
            $demande->lieu_execution = $validated['lieu_execution'];
            $demande->lieu_code = $request->input('lieu_code');
            $demande->ouvrage_type = $validated['ouvrage_type'] ?? 'ligne';
            $demande->ouvrage_type_installer = $request->input('ouvrage_type_installer', 'ligne_installer');
            
            // Traiter les données GMAO
            $this->processGmaoData($request, $demande);
        }
        
        // Schéma si fourni
        if ($request->hasFile('schema') && $request->file('schema')->isValid()) {
            $demande->schema = $request->file('schema')->store('schema', 'public');
        }
        
        $demande->save();
        
        // Générer le PDF
        $this->generatePDF($demande);
        
        // Envoi du mail (seulement si pas brouillon)
        if ($statut !== Demande::STATUT_BROUILLON) {
            $this->sendNotificationEmail($demande, 'Votre demande a été créée avec succès');
            
            // Notification interne aux DESA
            app(NotificationService::class)->notifyDaptCreated($demande);
        }
        
        // Message de succès
        $message = ($statut === Demande::STATUT_BROUILLON)
            ? 'Demande enregistrée en tant que brouillon avec succès !'
            : 'Demande soumise avec succès ! Numéro: ' . $demande->numero_demande;
        
        // Réponse JSON pour requête AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'numero' => $demande->numero_demande,
                'redirect' => route('demandeur.demandes.index')
            ]);
        }
            
        return redirect()->route('demandeur.demandes.index')
                         ->with('success', $message);
                         
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur création demande: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified demande.
     */
    public function show(Demande $demande)
    {
        $user = Auth::user();
        
        // Vérifier l'accès (sa demande ou même groupe)
        if ($demande->demandeur_id !== $user->id && 
            $demande->demandeur?->groupe_id !== $user->groupe_id) {
            abort(403, 'Accès non autorisé');
        }
        
        $demande->load(['notes', 'chargeTravaux', 'demandeur']);
        return view('demandeur.demandes.show', compact('demande'));
    }

    /**
     * Show the form for editing the specified demande.
     */
    public function edit(Demande $demande)
    {
        $user = Auth::user();
        
        // Seul le créateur peut modifier sa demande
        if ($demande->demandeur_id !== $user->id) {
            abort(403, 'Vous ne pouvez modifier que vos propres demandes.');
        }
        
        // Ne peut modifier que si statut retournée ou brouillon (pas créée/en cours/acceptée)
        if (!in_array($demande->statut, [Demande::STATUT_RETOURNEE, Demande::STATUT_BROUILLON])) {
            return redirect()->route('demandeur.demandes.show', $demande)
                             ->with('error', 'Cette demande ne peut plus être modifiée.');
        }
        
        // Récupérer les demandeurs
        $demandeurs = User::where('groupe_id', $user->groupe_id)
                          ->orWhereHas('roles', function($q) {
                              $q->where('name', 'demandeur');
                          })
                          ->orderBy('name')
                          ->get();
        
        $cts = User::orderBy('name')->get();
        
        // Chargés de travaux externes (requis pour pré-sélectionner le CT en édition)
        $ctsExternes = ChargeTravaux::actif()->orderBy('nom')->get();
        
        // Charger les relations pour le formulaire
        $demande->load(['chargeTravaux', 'chargeTravauxExterne']);
        
        return view('demandeur.demandes.create', compact('demande', 'demandeurs', 'cts', 'ctsExternes'));
    }

    /**
     * Update the specified demande in storage.
     */
    public function update(Request $request, Demande $demande)
    {
        $user = Auth::user();
        
        // Seul le créateur peut modifier sa demande
        if ($demande->demandeur_id !== $user->id) {
            abort(403, 'Vous ne pouvez modifier que vos propres demandes.');
        }
        
        // Ne peut modifier que si statut retournée ou brouillon (pas créée/en cours/acceptée)
        if (!in_array($demande->statut, [Demande::STATUT_RETOURNEE, Demande::STATUT_BROUILLON])) {
            return redirect()->route('demandeur.demandes.show', $demande)
                             ->with('error', 'Cette demande ne peut plus être modifiée.');
        }
        
        // Validation (alignée sur store : CT interne OU externe)
        $rules = [
            'date' => 'required|date',
            'destinataire' => 'required|string|max:255|in:DESA,DD',
            'designation' => 'required|string',
            'ddp' => 'required|date',
            'hdp' => 'required',
            'dfp' => 'required|date|after_or_equal:ddp',
            'hfp' => 'required',
            'mode_saisie' => 'required|in:gmao,manuel',
            'mte' => 'required|in:oui,non',
            'mcce' => 'required|in:oui,non',
            'etape' => 'required|in:ue,de',
            'demandeur_id' => 'required|exists:users,id',
            'charge_travaux_id' => 'nullable|exists:users,id',
            'charge_travaux_externe_id' => 'nullable|exists:charges_travaux,id',
            'ct_externe_nom' => 'nullable|string|max:255',
            'ct_externe_telephone' => 'nullable|string|max:50',
            'ct_externe_entreprise' => 'nullable|string|max:255',
            'ct_externe_service' => 'nullable|string|max:255',
            'renseignement' => 'required|string',
            'schema' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            'telephone_demandeur' => 'nullable|string|max:50',
            'telephone_charge' => 'nullable|string|max:50',
        ];
        
        if ($request->input('mode_saisie') === 'manuel') {
            $rules['lieu_execution_manuel'] = 'required|string';
            $rules['ouvrages_consigner_manuel'] = 'required|string';
        } else {
            $rules['lieu_execution'] = 'required|string|max:255';
            $rules['ouvrage_type'] = 'required|in:ligne,poste';
        }
        
        $validated = $request->validate($rules);
        
        // Vérifier qu'on a soit un CT interne, soit un CT externe
        if (empty($validated['charge_travaux_id']) && empty($validated['charge_travaux_externe_id']) && empty($validated['ct_externe_nom'])) {
            return back()->withErrors(['charge_travaux_id' => 'Veuillez sélectionner ou ajouter un chargé de travaux.'])->withInput();
        }
        
        // Gérer le chargé de travaux externe (comme dans store)
        $chargeTravauxExterneId = null;
        if (!empty($validated['charge_travaux_externe_id'])) {
            $chargeTravauxExterneId = $validated['charge_travaux_externe_id'];
        } elseif (!empty($validated['ct_externe_nom'])) {
            $ctExterne = ChargeTravaux::where('nom', $validated['ct_externe_nom'])
                ->where('telephone', $validated['ct_externe_telephone'] ?? null)
                ->first();
            if (!$ctExterne) {
                $ctExterne = ChargeTravaux::create([
                    'nom' => $validated['ct_externe_nom'],
                    'telephone' => $validated['ct_externe_telephone'] ?? null,
                    'entreprise' => $validated['ct_externe_entreprise'] ?? null,
                    'service' => $validated['ct_externe_service'] ?? null,
                    'actif' => true,
                ]);
            }
            $chargeTravauxExterneId = $ctExterne->id;
        }
        
        // Mettre à jour les champs
        $demande->date = $validated['date'];
        $demande->destinataire = $validated['destinataire'];
        $demande->designation = $validated['designation'];
        $demande->ddp = $validated['ddp'];
        $demande->hdp = $validated['hdp'];
        $demande->dfp = $validated['dfp'];
        $demande->hfp = $validated['hfp'];
        $demande->mode_saisie = $validated['mode_saisie'];
        $demande->mte = $validated['mte'];
        $demande->mcce = $validated['mcce'];
        $demande->etape = $validated['etape'];
        $demande->demandeur_id = $validated['demandeur_id'];
        $demande->charge_travaux_id = $validated['charge_travaux_id'] ?? null;
        $demande->charge_travaux_externe_id = $chargeTravauxExterneId;
        $demande->renseignement = $validated['renseignement'] ?? null;
        $demande->telephone_demandeur = $request->input('telephone_demandeur');
        $demande->telephone_charge = $request->input('telephone_charge');
        
        // Gérer DMRP
        if ($request->input('dmrp_type') === 'non_applicable' || !$request->filled('dmrp')) {
            $demande->dmrp = null;
        } else {
            $demande->dmrp = $request->input('dmrp');
        }
        $demande->dmrp_restitution = $request->boolean('dmrp_restitution');
        
        // Si retournée ou brouillon, remettre en créée (soumission)
        if (in_array($demande->statut, [Demande::STATUT_RETOURNEE, Demande::STATUT_BROUILLON])) {
            $demande->statut = Demande::STATUT_CREEE;
        }
        
        // Mode de saisie
        if ($validated['mode_saisie'] === 'manuel') {
            $demande->lieu_execution = $request->input('lieu_execution_manuel');
            $demande->lieu_execution_manuel = $request->input('lieu_execution_manuel');
            $demande->ouvrages_consigner_manuel = $request->input('ouvrages_consigner_manuel');
            $demande->ouvrages_installer_manuel = $request->input('ouvrages_installer_manuel');
            $demande->ouvrage_type = 'manuel';
        } else {
            $demande->lieu_execution = $validated['lieu_execution'];
            $demande->lieu_code = $request->input('lieu_code');
            $demande->ouvrage_type = $validated['ouvrage_type'] ?? 'ligne';
            
            // Traiter les données GMAO
            $this->processGmaoData($request, $demande);
        }
        
        // Schéma si fourni
        if ($request->hasFile('schema') && $request->file('schema')->isValid()) {
            // Supprimer l'ancien fichier
            if ($demande->schema) {
                Storage::disk('public')->delete($demande->schema);
            }
            $demande->schema = $request->file('schema')->store('schema', 'public');
        }
        
        $demande->save();
        
        // Régénérer le PDF
        $this->generatePDF($demande);
        
        return redirect()->route('demandeur.demandes.show', $demande)
                         ->with('success', 'Demande mise à jour avec succès.');
    }

    /**
     * Remove the specified demande from storage.
     */
    public function destroy(Demande $demande)
    {
        $user = Auth::user();
        
        // Seul le créateur peut supprimer sa demande
        if ($demande->demandeur_id !== $user->id) {
            abort(403, 'Vous ne pouvez supprimer que vos propres demandes.');
        }
        
        // Ne peut supprimer que les brouillons (demande non envoyée)
        if ($demande->statut !== Demande::STATUT_BROUILLON) {
            return redirect()->route('demandeur.demandes.index')
                             ->with('error', 'Cette demande ne peut pas être supprimée.');
        }
        
        if ($demande->notes()->count() > 0) {
            return redirect()->route('demandeur.demandes.index')
                             ->with('error', 'Cette demande a des notes associées et ne peut pas être supprimée.');
        }
        
        // Supprimer le schéma et le PDF
        if ($demande->schema) {
            Storage::disk('public')->delete($demande->schema);
        }
        if ($demande->pdf_path) {
            Storage::disk('public')->delete($demande->pdf_path);
        }
        
        $demande->delete();
        
        return redirect()->route('demandeur.demandes.index')
                         ->with('success', 'Demande supprimée avec succès.');
    }

    /**
     * Traitement des données GMAO (lignes et équipements hiérarchiques)
     */
    private function processGmaoData(Request $request, Demande $demande): void
    {
        Log::info('processGmaoData appelé', [
            'all_inputs' => $request->all(),
            'has_ligne_ids' => $request->has('ligne_ids'),
            'ligne_ids' => $request->input('ligne_ids'),
        ]);
        
        // Stocker les codes Oracle des lignes (À CONSIGNER)
        if ($request->has('ligne_ids') && !empty($request->input('ligne_ids'))) {
            $ligneIds = array_filter($request->input('ligne_ids'));
            if (!empty($ligneIds)) {
                $lignesData = $this->getOracleEquipementsData($ligneIds);
                $demande->lignes_oracle = json_encode($lignesData);
            }
        }

        // Stocker les codes Oracle des lignes (À INSTALLER)
        if ($request->has('ligne_installer_ids') && !empty($request->input('ligne_installer_ids'))) {
            $ligneInstallerIds = array_filter($request->input('ligne_installer_ids'));
            if (!empty($ligneInstallerIds)) {
                $lignesInstallerData = $this->getOracleEquipementsData($ligneInstallerIds);
                $demande->lignes_installer_oracle = json_encode($lignesInstallerData);
            }
        }

        // Gestion des équipements Oracle hiérarchiques (À CONSIGNER)
        $equipementsConsignerData = [];
        for ($level = 1; $level <= 6; $level++) {
            $fieldName = "equipements_consigner_level_{$level}";
            Log::info("Vérification équipements consigner niveau {$level}", [
                'fieldName' => $fieldName,
                'has_field' => $request->has($fieldName),
                'value' => $request->input($fieldName),
            ]);
            if ($request->has($fieldName) && !empty($request->input($fieldName))) {
                $codes = $request->input($fieldName);
                $equipementsAvecDescriptions = $this->getOracleEquipementsData($codes);
                $equipementsConsignerData[$fieldName] = $equipementsAvecDescriptions;
            }
        }

        if (!empty($equipementsConsignerData)) {
            $demande->equipements_oracle = json_encode($equipementsConsignerData);
        }

        // Gestion des équipements Oracle hiérarchiques (À INSTALLER)
        $equipementsInstallerData = [];
        for ($level = 1; $level <= 6; $level++) {
            $fieldName = "equipements_installer_level_{$level}";
            if ($request->has($fieldName) && !empty($request->input($fieldName))) {
                $codes = $request->input($fieldName);
                $equipementsAvecDescriptions = $this->getOracleEquipementsData($codes);
                $equipementsInstallerData[$fieldName] = $equipementsAvecDescriptions;
            }
        }

        if (!empty($equipementsInstallerData)) {
            $demande->equipements_installer_oracle = json_encode($equipementsInstallerData);
        }
    }

    /**
     * Récupère les données complètes des équipements Oracle par leurs codes
     */
    private function getOracleEquipementsData(array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        try {
            $oracleController = new \App\Http\Controllers\SqlServerEquipementController();
            return $oracleController->getEquipementsByCodes($codes);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des données GMAO: " . $e->getMessage());
            // Fallback: retourner les codes simples
            return array_map(function($code) {
                return ['code' => $code, 'description' => $code];
            }, $codes);
        }
    }

    /**
     * Générer le PDF de la demande
     */
    private function generatePDF(Demande $demande): void
    {
        try {
            // Récupérer le demandeur et son supérieur (n1)
            $n1 = $demande->demandeur?->n1;

            // Convertir le schéma et la signature en Base64
            $schema = $demande->schema 
                ? $this->convertImageToBase64(Storage::disk('public')->path($demande->schema)) 
                : null;
            $signatureN1 = ($n1 && $n1->signature) 
                ? $this->convertImageToBase64(Storage::disk('public')->path($n1->signature)) 
                : null;

            // Configurer Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            // Générer le HTML
            $html = view('pdf.dapt', compact('demande', 'schema', 'signatureN1'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Nom du fichier
            $fileName = 'demande_' . $demande->numero_demande . '.pdf';
            $filePath = 'pdfs/' . $fileName;

            // Supprimer les anciens PDF
            $existingFiles = Storage::disk('public')->files('pdfs');
            foreach ($existingFiles as $file) {
                if (str_contains($file, 'demande_' . $demande->numero_demande)) {
                    Storage::disk('public')->delete($file);
                }
            }

            // Enregistrer le nouveau PDF
            Storage::disk('public')->put($filePath, $dompdf->output());
            $demande->update(['pdf_path' => $filePath]);
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de la génération du PDF: " . $e->getMessage());
        }
    }

    /**
     * Convertir une image en Base64
     */
    private function convertImageToBase64(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $imageContent = file_get_contents($path);
        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageContent);
    }

    /**
     * Envoyer l'email de notification
     */
    private function sendNotificationEmail(Demande $demande, string $message): void
    {
        try {
            $recipients = [$demande->demandeur->email];
            $cc = ['previsions@senelec.sn'];
            
            // Ajouter le supérieur en copie si existe
            if ($demande->demandeur->n1) {
                $cc[] = $demande->demandeur->n1->email;
            }
            
            Mail::to($recipients)
                ->cc($cc)
                ->send(new StatutDemandeMail($demande, $message));
                
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email: " . $e->getMessage());
        }
    }
}
