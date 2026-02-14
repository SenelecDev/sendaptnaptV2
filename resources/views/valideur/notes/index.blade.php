@extends('layouts.app')

@section('title', 'Notes - Valideur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notes d'Arrêt Pour Travaux</h1>
            <p class="text-gray-600">Liste des NAPT à valider et suivre</p>
        </div>
    </div>

    <!-- Raccourcis par statut -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('valideur.notes.index', ['statut' => 'tous']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == 'tous' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Tous
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'vérifiée']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == 'vérifiée' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
            À valider
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'validée']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == 'validée' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
            Validée
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'en cours d\'exécution']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == "en cours d'exécution" ? 'bg-orange-500 text-white' : 'bg-orange-100 text-orange-800 hover:bg-orange-200' }}">
            En exécution
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'exécutée']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == 'exécutée' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}">
            Exécutée
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'retournée']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == 'retournée' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
            Retournée
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'annulée']) }}" 
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $statut == 'annulée' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Annulée
        </a>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-6">
        <form method="GET" action="{{ route('valideur.notes.index') }}" class="space-y-4">
            <!-- Ligne 1: Recherche et Dates -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                        <option value="">1-53</option>
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
            </div>
            <!-- Ligne 2: Statut et Boutons -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="label">Statut</label>
                    <select name="statut" class="input-senelec w-full">
                        <option value="tous" {{ $statut == 'tous' ? 'selected' : '' }}>Tous les statuts</option>
                        <option value="vérifiée" {{ $statut == 'vérifiée' ? 'selected' : '' }}>À valider</option>
                        <option value="validée" {{ $statut == 'validée' ? 'selected' : '' }}>Validée</option>
                        <option value="en cours d'exécution" {{ $statut == "en cours d'exécution" ? 'selected' : '' }}>En cours d'exécution</option>
                        <option value="exécutée" {{ $statut == 'exécutée' ? 'selected' : '' }}>Exécutée</option>
                        <option value="retournée" {{ $statut == 'retournée' ? 'selected' : '' }}>Retournée</option>
                        <option value="annulée" {{ $statut == 'annulée' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div></div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-senelec flex-1">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('valideur.notes.index') }}" class="btn-senelec-outline px-4" title="Réinitialiser">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="card-senelec overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">N° NAPT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Etabli Par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Vérifiée Par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Lieu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Ouvrages à consigner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Début / Fin travaux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notes as $note)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-senelec-purple">{{ $note->numero_note }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $note->etabliPar->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $note->verifiePar->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    {{ $note->demande->lieu_execution ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900" style="max-width: 250px;">
                                    @php
                                        $ouvragesDisplay = '-';
                                        if ($note->demande) {
                                            if ($note->demande->mode_saisie === 'manuelle' || $note->demande->mode_saisie === 'manuel') {
                                                $ouvragesDisplay = $note->demande->ouvrages_consigner_manuel ?? '-';
                                            } else {
                                                $ouvrages = [];
                                                // Check equipements_oracle first (GMAO data)
                                                if ($note->demande->equipements_oracle) {
                                                    $eqData = is_string($note->demande->equipements_oracle) 
                                                        ? json_decode($note->demande->equipements_oracle, true) 
                                                        : $note->demande->equipements_oracle;
                                                    if (is_array($eqData)) {
                                                        foreach (['equipements_consigner_level_1', 'equipements_consigner_level_2', 'equipements_consigner_level_3'] as $level) {
                                                            if (isset($eqData[$level]) && is_array($eqData[$level])) {
                                                                foreach ($eqData[$level] as $item) {
                                                                    if (isset($item['description'])) {
                                                                        $ouvrages[] = $item['description'];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                // Check lignes_oracle (for ligne type)
                                                if (empty($ouvrages) && $note->demande->lignes_oracle) {
                                                    $lignesData = is_string($note->demande->lignes_oracle) 
                                                        ? json_decode($note->demande->lignes_oracle, true) 
                                                        : $note->demande->lignes_oracle;
                                                    if (is_array($lignesData)) {
                                                        foreach ($lignesData as $item) {
                                                            if (is_array($item) && isset($item['description'])) {
                                                                $ouvrages[] = $item['description'];
                                                            }
                                                        }
                                                    }
                                                }
                                                // Fallback to ouvrages_consigner_gmao
                                                if (empty($ouvrages) && $note->demande->ouvrages_consigner_gmao) {
                                                    $gmaoData = is_string($note->demande->ouvrages_consigner_gmao) 
                                                        ? json_decode($note->demande->ouvrages_consigner_gmao, true) 
                                                        : $note->demande->ouvrages_consigner_gmao;
                                                    if (is_array($gmaoData)) {
                                                        foreach ($gmaoData as $item) {
                                                            if (is_array($item) && isset($item['description'])) {
                                                                $ouvrages[] = $item['description'];
                                                            }
                                                        }
                                                    }
                                                }
                                                $ouvragesDisplay = implode(', ', $ouvrages) ?: '-';
                                            }
                                        }
                                    @endphp
                                    {{ $ouvragesDisplay }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($note->ddt)
                                        {{ \Carbon\Carbon::parse($note->ddt)->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </div>
                                @if($note->dft)
                                    <div class="text-xs text-gray-500">
                                        au {{ \Carbon\Carbon::parse($note->dft)->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'vérifiée' => 'bg-yellow-100 text-yellow-800',
                                        'validée' => 'bg-green-100 text-green-800',
                                        'retournée' => 'bg-red-100 text-red-800',
                                        "en cours d'exécution" => 'bg-orange-100 text-orange-800',
                                        'exécutée' => 'bg-emerald-100 text-emerald-800',
                                        'annulée' => 'bg-gray-100 text-gray-600',
                                    ];
                                    $statusLabels = [
                                        'vérifiée' => 'À valider',
                                        'validée' => 'Validée',
                                        'retournée' => 'Retournée',
                                        "en cours d'exécution" => 'En exécution',
                                        'exécutée' => 'Exécutée',
                                        'annulée' => 'Annulée',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$note->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$note->statut] ?? ucfirst($note->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if($note->statut === 'vérifiée')
                                        <a href="{{ route('valideur.notes.show', $note) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-senelec-purple text-white text-xs font-medium rounded-md hover:bg-purple-700 transition-colors" title="Valider">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('valideur.notes.show', $note) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-white text-xs font-medium rounded-md transition-colors" style="background-color: #0D1CB0;" title="Voir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-lg font-medium">Aucune note trouvée</p>
                                <p class="mt-1 text-sm">Aucune note à valider pour le moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($notes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notes->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
