<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Traits\SearchableTrait;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesObservationsController extends Controller
{
    use SearchableTrait;
    /**
     * Display a listing of the user's observations.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Observation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filtre par statut lu/non lu
        if ($request->filled('lu')) {
            $query->where('lu', $request->lu === '1');
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par priorité
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        // Recherche
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['sujet', 'description'], []);
        }

        $observations = $query->paginate(15);

        // Stats
        $statsQuery = Observation::where('user_id', $user->id);
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'non_lues' => (clone $statsQuery)->where('lu', false)->count(),
            'ouvertes' => (clone $statsQuery)->where('statut', Observation::STATUT_OUVERT)->count(),
            'traitees' => (clone $statsQuery)->where('statut', Observation::STATUT_RESOLU)->count(),
        ];

        return view('mes-observations.index', compact('observations', 'stats'));
    }

    /**
     * Show the form for creating a new observation.
     */
    public function create()
    {
        return view('mes-observations.create');
    }

    /**
     * Store a newly created observation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:bug,suggestion,question,autre',
            'priorite' => 'required|in:basse,normale,haute,urgente',
        ], [
            'sujet.required' => 'Le sujet est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'type.required' => 'Le type est obligatoire.',
            'priorite.required' => 'La priorité est obligatoire.',
        ]);

        $observation = Observation::create([
            'user_id' => Auth::id(),
            'sujet' => $request->sujet,
            'description' => $request->description,
            'type' => $request->type,
            'priorite' => $request->priorite,
            'statut' => Observation::STATUT_OUVERT,
        ]);
        
        // Notifier les admins
        app(NotificationService::class)->notifyNewFeedback($observation);

        return redirect()->route('mes-observations.index')
            ->with('success', 'Votre observation a été envoyée avec succès.');
    }

    /**
     * Display the specified observation.
     */
    public function show(Observation $mes_observation)
    {
        // Ensure the user can only view their own observations
        if ($mes_observation->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas accéder à cette observation.');
        }

        // Marquer comme lue
        if (!$mes_observation->lu) {
            $mes_observation->lu = true;
            $mes_observation->save();
        }

        return view('mes-observations.show', compact('mes_observation'));
    }
}
