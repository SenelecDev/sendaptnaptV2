@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Absences / Intérims</h1>
        <a href="{{ route('admin.absences.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Nouvelle Absence
        </a>
    </div>

    <div class="card-senelec mb-6">
        <form method="GET" action="{{ route('admin.absences.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rechercher par nom ou matricule..." 
                       class="input w-full">
            </div>
            <div class="w-48">
                <select name="statut" class="input w-full">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>En cours</option>
                    <option value="future" {{ request('statut') === 'future' ? 'selected' : '' }}>À venir</option>
                    <option value="passee" {{ request('statut') === 'passee' ? 'selected' : '' }}>Passées</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-search mr-2"></i> Filtrer
            </button>
            <a href="{{ route('admin.absences.index') }}" class="btn btn-secondary">
                <i class="fas fa-times mr-2"></i> Réinitialiser
            </a>
        </form>
    </div>

    <div class="card-senelec overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titulaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intérimaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($absences as $absence)
                    @php
                        $now = now();
                        $isActive = $absence->date_debut <= $now && $absence->date_fin >= $now;
                        $isFuture = $absence->date_debut > $now;
                        $isPast = $absence->date_fin < $now;
                    @endphp
                    <tr class="{{ $isActive ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $absence->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $absence->user->matricule ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $absence->interim->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $absence->interim->matricule ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>Du {{ \Carbon\Carbon::parse($absence->date_debut)->format('d/m/Y') }}</div>
                            <div>Au {{ \Carbon\Carbon::parse($absence->date_fin)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $absence->role ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isActive)
                                <span class="badge badge-success">En cours</span>
                            @elseif($isFuture)
                                <span class="badge badge-info">À venir</span>
                            @else
                                <span class="badge badge-secondary">Passée</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.absences.show', $absence) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded hover:bg-indigo-200" 
                                   title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.absences.edit', $absence) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded hover:bg-blue-200" 
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded hover:bg-red-200" 
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Aucune absence trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $absences->withQueryString()->links() }}
    </div>
</div>
@endsection
