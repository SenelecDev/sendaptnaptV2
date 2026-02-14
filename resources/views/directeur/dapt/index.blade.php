@extends('layouts.app')

@section('title', 'DAPT - Directeur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('directeur.dashboard') }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour au tableau de bord
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Consultation des DAPT</h1>
            <p class="text-gray-600">Toutes les demandes d'arrêt pour travaux (lecture seule)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('directeur.dapt.statistiques') }}" class="btn bg-senelec-teal text-white hover:bg-teal-700">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="card-senelec p-4 text-center">
            <p class="text-2xl font-bold text-senelec-purple">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="card-senelec p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['creees'] }}</p>
            <p class="text-xs text-gray-500">Créées</p>
        </div>
        <div class="card-senelec p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['en_cours'] }}</p>
            <p class="text-xs text-gray-500">En traitement</p>
        </div>
        <div class="card-senelec p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['acceptees'] }}</p>
            <p class="text-xs text-gray-500">Acceptées</p>
        </div>
        <div class="card-senelec p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $stats['retournees'] }}</p>
            <p class="text-xs text-gray-500">Retournées</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-6">
        <form method="GET" action="{{ route('directeur.dapt') }}" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="N° DAPT, désignation..." class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Statut</label>
                <select name="statut" class="input-senelec w-full">
                    <option value="">Tous</option>
                    <option value="créée" {{ request('statut') == 'créée' ? 'selected' : '' }}>Créée</option>
                    <option value="en cours de traitement" {{ request('statut') == 'en cours de traitement' ? 'selected' : '' }}>En traitement</option>
                    <option value="acceptée" {{ request('statut') == 'acceptée' ? 'selected' : '' }}>Acceptée</option>
                    <option value="retournée" {{ request('statut') == 'retournée' ? 'selected' : '' }}>Retournée</option>
                </select>
            </div>
            <div>
                <label class="label">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Semaine</label>
                <select name="semaine" class="input-senelec w-full">
                    <option value="">Toutes</option>
                    @for($i = 1; $i <= 53; $i++)
                        <option value="{{ $i }}" {{ request('semaine') == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-senelec flex-1">Filtrer</button>
                <a href="{{ route('directeur.dapt') }}" class="btn-senelec-outline px-3" title="Réinitialiser">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="card-senelec overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° DAPT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Désignation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($demandes as $demande)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $demande->numero_demande }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $demande->demandeur->full_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $demande->demandeur->groupe->nom ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $demande->designation }}">
                                {{ Str::limit($demande->designation, 40) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $demande->lieu_execution ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $demande->date ? $demande->date->format('d/m/Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="{{ $demande->getStatutBadgeClass() }}">{{ ucfirst($demande->statut) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('directeur.dapt.show', $demande) }}" class="text-senelec-purple hover:text-senelec-magenta" title="Voir les détails">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-lg font-medium">Aucune demande trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($demandes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $demandes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
