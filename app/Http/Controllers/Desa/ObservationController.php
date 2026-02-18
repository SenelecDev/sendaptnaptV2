<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Observation;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObservationController extends Controller
{
    use SearchableTrait;
    /**
     * Display a listing of observations.
     * Admin sees all, other users see only their own.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $query = Observation::with('user')
            ->orderBy('created_at', 'desc');

        // Non-admin users can only see their own observations
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

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

        // Stats also filtered for non-admin
        $statsQuery = Observation::query();
        if (!$isAdmin) {
            $statsQuery->where('user_id', $user->id);
        }
        
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'non_lues' => (clone $statsQuery)->where('lu', false)->count(),
            'ouvertes' => (clone $statsQuery)->where('statut', Observation::STATUT_OUVERT)->count(),
            'traitees' => (clone $statsQuery)->where('statut', Observation::STATUT_RESOLU)->count(),
        ];

        // Determine view prefix based on route
        $routePrefix = explode('.', $request->route()->getName())[0] ?? 'desa';

        return view("{$routePrefix}.observations.index", compact('observations', 'stats'));
    }

    /**
     * Display the specified observation.
     * Non-admin users can only view their own observations.
     */
    public function show(Request $request, Observation $observation)
    {
        $user = Auth::user();
        
        // Non-admin users can only view their own observations
        if (!$user->hasRole('admin') && $observation->user_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette observation.');
        }

        // Marquer comme lue
        if (!$observation->lu) {
            $observation->lu = true;
            $observation->save();
        }

        $observation->load('user');

        // Determine view prefix based on route
        $routePrefix = explode('.', $request->route()->getName())[0] ?? 'desa';

        return view("{$routePrefix}.observations.show", compact('observation'));
    }
}
