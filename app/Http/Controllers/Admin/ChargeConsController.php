<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChargeCons;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;

class ChargeConsController extends Controller
{
    use SearchableTrait;
    public function index(Request $request)
    {
        $query = ChargeCons::query();
        
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['nom', 'matricule', 'fonction'], []);
        }
        
        $chargecons = $query->orderBy('nom')->paginate(20);
        
        return view('admin.chargecons.index', compact('chargecons'));
    }

    public function create()
    {
        return view('admin.chargecons.create');
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
        
        ChargeCons::create($validated);
        
        return redirect()->route('admin.chargecons.index')
                         ->with('success', 'Chargé de consignation créé avec succès.');
    }

    public function show(ChargeCons $chargecon)
    {
        return view('admin.chargecons.show', compact('chargecon'));
    }

    public function edit(ChargeCons $chargecon)
    {
        return view('admin.chargecons.edit', compact('chargecon'));
    }

    public function update(Request $request, ChargeCons $chargecon)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'matricule' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
        ]);
        
        $chargecon->update($validated);
        
        return redirect()->route('admin.chargecons.index')
                         ->with('success', 'Chargé de consignation mis à jour avec succès.');
    }

    public function destroy(ChargeCons $chargecon)
    {
        $chargecon->delete();
        
        return redirect()->route('admin.chargecons.index')
                         ->with('success', 'Chargé de consignation supprimé avec succès.');
    }
}
