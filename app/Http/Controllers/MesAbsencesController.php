<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MesAbsencesController extends Controller
{
    /**
     * Liste des absences de l'utilisateur connecté (comme titulaire)
     * et les intérims qu'il assure (comme intérimaire)
     */
    public function index()
    {
        $user = auth()->user();
        
        // Mes absences (je suis le titulaire absent)
        $mesAbsences = Absence::where('user_id', $user->id)
            ->with('interim')
            ->orderBy('date_debut', 'desc')
            ->get();
        
        // Intérims que j'assure (je suis l'intérimaire)
        $mesInterims = Absence::where('interim_id', $user->id)
            ->with('user')
            ->orderBy('date_debut', 'desc')
            ->get();
        
        // Utilisateurs disponibles pour être intérimaires (même groupe ou tous si admin)
        $users = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('mes-absences.index', compact('mesAbsences', 'mesInterims', 'users'));
    }

    /**
     * Formulaire de création d'absence
     */
    public function create()
    {
        $user = auth()->user();
        
        // Utilisateurs disponibles pour être intérimaires
        $users = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Mes rôles
        $mesRoles = $user->getRoleNames()->toArray();
        
        return view('mes-absences.create', compact('users', 'mesRoles'));
    }

    /**
     * Enregistrer une nouvelle absence
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'interim_id' => 'required|exists:users,id|different:user_id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:50',
        ]);
        
        // Vérifier que l'intérimaire n'est pas lui-même
        if ($validated['interim_id'] == $user->id) {
            return back()->withInput()->withErrors([
                'interim_id' => 'Vous ne pouvez pas vous désigner comme votre propre intérimaire.'
            ]);
        }
        
        // Vérifier qu'il n'y a pas de chevauchement
        $chevauchement = Absence::where('user_id', $user->id)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                  ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('date_debut', '<=', $validated['date_debut'])
                         ->where('date_fin', '>=', $validated['date_fin']);
                  });
            })
            ->exists();
            
        if ($chevauchement) {
            return back()->withInput()->withErrors([
                'date_debut' => 'Vous avez déjà une absence déclarée sur cette période.'
            ]);
        }
        
        // Si le rôle est vide, le mettre à null
        if (empty($validated['role'])) {
            $validated['role'] = null;
        }
        
        $absence = Absence::create([
            'user_id' => $user->id,
            'interim_id' => $validated['interim_id'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'motif' => $validated['motif'] ?? null,
            'role' => $validated['role'],
        ]);
        
        // Notifier l'intérimaire
        app(NotificationService::class)->notifyInterimAssigned($absence);
        
        return redirect()->route('mes-absences.index')
                         ->with('success', 'Absence déclarée avec succès. Votre intérimaire a été notifié.');
    }

    /**
     * Formulaire d'édition d'une absence
     */
    public function edit(Absence $mes_absence)
    {
        $user = auth()->user();
        
        // Seul le titulaire ou un admin peut modifier
        if ($mes_absence->user_id != $user->id && !$user->hasRole('admin')) {
            abort(403, 'Vous ne pouvez modifier que vos propres absences.');
        }
        
        $users = User::where('id', '!=', $mes_absence->user_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $mesRoles = $user->getRoleNames()->toArray();
        $absence = $mes_absence; // Pour la vue
        
        return view('mes-absences.edit', compact('absence', 'users', 'mesRoles'));
    }

    /**
     * Mettre à jour une absence
     */
    public function update(Request $request, Absence $mes_absence)
    {
        $user = auth()->user();
        
        // Seul le titulaire ou un admin peut modifier
        if ($mes_absence->user_id != $user->id && !$user->hasRole('admin')) {
            abort(403, 'Vous ne pouvez modifier que vos propres absences.');
        }
        
        $validated = $request->validate([
            'interim_id' => 'required|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:50',
        ]);
        
        // Vérifier qu'il n'y a pas de chevauchement (sauf cette absence)
        $chevauchement = Absence::where('user_id', $user->id)
            ->where('id', '!=', $mes_absence->id)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                  ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('date_debut', '<=', $validated['date_debut'])
                         ->where('date_fin', '>=', $validated['date_fin']);
                  });
            })
            ->exists();
            
        if ($chevauchement) {
            return back()->withInput()->withErrors([
                'date_debut' => 'Vous avez déjà une absence déclarée sur cette période.'
            ]);
        }
        
        if (empty($validated['role'])) {
            $validated['role'] = null;
        }
        
        $mes_absence->update($validated);
        
        return redirect()->route('mes-absences.index')
                         ->with('success', 'Absence mise à jour avec succès.');
    }

    /**
     * Supprimer une absence
     */
    public function destroy(Absence $mes_absence)
    {
        $user = auth()->user();
        
        // Seul le titulaire ou un admin peut supprimer
        if ($mes_absence->user_id != $user->id && !$user->hasRole('admin')) {
            abort(403, 'Vous ne pouvez supprimer que vos propres absences.');
        }
        
        $mes_absence->delete();
        
        return redirect()->route('mes-absences.index')
                         ->with('success', 'Absence supprimée avec succès.');
    }
}
