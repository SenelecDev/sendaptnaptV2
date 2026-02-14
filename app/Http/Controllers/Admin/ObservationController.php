<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Observation;
use App\Notifications\WorkflowNotification;
use Illuminate\Http\Request;

class ObservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Observation::with(['user', 'traitePar']);
        
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(sujet) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) like ?', ["%{$search}%"]);
            });
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        
        $observations = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.observations.index', compact('observations'));
    }

    public function show(Observation $observation)
    {
        $observation->load(['user', 'traitePar']);
        return view('admin.observations.show', compact('observation'));
    }

    public function update(Request $request, Observation $observation)
    {
        $validated = $request->validate([
            'statut' => 'required|in:ouvert,en cours,résolu,fermé',
            'reponse_admin' => 'nullable|string',
        ]);
        
        $hasNewResponse = $request->filled('reponse_admin') && $observation->reponse_admin !== $request->reponse_admin;
        
        if ($request->filled('reponse_admin')) {
            $validated['date_reponse'] = now();
            $validated['traite_par'] = auth()->id();
        }
        
        $observation->update($validated);
        
        // Notifier l'utilisateur qui a créé l'observation si une réponse a été ajoutée
        if ($hasNewResponse && $observation->user) {
            $observation->user->notify(new WorkflowNotification(
                type: 'feedback_response',
                title: 'Réponse à votre feedback',
                message: "L'administrateur a répondu à votre observation : \"{$observation->sujet}\"",
                actionUrl: "/directeur/feedback",
                actionText: 'Voir la réponse',
                data: ['observation_id' => $observation->id, 'sujet' => $observation->sujet]
            ));
        }
        
        return redirect()->route('admin.observations.index')
                         ->with('success', 'Observation mise à jour avec succès.');
    }

    public function destroy(Observation $observation)
    {
        $observation->delete();
        
        return redirect()->route('admin.observations.index')
                         ->with('success', 'Observation supprimée avec succès.');
    }
    
    public function markAsProcessed(Observation $observation)
    {
        $observation->update([
            'statut' => 'résolu',
            'traite_par' => auth()->id(),
        ]);
        
        // Notifier l'utilisateur que son observation a été résolue
        if ($observation->user) {
            $observation->user->notify(new WorkflowNotification(
                type: 'feedback_resolved',
                title: 'Votre feedback a été traité',
                message: "Votre observation \"{$observation->sujet}\" a été marquée comme résolue.",
                actionUrl: "/directeur/feedback",
                actionText: 'Voir le feedback',
                data: ['observation_id' => $observation->id, 'sujet' => $observation->sujet]
            ));
        }
        
        return redirect()->back()
                         ->with('success', 'Observation marquée comme résolue.');
    }
}
