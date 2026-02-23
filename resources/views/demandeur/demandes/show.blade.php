@extends('layouts.app')

@section('title', 'Demande ' . $demande->numero_demande)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('demandeur.demandes.index') }}" class="text-gray-500 hover:text-gray-700">
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
            @if(in_array($demande->statut, ['retournée', 'brouillon']))
                <a href="{{ route('demandeur.demandes.edit', $demande) }}" class="btn-senelec-outline py-2 px-4">
                    <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
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
                @case('brouillon')
                    <span class="badge badge-secondary text-sm px-3 py-1">Brouillon</span>
                    @break
                @default
                    <span class="badge badge-secondary text-sm px-3 py-1">{{ ucfirst($demande->statut) }}</span>
            @endswitch
            @if($demande->statut === 'retournée')
                <div class="text-red-600 text-sm ml-4">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Veuillez corriger et resoumettre votre demande
                </div>
            @endif
        </div>
    </div>

    <!-- Alerte motif de retour -->
    @if($demande->statut === 'retournée' && $demande->motif_retour)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-red-800 font-semibold">Motif du retour</h4>
                    <p class="text-red-700 mt-1">{{ $demande->motif_retour }}</p>
                    <a href="{{ route('demandeur.demandes.edit', $demande) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier et resoumettre
                    </a>
                </div>
            </div>
        </div>
    @endif

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
                </dl>
            </div>

            <!-- Ouvrages -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Ouvrages
                    @if($demande->mode_saisie)
                        <span class="ml-2 text-xs badge {{ $demande->mode_saisie === 'gmao' ? 'badge-info' : 'badge-secondary' }}">
                            Mode {{ $demande->mode_saisie === 'gmao' ? 'GMAO' : 'Manuel' }}
                        </span>
                    @endif
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
                                $hasConsignerData = false;
                                $lignesOracle = is_array($demande->lignes_oracle) ? $demande->lignes_oracle : ($demande->lignes_oracle ? json_decode($demande->lignes_oracle, true) : []);
                                $equipementsOracle = is_array($demande->equipements_oracle) ? $demande->equipements_oracle : ($demande->equipements_oracle ? json_decode($demande->equipements_oracle, true) : []);
                            @endphp
                            
                            @if(!empty($lignesOracle))
                                @php $hasConsignerData = true; @endphp
                                <div class="space-y-1">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Lignes</p>
                                    @foreach($lignesOracle as $ligne)
                                        @php
                                            $ligneCode = is_array($ligne) ? ($ligne['code'] ?? $ligne['EQUIPMENT_CD'] ?? '-') : $ligne;
                                            $ligneDesc = is_array($ligne) ? ($ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? '') : '';
                                        @endphp
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="text-gray-700">• {{ $ligneDesc ?: $ligneCode }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(!empty($equipementsOracle))
                                @php $hasConsignerData = true; @endphp
                                <div class="space-y-2 {{ !empty($lignesOracle) ? 'mt-3 pt-3 border-t border-gray-200' : '' }}">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Équipements</p>
                                    @foreach($equipementsOracle as $levelName => $equipements)
                                        @if(!empty($equipements))
                                            @php
                                                // Extraire le numéro de niveau du nom (ex: equipements_consigner_level_1 -> 1)
                                                preg_match('/level_(\d+)/', $levelName, $matches);
                                                $levelNum = $matches[1] ?? '';
                                            @endphp
                                            @if($levelNum)
                                                <p class="text-xs font-semibold text-senelec-orange mt-2">Niveau {{ $levelNum }}</p>
                                            @endif
                                            @foreach($equipements as $equipement)
                                                @php
                                                    $eqCode = is_array($equipement) ? ($equipement['code'] ?? $equipement['EQUIPMENT_CD'] ?? '-') : $equipement;
                                                    $eqDesc = is_array($equipement) ? ($equipement['description'] ?? $equipement['EQUIPMENT_DES'] ?? '') : '';
                                                @endphp
                                                <div class="flex items-center gap-2 text-sm pl-3">
                                                    <span class="text-gray-700">• {{ $eqDesc ?: $eqCode }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(!$hasConsignerData)
                                <p class="text-gray-400 italic">-</p>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Sur lesquels les travaux sont à réaliser -->
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
                                $hasInstallerData = false;
                                $lignesInstallerOracle = is_array($demande->lignes_installer_oracle) ? $demande->lignes_installer_oracle : ($demande->lignes_installer_oracle ? json_decode($demande->lignes_installer_oracle, true) : []);
                                $equipementsInstallerOracle = is_array($demande->equipements_installer_oracle) ? $demande->equipements_installer_oracle : ($demande->equipements_installer_oracle ? json_decode($demande->equipements_installer_oracle, true) : []);
                            @endphp
                            
                            @if(!empty($lignesInstallerOracle))
                                @php $hasInstallerData = true; @endphp
                                <div class="space-y-1">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Lignes</p>
                                    @foreach($lignesInstallerOracle as $ligne)
                                        @php
                                            $ligneCodeI = is_array($ligne) ? ($ligne['code'] ?? $ligne['EQUIPMENT_CD'] ?? '-') : $ligne;
                                            $ligneDescI = is_array($ligne) ? ($ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? '') : '';
                                        @endphp
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="text-gray-700">• {{ $ligneDescI ?: $ligneCodeI }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(!empty($equipementsInstallerOracle))
                                @php $hasInstallerData = true; @endphp
                                <div class="space-y-2 {{ !empty($lignesInstallerOracle) ? 'mt-3 pt-3 border-t border-gray-200' : '' }}">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Équipements</p>
                                    @foreach($equipementsInstallerOracle as $levelName => $equipements)
                                        @if(!empty($equipements))
                                            @php
                                                // Extraire le numéro de niveau du nom (ex: equipements_installer_level_1 -> 1)
                                                preg_match('/level_(\d+)/', $levelName, $matches);
                                                $levelNumI = $matches[1] ?? '';
                                            @endphp
                                            @if($levelNumI)
                                                <p class="text-xs font-semibold text-senelec-teal mt-2">Niveau {{ $levelNumI }}</p>
                                            @endif
                                            @foreach($equipements as $equipement)
                                                @php
                                                    $eqCodeI = is_array($equipement) ? ($equipement['code'] ?? $equipement['EQUIPMENT_CD'] ?? '-') : $equipement;
                                                    $eqDescI = is_array($equipement) ? ($equipement['description'] ?? $equipement['EQUIPMENT_DES'] ?? '') : '';
                                                @endphp
                                                <div class="flex items-center gap-2 text-sm pl-3">
                                                    <span class="text-gray-700">• {{ $eqDescI ?: $eqCodeI }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            
                            @if(!$hasInstallerData)
                                <p class="text-gray-400 italic">-</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dates et heures -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Dates et heures
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dates prévues -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="font-medium text-blue-800 mb-3">Dates prévues</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-blue-600">Début</dt>
                                <dd class="text-blue-900 font-medium">
                                    {{ $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('d/m/Y') : '-' }}
                                    {{ $demande->hdp ? 'à ' . $demande->hdp : '' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-blue-600">Fin</dt>
                                <dd class="text-blue-900 font-medium">
                                    {{ $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('d/m/Y') : '-' }}
                                    {{ $demande->hfp ? 'à ' . $demande->hfp : '' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <!-- Dates acceptées -->
                    @if($demande->statut === 'acceptée')
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-medium text-green-800 mb-3">Dates acceptées</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-green-600">Début</dt>
                                    <dd class="text-green-900 font-medium">
                                        {{ $demande->dda ? \Carbon\Carbon::parse($demande->dda)->format('d/m/Y') : '-' }}
                                        {{ $demande->hda ? 'à ' . $demande->hda : '' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-green-600">Fin</dt>
                                    <dd class="text-green-900 font-medium">
                                        {{ $demande->dfa ? \Carbon\Carbon::parse($demande->dfa)->format('d/m/Y') : '-' }}
                                        {{ $demande->hfa ? 'à ' . $demande->hfa : '' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    @endif
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
                    @php
                        $dmrpRaw = $demande->getAttributes()['dmrp'] ?? null;
                        $hasDmrp = $dmrpRaw && $dmrpRaw !== 'non_applicable' && $dmrpRaw !== '';
                    @endphp
                    @if($hasDmrp)
                    <div class="text-right">
                        <span class="text-sm text-indigo-600">Délai max</span>
                        <span class="ml-2 font-semibold text-indigo-900">{{ $dmrpRaw }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Options -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Options</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Mise à la terre aux extrémités</span>
                        <span class="text-sm font-medium {{ $demande->mte ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $demande->mte ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Mise en court-circuit aux extrémités</span>
                        <span class="text-sm font-medium {{ $demande->mcce ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $demande->mcce ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        @php
                            $dmrpRaw = $demande->getAttributes()['dmrp'] ?? null;
                            $hasDmrp = $dmrpRaw && $dmrpRaw !== 'non_applicable' && $dmrpRaw !== '';
                        @endphp
                        <span class="text-sm text-gray-700">Délai max de restitution</span>
                        <span class="text-sm font-medium {{ $hasDmrp ? 'text-green-600' : 'text-gray-500' }}">
                            @if($hasDmrp)
                                {{ $dmrpRaw }}
                            @else
                                Non applicable
                            @endif
                        </span>
                    </div>
                    @if($demande->dmrp_restitution)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Restituer le soir</span>
                        <span class="text-sm font-medium text-green-600">Oui</span>
                    </div>
                    @endif
                    <div class="pt-2 border-t flex items-center justify-between">
                        <span class="text-sm text-gray-700">Type d'étape</span>
                        <span class="text-sm font-medium text-gray-900">{{ $demande->etape === 'ue' ? 'Une étape' : 'Deux étapes' }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes liées -->
            @if($demande->note)
                <div class="card-senelec p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Note NAPT liée</h2>
                    <div class="space-y-3">
                        @php $note = $demande->note; @endphp
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="font-medium text-senelec-purple">{{ $note->numero_note }}</div>
                                <div class="text-sm text-gray-500">{{ $note->statut }}</div>
                            </div>
                    </div>
                </div>
            @endif

            <!-- Renseignements -->
            @if($demande->renseignement)
                <div class="card-senelec p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Renseignements</h2>
                    <p class="text-gray-700 text-sm whitespace-pre-line">{{ $demande->renseignement }}</p>
                </div>
            @endif

            <!-- Schéma -->
            @if($demande->schema)
                <div class="card-senelec p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schéma</h2>
                    <a href="{{ Storage::url($demande->schema) }}" target="_blank" class="btn-senelec-outline w-full py-2">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Voir le schéma
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
