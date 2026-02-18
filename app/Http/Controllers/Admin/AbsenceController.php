<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    use SearchableTrait;

    public function index(Request $request)
    {
        $query = Absence::with(['user', 'interim']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($q2) use ($request) {
                    $this->applySimpleSearch($q2, $request->search, ['name', 'matricule'], []);
                })->orWhereHas('interim', function ($q2) use ($request) {
                    $this->applySimpleSearch($q2, $request->search, ['name', 'matricule'], []);
                });
            });
        }
        
        if ($request->filled('statut')) {
            if ($request->statut === 'active') {
                $query->where('date_debut', '<=', now())
                      ->where('date_fin', '>=', now());
            } elseif ($request->statut === 'future') {
                $query->where('date_debut', '>', now());
            } elseif ($request->statut === 'passee') {
                $query->where('date_fin', '<', now());
            }
        }
        
        $absences = $query->orderBy('date_debut', 'desc')->paginate(20);
        $users = User::orderBy('name')->get();
        
        return view('admin.absences.index', compact('absences', 'users'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.absences.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'interim_id' => 'required|exists:users,id|different:user_id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:50',
        ]);
        
        // Convertir role vide en NULL (tous les rôles)
        if (empty($validated['role'])) {
            $validated['role'] = null;
        }
        
        // Vérifier qu'il n'y a pas de chevauchement pour le titulaire
        $chevauchement = Absence::where('user_id', $validated['user_id'])
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
                'date_debut' => 'Il existe déjà une absence pour ce titulaire sur cette période.'
            ]);
        }
        
        $absence = Absence::create($validated);
        
        // Notifier l'intérimaire
        app(NotificationService::class)->notifyInterimAssigned($absence);
        
        return redirect()->route('admin.absences.index')
                         ->with('success', 'Absence créée avec succès.');
    }

    public function show(Absence $absence)
    {
        $absence->load(['user', 'interim']);
        return view('admin.absences.show', compact('absence'));
    }

    public function edit(Absence $absence)
    {
        $users = User::orderBy('name')->get();
        return view('admin.absences.edit', compact('absence', 'users'));
    }

    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'interim_id' => 'required|exists:users,id|different:user_id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:50',
        ]);
        
        // Convertir role vide en NULL (tous les rôles)
        if (empty($validated['role'])) {
            $validated['role'] = null;
        }
        
        // Vérifier qu'il n'y a pas de chevauchement (sauf l'absence courante)
        $chevauchement = Absence::where('user_id', $validated['user_id'])
            ->where('id', '!=', $absence->id)
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
                'date_debut' => 'Il existe déjà une absence pour ce titulaire sur cette période.'
            ]);
        }
        
        $absence->update($validated);
        
        return redirect()->route('admin.absences.index')
                         ->with('success', 'Absence mise à jour avec succès.');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        
        return redirect()->route('admin.absences.index')
                         ->with('success', 'Absence supprimée avec succès.');
    }
}
