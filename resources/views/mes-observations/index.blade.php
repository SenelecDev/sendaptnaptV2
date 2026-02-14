@extends('layouts.app')

@section('title', 'Mes Observations')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Mes Observations</h1>
            <p class="text-gray-600">Vos retours et suggestions envoyés à l'administration</p>
        </div>
        <a href="{{ route('mes-observations.create') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle observation
        </a>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card-purple">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
        <a href="{{ route('mes-observations.index', ['lu' => '0']) }}" class="stat-card-orange hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value">{{ $stats['non_lues'] }}</div>
            <div class="stat-label">Non lues</div>
        </a>
        <div class="stat-card-blue">
            <div class="stat-value">{{ $stats['ouvertes'] }}</div>
            <div class="stat-label">Ouvertes</div>
        </div>
        <div class="stat-card-green">
            <div class="stat-value">{{ $stats['traitees'] }}</div>
            <div class="stat-label">Traitées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-4">
        <form method="GET" action="{{ route('mes-observations.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Sujet, description..." class="input-senelec w-full">
            </div>
            <div class="w-32">
                <label class="label">Type</label>
                <select name="type" class="select-senelec w-full">
                    <option value="">Tous</option>
                    <option value="bug" {{ request('type') === 'bug' ? 'selected' : '' }}>Bug</option>
                    <option value="suggestion" {{ request('type') === 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                    <option value="question" {{ request('type') === 'question' ? 'selected' : '' }}>Question</option>
                    <option value="autre" {{ request('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
            <div class="w-32">
                <label class="label">Priorité</label>
                <select name="priorite" class="select-senelec w-full">
                    <option value="">Toutes</option>
                    <option value="urgente" {{ request('priorite') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    <option value="haute" {{ request('priorite') === 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="normale" {{ request('priorite') === 'normale' ? 'selected' : '' }}>Normale</option>
                    <option value="basse" {{ request('priorite') === 'basse' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-senelec py-2 px-4">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('mes-observations.index') }}" class="btn-senelec-outline py-2 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des observations -->
    <div class="card-senelec overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($observations as $observation)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$observation->lu ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $observation->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                    {{ $observation->sujet }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($observation->type)
                                    @case('bug')
                                        <span class="badge badge-danger">Bug</span>
                                        @break
                                    @case('suggestion')
                                        <span class="badge badge-info">Suggestion</span>
                                        @break
                                    @case('question')
                                        <span class="badge badge-warning">Question</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Autre</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($observation->priorite)
                                    @case('urgente')
                                        <span class="badge badge-danger">Urgente</span>
                                        @break
                                    @case('haute')
                                        <span class="badge badge-orange">Haute</span>
                                        @break
                                    @case('normale')
                                        <span class="badge badge-info">Normale</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Basse</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($observation->statut)
                                    @case('ouvert')
                                        <span class="badge badge-info">Ouvert</span>
                                        @break
                                    @case('en cours')
                                        <span class="badge badge-warning">En cours</span>
                                        @break
                                    @case('résolu')
                                        <span class="badge badge-success">Résolu</span>
                                        @break
                                    @case('fermé')
                                        <span class="badge badge-secondary">Fermé</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $observation->statut }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('mes-observations.show', $observation) }}" 
                                   class="text-senelec-purple hover:text-senelec-magenta transition-colors" title="Voir">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <p class="mt-2">Aucune observation envoyée</p>
                                <a href="{{ route('mes-observations.create') }}" class="mt-4 inline-flex btn-senelec">
                                    Envoyer ma première observation
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($observations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $observations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
