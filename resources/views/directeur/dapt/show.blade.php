@extends('layouts.app')

@section('title', 'DAPT ' . $demande->numero_demande . ' - Directeur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('directeur.dapt') }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">DAPT {{ $demande->numero_demande }}</h1>
            <p class="text-gray-600">Détails de la demande (lecture seule)</p>
        </div>
        <span class="{{ $demande->getStatutBadgeClass() }} text-lg px-4 py-2">{{ ucfirst($demande->statut) }}</span>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations générales -->
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">N° DAPT</p>
                            <p class="font-medium">{{ $demande->numero_demande }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de création</p>
                            <p class="font-medium">{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Désignation des travaux</p>
                        <p class="font-medium">{{ $demande->designation }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Lieu d'exécution</p>
                            <p class="font-medium">{{ $demande->lieu_execution ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date souhaitée</p>
                            <p class="font-medium">{{ $demande->date ? $demande->date->format('d/m/Y') : 'N/A' }}</p>
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
                        @if($demande->ouvrages_consigner_manuel)
                            <p class="font-medium bg-gray-50 p-3 rounded-lg">{{ $demande->ouvrages_consigner_manuel }}</p>
                        @elseif($demande->ouvrages_consigner_gmao && count($demande->ouvrages_consigner_gmao) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($demande->ouvrages_consigner_gmao as $ouvrage)
                                    <span class="px-2 py-1 bg-senelec-purple/10 text-senelec-purple rounded text-sm">{{ $ouvrage }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">Non renseigné</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Ouvrages à installer</p>
                        @if($demande->ouvrages_installer_manuel)
                            <p class="font-medium bg-gray-50 p-3 rounded-lg">{{ $demande->ouvrages_installer_manuel }}</p>
                        @elseif($demande->ouvrages_installer_gmao && count($demande->ouvrages_installer_gmao) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($demande->ouvrages_installer_gmao as $ouvrage)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm">{{ $ouvrage }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">Non renseigné</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Note associée -->
            @if($demande->note)
            <div class="card-senelec">
                <div class="card-header bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900">NAPT associée</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-blue-900">{{ $demande->note->numero_note }}</p>
                            <p class="text-sm text-gray-500">
                                Créée le {{ $demande->note->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="{{ $demande->note->getStatutBadgeClass() }}">{{ ucfirst($demande->note->statut) }}</span>
                            <a href="{{ route('directeur.napt.show', $demande->note) }}" class="btn-senelec text-sm">Voir la NAPT</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Colonne droite -->
        <div class="space-y-6">
            <!-- Demandeur -->
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Demandeur</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-senelec-purple/10 rounded-full flex items-center justify-center">
                            <span class="text-senelec-purple font-bold text-lg">
                                {{ strtoupper(substr($demande->demandeur->full_name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $demande->demandeur->full_name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $demande->demandeur->email ?? '' }}</p>
                        </div>
                    </div>
                    @if($demande->demandeur && $demande->demandeur->groupe)
                    <div>
                        <p class="text-sm text-gray-500">Groupe</p>
                        <p class="font-medium">{{ $demande->demandeur->groupe->nom }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Chargé de travaux -->
            @if($demande->chargeTravauxInfo)
            <div class="card-senelec">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Chargé de travaux</h3>
                </div>
                <div class="p-6 space-y-2">
                    <p class="font-medium text-gray-900">{{ $demande->chargeTravauxInfo->nom }}</p>
                    @if($demande->chargeTravauxInfo->telephone)
                        <p class="text-sm text-gray-500">{{ $demande->chargeTravauxInfo->telephone }}</p>
                    @endif
                    <p class="text-xs text-gray-400">{{ $demande->chargeTravauxInfo->entreprise }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Historique complet -->
    <div class="card-senelec">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-clock text-senelec-purple mr-2"></i>
                Historique
            </h3>
        </div>
        <div class="p-6">
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
                                    <a href="{{ route('directeur.napt.show', $demande->note) }}" class="text-indigo-600 hover:underline">
                                        {{ $demande->note->numero_note }} →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Journal des modifications - Full Width Timeline -->
    @if($demande->histories && $demande->histories->count() > 0)
    <div class="card-senelec">
        <div class="flex items-center justify-between mb-6 p-6 pb-0">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history text-senelec-purple mr-2"></i>
                Journal des modifications
            </h3>
            <span class="badge badge-info">{{ $demande->histories->count() }} entrée(s)</span>
        </div>
        
        <div class="p-6 pt-0">
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
    </div>
    @endif

    <!-- PDF DAPT (pleine largeur) -->
    @if($demande->pdf_url)
    <div class="card-senelec">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M10.92,12.31C10.68,11.54 10.15,9.08 11.55,9.04C12.95,9 12.03,12.16 12.03,12.16C12.42,13.65 14.05,14.72 14.05,14.72C14.55,14.57 17.4,14.24 17,15.72C16.57,17.2 13.5,15.81 13.5,15.81C11.55,15.95 10.09,16.47 10.09,16.47C8.96,18.58 7.64,19.5 7.1,18.61C6.43,17.5 9.23,16.07 9.23,16.07C10.68,13.72 10.68,12.14 10.92,12.31Z"/>
                </svg>
                Document DAPT
            </h3>
            <div class="flex gap-2">
                <a href="{{ $demande->pdf_url }}" download class="btn-senelec-outline text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger
                </a>
                <a href="{{ $demande->pdf_url }}" target="_blank" class="btn-senelec text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ouvrir
                </a>
            </div>
        </div>
        <div class="p-0">
            <iframe 
                src="{{ $demande->pdf_url }}" 
                class="w-full rounded-b-lg border-0" 
                style="height: 800px;"
                title="DAPT {{ $demande->numero_demande }}">
            </iframe>
        </div>
    </div>
    @else
    <div class="card-senelec p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-500 text-lg">Aucun document PDF disponible</p>
        <p class="text-gray-400 text-sm mt-1">Le demandeur n'a pas joint de fichier PDF à cette demande.</p>
    </div>
    @endif
</div>
@endsection
