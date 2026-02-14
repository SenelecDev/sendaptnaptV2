@extends('layouts.app')

@section('title', 'Gestion des DAPT')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des DAPT</h1>
            <p class="text-gray-600">Demandes d'Arrêt Pour Travaux</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.demandes.statistiques') }}" class="btn bg-senelec-teal text-white hover:bg-teal-700">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </a>
        </div>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="stat-card-purple">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card-gray">
            <div class="stat-value">{{ $stats['creees'] }}</div>
            <div class="stat-label">Créées</div>
        </div>
        <div class="stat-card-blue">
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </div>
        <div class="stat-card-green">
            <div class="stat-value">{{ $stats['acceptees'] }}</div>
            <div class="stat-label">Acceptées</div>
        </div>
        <div class="stat-card-orange">
            <div class="stat-value">{{ $stats['retournees'] }}</div>
            <div class="stat-label">Retournées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec">
        <form method="GET" action="{{ route('admin.demandes.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Numéro, objet..." class="input">
            </div>
            <div>
                <label class="label">Statut</label>
                <select name="statut" class="input">
                    <option value="">Tous les statuts</option>
                    <option value="créée" {{ request('statut') == 'créée' ? 'selected' : '' }}>Créée</option>
                    <option value="en cours de traitement" {{ request('statut') == 'en cours de traitement' ? 'selected' : '' }}>En cours</option>
                    <option value="acceptée" {{ request('statut') == 'acceptée' ? 'selected' : '' }}>Acceptée</option>
                    <option value="retournée" {{ request('statut') == 'retournée' ? 'selected' : '' }}>Retournée</option>
                </select>
            </div>
            <div>
                <label class="label">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input">
            </div>
            <div>
                <label class="label">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-senelec flex-1">Filtrer</button>
                <a href="{{ route('admin.demandes.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Désignation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date création</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($demandes as $demande)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-senelec-purple">{{ $demande->numero_demande }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $demande->designation }}">
                                    {{ $demande->designation ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $demande->demandeur?->full_name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $demande->demandeur?->matricule }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $demande->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($demande->statut)
                                    @case('créée')
                                        <span class="status-creee">Créée</span>
                                        @break
                                    @case('en cours de traitement')
                                        <span class="status-en-cours">En cours</span>
                                        @break
                                    @case('acceptée')
                                        <span class="status-acceptee">Acceptée</span>
                                        @break
                                    @case('retournée')
                                        <span class="status-retournee">Retournée</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $demande->statut }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $demande->note ? 1 : 0 }} NAPT
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.demandes.show', $demande) }}" 
                                       class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if(!$demande->note)
                                        <form action="{{ route('admin.demandes.destroy', $demande) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2">Aucune demande trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($demandes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $demandes->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
