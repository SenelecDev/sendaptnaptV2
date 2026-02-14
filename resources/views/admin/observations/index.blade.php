@extends('layouts.app')

@section('title', 'Observations')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Observations / Feedback</h1>
            <p class="text-gray-600">{{ $observations->total() }} observation(s) au total</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec">
        <form method="GET" action="{{ route('admin.observations.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rechercher par sujet ou description..." class="input">
            </div>
            <div>
                <select name="type" class="input">
                    <option value="">Tous les types</option>
                    <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>Bug</option>
                    <option value="suggestion" {{ request('type') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                    <option value="question" {{ request('type') == 'question' ? 'selected' : '' }}>Question</option>
                    <option value="autre" {{ request('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
            <div>
                <select name="statut" class="input">
                    <option value="">Tous les statuts</option>
                    <option value="ouvert" {{ request('statut') == 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                    <option value="en cours" {{ request('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                    <option value="résolu" {{ request('statut') == 'résolu' ? 'selected' : '' }}>Résolu</option>
                    <option value="fermé" {{ request('statut') == 'fermé' ? 'selected' : '' }}>Fermé</option>
                </select>
            </div>
            <div>
                <select name="priorite" class="input">
                    <option value="">Toutes priorités</option>
                    <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="normale" {{ request('priorite') == 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>
            <button type="submit" class="btn-senelec">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrer
            </button>
            @if(request()->hasAny(['search', 'type', 'statut', 'priorite']))
                <a href="{{ route('admin.observations.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Liste -->
    <div class="card-senelec overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($observations as $observation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($observation->sujet, 40) }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($observation->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $observation->user->full_name ?? 'Inconnu' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge {{ $observation->getTypeBadgeClass() }}">
                                {{ ucfirst($observation->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge {{ $observation->getPrioriteBadgeClass() }}">
                                {{ ucfirst($observation->priorite) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge {{ $observation->getStatutBadgeClass() }}">
                                {{ ucfirst($observation->statut) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $observation->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.observations.show', $observation) }}" 
                                   class="p-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($observation->statut !== 'résolu' && $observation->statut !== 'fermé')
                                    <form action="{{ route('admin.observations.mark-processed', $observation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors" title="Marquer résolu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.observations.destroy', $observation) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette observation ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <p class="mt-2 text-gray-500">Aucune observation trouvée</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($observations->hasPages())
        <div class="mt-6">
            {{ $observations->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
