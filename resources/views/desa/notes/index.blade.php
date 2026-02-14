@extends('layouts.app')

@section('title', 'Mes NAPT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">NAPTs</h1>
            <p class="text-gray-600">Notes d'Arrêt Pour Travaux que j'ai créées</p>
        </div>
        <a href="{{ route('desa.notes.select-demande') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Créer une NAPT
        </a>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-6">
        <form method="GET" action="{{ route('desa.notes.index') }}" class="space-y-4">
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
                           class="input-senelec w-full" placeholder="jj/mm/aaaa">
                </div>
                <div>
                    <label class="label">Date fin</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" 
                           class="input-senelec w-full" placeholder="jj/mm/aaaa">
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
            <!-- Ligne 2: Statut, Groupe et Boutons -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="label">Statut</label>
                    <select name="statut" class="input-senelec w-full">
                        <option value="">Tous les statuts</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="en_etude" {{ request('statut') == 'en_etude' ? 'selected' : '' }}>En étude</option>
                        <option value="en_attente_verification" {{ request('statut') == 'en_attente_verification' ? 'selected' : '' }}>À vérifier</option>
                        <option value="verifiee" {{ request('statut') == 'verifiee' ? 'selected' : '' }}>Vérifiée</option>
                        <option value="en_attente_validation" {{ request('statut') == 'en_attente_validation' ? 'selected' : '' }}>À valider</option>
                        <option value="validee" {{ request('statut') == 'validee' ? 'selected' : '' }}>Validée</option>
                        <option value="en_cours_execution" {{ request('statut') == 'en_cours_execution' ? 'selected' : '' }}>En exécution</option>
                        <option value="executee" {{ request('statut') == 'executee' ? 'selected' : '' }}>Exécutée</option>
                        <option value="retournee" {{ request('statut') == 'retournee' ? 'selected' : '' }}>Retournée</option>
                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div>
                    <label class="label">Groupe</label>
                    <select name="groupe_id" class="input-senelec w-full">
                        <option value="">Tous les groupes</option>
                        @foreach(\App\Models\Groupe::orderBy('nom')->get() as $groupe)
                            <option value="{{ $groupe->id }}" {{ request('groupe_id') == $groupe->id ? 'selected' : '' }}>{{ $groupe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-2">
                    <button type="submit" class="btn-senelec flex-1">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('desa.notes.export-pdf', request()->query()) }}" class="inline-flex items-center justify-center px-4 py-3 bg-emerald-500 text-white font-semibold rounded-xl shadow-md hover:bg-emerald-600 hover:shadow-lg transition-all duration-200 border-2 border-emerald-500" title="Imprimer PDF" target="_blank">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </a>
                    <a href="{{ route('desa.notes.index') }}" class="btn-senelec-outline px-4" title="Réinitialiser">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Service Demandeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date travaux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Lieu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Ouvrages à consigner</th>
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
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $note->demande->demandeur->groupe->nom ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $note->demande->demandeur->name ?? '' }}</div>
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
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    {{ $note->demande->lieu_execution ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    @if($note->demande->mode_saisie === 'manuel' || $note->demande->mode_saisie === 'manuelle')
                                        <div class="whitespace-pre-line">{{ $note->demande->ouvrages_consigner_manuel ?: '-' }}</div>
                                    @else
                                        @php
                                            $ouvrages = [];
                                            // Récupérer depuis lignes_oracle
                                            $lignesOracle = $note->demande->lignes_oracle ? (is_array($note->demande->lignes_oracle) ? $note->demande->lignes_oracle : json_decode($note->demande->lignes_oracle, true)) : [];
                                            if (is_array($lignesOracle)) {
                                                foreach ($lignesOracle as $ligne) {
                                                    if (is_array($ligne)) {
                                                        $ouvrages[] = $ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? $ligne['code'] ?? '';
                                                    } else {
                                                        $ouvrages[] = $ligne;
                                                    }
                                                }
                                            }
                                            // Récupérer depuis equipements_oracle
                                            $equipementsOracle = $note->demande->equipements_oracle ? (is_array($note->demande->equipements_oracle) ? $note->demande->equipements_oracle : json_decode($note->demande->equipements_oracle, true)) : [];
                                            if (is_array($equipementsOracle)) {
                                                foreach ($equipementsOracle as $levelEquipements) {
                                                    if (is_array($levelEquipements)) {
                                                        foreach ($levelEquipements as $eq) {
                                                            if (is_array($eq)) {
                                                                $ouvrages[] = $eq['description'] ?? $eq['EQUIPMENT_DES'] ?? $eq['code'] ?? '';
                                                            } else {
                                                                $ouvrages[] = $eq;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            // Fallback to ouvrages_consigner_gmao
                                            if (empty($ouvrages) && $note->demande->ouvrages_consigner_gmao) {
                                                $gmaoData = is_array($note->demande->ouvrages_consigner_gmao) 
                                                    ? $note->demande->ouvrages_consigner_gmao 
                                                    : json_decode($note->demande->ouvrages_consigner_gmao, true);
                                                if (is_array($gmaoData)) {
                                                    foreach ($gmaoData as $item) {
                                                        if (is_array($item) && isset($item['description'])) {
                                                            $ouvrages[] = $item['description'];
                                                        } elseif (is_string($item)) {
                                                            $ouvrages[] = $item;
                                                        }
                                                    }
                                                }
                                            }
                                            $ouvrages = array_filter($ouvrages);
                                        @endphp
                                        @if(count($ouvrages) > 0)
                                            <div class="space-y-1">
                                                @foreach($ouvrages as $ouvrage)
                                                    <div>{{ $ouvrage }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            -
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'brouillon' => 'bg-gray-100 text-gray-800',
                                        'en étude' => 'bg-blue-100 text-blue-800',
                                        'en attente de vérification' => 'bg-yellow-100 text-yellow-800',
                                        'vérifiée' => 'bg-teal-100 text-teal-800',
                                        'en attente de validation' => 'bg-purple-100 text-purple-800',
                                        'validée' => 'bg-green-100 text-green-800',
                                        'en cours d\'exécution' => 'bg-orange-100 text-orange-800',
                                        'executée' => 'bg-green-600 text-white',
                                        'retournée' => 'bg-red-100 text-red-800',
                                        'annulée' => 'bg-gray-500 text-white',
                                    ];
                                    $statusLabels = [
                                        'brouillon' => 'Brouillon',
                                        'en étude' => 'En étude',
                                        'en attente de vérification' => 'En vérification',
                                        'vérifiée' => 'Vérifiée',
                                        'en attente de validation' => 'En validation',
                                        'validée' => 'Validée',
                                        'en cours d\'exécution' => 'En exécution',
                                        'executée' => 'Exécutée',
                                        'retournée' => 'Retournée',
                                        'annulée' => 'Annulée',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$note->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$note->statut] ?? ucfirst($note->statut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('desa.notes.show', $note) }}" 
                                       class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    
                                    @if(in_array($note->statut, ['brouillon', 'en étude', 'retournée']))
                                        <a href="{{ route('desa.notes.edit', $note) }}" 
                                           class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    @if($note->statut === 'brouillon')
                                        <form action="{{ route('desa.notes.destroy', $note) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')" class="inline">
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
                                <p class="mt-2 text-lg font-medium">Aucune note trouvée</p>
                                <p class="mt-1 text-sm">Créez une NAPT à partir d'une demande acceptée.</p>
                                <a href="{{ route('desa.demandes.index') }}" class="mt-4 inline-flex btn-senelec">
                                    Voir les demandes
                                </a>
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
