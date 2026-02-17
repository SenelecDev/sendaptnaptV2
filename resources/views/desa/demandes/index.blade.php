@extends('layouts.app')

@section('title', 'Gestion des Demandes DAPT')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Gestion des Demandes DAPT</h1>
            <p class="text-gray-600">Traitez les Demandes d'Arrêt Pour Travaux</p>
        </div>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="stat-card-purple">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card-blue">
            <div class="stat-value">{{ $stats['creees'] }}</div>
            <div class="stat-label">Créées</div>
        </div>
        <div class="stat-card-orange">
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </div>
        <div class="stat-card-green">
            <div class="stat-value">{{ $stats['acceptees'] }}</div>
            <div class="stat-label">Acceptées</div>
        </div>
        <div class="card-senelec p-4 border-l-4 border-red-500">
            <div class="stat-value text-red-600">{{ $stats['retournees'] }}</div>
            <div class="stat-label">Retournées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-4">
        <form method="GET" action="{{ route('desa.demandes.index') }}" class="space-y-4">
            <!-- Première ligne de filtres -->
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="label">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Numéro, lieu, désignation, ouvrage à consigner..." class="input-senelec w-full">
                </div>
                <div class="w-36">
                    <label class="label">Créé le</label>
                    <input type="date" name="date_creation" value="{{ request('date_creation') }}" class="input-senelec w-full">
                </div>
                <div class="w-24">
                    <label class="label">Semaine</label>
                    <select name="semaine" class="select-senelec w-full">
                        <option value="">1-53</option>
                        @for($i = 1; $i <= 53; $i++)
                            <option value="{{ $i }}" {{ request('semaine') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-24">
                    <label class="label">Année</label>
                    <select name="annee" class="select-senelec w-full">
                        <option value="">Toutes</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('annee') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <!-- Deuxième ligne de filtres -->
            <div class="flex flex-wrap items-end gap-4">
                <div class="w-40">
                    <label class="label">Statut</label>
                    <select name="statut" class="select-senelec w-full">
                        <option value="">Tous les statuts</option>
                        <option value="créée" {{ request('statut') == 'créée' ? 'selected' : '' }}>Créée</option>
                        <option value="en cours de traitement" {{ request('statut') == 'en cours de traitement' ? 'selected' : '' }}>En cours</option>
                        <option value="acceptée" {{ request('statut') == 'acceptée' ? 'selected' : '' }}>Acceptée</option>
                        <option value="retournée" {{ request('statut') == 'retournée' ? 'selected' : '' }}>Retournée</option>
                    </select>
                </div>
                <div class="w-48">
                    <label class="label">Groupe</label>
                    <select name="groupe" class="select-senelec w-full">
                        <option value="">Tous les groupes</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ request('groupe') == $groupe->id ? 'selected' : '' }}>{{ $groupe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 ml-auto">
                    <button type="submit" class="btn-senelec py-2 px-4">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('desa.demandes.index') }}" class="btn-senelec-outline py-2 px-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </a>
                    <a href="{{ route('desa.demandes.export-pdf', request()->query()) }}" 
                       class="inline-flex items-center justify-center px-4 py-3 bg-emerald-500 text-white font-semibold rounded-xl shadow-md hover:bg-emerald-600 hover:shadow-lg transition-all duration-200 border-2 border-emerald-500"
                       title="Exporter en PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° DAPT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandé Par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chargé de travaux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($demandes as $demande)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-senelec-purple">{{ $demande->numero_demande }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $demande->demandeur->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $demande->demandeur->groupe->nom ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $demande->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $demande->lieu_execution ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php $chargeTravaux = $demande->chargeTravauxInfo; @endphp
                                @if($chargeTravaux)
                                    <div class="text-sm text-gray-900">{{ $chargeTravaux->nom ?? '-' }}</div>
                                    @if(isset($chargeTravaux->entreprise))
                                        <div class="text-xs text-gray-500">{{ $chargeTravaux->entreprise }}</div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($demande->statut)
                                    @case('créée')
                                        <span class="badge badge-info">Créée</span>
                                        @break
                                    @case('en cours de traitement')
                                        <span class="badge badge-warning">En cours</span>
                                        @break
                                    @case('acceptée')
                                        <span class="badge badge-success">Acceptée</span>
                                        @break
                                    @case('retournée')
                                        <span class="badge badge-danger">Retournée</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $demande->statut }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('desa.demandes.show', $demande) }}" 
                                       class="text-senelec-purple hover:text-senelec-magenta transition-colors" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($demande->statut === 'créée' || ($demande->statut === 'en cours de traitement' && !$demande->note))
                                        <a href="{{ route('desa.demandes.edit', $demande) }}" 
                                           class="text-senelec-teal hover:text-senelec-teal-dark transition-colors" title="Traiter">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </a>
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
