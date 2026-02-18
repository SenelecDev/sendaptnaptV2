@extends('layouts.app')

@section('title', 'Mes Demandes DAPT')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Mes Demandes DAPT</h1>
            <p class="text-gray-600">Gérez vos Demandes d'Arrêt Pour Travaux</p>
        </div>
        <a href="{{ route('demandeur.demandes.create') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle demande
        </a>
    </div>

    <!-- Stats rapides (cliquables pour filtrer) -->
    @php $statutActif = request('statut'); @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="{{ route('demandeur.demandes.index') }}" class="stat-card-purple hover:scale-105 transition-transform cursor-pointer block {{ !$statutActif ? 'ring-2 ring-senelec-purple ring-offset-2' : '' }}">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'créée']) }}" class="stat-card-blue hover:scale-105 transition-transform cursor-pointer block {{ $statutActif === 'créée' ? 'ring-2 ring-blue-500 ring-offset-2' : '' }}">
            <div class="stat-value">{{ $stats['creees'] }}</div>
            <div class="stat-label">Créées</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'en cours de traitement']) }}" class="stat-card-orange hover:scale-105 transition-transform cursor-pointer block {{ $statutActif === 'en cours de traitement' ? 'ring-2 ring-orange-500 ring-offset-2' : '' }}">
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'acceptée']) }}" class="stat-card-green hover:scale-105 transition-transform cursor-pointer block {{ $statutActif === 'acceptée' ? 'ring-2 ring-green-500 ring-offset-2' : '' }}">
            <div class="stat-value">{{ $stats['acceptees'] }}</div>
            <div class="stat-label">Acceptées</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'retournée']) }}" class="card-senelec p-4 border-l-4 border-red-500 hover:scale-105 transition-transform cursor-pointer block {{ $statutActif === 'retournée' ? 'ring-2 ring-red-500 ring-offset-2' : '' }}">
            <div class="stat-value text-red-600">{{ $stats['retournees'] }}</div>
            <div class="stat-label">Retournées</div>
        </a>
    </div>

    <!-- Filtres -->
    <div class="card-senelec p-4">
        <form method="GET" action="{{ route('demandeur.demandes.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Numéro, lieu, désignation..." class="input-senelec w-full">
            </div>
            <div class="w-40">
                <label class="label">Statut</label>
                <select name="statut" class="select-senelec w-full">
                    <option value="">Tous</option>
                    <option value="créée" {{ request('statut') == 'créée' ? 'selected' : '' }}>Créée</option>
                    <option value="en cours de traitement" {{ request('statut') == 'en cours de traitement' ? 'selected' : '' }}>En cours</option>
                    <option value="acceptée" {{ request('statut') == 'acceptée' ? 'selected' : '' }}>Acceptée</option>
                    <option value="retournée" {{ request('statut') == 'retournée' ? 'selected' : '' }}>Retournée</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-senelec py-2 px-4">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('demandeur.demandes.index') }}" class="btn-senelec-outline py-2 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $demande->demandeur->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($demande->date)
                                    {{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $demande->lieu_execution }}">{{ $demande->lieu_execution ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($demande->charge_travaux_info)
                                    <div class="text-sm text-gray-900">
                                        {{ $demande->charge_travaux_info->nom }}
                                        @if($demande->charge_travaux_info->type === 'externe')
                                            <span class="text-xs text-senelec-orange">(Ext.)</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
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
                                    @case('brouillon')
                                        <span class="badge badge-secondary">Brouillon</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $demande->statut }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('demandeur.demandes.show', $demande) }}" 
                                       class="text-senelec-purple hover:text-senelec-magenta transition-colors" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($demande->pdf_url)
                                        <a href="{{ $demande->pdf_url }}" 
                                           target="_blank"
                                           class="text-red-500 hover:text-red-700 transition-colors" title="Voir PDF DAPT">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($demande->notes && $demande->notes->count() > 0)
                                        @php $note = $demande->notes->first(); @endphp
                                        <a href="{{ route('pdf.napt.view', $note) }}" 
                                           target="_blank"
                                           class="text-senelec-orange hover:text-orange-700 transition-colors" title="Voir NAPT (PDF)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($demande->demandeur_id == Auth::id() && in_array($demande->statut, ['retournée', 'brouillon']))
                                        <a href="{{ route('demandeur.demandes.edit', $demande) }}" 
                                           class="text-senelec-teal hover:text-senelec-teal-dark transition-colors" title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($demande->demandeur_id == Auth::id() && $demande->statut === 'brouillon')
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900 transition-colors" 
                                                title="Supprimer"
                                                onclick="openDeleteModal('{{ route('demandeur.demandes.destroy', $demande) }}', '{{ $demande->numero_demande }}')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
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
                                <p class="mt-2">Aucune demande trouvée</p>
                                <a href="{{ route('demandeur.demandes.create') }}" class="mt-4 inline-flex btn-senelec">
                                    Créer ma première demande
                                </a>
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

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay sombre -->
    <div class="fixed inset-0 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>

    <!-- Container centré -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <!-- Contenu du modal -->
        <div class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-md border border-gray-200">
            <!-- Header avec fond coloré -->
            <div style="background: linear-gradient(to right, #ef4444, #dc2626); padding: 1.25rem 2rem;">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full" style="background: rgba(255,255,255,0.2);">
                        <svg class="h-7 w-7" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: white;" id="modal-title">
                        Supprimer la demande
                    </h3>
                </div>
            </div>
            
            <!-- Corps du modal -->
            <div class="px-8 py-8">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 ml-2">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-base text-gray-700 leading-relaxed">
                            Êtes-vous sûr de vouloir supprimer la demande :
                        </p>
                        <p class="mt-3 text-xl font-bold text-red-600" id="deleteDemandeNumero"></p>
                        <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                            Cette action est irréversible. Toutes les données associées seront définitivement supprimées.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer avec boutons -->
            <div class="bg-gray-100 px-8 py-5 flex justify-end gap-4 border-t border-gray-200">
                <button type="button" onclick="closeDeleteModal()" class="px-6 py-3 rounded-lg bg-white text-gray-700 font-semibold text-sm border border-gray-300 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 shadow-sm">
                    Annuler
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 rounded-lg bg-red-600 text-white font-semibold text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                        Confirmer la suppression
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDeleteModal(actionUrl, numeroDemande) {
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteDemandeNumero').textContent = numeroDemande;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection
