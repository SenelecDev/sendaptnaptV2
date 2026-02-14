@extends('layouts.app')

@section('title', 'Traiter la demande ' . $demande->numero_demande)

@push('styles')
<style>
    /* Styles pour l'affichage des données Oracle */
    .oracle-saved-data {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .oracle-saved-data h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
    }

    .oracle-equipment-item {
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 0.25rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        position: relative;
    }

    .oracle-level-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
        margin-right: 0.5rem;
    }

    .oracle-level-1 { background-color: #dc3545; color: white; }
    .oracle-level-2 { background-color: #fd7e14; color: white; }
    .oracle-level-3 { background-color: #ffc107; color: black; }
    .oracle-level-4 { background-color: #28a745; color: white; }
    .oracle-level-5 { background-color: #17a2b8; color: white; }
    .oracle-level-6 { background-color: #6f42c1; color: white; }

    .oracle-equipment-code {
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        color: #6c757d;
        background-color: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    .schema-container {
        border: 2px solid #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: #fff;
    }

    .schema-container img {
        max-width: 100%;
        height: auto;
        display: block;
    }

    .schema-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ligne-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        border: 1px solid #4caf50;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .poste-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 1px solid #2196f3;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

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
                    Traiter la demande <span class="text-senelec-purple">{{ $demande->numero_demande }}</span>
                </h1>
                <p class="text-gray-600">Statut actuel: 
                    @switch($demande->statut)
                        @case('créée')
                            <span class="badge badge-info">Créée</span>
                            @break
                        @case('en cours de traitement')
                            <span class="badge badge-warning">En cours de traitement</span>
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
                </p>
            </div>
        </div>

        <!-- Boutons d'action rapides -->
        <div class="flex gap-2">
            @if($demande->schema)
                <a href="{{ $demande->schema_url }}" target="_blank" class="btn-senelec-outline">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Voir schéma
                </a>
            @endif
            @if($demande->pdf_path)
                <a href="{{ $demande->pdf_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger DAPT
                </a>
            @endif
        </div>
    </div>

    <!-- Alerte si demande retournée -->
    @if($demande->statut === 'retournée' && $demande->motif_retour)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h4 class="text-red-800 font-semibold">Demande retournée</h4>
                    <p class="text-red-700 mt-1">{{ $demande->motif_retour }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations de la demande -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Résumé de la demande -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Résumé de la demande
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Demandeur</dt>
                        <dd class="text-gray-900 font-medium">{{ $demande->demandeur->name ?? '-' }}</dd>
                        @if($demande->demandeur)
                            <dd class="text-xs text-gray-500 mt-1">{{ $demande->demandeur->service ?? '' }} • {{ $demande->demandeur->user_title ?? '' }}</dd>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Date de la demande</dt>
                        <dd class="text-gray-900">{{ $demande->date ? \Carbon\Carbon::parse($demande->date)->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Destinataire</dt>
                        <dd class="text-gray-900">{{ $demande->destinataire ?? '-' }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Mode de saisie</dt>
                        <dd class="text-gray-900">
                            @if($demande->mode_saisie === 'manuel')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Saisie manuelle
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                    </svg>
                                    GMAO
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="md:col-span-2 space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Désignation des travaux</dt>
                        <dd class="text-gray-900 bg-gray-50 p-3 rounded-lg mt-1">{{ $demande->designation ?? '-' }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Lieu d'exécution</dt>
                        <dd class="text-gray-900 font-medium">{{ $demande->lieu_execution ?? $demande->lieu_execution_manuel ?? '-' }}</dd>
                        @if($demande->lieu_code)
                            <dd class="text-xs text-gray-500">Code: {{ $demande->lieu_code }}</dd>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Type d'ouvrage</dt>
                        <dd class="text-gray-900 capitalize">
                            @if($demande->ouvrage_type === 'ligne')
                                <span class="ligne-badge">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Ligne
                                </span>
                            @elseif($demande->ouvrage_type === 'poste')
                                <span class="poste-badge">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Poste
                                </span>
                            @else
                                {{ $demande->ouvrage_type ?? '-' }}
                            @endif
                        </dd>
                    </div>
                    @if($demande->renseignement)
                    <div class="md:col-span-2 space-y-1">
                        <dt class="text-sm font-medium text-gray-500">Renseignements complémentaires</dt>
                        <dd class="text-gray-900 bg-yellow-50 border border-yellow-200 p-3 rounded-lg mt-1">{{ $demande->renseignement }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Période proposée -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Période proposée par le demandeur
                </h2>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <!-- Labels sur une ligne -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-2">
                        <dt class="text-xs font-medium text-blue-600">Date début</dt>
                        <dt class="text-xs font-medium text-blue-600">Heure début</dt>
                        <dt class="text-xs font-medium text-blue-600">Date fin</dt>
                        <dt class="text-xs font-medium text-blue-600">Heure fin</dt>
                        <dt class="text-xs font-medium text-blue-600">Délai max de restitution</dt>
                    </div>
                    <!-- Valeurs sur une ligne -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <dd class="text-sm text-gray-900 font-medium">{{ $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('d/m/Y') : '-' }}</dd>
                        <dd class="text-sm text-gray-900 font-medium">{{ $demande->hdp ?? '-' }}</dd>
                        <dd class="text-sm text-gray-900 font-medium">{{ $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('d/m/Y') : '-' }}</dd>
                        <dd class="text-sm text-gray-900 font-medium">{{ $demande->hfp ?? '-' }}</dd>
                        <dd class="text-sm text-gray-900 font-medium">
                            @if($demande->dmrp === 'non_applicable' || !$demande->dmrp)
                                <span class="text-gray-500">N/A</span>
                            @else
                                {{ $demande->dmrp }}
                            @endif
                        </dd>
                    </div>
                    @if($demande->dmrp_restitution)
                        <div class="mt-3 pt-3 border-t border-blue-200 flex items-center justify-center">
                            <span class="text-lg mr-2">🌙</span>
                            <span class="text-sm font-medium text-orange-600">Restitution le soir prévue</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Schéma joint -->
            @if($demande->schema)
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Schéma joint
                </h2>
                <div class="schema-container">
                    <div class="schema-header">
                        <span class="text-sm text-gray-600">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Document joint à la demande
                        </span>
                        <a href="{{ $demande->schema_url }}" target="_blank" class="text-sm text-senelec-purple hover:text-senelec-purple/80 font-medium">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Ouvrir en plein écran
                        </a>
                    </div>
                    <div class="p-4">
                        <img src="{{ $demande->schema_url }}" alt="Schéma de la demande" class="max-w-full h-auto mx-auto rounded shadow-sm" style="max-height: 400px; object-fit: contain;">
                    </div>
                </div>
            </div>
            @else
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Schéma joint
                </h2>
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500">Aucun schéma joint à cette demande</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ouvrages à consigner -->
            <div class="card-senelec p-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">
                    <svg class="w-4 h-4 inline mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Ouvrages à consigner
                </h2>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    @if($demande->mode_saisie === 'manuel')
                        <div class="text-sm text-gray-700">
                            {{ $demande->ouvrages_consigner_manuel ?: 'Non spécifié' }}
                        </div>
                    @else
                        @php
                            $lignesOracle = $demande->lignes_oracle ? json_decode($demande->lignes_oracle, true) : [];
                            $equipementsOracle = $demande->equipements_oracle ? json_decode($demande->equipements_oracle, true) : [];
                            
                            // Ligne disponible consigner
                            $ligneDisponibleConsigner = $demande->ligne_disponible_consigner ?? null;
                        @endphp
                        
                        @if($ligneDisponibleConsigner)
                            <div class="mb-2">
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    Ligne: {{ $ligneDisponibleConsigner }}
                                </span>
                            </div>
                        @endif
                        
                        @if(!empty($lignesOracle))
                            <div class="space-y-1">
                                @foreach($lignesOracle as $ligne)
                                    @php
                                        $description = is_array($ligne) ? ($ligne['description'] ?? $ligne['code'] ?? '-') : $ligne;
                                        $code = is_array($ligne) ? ($ligne['code'] ?? '') : '';
                                    @endphp
                                    <div class="flex items-center text-sm">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                        <span class="text-gray-700">{{ $description }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(!empty($equipementsOracle))
                            <div class="mt-2 space-y-1">
                                @foreach($equipementsOracle as $levelName => $levelData)
                                    @php
                                        $equipements = is_array($levelData) && isset($levelData['values']) ? $levelData['values'] : (is_array($levelData) ? $levelData : []);
                                    @endphp
                                    @foreach($equipements as $eq)
                                        @php
                                            $description = is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '-') : $eq;
                                            $code = is_array($eq) ? ($eq['code'] ?? '') : '';
                                        @endphp
                                        <div class="flex items-center text-sm">
                                            <span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span>
                                            <span class="text-gray-700">{{ $description }}</span>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                        
                        @if(empty($lignesOracle) && empty($equipementsOracle) && !$ligneDisponibleConsigner)
                            <span class="text-gray-500 text-sm">Non spécifié</span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Ouvrages sur lesquels réaliser les travaux -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    À réaliser
                </h2>
                
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-3">
                    @if($demande->mode_saisie === 'manuel')
                        <div class="text-sm text-gray-700">
                            {{ $demande->ouvrages_installer_manuel ?: 'Non spécifié' }}
                        </div>
                    @else
                        @php
                            $lignesInstallerOracle = $demande->lignes_installer_oracle ? json_decode($demande->lignes_installer_oracle, true) : [];
                            $equipementsInstaller = $demande->equipements_installer_oracle ? json_decode($demande->equipements_installer_oracle, true) : [];
                            
                            // Ligne disponible installer
                            $ligneDisponibleInstaller = $demande->ligne_disponible_installer ?? null;
                        @endphp
                        
                        @if($ligneDisponibleInstaller)
                            <div class="mb-2">
                                <span class="inline-flex items-center px-2 py-1 bg-teal-100 text-teal-800 rounded text-xs font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    Ligne: {{ $ligneDisponibleInstaller }}
                                </span>
                            </div>
                        @endif
                        
                        @if(!empty($lignesInstallerOracle))
                            <div class="space-y-1">
                                @foreach($lignesInstallerOracle as $ligne)
                                    @php
                                        $description = is_array($ligne) ? ($ligne['description'] ?? $ligne['code'] ?? '-') : $ligne;
                                        $code = is_array($ligne) ? ($ligne['code'] ?? '') : '';
                                    @endphp
                                    <div class="flex items-center text-sm">
                                        <span class="w-2 h-2 bg-teal-500 rounded-full mr-2"></span>
                                        <span class="text-gray-700">{{ $description }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(!empty($equipementsInstaller))
                            <div class="mt-2 space-y-1">
                                @foreach($equipementsInstaller as $levelName => $levelData)
                                    @php
                                        $equipements = is_array($levelData) && isset($levelData['values']) ? $levelData['values'] : (is_array($levelData) ? $levelData : []);
                                    @endphp
                                    @foreach($equipements as $eq)
                                        @php
                                            $description = is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '-') : $eq;
                                            $code = is_array($eq) ? ($eq['code'] ?? '') : '';
                                        @endphp
                                        <div class="flex items-center text-sm">
                                            <span class="w-2 h-2 bg-teal-400 rounded-full mr-2"></span>
                                            <span class="text-gray-700">{{ $description }}</span>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                        
                        @if(empty($lignesInstallerOracle) && empty($equipementsInstaller) && !$ligneDisponibleInstaller)
                            <span class="text-gray-500 text-sm">Non spécifié</span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Options de consignation -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Options
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Consignation</span>
                        <span class="font-medium px-2 py-1 bg-gray-100 rounded">
                            {{ $demande->etape == 'de' ? '2 étapes' : '1 étape' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Mise à la terre (MTE)</span>
                        <span class="font-medium {{ $demande->mte === 'oui' ? 'text-green-600 bg-green-100' : 'text-gray-500 bg-gray-100' }} px-2 py-1 rounded">
                            {{ $demande->mte === 'oui' ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Mise en court-circuit (MCCE)</span>
                        <span class="font-medium {{ $demande->mcce === 'oui' ? 'text-green-600 bg-green-100' : 'text-gray-500 bg-gray-100' }} px-2 py-1 rounded">
                            {{ $demande->mcce === 'oui' ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informations du demandeur -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Demandeur
                </h2>
                <div class="space-y-4 text-sm">
                    @if($demande->demandeur)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Nom</dt>
                        <dd class="text-gray-900 font-medium">{{ $demande->demandeur->name ?? '-' }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Matricule</dt>
                        <dd class="text-gray-900">{{ $demande->demandeur->matricule ?? '-' }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Service</dt>
                        <dd class="text-gray-900">{{ $demande->demandeur->service ?? '-' }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Téléphone</dt>
                        <dd class="text-gray-900">{{ $demande->telephone_demandeur ?? $demande->demandeur->telephone ?? '-' }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Chargé de travaux -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Chargé de travaux
                    @if($demande->charge_travaux_info && $demande->charge_travaux_info->type === 'externe')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 ml-2">Externe</span>
                    @endif
                </h2>
                <div class="space-y-4 text-sm">
                    @if($demande->charge_travaux_info)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Nom</dt>
                        <dd class="text-gray-900 font-medium">{{ $demande->charge_travaux_info->nom ?? '-' }}</dd>
                    </div>
                    @if($demande->charge_travaux_info->matricule)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Matricule</dt>
                        <dd class="text-gray-900">{{ $demande->charge_travaux_info->matricule }}</dd>
                    </div>
                    @endif
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Entreprise/Service</dt>
                        <dd class="text-gray-900">{{ $demande->charge_travaux_info->entreprise ?? '-' }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 mb-1">Téléphone</dt>
                        <dd class="text-gray-900">{{ $demande->charge_travaux_info->telephone ?? '-' }}</dd>
                    </div>
                    @else
                    <p class="text-gray-500 p-3 bg-gray-50 rounded-lg">Non spécifié</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions DESA - Pleine largeur -->
    <div class="card-senelec p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <svg class="w-5 h-5 inline mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Actions DESA
        </h2>
        
        @if($demande->statut === 'créée')
            <!-- Mettre en traitement -->
            <form action="{{ route('desa.demandes.update', $demande) }}" method="POST" class="mb-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="traiter">
                <button type="submit" class="btn-senelec-outline w-full py-3">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mettre en cours de traitement
                </button>
            </form>
        @endif
        
        @if($demande->statut !== 'acceptée' && $demande->statut !== 'retournée')
            <!-- Deux formulaires côte à côte -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                <!-- Faire la NAPT -->
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h3 class="font-semibold text-green-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Faire la NAPT
                    </h3>
                    <form action="{{ route('desa.demandes.faire-napt', $demande) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4 p-3 bg-green-100 rounded-lg">
                            <p class="text-sm text-green-800">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                La demande passera en cours de traitement et vous serez redirigé vers la création de la NAPT. La demande sera acceptée une fois la NAPT validée.
                            </p>
                        </div>
                        
                        <h4 class="text-sm font-medium text-green-700 mb-3">Période acceptée</h4>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="label text-green-700 text-xs">Date début *</label>
                                <input type="date" name="dda" value="{{ old('dda', $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('Y-m-d') : '') }}" class="input-senelec w-full text-sm" required>
                            </div>
                            <div>
                                <label class="label text-green-700 text-xs">Heure début *</label>
                                <input type="time" name="hda" value="{{ old('hda', $demande->hdp) }}" class="input-senelec w-full text-sm" required>
                            </div>
                            <div>
                                <label class="label text-green-700 text-xs">Date fin *</label>
                                <input type="date" name="dfa" value="{{ old('dfa', $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('Y-m-d') : '') }}" class="input-senelec w-full text-sm" required>
                            </div>
                            <div>
                                <label class="label text-green-700 text-xs">Heure fin *</label>
                                <input type="time" name="hfa" value="{{ old('hfa', $demande->hfp) }}" class="input-senelec w-full text-sm" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Faire la NAPT
                        </button>
                    </form>
                </div>
                
                <!-- Retourner la demande -->
                <div class="border border-red-200 rounded-lg p-4 bg-red-50 flex flex-col">
                    <h3 class="font-semibold text-red-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Retourner la demande
                    </h3>
                    <form action="{{ route('desa.demandes.retourner-napt', $demande) }}" method="POST" class="flex flex-col flex-1">
                        @csrf
                        
                        @if($errors->any())
                            <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded-lg">
                                <ul class="text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <label class="label text-red-700">Motif du retour * <span class="text-xs font-normal">(min. 2 caractères)</span></label>
                            <textarea name="comment" rows="5" class="input-senelec w-full @if($errors->has('comment')) border-red-500 @endif" placeholder="Expliquez pourquoi la demande est retournée..." required minlength="2">{{ old('comment') }}</textarea>
                        </div>
                        
                        <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center mt-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Retourner la demande
                        </button>
                    </form>
                </div>
            </div>
        @elseif($demande->statut === 'acceptée')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-green-800 font-semibold">Demande acceptée</h4>
                        <p class="text-green-700 text-sm">Cette demande a été acceptée. Une NAPT peut être créée.</p>
                    </div>
                </div>
                @if($demande->note)
                    <a href="{{ route('desa.notes.edit', $demande->note) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Voir/Modifier la NAPT
                    </a>
                @endif
            </div>
        @elseif($demande->statut === 'retournée')
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="text-orange-800 font-semibold">Demande retournée</h4>
                        <p class="text-orange-700 text-sm">Cette demande a été retournée au demandeur pour correction.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
