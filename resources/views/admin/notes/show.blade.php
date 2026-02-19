@extends('layouts.app')

@section('title', 'Détails NAPT - ' . $note->numero_note)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.notes.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">NAPT {{ $note->numero_note }}</h1>
                <p class="text-gray-600">{{ $note->demande->designation ?? 'Sans désignation' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="badge badge-info">Semaine {{ $note->numero_semaine }}</span>
            @switch($note->statut)
                @case('brouillon')
                    <span class="status-brouillon text-lg px-4 py-2">Brouillon</span>
                    @break
                @case('en étude')
                    <span class="status-en-etude text-lg px-4 py-2">En étude</span>
                    @break
                @case('en attente de vérification')
                    <span class="status-en-attente text-lg px-4 py-2">En attente</span>
                    @break
                @case('vérifiée')
                    <span class="status-verifiee text-lg px-4 py-2">Vérifiée</span>
                    @break
                @case('validée')
                    <span class="status-validee text-lg px-4 py-2">Validée</span>
                    @break
                @case('en cours d\'exécution')
                    <span class="status-en-execution text-lg px-4 py-2">En exécution</span>
                    @break
                @case('exécutée')
                    <span class="status-executee text-lg px-4 py-2">Exécutée</span>
                    @break
                @case('retournée')
                    <span class="status-retournee text-lg px-4 py-2">Retournée</span>
                    @break
                @case('annulée')
                    <span class="badge badge-danger text-lg px-4 py-2">Annulée</span>
                    @break
            @endswitch
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails de la note -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de la note</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Numéro</dt>
                        <dd class="mt-1 text-gray-900 font-mono">{{ $note->numero_note }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Semaine</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->numero_semaine }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">DAPT associée</dt>
                        <dd class="mt-1">
                            @if($note->demande)
                                <a href="{{ route('admin.demandes.show', $note->demande) }}" 
                                   class="text-senelec-purple hover:underline font-mono">
                                    {{ $note->demande->numero_demande }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Désignation (depuis DAPT)</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->demande->designation ?? '-' }}</dd>
                    </div>
                    @if($note->renseignementN)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Renseignements</dt>
                        <dd class="mt-1 text-gray-900 whitespace-pre-line bg-gray-50 p-3 rounded-lg">{{ $note->renseignementN }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Dates de la note -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Planification</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de remise étude</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->dre ? $note->dre->format('d/m/Y H:i') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date réelle exécution</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->drex ? $note->drex->format('d/m/Y H:i') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date début travaux</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->ddt ? $note->ddt->format('d/m/Y H:i') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date fin travaux</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->dft ? $note->dft->format('d/m/Y H:i') : '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- DAPT Source -->
            @if($note->demande)
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Demande source (DAPT)</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Numéro DAPT</dt>
                        <dd class="mt-1">
                            <a href="{{ route('admin.demandes.show', $note->demande) }}" class="text-senelec-purple hover:underline font-mono">
                                {{ $note->demande->numero_demande }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut DAPT</dt>
                        <dd class="mt-1">
                            @switch($note->demande->statut)
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
                            @endswitch
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lieu d'exécution</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->demande->lieu_execution ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Destinataire</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->demande->destinataire ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Destinataires -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Destinataires</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Charges de consignation -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Chargés de consignation</h4>
                        @if($note->chargesCons && $note->chargesCons->count() > 0)
                            <ul class="space-y-1">
                                @foreach($note->chargesCons as $charge)
                                    <li class="text-sm text-gray-900">{{ $charge->nom }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                        @if($note->adresse_charges_consignation)
                            <p class="mt-2 text-xs text-gray-500">{{ $note->adresse_charges_consignation }}</p>
                        @endif
                    </div>

                    <!-- Correspondants -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Correspondants</h4>
                        @if($note->correspondants && $note->correspondants->count() > 0)
                            <ul class="space-y-1">
                                @foreach($note->correspondants as $correspondant)
                                    <li class="text-sm text-gray-900">{{ $correspondant->nom }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                        @if($note->adresse_correspondants)
                            <p class="mt-2 text-xs text-gray-500">{{ $note->adresse_correspondants }}</p>
                        @endif
                    </div>

                    <!-- Services destinataires -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Services destinataires</h4>
                        @if($note->servicesDest && $note->servicesDest->count() > 0)
                            <ul class="space-y-1">
                                @foreach($note->servicesDest as $service)
                                    <li class="text-sm text-gray-900">{{ $service->nom }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Motifs (si retournée/annulée) -->
            @if($note->motif || $note->motifbis || $note->commentanul)
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Motifs / Commentaires</h3>
                <div class="space-y-4">
                    @if($note->motif)
                        <div class="p-3 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                            <dt class="text-sm font-medium text-orange-700">Motif de retour 1</dt>
                            <dd class="mt-1 text-gray-900">{{ $note->motif }}</dd>
                        </div>
                    @endif
                    @if($note->motifbis)
                        <div class="p-3 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                            <dt class="text-sm font-medium text-orange-700">Motif de retour 2</dt>
                            <dd class="mt-1 text-gray-900">{{ $note->motifbis }}</dd>
                        </div>
                    @endif
                    @if($note->commentanul)
                        <div class="p-3 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                            <dt class="text-sm font-medium text-red-700">Commentaire d'annulation</dt>
                            <dd class="mt-1 text-gray-900">{{ $note->commentanul }}</dd>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Établi par -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Établi par</h3>
                @if($note->etabli)
                    <div class="flex items-center gap-4">
                        <span class="inline-flex">
                            @if($note->etabli->photo_url)
                                <img class="h-12 w-12 rounded-full object-cover" 
                                     src="{{ $note->etabli->photo_url }}" 
                                     alt="{{ $note->etabli->full_name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="h-12 w-12 rounded-full bg-senelec-purple flex items-center justify-center text-white font-semibold" style="{{ $note->etabli->photo_url ? 'display:none' : '' }}">
                                {{ $note->etabli->initials }}
                            </div>
                        </span>
                        <div>
                            <div class="font-medium text-gray-900">{{ $note->etabli->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $note->etabli->matricule }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Non disponible</p>
                @endif
            </div>

            <!-- Intervenants -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Intervenants</h3>
                <div class="space-y-4">
                    <!-- Vérificateur -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Vérifié par</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $note->verifie?->full_name ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- Valideur -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Validé par</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $note->valide?->full_name ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- Exécuté par -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Exécuté par</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $note->execute?->full_name ?? '-' }}
                        </span>
                    </div>

                    @if($note->enCoursExecution)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">En execution par</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $note->enCoursExecution->full_name }}
                        </span>
                    </div>
                    @endif

                    @if($note->retourne1)
                    <div class="flex items-center justify-between text-orange-600">
                        <span class="text-sm">Retourné par (1)</span>
                        <span class="text-sm font-medium">
                            {{ $note->retourne1->full_name }}
                        </span>
                    </div>
                    @endif

                    @if($note->retourne2)
                    <div class="flex items-center justify-between text-orange-600">
                        <span class="text-sm">Retourné par (2)</span>
                        <span class="text-sm font-medium">
                            {{ $note->retourne2->full_name }}
                        </span>
                    </div>
                    @endif

                    @if($note->annule)
                    <div class="flex items-center justify-between text-red-600">
                        <span class="text-sm">Annulé par</span>
                        <span class="text-sm font-medium">
                            {{ $note->annule->full_name }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dates clés -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates clés</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Création</span>
                        <span class="text-gray-900">{{ $note->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($note->date)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Date note</span>
                            <span class="text-gray-900">{{ $note->date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($note->dre)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Remise étude</span>
                            <span class="text-gray-900">{{ $note->dre->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($note->ddt)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Début travaux</span>
                            <span class="text-gray-900">{{ $note->ddt->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($note->dft)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fin travaux</span>
                            <span class="text-gray-900">{{ $note->dft->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($note->drex)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Exécution réelle</span>
                            <span class="text-green-600 font-medium">{{ $note->drex->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dernière MAJ</span>
                        <span class="text-gray-900">{{ $note->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents</h3>
                <div class="space-y-3">
                    <a href="{{ route('pdf.napt.view', $note) }}" target="_blank" 
                       class="flex items-center gap-3 p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors border border-red-200">
                        <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                        <span class="text-sm font-medium text-red-700">PDF NAPT</span>
                    </a>
                    @if($note->document)
                        <a href="{{ $note->document_url }}" target="_blank" 
                           class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-200">
                            <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                            <span class="text-sm font-medium text-gray-900">Document</span>
                        </a>
                    @endif
                    @if($note->etude && !in_array($note->etude, ['oui', 'non']))
                        <a href="{{ $note->etude_url }}" target="_blank" 
                           class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                            <i class="fas fa-file-alt text-blue-500 text-xl"></i>
                            <span class="text-sm font-medium text-blue-700">Étude</span>
                        </a>
                    @endif
                    @if($note->fiche_manoeuvre)
                        <a href="{{ $note->fiche_manoeuvre_url }}" target="_blank" 
                           class="flex items-center gap-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200">
                            <i class="fas fa-clipboard-list text-green-500 text-xl"></i>
                            <span class="text-sm font-medium text-green-700">Fiche de manœuvre</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if(in_array($note->statut, ['brouillon', 'en étude']))
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <form action="{{ route('admin.notes.destroy', $note) }}" method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn bg-red-600 text-white hover:bg-red-700 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer la note
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Journal des modifications - Full Width Timeline -->
    @if($note->histories && $note->histories->count() > 0)
    <div class="card-senelec">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history text-senelec-purple mr-2"></i>
                Journal des modifications
            </h3>
            <span class="badge badge-info">{{ $note->histories->count() }} entrée(s)</span>
        </div>
        
        <div class="relative">
            <!-- Ligne verticale de la timeline -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gradient-to-b from-senelec-purple via-blue-400 to-gray-200"></div>
            
            <div class="space-y-6">
                @foreach($note->histories as $index => $history)
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
                            @elseif($history->action === 'verified')
                                <div class="w-8 h-8 rounded-full bg-cyan-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @elseif($history->action === 'validated')
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-check-double text-white text-xs"></i>
                                </div>
                            @elseif($history->action === 'executed')
                                <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-flag-checkered text-white text-xs"></i>
                                </div>
                            @elseif($history->action === 'returned')
                                <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-undo text-white text-xs"></i>
                                </div>
                            @elseif($history->action === 'cancelled')
                                <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                    <i class="fas fa-times text-white text-xs"></i>
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
                                    @elseif(in_array($history->action, ['returned', 'cancelled']) && $history->new_value)
                                        <div class="mt-3 p-3 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                                            <p class="text-sm text-orange-800">{{ $history->new_value }}</p>
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
