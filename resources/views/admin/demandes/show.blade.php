@extends('layouts.app')

@section('title', 'Détails DAPT - ' . $demande->numero_demande)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.demandes.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">DAPT {{ $demande->numero_demande }}</h1>
                <p class="text-gray-600">{{ $demande->designation ?? 'Sans désignation' }}</p>
            </div>
        </div>
        @switch($demande->statut)
            @case('créée')
                <span class="status-creee text-lg px-4 py-2">Créée</span>
                @break
            @case('en cours de traitement')
                <span class="status-en-cours text-lg px-4 py-2">En cours</span>
                @break
            @case('acceptée')
                <span class="status-acceptee text-lg px-4 py-2">Acceptée</span>
                @break
            @case('retournée')
                <span class="status-retournee text-lg px-4 py-2">Retournée</span>
                @break
        @endswitch
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails de la demande -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de la demande</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Numéro</dt>
                        <dd class="mt-1 text-gray-900 font-mono">{{ $demande->numero_demande }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Désignation</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->designation ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lieu d'exécution</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->lieu_execution ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Destinataire</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->destinataire ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Renseignements</dt>
                        <dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $demande->renseignement ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date début prévue</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->ddp ? $demande->ddp->format('d/m/Y') : '-' }} {{ $demande->hdp ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date fin prévue</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->dfp ? $demande->dfp->format('d/m/Y') : '-' }} {{ $demande->hfp ?? '' }}</dd>
                    </div>
                    @if($demande->statut === 'acceptée')
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date début acceptée</dt>
                        <dd class="mt-1 text-green-600 font-medium">{{ $demande->dda ? $demande->dda->format('d/m/Y') : '-' }} {{ $demande->hda ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date fin acceptée</dt>
                        <dd class="mt-1 text-green-600 font-medium">{{ $demande->dfa ? $demande->dfa->format('d/m/Y') : '-' }} {{ $demande->hfa ?? '' }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Notes associées -->
            <div class="card-senelec">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Notes associées (NAPT)</h3>
                    <span class="badge badge-info">{{ $demande->note ? 1 : 0 }} note(s)</span>
                </div>
                
                @if($demande->note)
                    <div class="space-y-3">
                        @php $note = $demande->note; @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $note->numero_note }}</div>
                                    <div class="text-sm text-gray-600">Semaine {{ $note->numero_semaine ?? '-' }}</div>
                                </div>
                                <div class="flex items-center gap-4">
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
                                    @endswitch
                                    <a href="{{ route('admin.notes.show', $note) }}" class="text-senelec-magenta hover:underline">
                                        Voir →
                                    </a>
                                </div>
                            </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2">Aucune note associée à cette demande</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Demandeur -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Demandeur</h3>
                @if($demande->demandeur)
                    <div class="flex items-center gap-4">
                        <span class="inline-flex">
                            @if($demande->demandeur->photo_url)
                                <img class="h-12 w-12 rounded-full object-cover" 
                                     src="{{ $demande->demandeur->photo_url }}" 
                                     alt="{{ $demande->demandeur->full_name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="h-12 w-12 rounded-full bg-senelec-purple flex items-center justify-center text-white font-semibold" style="{{ $demande->demandeur->photo_url ? 'display:none' : '' }}">
                                {{ $demande->demandeur->initials }}
                            </div>
                        </span>
                        <div>
                            <div class="font-medium text-gray-900">{{ $demande->demandeur->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $demande->demandeur->matricule }}</div>
                        </div>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Direction</dt>
                            <dd class="text-gray-900">{{ $demande->demandeur->direction ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Téléphone</dt>
                            <dd class="text-gray-900">{{ $demande->demandeur->telephone ?? '-' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="text-gray-500">Demandeur non disponible</p>
                @endif
            </div>

            <!-- Timeline -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique</h3>
                <div class="relative">
                    <!-- Ligne verticale -->
                    <div class="absolute left-1 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                    
                    <div class="space-y-4">
                        <!-- Création -->
                        <div class="flex gap-3 relative">
                            <div class="flex-shrink-0 w-2.5 h-2.5 mt-1.5 bg-senelec-purple rounded-full ring-4 ring-white z-10"></div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">Demande créée</div>
                                <div class="text-xs text-gray-500">{{ $demande->created_at->format('d/m/Y à H:i') }}</div>
                                @if($demande->demandeur)
                                    <div class="text-xs text-gray-400 mt-1">Par {{ $demande->demandeur->full_name }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- En cours de traitement -->
                        @if(in_array($demande->statut, ['en cours de traitement', 'acceptée', 'retournée']))
                            <div class="flex gap-3 relative">
                                <div class="flex-shrink-0 w-2.5 h-2.5 mt-1.5 bg-blue-500 rounded-full ring-4 ring-white z-10"></div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">Prise en charge</div>
                                    <div class="text-xs text-gray-500">Mise en cours de traitement</div>
                                    @if($demande->traite)
                                        <div class="text-xs text-gray-400 mt-1">Par {{ $demande->traite->full_name }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Traitement final (acceptée ou retournée) -->
                        @if($demande->statut === 'acceptée')
                            <div class="flex gap-3 relative">
                                <div class="flex-shrink-0 w-2.5 h-2.5 mt-1.5 bg-green-500 rounded-full ring-4 ring-white z-10"></div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-green-700">Demande acceptée</div>
                                    @if($demande->date_traitement)
                                        <div class="text-xs text-gray-500">{{ $demande->date_traitement->format('d/m/Y à H:i') }}</div>
                                    @endif
                                    @if($demande->traite)
                                        <div class="text-xs text-gray-400 mt-1">Par {{ $demande->traite->full_name }}</div>
                                    @endif
                                    @if($demande->dda && $demande->dfa)
                                        <div class="mt-2 p-2 bg-green-50 rounded text-xs">
                                            <div class="text-green-700 font-medium">Dates acceptées :</div>
                                            <div class="text-green-600">Du {{ $demande->dda->format('d/m/Y') }} {{ $demande->hda ?? '' }}</div>
                                            <div class="text-green-600">Au {{ $demande->dfa->format('d/m/Y') }} {{ $demande->hfa ?? '' }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($demande->statut === 'retournée')
                            <div class="flex gap-3 relative">
                                <div class="flex-shrink-0 w-2.5 h-2.5 mt-1.5 bg-orange-500 rounded-full ring-4 ring-white z-10"></div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-orange-700">Demande retournée</div>
                                    @if($demande->date_traitement)
                                        <div class="text-xs text-gray-500">{{ $demande->date_traitement->format('d/m/Y à H:i') }}</div>
                                    @endif
                                    @if($demande->traite)
                                        <div class="text-xs text-gray-400 mt-1">Par {{ $demande->traite->full_name }}</div>
                                    @endif
                                    @if($demande->motif_retour)
                                        <div class="mt-2 p-2 bg-orange-50 rounded text-xs">
                                            <div class="text-orange-700 font-medium">Motif :</div>
                                            <div class="text-orange-600">{{ $demande->motif_retour }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- NAPT créée -->
                        @if($demande->note)
                            <div class="flex gap-3 relative">
                                <div class="flex-shrink-0 w-2.5 h-2.5 mt-1.5 bg-indigo-500 rounded-full ring-4 ring-white z-10"></div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-indigo-700">NAPT créée</div>
                                    <div class="text-xs text-gray-500">{{ $demande->note->created_at->format('d/m/Y à H:i') }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <a href="{{ route('admin.notes.show', $demande->note) }}" class="text-indigo-600 hover:underline">
                                            {{ $demande->note->numero_note }} →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if(!$demande->note)
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <form action="{{ route('admin.demandes.destroy', $demande) }}" method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn bg-red-600 text-white hover:bg-red-700">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer la demande
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Journal des modifications - Full Width Timeline -->
    @if($demande->histories->count() > 0)
    <div class="card-senelec">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history text-senelec-purple mr-2"></i>
                Journal des modifications
            </h3>
            <span class="badge badge-info">{{ $demande->histories->count() }} entrée(s)</span>
        </div>
        
        <div class="relative">
            <!-- Ligne verticale de la timeline -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gradient-to-b from-senelec-purple via-blue-400 to-gray-200"></div>
            
            <div class="space-y-6">
                @foreach($demande->histories as $index => $history)
                    <div class="relative flex items-start gap-3 ml-4">
                        <!-- Point et icône de la timeline -->
                        <div class="flex-shrink-0 -ml-4">
                            @if($history->action === 'created')
                                <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-plus text-white text-xs"></i>
                                </div>
                            @elseif($history->action === 'status_changed')
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-exchange-alt text-white text-xs"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-edit text-white text-xs"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Contenu -->
                        <div class="flex-1 pb-6">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                <div class="p-4">
                                    <!-- En-tête avec date -->
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $history->description }}</h4>
                                            @if($history->user)
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <i class="fas fa-user text-gray-400 mr-1"></i>
                                                    {{ $history->user->full_name }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-sm font-medium text-gray-700">{{ $history->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $history->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Détails du changement -->
                                    @if($history->action === 'status_changed' && $history->old_value && $history->new_value)
                                        <div class="mt-3 flex items-center gap-3 flex-wrap">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-gray-100 text-gray-700">
                                                {{ $history->old_value }}
                                            </span>
                                            <span class="text-gray-400">
                                                <i class="fas fa-arrow-right"></i>
                                            </span>
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-senelec-purple text-white font-medium">
                                                {{ $history->new_value }}
                                            </span>
                                        </div>
                                    @elseif($history->action === 'updated' && $history->old_value && $history->new_value)
                                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ancienne valeur</span>
                                                    <p class="mt-1 text-red-600 line-through">{{ $history->old_value }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nouvelle valeur</span>
                                                    <p class="mt-1 text-green-600 font-medium">{{ $history->new_value }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
