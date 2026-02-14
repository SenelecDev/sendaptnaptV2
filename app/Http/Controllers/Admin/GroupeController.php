<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index(Request $request)
    {
        $query = Groupe::withCount('users');
        
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(nom) like ?', ["%{$search}%"]);
        }
        
        $groupes = $query->orderBy('nom')->paginate(20);
        
        return view('admin.groupes.index', compact('groupes'));
    }

    public function create()
    {
        return view('admin.groupes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:groupes',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
        ]);
        
        Groupe::create($validated);
        
        return redirect()->route('admin.groupes.index')
                         ->with('success', 'Groupe créé avec succès.');
    }

    public function show(Groupe $groupe)
    {
        $groupe->load('users.roles');
        
        // Utilisateurs disponibles (sans groupe ou dans un autre groupe)
        $availableUsers = User::whereNull('groupe_id')
            ->orWhere('groupe_id', '!=', $groupe->id)
            ->orderBy('name')
            ->get();
        
        return view('admin.groupes.show', compact('groupe', 'availableUsers'));
    }

    public function edit(Groupe $groupe)
    {
        return view('admin.groupes.edit', compact('groupe'));
    }

    public function update(Request $request, Groupe $groupe)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:groupes,nom,' . $groupe->id,
            'email' => 'nullable|email',
            'description' => 'nullable|string',
        ]);
        
        $groupe->update($validated);
        
        return redirect()->route('admin.groupes.index')
                         ->with('success', 'Groupe mis à jour avec succès.');
    }

    public function destroy(Groupe $groupe)
    {
        if ($groupe->users()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un groupe contenant des utilisateurs.');
        }
        
        $groupe->delete();
        
        return redirect()->route('admin.groupes.index')
                         ->with('success', 'Groupe supprimé avec succès.');
    }
    
    /**
     * Ajouter des utilisateurs au groupe
     */
    public function addUser(Request $request, Groupe $groupe)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        $users = User::whereIn('id', $request->user_ids)->get();
        $count = 0;
        
        foreach ($users as $user) {
            $user->update(['groupe_id' => $groupe->id]);
            $count++;
        }
        
        $message = $count > 1 
            ? "{$count} utilisateurs ont été ajoutés au groupe."
            : "{$users->first()->name} a été ajouté au groupe.";
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Retirer un utilisateur du groupe
     */
    public function removeUser(Groupe $groupe, User $user)
    {
        if ($user->groupe_id !== $groupe->id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cet utilisateur n'appartient pas à ce groupe."
                ], 400);
            }
            return back()->with('error', "Cet utilisateur n'appartient pas à ce groupe.");
        }
        
        $user->update(['groupe_id' => null]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name} a été retiré du groupe."
            ]);
        }
        
        return back()->with('success', "{$user->name} a été retiré du groupe.");
    }
}
