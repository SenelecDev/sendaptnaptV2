@extends('layouts.app')

@section('title', 'Demande ' . $demande->numero_demande)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('desa.demandes.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">
                    Demande <span class="text-senelec-purple">{{ $demande->numero_demande }}</span>
                </h1>
                <p class="text-gray-600">Créée le {{ $demande->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($demande->statut === 'créée' || ($demande->statut === 'en cours de traitement' && !$demande->note))
                <a href="{{ route('desa.demandes.edit', $demande) }}" class="btn-senelec">
                    <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Traiter
                </a>
            @endif
            @if($demande->pdf_path)
                <a href="{{ Storage::url($demande->pdf_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-senelec-purple hover:bg-senelec-purple/90 text-white text-sm font-medium rounded-lg transition-all duration-200 hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer DAPT
                </a>
            @endif
        </div>
    </div>

    <!-- Statut -->
    <div class="card-senelec p-4">
        <div class="flex items-center justify-center gap-3">
            <span class="text-gray-700 font-medium">Statut actuel:</span>
            @switch($demande->statut)
                @case('créée')
                    <span class="badge badge-info text-sm px-3 py-1">Créée</span>
                    @break
                @case('en cours de traitement')
                    <span class="badge badge-warning text-sm px-3 py-1">En cours de traitement</span>
                    @break
                @case('acceptée')
                    <span class="badge badge-success text-sm px-3 py-1">Acceptée</span>
                    @break
                @case('retournée')
                    <span class="badge badge-danger text-sm px-3 py-1">Retournée</span>
                    @break
                @default
                    <span class="badge text-sm px-3 py-1">{{ $demande->statut }}</span>
            @endswitch
        </div>
        
        @if($demande->statut === 'retournée' && $demande->motif_retour)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="text-red-800 font-semibold text-sm">Motif du retour</h4>
                        <p class="text-red-700 mt-1">{{ $demande->motif_retour }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails de la demande -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Détails de la demande
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de la demande</dt>
                        <dd class="text-gray-900">{{ $demande->date ? \Carbon\Carbon::parse($demande->date)->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Destinataire</dt>
                        <dd class="text-gray-900">{{ $demande->destinataire ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Désignation des travaux</dt>
                        <dd class="text-gray-900">{{ $demande->designation ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lieu d'exécution</dt>
                        <dd class="text-gray-900">{{ $demande->lieu_execution ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type d'ouvrage</dt>
                        <dd class="text-gray-900 capitalize">{{ $demande->ouvrage_type ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Ouvrages -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Ouvrages
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- À consigner -->
                    <div class="bg-senelec-orange/5 border border-senelec-orange/30 rounded-lg p-4">
                        <h3 class="font-medium text-senelec-orange mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            À consigner
                        </h3>
                        @if($demande->mode_saisie === 'manuel')
                            <p class="text-gray-900 whitespace-pre-line">{{ $demande->ouvrages_consigner_manuel ?: '-' }}</p>
                        @else
                            @php
                                $lignesOracle = is_array($demande->lignes_oracle) ? $demande->lignes_oracle : ($demande->lignes_oracle ? json_decode($demande->lignes_oracle, true) : []);
                                $equipementsOracle = is_array($demande->equipements_oracle) ? $demande->equipements_oracle : ($demande->equipements_oracle ? json_decode($demande->equipements_oracle, true) : []);
                            @endphp
                            
                            @if(!empty($lignesOracle))
                                <div class="space-y-1">
                                    @foreach($lignesOracle as $ligne)
                                        @php
                                            $ligneDesc = is_array($ligne) ? ($ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? '') : '';
                                        @endphp
                                        <div class="text-sm text-gray-700">• {{ $ligneDesc }}</div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(!empty($equipementsOracle))
                                <div class="space-y-1 {{ !empty($lignesOracle) ? 'mt-2' : '' }}">
                                    @foreach($equipementsOracle as $levelName => $equipements)
                                        @if(!empty($equipements))
                                            @foreach($equipements as $equipement)
                                                @php
                                                    $eqDesc = is_array($equipement) ? ($equipement['description'] ?? $equipement['EQUIPMENT_DES'] ?? '') : '';
                                                @endphp
                                                <div class="text-sm text-gray-700">• {{ $eqDesc }}</div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(empty($lignesOracle) && empty($equipementsOracle))
                                <p class="text-gray-400 italic">-</p>
                            @endif
                        @endif
                    </div>
                    
                    <!-- À réaliser -->
                    <div class="bg-senelec-teal/5 border border-senelec-teal/30 rounded-lg p-4">
                        <h3 class="font-medium text-senelec-teal mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            À réaliser
                        </h3>
                        @if($demande->mode_saisie === 'manuel')
                            <p class="text-gray-900 whitespace-pre-line">{{ $demande->ouvrages_installer_manuel ?: '-' }}</p>
                        @else
                            @php
                                $lignesInstallerOracle = is_array($demande->lignes_installer_oracle) ? $demande->lignes_installer_oracle : ($demande->lignes_installer_oracle ? json_decode($demande->lignes_installer_oracle, true) : []);
                                $equipementsInstaller = is_array($demande->equipements_installer_oracle) ? $demande->equipements_installer_oracle : ($demande->equipements_installer_oracle ? json_decode($demande->equipements_installer_oracle, true) : []);
                            @endphp
                            
                            @if(!empty($lignesInstallerOracle))
                                <div class="space-y-1">
                                    @foreach($lignesInstallerOracle as $ligne)
                                        @php
                                            $ligneDesc = is_array($ligne) ? ($ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? '') : '';
                                        @endphp
                                        <div class="text-sm text-gray-700">• {{ $ligneDesc }}</div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(!empty($equipementsInstaller))
                                <div class="space-y-1 {{ !empty($lignesInstallerOracle) ? 'mt-2' : '' }}">
                                    @foreach($equipementsInstaller as $levelName => $equipements)
                                        @if(!empty($equipements))
                                            @foreach($equipements as $equipement)
                                                @php
                                                    $eqDesc = is_array($equipement) ? ($equipement['description'] ?? $equipement['EQUIPMENT_DES'] ?? '') : '';
                                                @endphp
                                                <div class="text-sm text-gray-700">• {{ $eqDesc }}</div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(empty($lignesInstallerOracle) && empty($equipementsInstaller))
                                <p class="text-gray-400 italic">-</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dates prévues -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Dates prévues
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Début prévu</dt>
                        <dd class="text-gray-900">{{ $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Heure début</dt>
                        <dd class="text-gray-900">{{ $demande->hdp ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fin prévue</dt>
                        <dd class="text-gray-900">{{ $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Heure fin</dt>
                        <dd class="text-gray-900">{{ $demande->hfp ?? '-' }}</dd>
                    </div>
                </div>
                
                @if($demande->dmrp_restitution)
                <div class="mt-4 bg-indigo-50 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 rounded-full">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                            </svg>
                        </div>
                        <span class="text-indigo-800 font-medium">À restituer le soir</span>
                    </div>
                    @if($demande->dmrp && $demande->dmrp !== 'non_applicable')
                    <div class="text-right">
                        <span class="text-sm text-indigo-600">Délai max</span>
                        <span class="ml-2 font-semibold text-indigo-900">{{ $demande->dmrp }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Demandeur -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Demandeur
                </h2>
                <div class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nom</dt>
                        <dd class="text-gray-900">{{ $demande->demandeur->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Service</dt>
                        <dd class="text-gray-900">{{ $demande->demandeur->appartenance ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $demande->demandeur->email ?? '-' }}</dd>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Options</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Consignation en:</span>
                        <span class="font-medium">{{ $demande->consignation_etape == 2 ? 'Deux étapes' : 'Une étape' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Mise à la terre:</span>
                        <span class="font-medium">{{ $demande->mte ? 'Oui' : 'Non' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Mise en court-circuit:</span>
                        <span class="font-medium">{{ $demande->mcce ? 'Oui' : 'Non' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Délai max de restitution:</span>
                        <span class="font-medium">{{ $demande->dmrp ?: 'Non applicable' }}</span>
                    </div>
                    @if($demande->dmrp_restitution)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Restituer le soir:</span>
                        <span class="font-medium text-green-600">Oui</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($demande->statut === 'acceptée')
            <!-- Dates acceptées -->
            <div class="card-senelec p-6 border-l-4 border-green-500">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Dates acceptées
                </h2>
                <div class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Début accepté</dt>
                        <dd class="text-gray-900">{{ $demande->dda ? \Carbon\Carbon::parse($demande->dda)->format('d/m/Y') : '-' }} {{ $demande->hda ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fin acceptée</dt>
                        <dd class="text-gray-900">{{ $demande->dfa ? \Carbon\Carbon::parse($demande->dfa)->format('d/m/Y') : '-' }} {{ $demande->hfa ?? '' }}</dd>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
