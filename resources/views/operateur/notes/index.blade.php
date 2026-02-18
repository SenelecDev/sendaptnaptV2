@extends('layouts.app')

@section('title', 'Notes à exécuter - Opérateur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notes à exécuter</h1>
            <p class="text-gray-600">Notes avec fiche de manœuvre jointe</p>
        </div>
    </div>

    <!-- Raccourcis par statut -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('operateur.notes.index', ['statut' => 'validee']) }}" 
           class="card-senelec p-4 hover:shadow-lg transition-shadow {{ request('statut', 'validee') == 'validee' ? 'ring-2 ring-green-500' : '' }}">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Validées</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $stats['validees'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('operateur.notes.index', ['statut' => 'en_cours_execution']) }}" 
           class="card-senelec p-4 hover:shadow-lg transition-shadow {{ request('statut') == 'en_cours_execution' ? 'ring-2 ring-blue-500' : '' }}">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">En cours</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $stats['en_cours'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('operateur.notes.index', ['statut' => 'executee']) }}" 
           class="card-senelec p-4 hover:shadow-lg transition-shadow {{ request('statut') == 'executee' ? 'ring-2 ring-gray-500' : '' }}">
            <div class="flex items-center">
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Exécutées</p>
                    <p class="text-2xl font-semibold text-gray-600">{{ $stats['executees'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('operateur.notes.index', ['statut' => 'annulee']) }}" 
           class="card-senelec p-4 hover:shadow-lg transition-shadow {{ request('statut') == 'annulee' ? 'ring-2 ring-red-500' : '' }}">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Annulées</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $stats['annulees'] }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-6">
        <form method="GET" action="{{ route('operateur.notes.index') }}" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 items-end">
            <div>
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Numéro NAPT..." class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" 
                       class="input-senelec w-full">
            </div>
            <div>
                <label class="label">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" 
                       class="input-senelec w-full">
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
            <div>
                <label class="label">Année</label>
                <select name="annee" class="input-senelec w-full">
                    <option value="">Toutes</option>
                    <option value="{{ date('Y') }}" {{ request('annee') == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                    <option value="{{ date('Y') - 1 }}" {{ request('annee') == date('Y') - 1 ? 'selected' : '' }}>{{ date('Y') - 1 }}</option>
                    <option value="{{ date('Y') - 2 }}" {{ request('annee') == date('Y') - 2 ? 'selected' : '' }}>{{ date('Y') - 2 }}</option>
                </select>
            </div>
            <div class="flex gap-2 lg:col-span-2">
                <button type="submit" class="btn-senelec flex-1">
                    <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('operateur.notes.index') }}" class="btn-senelec-outline px-3" title="Réinitialiser">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Etabli par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notes as $note)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $note->numero_note }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs">
                            @if($note->demande)
                                @if($note->demande->mode_saisie === 'manuel' || $note->demande->mode_saisie === 'manuelle')
                                    <span class="truncate block" title="{{ $note->demande->ouvrages_consigner_manuel ?? '' }}">
                                        {{ Str::limit($note->demande->ouvrages_consigner_manuel ?: 'N/A', 50) }}
                                    </span>
                                @else
                                    @php
                                        $ouvragesList = [];
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
                                            $gmaoData = is_array($note->demande->ouvrages_consigner_gmao)
                                                ? $note->demande->ouvrages_consigner_gmao
                                                : json_decode($note->demande->ouvrages_consigner_gmao, true);
                                            if (is_array($gmaoData)) {
                                                foreach ($gmaoData as $item) {
                                                    $ouvragesList[] = is_array($item) ? ($item['description'] ?? '') : $item;
                                                }
                                            }
                                        }
                                        if (empty($ouvragesList) && !empty($note->demande->ouvrages_consigner_manuel)) {
                                            $ouvragesList[] = $note->demande->ouvrages_consigner_manuel;
                                        }
                                        $ouvragesList = array_filter($ouvragesList);
                                    @endphp
                                    @if(count($ouvragesList) > 0)
                                        <span class="truncate block" title="{{ implode(', ', $ouvragesList) }}">
                                            {{ Str::limit(implode(', ', $ouvragesList), 50) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
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
                                {{ $note->ddt ? $note->ddt->format('d/m/Y H:i') : 'N/A' }}
                                <br>
                                <span class="text-gray-500">→ {{ $note->dft ? $note->dft->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $note->etabliPar->full_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="{{ $note->getStatutBadgeClass() }}">{{ ucfirst($note->statut) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('operateur.notes.show', $note) }}" class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="Voir">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if($note->statut == 'validée')
                            <a href="{{ route('operateur.notes.edit', $note) }}" class="text-senelec-purple hover:text-senelec-magenta" title="Démarrer l'exécution">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-lg font-medium">Aucune note trouvée</p>
                            <p class="text-sm">Les notes à exécuter apparaîtront ici.</p>
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
