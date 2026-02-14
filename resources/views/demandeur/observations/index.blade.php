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
        <a href="{{ route('demandeur.observations.create') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle observation
        </a>
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
                        <tr class="hover:bg-gray-50 transition-colors">
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
                                <a href="{{ route('demandeur.observations.show', $observation) }}" 
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
                                <a href="{{ route('demandeur.observations.create') }}" class="mt-4 inline-flex btn-senelec">
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
                {{ $observations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
