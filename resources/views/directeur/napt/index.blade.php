@extends('layouts.app')

@section('title', 'NAPT - Directeur')

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
            <h1 class="text-2xl font-bold text-gray-900">Consultation des NAPT</h1>
            <p class="text-gray-600">Toutes les notes d'arrêt pour travaux (lecture seule)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('directeur.napt.statistiques') }}" class="btn bg-senelec-teal text-white hover:bg-teal-700">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-10 gap-4">
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-senelec-purple">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-gray-600">{{ $stats['brouillon'] }}</p>
            <p class="text-xs text-gray-500">Brouillon</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-blue-600">{{ $stats['en_etude'] }}</p>
            <p class="text-xs text-gray-500">En étude</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-yellow-600">{{ $stats['en_attente_verification'] }}</p>
            <p class="text-xs text-gray-500">Att. vérif.</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-indigo-600">{{ $stats['verifiee'] }}</p>
            <p class="text-xs text-gray-500">Vérifiée</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-cyan-600">{{ $stats['en_attente_validation'] }}</p>
            <p class="text-xs text-gray-500">Att. valid.</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-teal-600">{{ $stats['validee'] }}</p>
            <p class="text-xs text-gray-500">Validée</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-purple-600">{{ $stats['en_cours_execution'] }}</p>
            <p class="text-xs text-gray-500">En exéc.</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-green-600">{{ $stats['executee'] }}</p>
            <p class="text-xs text-gray-500">Exécutée</p>
        </div>
        <div class="card-senelec p-3 text-center">
            <p class="text-xl font-bold text-red-600">{{ $stats['annulee'] }}</p>
            <p class="text-xs text-gray-500">Annulée</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-6">
        <form method="GET" action="{{ route('directeur.napt') }}" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="N° NAPT, lieu..." class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Statut</label>
                <select name="statut" class="input-senelec w-full">
                    <option value="">Tous</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="en étude" {{ request('statut') == 'en étude' ? 'selected' : '' }}>En étude</option>
                    <option value="en attente de vérification" {{ request('statut') == 'en attente de vérification' ? 'selected' : '' }}>Att. vérification</option>
                    <option value="vérifiée" {{ request('statut') == 'vérifiée' ? 'selected' : '' }}>Vérifiée</option>
                    <option value="en attente de validation" {{ request('statut') == 'en attente de validation' ? 'selected' : '' }}>Att. validation</option>
                    <option value="validée" {{ request('statut') == 'validée' ? 'selected' : '' }}>Validée</option>
                    <option value="en cours d'exécution" {{ request('statut') == "en cours d'exécution" ? 'selected' : '' }}>En exécution</option>
                    <option value="exécutée" {{ request('statut') == 'exécutée' ? 'selected' : '' }}>Exécutée</option>
                    <option value="annulée" {{ request('statut') == 'annulée' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            <div>
                <label class="label">Date début travaux</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Date fin travaux</label>
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
                <a href="{{ route('directeur.napt') }}" class="btn-senelec-outline px-3" title="Réinitialiser">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° NAPT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ouvrages à consigner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période travaux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Établi par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notes as $note)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('directeur.napt.show', $note) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $note->numero_note }}</div>
                            @if($note->demande)
                                <div class="text-xs text-gray-500">DAPT: {{ $note->demande->numero_demande }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                @if($note->demande)
                                    @php
                                        $ouvragesList = [];
                                        if ($note->demande->mode_saisie === 'manuel' || $note->demande->mode_saisie === 'manuelle') {
                                            if ($note->demande->ouvrages_consigner_manuel) {
                                                $ouvragesList[] = $note->demande->ouvrages_consigner_manuel;
                                            }
                                        } else {
                                            $lignesData = $note->demande->lignes_oracle ? (is_string($note->demande->lignes_oracle) ? json_decode($note->demande->lignes_oracle, true) : $note->demande->lignes_oracle) : [];
                                            if (is_array($lignesData)) {
                                                foreach ($lignesData as $ligne) {
                                                    $ouvragesList[] = is_array($ligne) ? ($ligne['description'] ?? $ligne['code'] ?? '') : $ligne;
                                                }
                                            }
                                            $eqRaw = $note->demande->equipements_oracle ? (is_string($note->demande->equipements_oracle) ? json_decode($note->demande->equipements_oracle, true) : $note->demande->equipements_oracle) : [];
                                            if (is_array($eqRaw)) {
                                                $niveauxAvecData = [];
                                                foreach ($eqRaw as $levelKey => $levelData) {
                                                    if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                                                        $niveauxAvecData[(int)$m[1]] = $levelData;
                                                    }
                                                }
                                                if (!empty($niveauxAvecData)) {
                                                    $dernierNiveau = max(array_keys($niveauxAvecData));
                                                    foreach ($niveauxAvecData[$dernierNiveau] as $eq) {
                                                        $ouvragesList[] = is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '') : $eq;
                                                    }
                                                }
                                            }
                                            if (empty($ouvragesList) && $note->demande->ouvrages_consigner_gmao) {
                                                $gmaoData = is_string($note->demande->ouvrages_consigner_gmao) ? json_decode($note->demande->ouvrages_consigner_gmao, true) : $note->demande->ouvrages_consigner_gmao;
                                                if (is_array($gmaoData)) {
                                                    foreach ($gmaoData as $item) {
                                                        $ouvragesList[] = is_array($item) ? ($item['description'] ?? '') : $item;
                                                    }
                                                }
                                            }
                                        }
                                        $ouvragesList = array_filter($ouvragesList);
                                    @endphp
                                    @if(count($ouvragesList) > 0)
                                        <span title="{{ implode(', ', $ouvragesList) }}">
                                            {{ Str::limit(implode(', ', $ouvragesList), 50) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $note->demande->lieu_execution ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($note->ddt && $note->dft)
                                    Du {{ $note->ddt->format('d/m/Y') }}<br>
                                    au {{ $note->dft->format('d/m/Y') }}
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $note->etabliPar->full_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="{{ $note->getStatutBadgeClass() }}">{{ ucfirst($note->statut) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" onclick="event.stopPropagation()">
                            <a href="{{ route('directeur.napt.show', $note) }}" class="text-senelec-purple hover:text-senelec-magenta" title="Voir les détails">
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
                            <p class="text-lg font-medium">Aucune note trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($notes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
