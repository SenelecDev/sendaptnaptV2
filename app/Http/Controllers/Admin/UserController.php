<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'groupe']);
        
        // Filters
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(matricule) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(name) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(nom) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(prenom) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) like ?', ["%{$search}%"]);
            });
        }
        
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        if ($request->filled('groupe_id')) {
            $query->where('groupe_id', $request->groupe_id);
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $users = $query->orderBy('nom')->paginate(20);
        $roles = Role::all();
        $groupes = \App\Models\Groupe::all();
        
        return view('admin.users.index', compact('users', 'roles', 'groupes'));
    }

    public function create()
    {
        $roles = Role::all();
        $groupes = \App\Models\Groupe::all();
        return view('admin.users.create', compact('roles', 'groupes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:users',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string',
            'poste' => 'nullable|string',
            'service' => 'nullable|string',
            'groupe_id' => 'nullable|exists:groupes,id',
            'roles' => 'array',
            'is_active' => 'boolean',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['name'] = $validated['prenom'] . ' ' . $validated['nom'];
        $validated['is_active'] = $request->boolean('is_active', true);
        
        $user = User::create($validated);
        
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'groupe', 'demandes', 'absences']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $groupes = \App\Models\Groupe::all();
        return view('admin.users.edit', compact('user', 'roles', 'groupes'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:users,matricule,' . $user->id,
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'nullable|string',
            'poste' => 'nullable|string',
            'service' => 'nullable|string',
            'groupe_id' => 'nullable|exists:groupes,id',
            'roles' => 'array',
            'is_active' => 'boolean',
        ]);
        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $validated['name'] = $validated['prenom'] . ' ' . $validated['nom'];
        $validated['is_active'] = $request->boolean('is_active', true);
        
        $user->update($validated);
        
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->ldap_guid || $user->oracle_person_id) {
            return back()->with('error', 'Les utilisateurs LDAP/Oracle ne peuvent pas être supprimés.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
