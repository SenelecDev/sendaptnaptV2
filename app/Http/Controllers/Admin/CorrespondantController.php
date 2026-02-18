<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Correspondant;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;

class CorrespondantController extends Controller
{
    use SearchableTrait;
    public function index(Request $request)
    {
        $query = Correspondant::query();
        
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['nom', 'matricule', 'fonction', 'adresse'], []);
        }
        
        $correspondants = $query->orderBy('nom')->paginate(20);
        
        return view('admin.correspondants.index', compact('correspondants'));
    }

    public function create()
    {
        return view('admin.correspondants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'matricule' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
        ]);
        
        Correspondant::create($validated);
        
        return redirect()->route('admin.correspondants.index')
                         ->with('success', 'Correspondant créé avec succès.');
    }

    public function show(Correspondant $correspondant)
    {
        return view('admin.correspondants.show', compact('correspondant'));
    }

    public function edit(Correspondant $correspondant)
    {
        return view('admin.correspondants.edit', compact('correspondant'));
    }

    public function update(Request $request, Correspondant $correspondant)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'matricule' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
        ]);
        
        $correspondant->update($validated);
        
        return redirect()->route('admin.correspondants.index')
                         ->with('success', 'Correspondant mis à jour avec succès.');
    }

    public function destroy(Correspondant $correspondant)
    {
        $correspondant->delete();
        
        return redirect()->route('admin.correspondants.index')
                         ->with('success', 'Correspondant supprimé avec succès.');
    }
}
