<?php

namespace App\Http\Controllers\Demandeur;

use App\Http\Controllers\Controller;
use App\Models\Observation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObservationController extends Controller
{
    /**
     * Display a listing of the user's observations.
     */
    public function index()
    {
        $observations = Observation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('demandeur.observations.index', compact('observations'));
    }

    /**
     * Show the form for creating a new observation.
     */
    public function create()
    {
        return view('demandeur.observations.create');
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

        return redirect()->route('demandeur.observations.index')
            ->with('success', 'Votre observation a été envoyée avec succès.');
    }

    /**
     * Display the specified observation.
     */
    public function show(Observation $observation)
    {
        // Ensure the user can only view their own observations
        if ($observation->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas accéder à cette observation.');
        }

        return view('demandeur.observations.show', compact('observation'));
    }
}
