@extends('layouts.app')

@section('title', 'NAPT ' . $note->numero_note . ' - Directeur')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('directeur.napt') }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">NAPT {{ $note->numero_note }}</h1>
            <p class="text-gray-600">Détails de la note (lecture seule)</p>
        </div>
        <span class="{{ $note->getStatutBadgeClass() }} text-lg px-4 py-2">{{ ucfirst($note->statut) }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations générales -->
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">N° NAPT</p>
                            <p class="font-medium">{{ $note->numero_note }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Semaine</p>
                            <p class="font-medium">{{ $note->semaine ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($note->demande)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">DAPT associée</p>
                            <a href="{{ route('directeur.dapt.show', $note->demande) }}" class="text-senelec-purple hover:underline font-medium">
                                {{ $note->demande->numero_demande }}
                            </a>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lieu d'exécution</p>
                            <p class="font-medium">{{ $note->demande->lieu_execution ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Date début travaux</p>
                            <p class="font-medium">{{ $note->ddt ? $note->ddt->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date fin travaux</p>
                            <p class="font-medium">{{ $note->dft ? $note->dft->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ouvrages -->
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Ouvrages</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Ouvrages à consigner</p>
                        @if($note->demande && $note->demande->ouvrages_consigner_manuel)
                            <p class="font-medium bg-gray-50 p-3 rounded-lg">{{ $note->demande->ouvrages_consigner_manuel }}</p>
                        @elseif($note->demande && $note->demande->ouvrages_consigner_gmao && count($note->demande->ouvrages_consigner_gmao) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($note->demande->ouvrages_consigner_gmao as $ouvrage)
                                    <span class="px-2 py-1 bg-senelec-purple/10 text-senelec-purple rounded text-sm">{{ $ouvrage }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">Non renseigné</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Ouvrages à installer</p>
                        @if($note->demande && $note->demande->ouvrages_installer_manuel)
                            <p class="font-medium bg-gray-50 p-3 rounded-lg">{{ $note->demande->ouvrages_installer_manuel }}</p>
                        @elseif($note->demande && $note->demande->ouvrages_installer_gmao && count($note->demande->ouvrages_installer_gmao) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($note->demande->ouvrages_installer_gmao as $ouvrage)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm">{{ $ouvrage }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">Non renseigné</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Consignes -->
            @if($note->consignes_particulieres || $note->mesures_securite)
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Consignes et mesures</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($note->consignes_particulieres)
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Consignes particulières</p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                            {{ $note->consignes_particulieres }}
                        </div>
                    </div>
                    @endif
                    @if($note->mesures_securite)
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Mesures de sécurité</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm">
                            {{ $note->mesures_securite }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Observations -->
            @if($note->observations && $note->observations->count() > 0)
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Observations</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($note->observations as $observation)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-senelec-purple/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-senelec-purple font-medium text-sm">
                                        {{ strtoupper(substr($observation->user->full_name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $observation->user->full_name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $observation->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $observation->contenu }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne droite -->
        <div class="space-y-6">
            <!-- Intervenants -->
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Intervenants</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($note->etabliPar)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Établi par</p>
                        <p class="font-medium text-gray-900">{{ $note->etabliPar->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $note->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($note->verifiePar)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Vérifié par</p>
                        <p class="font-medium text-gray-900">{{ $note->verifiePar->full_name }}</p>
                        @if($note->verified_at)
                            <p class="text-xs text-gray-500">{{ $note->verified_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    @endif
                    @if($note->validePar)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Validé par</p>
                        <p class="font-medium text-gray-900">{{ $note->validePar->full_name }}</p>
                        @if($note->validated_at)
                            <p class="text-xs text-gray-500">{{ $note->validated_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    @endif
                    @if($note->execute)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Exécuté par</p>
                        <p class="font-medium text-gray-900">{{ $note->execute->full_name }}</p>
                        @if($note->executed_at)
                            <p class="text-xs text-gray-500">{{ $note->executed_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contacts -->
            @if($note->demande && ($note->demande->chargeCons || $note->demande->correspondant))
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Contacts</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($note->demande->chargeCons)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Chargé de consignation</p>
                        <p class="font-medium text-gray-900">{{ $note->demande->chargeCons->nom_prenoms }}</p>
                        @if($note->demande->chargeCons->telephone)
                            <p class="text-xs text-gray-500">{{ $note->demande->chargeCons->telephone }}</p>
                        @endif
                    </div>
                    @endif
                    @if($note->demande->correspondant)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Correspondant</p>
                        <p class="font-medium text-gray-900">{{ $note->demande->correspondant->nom_prenoms }}</p>
                        @if($note->demande->correspondant->telephone)
                            <p class="text-xs text-gray-500">{{ $note->demande->correspondant->telephone }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Historique -->
            @if($note->histories && $note->histories->count() > 0)
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Historique</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @php
                            $actionLabels = [
                                'created' => 'Note créée',
                                'updated' => 'Note modifiée',
                                'status_changed' => 'Statut modifié',
                                'submitted' => 'Note soumise',
                                'verified' => 'Note vérifiée',
                                'validated' => 'Note validée',
                                'returned' => 'Note retournée',
                                'returned_v' => 'Retournée par vérificateur',
                                'returned_val' => 'Retournée par valideur',
                                'executed' => 'Note exécutée',
                                'execution_started' => 'Exécution démarrée',
                                'cancelled' => 'Note annulée',
                                'pdf_generated' => 'PDF généré',
                                'fiche_generated' => 'Fiche manœuvre générée',
                            ];
                        @endphp
                        @foreach($note->histories->sortByDesc('created_at')->take(5) as $history)
                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-2 h-2 bg-senelec-purple rounded-full mt-2"></div>
                            <div>
                                <p class="text-gray-900">{{ $actionLabels[$history->action] ?? ucfirst(str_replace('_', ' ', $history->action)) }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                    @if($history->user)
                                        - {{ $history->user->full_name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Documents annexes -->
    @if(($note->etude && Storage::exists($note->etude)) || ($note->fiche_manoeuvre && Storage::exists($note->fiche_manoeuvre)))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($note->etude && Storage::exists($note->etude))
        <div class="card-senelec">
            <div class="card-header flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Document en étude</h3>
                <a href="{{ $note->etude_url }}" target="_blank" class="btn-senelec-outline text-sm py-2 px-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger
                </a>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 text-gray-600">
                    <svg class="w-10 h-10 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900">{{ basename($note->etude) }}</p>
                        <p class="text-sm text-gray-500">Document joint à l'étude</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($note->fiche_manoeuvre && Storage::exists($note->fiche_manoeuvre))
        <div class="card-senelec">
            <div class="card-header flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Fiche de manœuvre</h3>
                <a href="{{ $note->fiche_manoeuvre_url }}" target="_blank" class="btn-senelec-outline text-sm py-2 px-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger
                </a>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 text-gray-600">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900">{{ basename($note->fiche_manoeuvre) }}</p>
                        <p class="text-sm text-gray-500">Fiche de manœuvre opérateur</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Journal des modifications - Full Width Timeline -->
    @if($note->histories && $note->histories->count() > 0)
    <div class="card-senelec">
        <div class="flex items-center justify-between mb-6 p-6 pb-0">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history text-senelec-purple mr-2"></i>
                Journal des modifications
            </h3>
            <span class="badge badge-info">{{ $note->histories->count() }} entrée(s)</span>
        </div>
        
        <div class="p-6 pt-0">
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
    </div>
    @endif

    <!-- Document PDF NAPT -->
    <div class="card-senelec">
        <div class="card-header flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Document NAPT</h3>
            <a href="{{ route('pdf.napt.view', $note) }}" target="_blank" class="btn-senelec-outline text-sm py-2 px-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Ouvrir dans un nouvel onglet
            </a>
        </div>
        <div class="p-0">
            <iframe src="{{ route('pdf.napt.view', $note) }}" class="w-full border-0" style="height: 900px;"></iframe>
        </div>
    </div>
</div>
@endsection
