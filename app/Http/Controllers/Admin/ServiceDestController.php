<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceDest;
use App\Traits\SearchableTrait;
use Illuminate\Http\Request;

class ServiceDestController extends Controller
{
    use SearchableTrait;
    public function index(Request $request)
    {
        $query = ServiceDest::query();
        
        if ($request->filled('search')) {
            $this->applySimpleSearch($query, $request->search, ['nom', 'responsable', 'email'], []);
        }
        
        $services = $query->orderBy('nom')->paginate(20);
        
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);
        
        ServiceDest::create($validated);
        
        return redirect()->route('admin.services.index')
                         ->with('success', 'Service destinataire créé avec succès.');
    }

    public function show(ServiceDest $service)
    {
        return view('admin.services.show', compact('service'));
    }

    public function edit(ServiceDest $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, ServiceDest $service)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);
        
        $service->update($validated);
        
        return redirect()->route('admin.services.index')
                         ->with('success', 'Service destinataire mis à jour avec succès.');
    }

    public function destroy(ServiceDest $service)
    {
        $service->delete();
        
        return redirect()->route('admin.services.index')
                         ->with('success', 'Service destinataire supprimé avec succès.');
    }
}
