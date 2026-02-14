@extends('layouts.app')

@section('title', 'Gestion des NAPT')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des NAPT</h1>
            <p class="text-gray-600">Notes d'Arrêt Pour Travaux</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.notes.statistiques') }}" class="btn bg-senelec-teal text-white hover:bg-teal-700">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </a>
        </div>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
        <div class="stat-card-purple">
            <div class="stat-value text-lg">{{ $stats['total'] }}</div>
            <div class="stat-label text-xs">Total</div>
        </div>
        <div class="stat-card-gray">
            <div class="stat-value text-lg">{{ $stats['brouillon'] }}</div>
            <div class="stat-label text-xs">Brouillon</div>
        </div>
        <div class="stat-card-blue">
            <div class="stat-value text-lg">{{ $stats['en_attente_verification'] }}</div>
            <div class="stat-label text-xs">En attente</div>
        </div>
        <div class="stat-card-teal">
            <div class="stat-value text-lg">{{ $stats['verifiees'] }}</div>
            <div class="stat-label text-xs">Vérifiées</div>
        </div>
        <div class="stat-card-green">
            <div class="stat-value text-lg">{{ $stats['validees'] }}</div>
            <div class="stat-label text-xs">Validées</div>
        </div>
        <div class="stat-card-orange">
            <div class="stat-value text-lg">{{ $stats['en_execution'] }}</div>
            <div class="stat-label text-xs">En exécution</div>
        </div>
        <div class="stat-card-emerald">
            <div class="stat-value text-lg">{{ $stats['executees'] }}</div>
            <div class="stat-label text-xs">Exécutées</div>
        </div>
        <div class="stat-card-red">
            <div class="stat-value text-lg">{{ $stats['retournees'] }}</div>
            <div class="stat-label text-xs">Retournées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec">
        <form method="GET" action="{{ route('admin.notes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Numéro, motif..." class="input w-full">
            </div>
            <div>
                <label class="label">Statut</label>
                <select name="statut" class="input w-full">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="en étude" {{ request('statut') == 'en étude' ? 'selected' : '' }}>En étude</option>
                    <option value="en attente de vérification" {{ request('statut') == 'en attente de vérification' ? 'selected' : '' }}>En attente vérif.</option>
                    <option value="vérifiée" {{ request('statut') == 'vérifiée' ? 'selected' : '' }}>Vérifiée</option>
                    <option value="validée" {{ request('statut') == 'validée' ? 'selected' : '' }}>Validée</option>
                    <option value="en cours d'exécution" {{ request('statut') == "en cours d'exécution" ? 'selected' : '' }}>En exécution</option>
                    <option value="exécutée" {{ request('statut') == 'exécutée' ? 'selected' : '' }}>Exécutée</option>
                    <option value="retournée" {{ request('statut') == 'retournée' ? 'selected' : '' }}>Retournée</option>
                </select>
            </div>
            <div>
                <label class="label">Semaine</label>
                <select name="semaine" class="input w-full">
                    <option value="">Toutes</option>
                    @for($i = 1; $i <= 52; $i++)
                        <option value="{{ $i }}" {{ request('semaine') == $i ? 'selected' : '' }}>S{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="label">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input w-full">
            </div>
            <div>
                <label class="label">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input w-full">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-senelec flex-1">Filtrer</button>
                <a href="{{ route('admin.notes.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DAPT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Établi par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notes as $note)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-senelec-teal">{{ $note->numero_note }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($note->demande)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.demandes.show', $note->demande) }}" 
                                           class="text-sm font-mono text-senelec-purple hover:underline">
                                            {{ $note->demande->numero }}
                                        </a>
                                        @if($note->demande->pdf_path)
                                            <a href="{{ $note->demande->pdf_url }}" 
                                               target="_blank"
                                               class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors" 
                                               title="Télécharger DAPT">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $note->etabli?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($note->mode == 'gmao')
                                    <span class="badge badge-info">GMAO</span>
                                @else
                                    <span class="badge badge-secondary">Manuel</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($note->statut)
                                    @case('brouillon')
                                        <span class="status-brouillon">Brouillon</span>
                                        @break
                                    @case('en étude')
                                        <span class="status-en-etude">En étude</span>
                                        @break
                                    @case('en attente de vérification')
                                        <span class="status-en-attente">En attente</span>
                                        @break
                                    @case('vérifiée')
                                        <span class="status-verifiee">Vérifiée</span>
                                        @break
                                    @case('validée')
                                        <span class="status-validee">Validée</span>
                                        @break
                                    @case('en cours d\'exécution')
                                        <span class="status-en-execution">En exécution</span>
                                        @break
                                    @case('exécutée')
                                        <span class="status-executee">Exécutée</span>
                                        @break
                                    @case('retournée')
                                        <span class="status-retournee">Retournée</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $note->statut }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.notes.show', $note) }}" 
                                       class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.notes.timeline', $note) }}" 
                                       class="text-senelec-teal hover:text-teal-700" title="Timeline">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </a>
                                    @if(in_array($note->statut, ['brouillon', 'en étude']))
                                        <form action="{{ route('admin.notes.destroy', $note) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2">Aucune note trouvée</p>
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
