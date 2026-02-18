@extends('layouts.app')

@section('title', isset($demande) ? 'Modifier la Demande DAPT' : 'Nouvelle Demande DAPT')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 avec thème SENELEC */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        min-height: 42px;
        padding: 2px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 6px 12px;
        color: #374151;
        line-height: 1.5;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #2B1444;
        box-shadow: 0 0 0 3px rgba(43, 20, 68, 0.1);
        outline: none;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2B1444;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(43, 20, 68, 0.1);
        color: #2B1444;
        border: 1px solid rgba(43, 20, 68, 0.3);
        border-radius: 0.375rem;
        padding: 4px 10px;
        margin: 3px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #2B1444;
        margin-right: 8px;
        padding-right: 8px;
        border: none;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        margin-left: 5px;
    }
    .select2-dropdown {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .select2-results__option {
        padding: 10px 14px;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 0.375rem;
        border-color: #d1d5db;
        padding: 8px 12px;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #2B1444;
        outline: none;
        box-shadow: 0 0 0 2px rgba(43, 20, 68, 0.1);
    }
    
    /* Hiérarchie équipements */
    .niveau-equipement {
        margin-bottom: 1rem;
        padding: 1.25rem;
        border: 2px solid;
        border-radius: 0.75rem;
        position: relative;
        transition: all 0.2s ease;
        background: white;
    }
    .niveau-equipement:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .niveau-equipement.niveau-1 { 
        border-color: #2B1444; 
        background: linear-gradient(135deg, rgba(43, 20, 68, 0.03) 0%, white 100%);
    }
    .niveau-equipement.niveau-2 { 
        border-color: #0A91A3; 
        background: linear-gradient(135deg, rgba(10, 145, 163, 0.03) 0%, white 100%);
        margin-left: 1.5rem; 
    }
    .niveau-equipement.niveau-3 { 
        border-color: #E87400; 
        background: linear-gradient(135deg, rgba(232, 116, 0, 0.03) 0%, white 100%);
        margin-left: 3rem; 
    }
    .niveau-equipement.niveau-4 { 
        border-color: #B3006C; 
        background: linear-gradient(135deg, rgba(179, 0, 108, 0.03) 0%, white 100%);
        margin-left: 4.5rem; 
    }
    .niveau-equipement.niveau-5 { 
        border-color: #059669; 
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.03) 0%, white 100%);
        margin-left: 6rem; 
    }
    .niveau-equipement.niveau-6 { 
        border-color: #7C3AED; 
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.03) 0%, white 100%);
        margin-left: 7.5rem; 
    }
    .niveau-badge {
        position: absolute;
        top: -0.75rem;
        left: 1rem;
        padding: 0.25rem 0.875rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
        border-radius: 9999px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }
    .niveau-1 .niveau-badge { background: linear-gradient(135deg, #2B1444 0%, #4a2970 100%); }
    .niveau-2 .niveau-badge { background: linear-gradient(135deg, #0A91A3 0%, #0cc4d9 100%); }
    .niveau-3 .niveau-badge { background: linear-gradient(135deg, #E87400 0%, #ff9b33 100%); }
    .niveau-4 .niveau-badge { background: linear-gradient(135deg, #B3006C 0%, #e6008a 100%); }
    .niveau-5 .niveau-badge { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
    .niveau-6 .niveau-badge { background: linear-gradient(135deg, #7C3AED 0%, #a78bfa 100%); }
    
    .selection-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 9999px;
        background-color: #2B1444;
        color: white;
        margin: 0.125rem;
    }
    
    /* Fieldset moderne */
    .fieldset-modern {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        position: relative;
        margin-bottom: 1.5rem;
        background: white;
    }
    .fieldset-modern legend {
        position: absolute;
        top: -0.75rem;
        left: 1rem;
        padding: 0.25rem 0.875rem;
        font-size: 0.875rem;
        font-weight: 600;
        background-color: white;
        color: #2B1444;
        border-radius: 9999px;
        white-space: nowrap;
    }
    
    /* Animation pour les sections */
    .section-animate {
        transition: all 0.3s ease-in-out;
    }
    
    /* Loading spinner */
    .loading-spinner {
        display: inline-block;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #2B1444;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Card hover effect */
    .card-senelec {
        transition: box-shadow 0.2s ease;
    }
    .card-senelec:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    /* Drag and drop zone */
    .dropzone-active {
        border-color: #2B1444 !important;
        background-color: rgba(43, 20, 68, 0.05) !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- En-tête avec breadcrumb -->
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-senelec-purple">Tableau de bord</a>
                </li>
                <li>
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li>
                    <a href="{{ route('demandeur.demandes.index') }}" class="text-gray-500 hover:text-senelec-purple">Mes demandes</a>
                </li>
                <li>
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="text-gray-700 font-medium">{{ isset($demande) ? 'Modifier' : 'Nouvelle demande' }}</li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ isset($demande) ? 'Modifier la Demande DAPT #'.$demande->numero_demande : 'Nouvelle Demande DAPT' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ isset($demande) ? 'Modifiez les informations de votre demande d\'arrêt pour travaux' : 'Créez une nouvelle Demande d\'Arrêt Pour Travaux (DAPT)' }}
        </p>
        @if(isset($demande))
            <span class="mt-2 inline-flex badge {{ $demande->statut === 'retournée' ? 'badge-warning' : ($demande->statut === 'brouillon' ? 'badge-secondary' : 'badge-info') }}">
                {{ ucfirst($demande->statut) }}
            </span>
        @endif
    </div>

    <!-- Alerte si demande retournée -->
    @if(isset($demande) && $demande->statut === 'retournée' && !empty($demande->comment))
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">Demande retournée pour correction</h3>
                    <p class="mt-1 text-sm text-amber-700">{{ $demande->comment }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Erreurs -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Veuillez corriger les erreurs suivantes :</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulaire -->
    <form id="demande-form" 
          action="{{ isset($demande) ? route('demandeur.demandes.update', $demande) : route('demandeur.demandes.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if(isset($demande))
            @method('PUT')
        @endif

        <!-- Section 1: Mode de saisie -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">1</span>
                Mode de saisie
            </h2>
            <p class="text-sm text-gray-500 mb-4">Choisissez comment vous souhaitez renseigner les informations des ouvrages</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="mode_saisie" value="gmao" id="radio_mode_gmao" 
                           {{ old('mode_saisie', isset($demande) && $demande->mode_saisie == 'manuel' ? '' : 'checked') }}
                           class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-senelec-purple peer-checked:bg-senelec-purple/5 transition-all hover:border-senelec-purple/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-senelec-purple/10">
                                <svg class="w-6 h-6 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900">Mode GMAO</span>
                                <p class="text-xs text-gray-500">Sélection depuis la base de données</p>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="mode_saisie" value="manuel" id="radio_mode_manuelle" 
                           {{ old('mode_saisie', isset($demande) && $demande->mode_saisie == 'manuel' ? 'manuel' : '') == 'manuel' ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-senelec-teal peer-checked:bg-senelec-teal/5 transition-all hover:border-senelec-teal/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-senelec-teal/10">
                                <svg class="w-6 h-6 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900">Saisie manuelle</span>
                                <p class="text-xs text-gray-500">Saisie libre des informations</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Section 2: Informations générales -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">2</span>
                Informations générales
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="date" name="date" readonly
                           value="{{ isset($demande) && $demande->date ? old('date', $demande->date->format('Y-m-d')) : old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}" 
                           class="input-senelec bg-gray-50" required>
                </div>
                <div>
                    <label for="destinataire" class="block text-sm font-medium text-gray-700 mb-1">
                        Destinataire <span class="text-red-500">*</span>
                    </label>
                    <select id="destinataire" name="destinataire" class="input-senelec" required>
                        <option value="" disabled {{ old('destinataire', isset($demande) ? $demande->destinataire : '') == '' ? 'selected' : '' }}>Sélectionner</option>
                        <option value="DESA" {{ old('destinataire', isset($demande) ? $demande->destinataire : '') == 'DESA' ? 'selected' : '' }}>DESA</option>
                        <option value="DD" {{ old('destinataire', isset($demande) ? $demande->destinataire : '') == 'DD' ? 'selected' : '' }}>DD</option>
                    </select>
                </div>
                <div>
                    <label for="lieu_execution" class="block text-sm font-medium text-gray-700 mb-1">
                        Lieu d'exécution <span class="text-red-500">*</span>
                    </label>
                    <div id="lieu_execution_gmao_section">
                        <select class="input-senelec select2" name="lieu_execution" id="lieu_execution" 
                                data-placeholder="Rechercher un lieu...">
                            <option value="">Rechercher un lieu d'exécution...</option>
                            @if(isset($demande) && $demande->lieu_execution)
                                <option value="{{ $demande->lieu_execution }}" selected>{{ $demande->lieu_execution }}</option>
                            @endif
                        </select>
                        <input type="hidden" id="hidden_lieu_execution" name="lieu_execution" 
                               value="{{ isset($demande) ? $demande->lieu_execution : old('lieu_execution') }}">
                        <input type="hidden" id="hidden_lieu_code" name="lieu_code" 
                               value="{{ isset($demande) ? $demande->lieu_code : old('lieu_code') }}">
                    </div>
                    <div id="lieu_execution_manuel_section" style="display: none;">
                        <textarea class="input-senelec w-full" name="lieu_execution_manuel" id="lieu_execution_manuel" 
                                  rows="2" placeholder="Saisissez le lieu d'exécution">{{ isset($demande) ? old('lieu_execution_manuel', $demande->lieu_execution_manuel ?? $demande->lieu_execution) : old('lieu_execution_manuel') }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">
                    Désignation des travaux - Consistance <span class="text-red-500">*</span>
                </label>
                <textarea id="designation" name="designation" rows="3" 
                          class="input-senelec" placeholder="Description détaillée des travaux à effectuer..." required>{{ isset($demande) ? old('designation', $demande->designation) : old('designation') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Décrivez précisément la nature des travaux à réaliser</p>
            </div>
        </div>

        <!-- Section 3: Ouvrages ou Installations -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">3</span>
                Ouvrages ou Installations
            </h2>

            <!-- Section GMAO -->
            <div id="ouvrages_gmao_section">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- À consigner -->
                    <div class="border border-senelec-teal/30 rounded-xl p-4 bg-white">
                        <h3 class="text-base font-semibold text-senelec-teal mb-4 text-center border-b border-senelec-teal/20 pb-3">
                            A consigner
                        </h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ouvrage à consigner <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="ouvrage_type" value="ligne" id="radio_ligne" 
                                           {{ old('ouvrage_type', isset($demande) && $demande->ouvrage_type == 'ligne' ? 'ligne' : 'ligne') == 'ligne' ? 'checked' : '' }}
                                           class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                                    <span class="ml-2 text-gray-700">Ligne</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="ouvrage_type" value="poste" id="radio_poste" 
                                           {{ old('ouvrage_type', isset($demande) ? $demande->ouvrage_type : '') == 'poste' ? 'checked' : '' }}
                                           class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                                    <span class="ml-2 text-gray-700">Poste</span>
                                </label>
                            </div>
                        </div>

                        <!-- Section Ligne -->
                        <div id="ligne_section" style="display: {{ old('ouvrage_type', $demande->ouvrage_type ?? 'ligne') == 'ligne' ? 'block' : 'none' }};">
                            <div class="bg-senelec-teal/5 border border-senelec-teal/20 rounded-lg p-3 mb-3">
                                <div class="flex items-center gap-2 text-sm text-senelec-teal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <strong>Ligne parent :</strong>
                                    <span id="lieu-execution-display-consigner">
                                        @if(isset($demande) && $demande->lieu_execution)
                                            <span class="badge badge-success">{{ $demande->lieu_execution }}</span>
                                        @else
                                            <span class="text-gray-400">Aucune ligne sélectionnée</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label for="ligne_disponible_consigner" class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-1">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        Lignes à consigner
                                    </label>
                                    <select class="input-senelec select2" name="ligne_disponible_consigner" id="ligne_disponible_consigner">
                                        <option value="">Choisir une ligne</option>
                                    </select>
                                    <input type="hidden" id="ligne_disponible_consigner_code" name="ligne_disponible_consigner_code" 
                                           value="{{ isset($demande) ? $demande->ligne_disponible_consigner_code : old('ligne_disponible_consigner_code') }}">
                                </div>
                                <div>
                                    <label for="ligne_select" class="block text-sm font-medium text-gray-700 mb-1">Choisissez les sous équipements à consigner</label>
                                    <select class="input-senelec select2" name="ligne_ids[]" id="ligne_select" multiple>
                                        <option value="">Sélectionner d'abord un lieu d'exécution...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section Poste -->
                        <div id="poste_section" style="display: {{ old('ouvrage_type', $demande->ouvrage_type ?? 'ligne') == 'poste' ? 'block' : 'none' }};">
                            <div id="oracle-equipements-consigner">
                                <div id="equipement-levels-consigner"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sur lesquels les travaux sont à réaliser -->
                    <div class="border border-senelec-teal/30 rounded-xl p-4 bg-white">
                        <h3 class="text-base font-semibold text-senelec-teal mb-4 text-center border-b border-senelec-teal/20 pb-3">
                            Sur lesquels les travaux sont à réaliser
                        </h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ouvrage <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="ouvrage_type_installer" value="ligne_installer" id="radio_ligne_installer" 
                                           {{ old('ouvrage_type_installer', isset($demande) && $demande->ouvrage_type_installer == 'ligne_installer' ? 'ligne_installer' : 'ligne_installer') == 'ligne_installer' ? 'checked' : '' }}
                                           class="w-4 h-4 text-senelec-teal focus:ring-senelec-teal border-gray-300">
                                    <span class="ml-2 text-gray-700">Ligne</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="ouvrage_type_installer" value="poste_installer" id="radio_poste_installer" 
                                           {{ old('ouvrage_type_installer', isset($demande) ? $demande->ouvrage_type_installer : '') == 'poste_installer' ? 'checked' : '' }}
                                           class="w-4 h-4 text-senelec-teal focus:ring-senelec-teal border-gray-300">
                                    <span class="ml-2 text-gray-700">Poste</span>
                                </label>
                            </div>
                        </div>

                        <!-- Section Ligne Installer -->
                        <div id="ligne_section_installer" style="display: {{ old('ouvrage_type_installer', $demande->ouvrage_type_installer ?? 'ligne_installer') == 'ligne_installer' ? 'block' : 'none' }};">
                            <div class="space-y-3">
                                <div>
                                    <label for="ligne_disponible_installer" class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-1">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        Lignes
                                    </label>
                                    <select class="input-senelec select2" name="ligne_disponible_installer" id="ligne_disponible_installer">
                                        <option value="">Choisir une ligne</option>
                                    </select>
                                    <input type="hidden" id="ligne_disponible_installer_code" name="ligne_disponible_installer_code" 
                                           value="{{ isset($demande) ? $demande->ligne_disponible_installer_code : old('ligne_disponible_installer_code') }}">
                                </div>
                                <div>
                                    <label for="ligne_installer_select" class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-1">
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        Choisissez les sous équipements
                                    </label>
                                    <select class="input-senelec select2" name="ligne_installer_ids[]" id="ligne_installer_select" multiple>
                                        <option value="">Sélectionner d'abord un lieu d'exécution...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section Poste Installer -->
                        <div id="poste_section_installer" style="display: {{ old('ouvrage_type_installer', $demande->ouvrage_type_installer ?? 'ligne_installer') == 'poste_installer' ? 'block' : 'none' }};">
                            <div id="oracle-equipements-installer">
                                <div id="equipement-levels-installer"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif des équipements sélectionnés -->
                <div id="recap-equipements" class="mt-6 border-t border-gray-200 pt-6" style="display: none;">
                    <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-senelec-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Récapitulatif des équipements sélectionnés
                    </h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Récap À consigner -->
                        <div class="bg-senelec-orange/5 border border-senelec-orange/30 rounded-lg p-4">
                            <h5 class="text-sm font-semibold text-senelec-orange mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                À consigner
                            </h5>
                            <div id="recap-consigner-content" class="space-y-2 text-sm">
                                <p class="text-gray-400 italic">Aucun équipement sélectionné</p>
                            </div>
                        </div>
                        
                        <!-- Récap Sur lesquels les travaux -->
                        <div class="bg-senelec-teal/5 border border-senelec-teal/30 rounded-lg p-4">
                            <h5 class="text-sm font-semibold text-senelec-teal mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Sur lesquels les travaux sont à réaliser
                            </h5>
                            <div id="recap-installer-content" class="space-y-2 text-sm">
                                <p class="text-gray-400 italic">Aucun équipement sélectionné</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Saisie manuelle -->
            <div id="ouvrages_manuel_section" style="display: none;">
                <div class="bg-senelec-teal/5 border border-senelec-teal/20 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 text-sm text-senelec-teal mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <strong>Mode saisie manuelle</strong>
                    </div>
                    <p class="text-xs text-gray-600">Veuillez saisir librement les informations concernant les ouvrages.</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- À consigner (manuel) -->
                    <div class="border border-senelec-teal/30 rounded-xl p-4 bg-white">
                        <h3 class="text-base font-semibold text-senelec-teal mb-4 text-center border-b border-senelec-teal/20 pb-3">
                            A consigner
                        </h3>
                        <label for="ouvrages_consigner_manuel" class="block text-sm font-medium text-gray-700 mb-1">
                            Ouvrages à consigner <span class="text-red-500">*</span>
                        </label>
                        <textarea class="input-senelec" name="ouvrages_consigner_manuel" id="ouvrages_consigner_manuel" 
                                  rows="5" placeholder="Décrivez les ouvrages ou installations qui doivent être consignés...">{{ isset($demande) ? old('ouvrages_consigner_manuel', $demande->ouvrages_consigner_manuel) : old('ouvrages_consigner_manuel') }}</textarea>
                    </div>
                    
                    <!-- Sur lesquels les travaux (manuel) -->
                    <div class="border border-senelec-teal/30 rounded-xl p-4 bg-white">
                        <h3 class="text-base font-semibold text-senelec-teal mb-4 text-center border-b border-senelec-teal/20 pb-3">
                            Sur lesquels les travaux sont à réaliser
                        </h3>
                        <label for="ouvrages_installer_manuel" class="block text-sm font-medium text-gray-700 mb-1">
                            Ouvrages concernés <span class="text-red-500">*</span>
                        </label>
                        <textarea class="input-senelec" name="ouvrages_installer_manuel" id="ouvrages_installer_manuel" 
                                  rows="5" placeholder="Décrivez les ouvrages ou installations sur lesquels les travaux seront réalisés...">{{ isset($demande) ? old('ouvrages_installer_manuel', $demande->ouvrages_installer_manuel) : old('ouvrages_installer_manuel') }}</textarea>
                    </div>
                </div>

                <!-- Récapitulatif mode manuel -->
                <div id="recap-equipements-manuel" class="mt-6 border-t border-gray-200 pt-6" style="display: none;">
                    <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-senelec-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Récapitulatif des ouvrages saisis
                    </h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Récap À consigner (manuel) -->
                        <div class="bg-senelec-orange/5 border border-senelec-orange/30 rounded-lg p-4">
                            <h5 class="text-sm font-semibold text-senelec-orange mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                À consigner
                            </h5>
                            <div id="recap-consigner-manuel-content" class="text-sm">
                                <p class="text-gray-400 italic">Aucun ouvrage saisi</p>
                            </div>
                        </div>
                        
                        <!-- Récap Sur lesquels les travaux (manuel) -->
                        <div class="bg-senelec-teal/5 border border-senelec-teal/30 rounded-lg p-4">
                            <h5 class="text-sm font-semibold text-senelec-teal mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Sur lesquels les travaux sont à réaliser
                            </h5>
                            <div id="recap-installer-manuel-content" class="text-sm">
                                <p class="text-gray-400 italic">Aucun ouvrage saisi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Intervenants -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">4</span>
                Intervenants
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Demandeur -->
                <div class="border border-gray-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Demandeur
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label for="demandeur" class="block text-sm font-medium text-gray-700 mb-1">Demandé par <span class="text-red-500">*</span></label>
                            <select class="input-senelec select2" name="demandeur_id" id="demandeur" required>
                                <option value="{{ Auth::user()->id }}" 
                                        data-matricule="{{ Auth::user()->matricule }}"
                                        data-fonction="{{ Auth::user()->poste }}"
                                        data-service="{{ Auth::user()->service ?? Auth::user()->departement ?? '' }}"
                                        data-telephone="{{ Auth::user()->telephone }}"
                                        {{ old('demandeur_id', isset($demande) ? $demande->demandeur_id : Auth::user()->id) == Auth::user()->id ? 'selected' : '' }}>
                                    {{ Auth::user()->name }} @if(Auth::user()->matricule)({{ Auth::user()->matricule }})@endif @if(Auth::user()->service ?? Auth::user()->departement)- {{ Auth::user()->service ?? Auth::user()->departement }}@endif
                                </option>
                                @if(isset($demandeurs))
                                    @foreach ($demandeurs as $dem)
                                        @if($dem->id != Auth::user()->id)
                                        <option value="{{ $dem->id }}"
                                                data-matricule="{{ $dem->matricule }}"
                                                data-fonction="{{ $dem->poste }}"
                                                data-service="{{ $dem->service ?? $dem->departement ?? '' }}"
                                                data-telephone="{{ $dem->telephone }}"
                                                {{ old('demandeur_id', isset($demande) ? $demande->demandeur_id : '') == $dem->id ? 'selected' : '' }}>
                                            {{ $dem->name }} @if($dem->matricule)({{ $dem->matricule }})@endif @if($dem->service ?? $dem->departement)- {{ $dem->service ?? $dem->departement }}@endif
                                        </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                                <input type="text" class="input-senelec bg-gray-50" name="matricule" id="matricule" 
                                       value="{{ old('matricule', isset($demande) ? $demande->demandeur->matricule ?? '' : Auth::user()->matricule ?? '') }}" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                                <input type="text" class="input-senelec bg-gray-50" name="fonction" id="fonction" 
                                       value="{{ old('fonction', isset($demande) ? $demande->demandeur->poste ?? '' : Auth::user()->poste ?? '') }}" readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                <input type="text" class="input-senelec" name="telephone_demandeur" id="telephoned" 
                                       value="{{ old('telephone_demandeur', isset($demande) ? $demande->telephone_demandeur : Auth::user()->telephone ?? '') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                <input type="text" class="input-senelec bg-gray-50" name="appartenance" id="appartenance" 
                                       value="{{ old('appartenance', isset($demande) ? ($demande->demandeur->appartenance ?? '') : (Auth::user()->appartenance ?? '')) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chargé des travaux -->
                <div class="border border-gray-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Chargé des travaux
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label for="charge_travaux" class="block text-sm font-medium text-gray-700 mb-1">Chargé des travaux <span class="text-red-500">*</span></label>
                            <div class="flex gap-2 items-center">
                                <div class="flex-1">
                                    <select class="input-senelec select2" name="charge_travaux_id" id="charge_travaux">
                                        <option value="">Sélectionner le chargé des travaux</option>
                                        @if(isset($cts) && $cts->count() > 0)
                                            <optgroup label="👤 Internes (SENELEC)">
                                            @foreach ($cts as $c)
                                                <option value="{{ $c->id }}"
                                                        data-type="interne"
                                                        data-matricule="{{ $c->matricule }}"
                                                        data-telephone="{{ $c->telephone }}"
                                                        data-entreprise="{{ $c->entreprise ?? 'SENELEC' }}"
                                                        data-service="{{ $c->service ?? $c->departement ?? '' }}"
                                                        data-fonction="{{ $c->poste }}"
                                                        {{ old('charge_travaux_id', isset($demande) ? $demande->charge_travaux_id : '') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }} @if($c->matricule)({{ $c->matricule }})@endif @if($c->service ?? $c->departement)- {{ $c->service ?? $c->departement }}@endif
                                                </option>
                                            @endforeach
                                            </optgroup>
                                        @endif
                                        @if(isset($ctsExternes) && $ctsExternes->count() > 0)
                                            <optgroup label="🏢 Externes">
                                            @foreach ($ctsExternes as $cte)
                                                <option value="ext_{{ $cte->id }}"
                                                        data-type="externe"
                                                        data-externe-id="{{ $cte->id }}"
                                                        data-telephone="{{ $cte->telephone }}"
                                                        data-entreprise="{{ $cte->entreprise }}"
                                                        data-service="{{ $cte->service }}"
                                                        data-nom="{{ $cte->nom }}"
                                                        {{ old('charge_travaux_externe_id', isset($demande) ? $demande->charge_travaux_externe_id : '') == $cte->id ? 'selected' : '' }}>
                                                    {{ $cte->nom }} @if($cte->entreprise)({{ $cte->entreprise }})@endif
                                                </option>
                                            @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>
                                <button type="button" class="flex-shrink-0 bg-senelec-orange hover:bg-senelec-orange-dark text-white px-3 py-2 rounded-lg transition-all duration-200 hover:scale-105 hover:shadow-lg flex items-center gap-1" id="btnAjouterCT" onclick="openModalCT()" title="Ajouter un chargé des travaux externe">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span class="text-xs font-medium hidden sm:inline">Externe</span>
                                </button>
                            </div>
                            <!-- Champs cachés pour CT externe -->
                            <input type="hidden" name="charge_travaux_externe_id" id="charge_travaux_externe_id" value="">
                            <input type="hidden" name="ct_externe_nom" id="ct_externe_nom" value="">
                            <input type="hidden" name="ct_externe_telephone" id="ct_externe_telephone" value="">
                            <input type="hidden" name="ct_externe_entreprise" id="ct_externe_entreprise" value="">
                            <input type="hidden" name="ct_externe_service" id="ct_externe_service" value="">
                        </div>
                        <div class="grid grid-cols-2 gap-3" id="matricule_charge_group" style="display: none;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                                <input type="text" class="input-senelec bg-gray-50" name="matricule_charge" id="matricule_charge" 
                                       value="{{ old('matricule_charge', isset($demande) ? $demande->chargeTravaux->matricule ?? '' : '') }}" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                                <input type="text" class="input-senelec bg-gray-50" name="fonctionct" id="fonctionct" 
                                       value="{{ old('fonctionct', isset($demande) ? $demande->chargeTravaux->poste ?? '' : '') }}" readonly>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                <input type="text" class="input-senelec" name="telephone_charge" id="telephone" 
                                       value="{{ old('telephone_charge', isset($demande) ? $demande->telephone_charge : '') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Entreprise / Service</label>
                                <input type="text" class="input-senelec bg-gray-50" name="entreprise" id="entreprise" 
                                       value="{{ old('entreprise', isset($demande) && $demande->chargeTravaux ? $demande->chargeTravaux->entreprise . ' - ' . $demande->chargeTravaux->service : '') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Options de sécurité -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">5</span>
                Options de sécurité
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mise à la terre aux extrémités <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="mte" value="oui" 
                                   {{ old('mte', isset($demande) ? $demande->mte : 'oui') == 'oui' ? 'checked' : '' }}
                                   class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                            <span class="ml-2 text-gray-700">Oui</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="mte" value="non" 
                                   {{ old('mte', isset($demande) ? $demande->mte : '') == 'non' ? 'checked' : '' }}
                                   class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                            <span class="ml-2 text-gray-700">Non</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mise en court-circuit aux extrémités <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="mcce" value="oui" 
                                   {{ old('mcce', isset($demande) ? $demande->mcce : 'oui') == 'oui' ? 'checked' : '' }}
                                   class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                            <span class="ml-2 text-gray-700">Oui</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="mcce" value="non" 
                                   {{ old('mcce', isset($demande) ? $demande->mcce : '') == 'non' ? 'checked' : '' }}
                                   class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                            <span class="ml-2 text-gray-700">Non</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Consignation <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="etape" value="ue" id="radio_caue"
                                   {{ old('etape', isset($demande) ? $demande->etape : 'ue') == 'ue' ? 'checked' : '' }}
                                   class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                            <span class="ml-2 text-gray-700">Une étape</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="etape" value="de" id="radio_cade"
                                   {{ old('etape', isset($demande) ? $demande->etape : '') == 'de' ? 'checked' : '' }}
                                   class="w-4 h-4 text-senelec-purple focus:ring-senelec-purple border-gray-300">
                            <span class="ml-2 text-gray-700">Deux étapes</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Période proposée -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">6</span>
                Période proposée
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="ddp" class="block text-sm font-medium text-gray-700 mb-1">Date début <span class="text-red-500">*</span></label>
                    <input type="date" id="ddp" name="ddp" 
                           value="{{ old('ddp', isset($demande) && $demande->ddp ? $demande->ddp->format('Y-m-d') : '') }}" 
                           class="input-senelec" required>
                </div>
                <div>
                    <label for="hdp" class="block text-sm font-medium text-gray-700 mb-1">Heure début <span class="text-red-500">*</span></label>
                    <input type="time" id="hdp" name="hdp" 
                           value="{{ old('hdp', isset($demande) ? $demande->hdp : '08:00') }}" 
                           class="input-senelec" required>
                </div>
                <div>
                    <label for="dfp" class="block text-sm font-medium text-gray-700 mb-1">Date fin <span class="text-red-500">*</span></label>
                    <input type="date" id="dfp" name="dfp" 
                           value="{{ old('dfp', isset($demande) && $demande->dfp ? $demande->dfp->format('Y-m-d') : '') }}" 
                           class="input-senelec" required>
                </div>
                <div>
                    <label for="hfp" class="block text-sm font-medium text-gray-700 mb-1">Heure fin <span class="text-red-500">*</span></label>
                    <input type="time" id="hfp" name="hfp" 
                           value="{{ old('hfp', isset($demande) ? $demande->hfp : '16:00') }}" 
                           class="input-senelec" required>
                </div>
            </div>
            
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="dmrp_select" class="block text-sm font-medium text-gray-700 mb-1">Délai max de restitution</label>
                    <select class="input-senelec" id="dmrp_select" name="dmrp_type" onchange="toggleDmrpInput()">
                        @php
                            $dmrpValue = old('dmrp', isset($demande) ? $demande->dmrp : '');
                            $isNonApplicable = $dmrpValue === 'non_applicable' || $dmrpValue === null || $dmrpValue === '';
                        @endphp
                        <option value="non_applicable" {{ $isNonApplicable ? 'selected' : '' }}>Non Applicable</option>
                        <option value="time" {{ !$isNonApplicable ? 'selected' : '' }}>Saisir une heure</option>
                    </select>
                    <div id="dmrp_time_container" class="mt-2" style="display: {{ !$isNonApplicable ? 'block' : 'none' }};">
                        <input type="time" class="input-senelec" id="dmrp_time" name="dmrp" 
                               value="{{ old('dmrp', isset($demande) && $demande->dmrp !== 'non_applicable' ? $demande->dmrp : '') }}">
                    </div>
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dmrp_restitution" value="1" id="dmrp_soir"
                               {{ old('dmrp_restitution', isset($demande) ? $demande->dmrp_restitution : '') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-senelec-purple focus:ring-senelec-purple border-gray-300">
                        <span class="ml-2 text-gray-700">Restituer le soir</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 7: Informations complémentaires -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-senelec-purple text-white flex items-center justify-center text-sm">7</span>
                Informations complémentaires
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="renseignement" class="block text-sm font-medium text-gray-700 mb-1">
                        Renseignements ou informations complémentaires
                    </label>
                    <textarea id="renseignement" name="renseignement" rows="3" 
                              class="input-senelec" placeholder="Informations supplémentaires...">{{ old('renseignement', isset($demande) ? $demande->renseignement : '') }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="schema" class="block text-sm font-medium text-gray-700 mb-1">Schéma (image uniquement : PNG, JPG)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-senelec-purple/50 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="schema" class="relative cursor-pointer bg-white rounded-md font-medium text-senelec-purple hover:text-senelec-magenta">
                                        <span>Télécharger une image</span>
                                        <input type="file" id="schema" name="schema" accept=".png,.jpg,.jpeg" class="sr-only">
                                    </label>
                                    <p class="pl-1">ou glisser-déposer</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG uniquement (max 10MB)</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aperçu</label>
                        <div id="imagePreviewContainer" class="mt-1 border-2 border-gray-200 rounded-lg p-4 min-h-[150px] flex items-center justify-center bg-gray-50">
                            @if(isset($demande) && $demande->schema)
                                <img src="{{ $demande->schema_url }}" alt="Schéma" class="max-w-full max-h-48 rounded">
                            @else
                                <img id="imagePreview" src="#" alt="Aperçu" class="max-w-full max-h-48 rounded hidden">
                                <span id="noPreviewText" class="text-gray-400 text-sm">Aucun fichier sélectionné</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Champs cachés -->
        <input type="hidden" name="equipements" id="hidden_equipements" value="{{ json_encode(old('equipements', [])) }}">
        <input type="hidden" name="equipements_installer" id="hidden_equipements_installer" value="{{ json_encode(old('equipements_installer', [])) }}">

        <!-- Info Processus -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
            <div class="flex">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Processus de validation</h3>
                    <p class="mt-1 text-sm text-blue-700">
                        Votre demande sera transmise au DESA pour traitement. Vous serez notifié de l'avancement de votre demande.
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('demandeur.demandes.index') }}" class="btn-secondary">
                Annuler
            </a>
            <button type="submit" name="statut" value="brouillon" class="btn-secondary">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Enregistrer brouillon
            </button>
            @if(isset($demande) && $demande->statut == 'retournée')
                <button type="submit" name="statut" value="créée" class="btn-primary">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Resoumettre la demande
                </button>
            @elseif(isset($demande) && $demande->statut == 'brouillon')
                <button type="submit" name="statut" value="créée" class="btn-primary">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Soumettre la demande
                </button>
            @elseif(isset($demande))
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Modifier la demande
                </button>
            @else
                <button type="button" id="previewButton" class="btn-primary" onclick="showPreviewModal()">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Soumettre la demande
                </button>
            @endif
        </div>
    </form>
</div>

<!-- Modal Aperçu -->
<div id="recapModal" class="fixed inset-0 bg-black/10 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-senelec-purple to-senelec-magenta p-6">
            <h3 class="text-xl font-bold text-white">Récapitulatif de la Demande DAPT</h3>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]" id="recapContent">
            <!-- Contenu généré dynamiquement -->
        </div>
        <div class="border-t p-4 flex justify-end gap-3">
            <button type="button" onclick="closePreviewModal()" class="btn-secondary">Fermer</button>
            <button type="button" onclick="submitForm()" class="btn-primary">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Valider et soumettre
            </button>
        </div>
    </div>
</div>

<!-- Modal Ajouter CT -->
<div id="modalAjouterCT" class="fixed inset-0 bg-black/10 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="bg-gradient-to-r from-senelec-teal to-senelec-purple p-4">
            <h3 class="text-sm font-bold text-white flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Ajouter un Chargé de Travaux Externe
            </h3>
        </div>
        <form id="formAjouterCT" class="p-4 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1" for="nom_externe">Nom complet <span class="text-red-500">*</span></label>
                <input type="text" class="input-senelec text-sm py-1.5" id="nom_externe" name="nom_externe" required placeholder="Ex: Jean Dupont">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1" for="telephone_externe">Téléphone <span class="text-red-500">*</span></label>
                <input type="tel" class="input-senelec text-sm py-1.5" id="telephone_externe" name="telephone_externe" required placeholder="Ex: +221 77 123 45 67">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1" for="entreprise_externe">Entreprise <span class="text-red-500">*</span></label>
                    <input type="text" class="input-senelec text-sm py-1.5" id="entreprise_externe" name="entreprise_externe" required placeholder="Ex: SENELEC">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1" for="service_externe">Service</label>
                    <input type="text" class="input-senelec text-sm py-1.5" id="service_externe" name="service_externe" placeholder="Ex: Maintenance">
                </div>
            </div>
            <div class="bg-senelec-teal/10 rounded-lg p-2 text-xs text-senelec-teal">
                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Ce chargé des travaux externe sera ajouté et pourra être réutilisé.
            </div>
        </form>
        <div class="border-t p-3 flex justify-end gap-2">
            <button type="button" onclick="closeModalCT()" class="btn-secondary text-sm py-1.5 px-3">Annuler</button>
            <button type="button" onclick="saveCT()" class="btn-primary text-sm py-1.5 px-3" id="btnConfirmerAjoutCT">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Ajouter
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Variables globales pour stocker les sélections
var selectedEquipementsConsigner = [];
var selectedEquipementsInstaller = [];

// Configuration AJAX globale pour inclure le token CSRF
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // ===== HANDLER FORM SUBMIT (pour brouillon et autres soumissions normales) =====
    $('#demande-form').on('submit', function(e) {
        // Gérer le charge_travaux_id avant soumission
        var ctValue = $('#charge_travaux').val();
        if (ctValue && ctValue.toString().startsWith('ext_new_')) {
            // C'est un NOUVEAU CT externe (ajouté via modal)
            // Les champs ct_externe_nom, ct_externe_telephone, etc. sont déjà remplis par saveCT()
            // Ne pas toucher à charge_travaux_externe_id (doit rester vide pour la création)
            $('#charge_travaux_externe_id').val('');
            // Vider le charge_travaux_id pour éviter la validation exists:users
            $('#charge_travaux').val('');
        } else if (ctValue && ctValue.toString().startsWith('ext_')) {
            // C'est un CT externe EXISTANT, extraire l'ID numérique
            var externeId = ctValue.replace('ext_', '');
            // Remplir le champ caché avec l'ID existant
            $('#charge_travaux_externe_id').val(externeId);
            // Vider les champs de nouveau CT (on utilise l'ID existant)
            $('#ct_externe_nom').val('');
            $('#ct_externe_telephone').val('');
            $('#ct_externe_entreprise').val('');
            $('#ct_externe_service').val('');
            // Vider le charge_travaux_id pour éviter la validation exists:users
            $('#charge_travaux').val('');
        } else if (ctValue) {
            // C'est un CT interne, vider les champs externes
            $('#charge_travaux_externe_id').val('');
            $('#ct_externe_nom').val('');
            $('#ct_externe_telephone').val('');
            $('#ct_externe_entreprise').val('');
            $('#ct_externe_service').val('');
        }
    });

    // ===== INITIALISATION SELECT2 =====
    initSelect2();

    // ===== TOGGLE MODE SAISIE =====
    function toggleModeSaisie() {
        const modeGmao = $('#radio_mode_gmao').is(':checked');
        if (modeGmao) {
            $('#lieu_execution_gmao_section').show();
            $('#lieu_execution_manuel_section').hide();
            $('#ouvrages_gmao_section').show();
            $('#ouvrages_manuel_section').hide();
        } else {
            $('#lieu_execution_gmao_section').hide();
            $('#lieu_execution_manuel_section').show();
            $('#ouvrages_gmao_section').hide();
            $('#ouvrages_manuel_section').show();
        }
    }
    toggleModeSaisie();
    $('input[name="mode_saisie"]').change(toggleModeSaisie);

    // ===== TOGGLE OUVRAGE TYPE =====
    function toggleOuvrageSections() {
        if ($('#radio_ligne').is(':checked')) {
            $('#ligne_section').show();
            $('#poste_section, #oracle-equipements-consigner').hide();
        } else {
            $('#ligne_section').hide();
            $('#poste_section, #oracle-equipements-consigner').show();
        }
    }
    
    function toggleOuvrageSectionsInstaller() {
        if ($('#radio_ligne_installer').is(':checked')) {
            $('#ligne_section_installer').show();
            $('#poste_section_installer, #oracle-equipements-installer').hide();
        } else {
            $('#ligne_section_installer').hide();
            $('#poste_section_installer, #oracle-equipements-installer').show();
        }
    }
    
    toggleOuvrageSections();
    toggleOuvrageSectionsInstaller();
    $('input[name="ouvrage_type"]').change(toggleOuvrageSections);
    $('input[name="ouvrage_type_installer"]').change(toggleOuvrageSectionsInstaller);

    // ===== LIEU D'EXECUTION CHANGE =====
    $('#lieu_execution').on('select2:select', function(e) {
        var data = e.params.data;
        var lieuCode = data.id;
        var lieuDescription = data.text;
        
        $('#hidden_lieu_execution').val(lieuDescription);
        $('#hidden_lieu_code').val(lieuCode);
        $('#lieu-execution-display-consigner').html('<span class="badge badge-success">' + lieuDescription + '</span>');
        
        // Déterminer si c'est une ligne ou un poste en fonction du code ou de la description
        var isLigne = detectIfLigne(lieuCode, lieuDescription);
        
        // Mettre à jour les radio buttons et griser l'option non applicable
        updateOuvrageTypeBasedOnLieu(isLigne);
        
        if (isLigne) {
            // Pour une LIGNE : 
            // - Charger toutes les lignes dans "Lignes à consigner" et pré-sélectionner celle du lieu
            // - Charger les sous-équipements de cette ligne
            // - Synchroniser automatiquement vers "installer"
            loadAllLignes('ligne_disponible_consigner', lieuCode);
            loadAllLignes('ligne_disponible_installer', lieuCode);
            loadSousEquipementsLigneBoth(lieuCode); // Charge les deux en même temps
        } else {
            // Pour un POSTE :
            // Attendre que les sections soient visibles avant de charger
            setTimeout(function() {
                console.log('Chargement équipements poste après délai');
                createEquipementLevel('consigner', 1, lieuCode);
                createEquipementLevel('installer', 1, lieuCode);
            }, 100);
        }
        
        // Mettre à jour le récapitulatif
        setTimeout(function() {
            updateRecapEquipements();
        }, 200);
    });
    
    // ===== RESET QUAND LIEU D'EXECUTION EST VIDÉ =====
    $('#lieu_execution').on('select2:clear', function(e) {
        // Réactiver tous les radio buttons
        resetOuvrageTypeOptions();
        $('#lieu-execution-display-consigner').html('<span class="text-gray-400">Aucune ligne sélectionnée</span>');
        
        // Mettre à jour le récapitulatif
        updateRecapEquipements();
    });

    // ===== DEMANDEUR SELECT CHANGE =====
    $('#demandeur').on('select2:select', function(e) {
        var $selected = $(this).find(':selected');
        $('#matricule').val($selected.data('matricule') || '');
        $('#fonction').val($selected.data('fonction') || '');
        $('#appartenance').val($selected.data('service') || '');
        $('#telephoned').val($selected.data('telephone') || '');
    });

    // ===== CHARGE TRAVAUX SELECT CHANGE =====
    $('#charge_travaux').on('select2:select', function(e) {
        var $selected = $(this).find(':selected');
        var ctType = $selected.data('type') || 'interne';
        var value = $selected.val();
        
        // Réinitialiser les champs cachés
        $('#ct_externe_nom').val('');
        $('#ct_externe_telephone').val('');
        $('#ct_externe_entreprise').val('');
        $('#ct_externe_service').val('');
        
        if (ctType === 'externe' || (value && value.toString().startsWith('ext_'))) {
            // CT externe existant ou nouveau
            var telephone = $selected.data('telephone') || '';
            var entreprise = $selected.data('entreprise') || '';
            var service = $selected.data('service') || '';
            var nom = $selected.data('nom') || $selected.text().trim();
            
            $('#matricule_charge').val('');
            $('#fonctionct').val('Externe');
            $('#telephone').val(telephone);
            $('#entreprise').val((entreprise + (service ? ' - ' + service : '')).trim());
            $('#matricule_charge_group').hide();
            
            // Remplir les champs cachés pour l'externe
            if (value && value.toString().startsWith('ext_new_')) {
                // Nouveau CT externe (ajouté via modal)
                $('#ct_externe_nom').val(nom);
                $('#ct_externe_telephone').val(telephone);
                $('#ct_externe_entreprise').val(entreprise);
                $('#ct_externe_service').val(service);
            }
            // Si externe existant, on ne remplit pas car il sera récupéré par son ID
            
            // Vider le charge_travaux_id réel pour éviter la validation
            // On garde la valeur visible mais on n'envoie pas l'ID utilisateur
        } else {
            // CT interne
            var matricule = $selected.data('matricule');
            var telephone = $selected.data('telephone');
            var entreprise = $selected.data('entreprise') || '';
            var service = $selected.data('service') || '';
            var fonction = $selected.data('fonction') || '';
            
            $('#matricule_charge').val(matricule || '');
            $('#fonctionct').val(fonction || '');
            $('#telephone').val(telephone || '');
            $('#entreprise').val((entreprise + (service ? ' - ' + service : '')).trim());
            
            if (matricule) {
                $('#matricule_charge_group').show();
            } else {
                $('#matricule_charge_group').hide();
            }
        }
    });
    // Déclencher si une valeur est déjà sélectionnée
    if ($('#charge_travaux').val()) {
        $('#charge_travaux').trigger('select2:select');
    }

    // ===== LIGNES SELECT CHANGE (CONSIGNER) =====
    $('#ligne_disponible_consigner').on('select2:select', function(e) {
        var data = e.params.data;
        $('#ligne_disponible_consigner_code').val(data.id);
        loadSousEquipementsLigne(data.id, 'ligne_select');
        
        // Copier la sélection vers "installer"
        $('#ligne_disponible_installer').val(data.id).trigger('change');
        $('#ligne_disponible_installer_code').val(data.id);
        loadSousEquipementsLigne(data.id, 'ligne_installer_select');
        
        // Mettre à jour le récapitulatif
        setTimeout(function() { updateRecapEquipements(); }, 200);
    });
    
    // ===== LIGNES SELECT CHANGE (INSTALLER) - indépendant =====
    $('#ligne_disponible_installer').on('select2:select', function(e) {
        var data = e.params.data;
        $('#ligne_disponible_installer_code').val(data.id);
        loadSousEquipementsLigne(data.id, 'ligne_installer_select');
        
        // Mettre à jour le récapitulatif
        setTimeout(function() { updateRecapEquipements(); }, 200);
    });
    
    // ===== SOUS-ÉQUIPEMENTS SELECT CHANGE (CONSIGNER) =====
    $('#ligne_select').on('change', function() {
        // Copier les sélections vers "installer" après un court délai
        setTimeout(function() {
            var selectedValues = $('#ligne_select').val();
            if (selectedValues && selectedValues.length > 0) {
                $('#ligne_installer_select').val(selectedValues).trigger('change');
            }
            // Mettre à jour le récapitulatif
            updateRecapEquipements();
        }, 100);
    });
    
    // ===== SOUS-ÉQUIPEMENTS SELECT CHANGE (INSTALLER) =====
    $('#ligne_installer_select').on('change', function() {
        // Mettre à jour le récapitulatif
        setTimeout(function() { updateRecapEquipements(); }, 100);
    });

    // ===== RÉCAP MODE MANUEL - TEXTAREAS =====
    $('#ouvrages_consigner_manuel, #ouvrages_installer_manuel, #lieu_execution_manuel').on('input', function() {
        updateRecapManuel();
    });
    
    // Mettre à jour le récap manuel au chargement si déjà rempli
    if ($('#ouvrages_consigner_manuel').val() || $('#ouvrages_installer_manuel').val() || $('#lieu_execution_manuel').val()) {
        updateRecapManuel();
    }

    // ===== SCHEMA PREVIEW =====
    $('#schema').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).removeClass('hidden');
                    $('#noPreviewText').addClass('hidden');
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                $('#imagePreview').addClass('hidden');
                $('#noPreviewText').removeClass('hidden').text('Fichier PDF sélectionné: ' + file.name);
            }
        }
    });

    // ===== VALIDATION DATES =====
    const today = new Date().toISOString().split('T')[0];
    $('#ddp').attr('min', today);
    
    $('#ddp').change(function() {
        $('#dfp').attr('min', $(this).val());
        if ($('#dfp').val() && $('#dfp').val() < $(this).val()) {
            $('#dfp').val('');
        }
    });
    
    $('#dfp').change(function() {
        if ($(this).val() < $('#ddp').val()) {
            showNotification("La date de fin ne peut pas être antérieure à la date de début.", "error");
            $(this).val('');
        }
    });

    // ===== INITIALISER LES DONNÉES EXISTANTES (MODE ÉDITION) =====
    @if(isset($demande))
        // Données JSON des équipements existants
        var existingEquipementsConsigner = @json($demande->equipements_oracle ? json_decode($demande->equipements_oracle, true) : []);
        var existingEquipementsInstaller = @json($demande->equipements_installer_oracle ? json_decode($demande->equipements_installer_oracle, true) : []);
        var existingLignesConsigner = @json($demande->lignes_oracle ? json_decode($demande->lignes_oracle, true) : []);
        var existingLignesInstaller = @json($demande->lignes_installer_oracle ? json_decode($demande->lignes_installer_oracle, true) : []);
        var existingLieuCode = '{{ $demande->lieu_code ?? "" }}';
        var existingLieuDescription = '{{ $demande->lieu_execution ?? "" }}';
        var existingModeSaisie = '{{ $demande->mode_saisie ?? "manuel" }}';
        var existingOuvrageType = '{{ $demande->ouvrage_type ?? "ligne" }}';
        
        console.log('Mode édition - Données existantes:', {
            lieuCode: existingLieuCode,
            modeSaisie: existingModeSaisie,
            ouvrageType: existingOuvrageType,
            equipementsConsigner: existingEquipementsConsigner,
            equipementsInstaller: existingEquipementsInstaller,
            lignesConsigner: existingLignesConsigner,
            lignesInstaller: existingLignesInstaller
        });
        
        // Si mode GMAO et lieu sélectionné (lieu_code ou lieu_execution en fallback)
        var effectiveLieuCode = existingLieuCode || existingLieuDescription;
        if (existingModeSaisie === 'gmao' && effectiveLieuCode) {
            setTimeout(function() {
                // Ajouter le lieu comme option et le sélectionner (lieu_code ou lieu_execution en fallback)
                var newOption = new Option(existingLieuDescription, effectiveLieuCode, true, true);
                $('#lieu_execution').append(newOption).trigger('change');
                $('#hidden_lieu_execution').val(existingLieuDescription);
                $('#hidden_lieu_code').val(effectiveLieuCode);
                
                // Déterminer si ligne ou poste
                var isLigne = existingOuvrageType === 'ligne' || existingOuvrageType === 'ligne_installer';
                
                if (isLigne) {
                    // Mode Ligne
                    $('#radio_ligne').prop('checked', true).trigger('change');
                    $('#radio_ligne_installer').prop('checked', true).trigger('change');
                    
                    // Charger les lignes et pré-sélectionner
                    loadAllLignesWithExisting('ligne_disponible_consigner', effectiveLieuCode, existingLignesConsigner);
                    loadAllLignesWithExisting('ligne_disponible_installer', effectiveLieuCode, existingLignesInstaller);
                } else {
                    // Mode Poste
                    $('#radio_poste').prop('checked', true).trigger('change');
                    $('#radio_poste_installer').prop('checked', true).trigger('change');
                    
                    // Charger les niveaux d'équipements avec pré-sélection
                    setTimeout(function() {
                        loadEquipementsWithExisting('consigner', effectiveLieuCode, existingEquipementsConsigner);
                        loadEquipementsWithExisting('installer', effectiveLieuCode, existingEquipementsInstaller);
                    }, 300);
                }
                
                // Afficher le récapitulatif après un délai
                setTimeout(function() {
                    displayExistingRecap(existingEquipementsConsigner, existingEquipementsInstaller, existingLignesConsigner, existingLignesInstaller);
                }, 800);
                
            }, 500);
        }
    @endif
});

// ===== INITIALISATION SELECT2 =====
function initSelect2() {
    // Select2 basique pour les sélecteurs standards
    $('.select2').not('#lieu_execution').select2({
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() { return "Aucun résultat trouvé"; },
            searching: function() { return "Recherche..."; },
            inputTooShort: function(args) { return "Saisissez au moins " + args.minimum + " caractères"; }
        }
    });

    // Select2 avec recherche AJAX pour le lieu d'exécution
    $('#lieu_execution').select2({
        width: '100%',
        placeholder: 'Cliquez pour rechercher un lieu d\'exécution...',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: '{{ route("api.lieux-execution") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return { q: params.term || '' };
            },
            processResults: function(data) {
                console.log('API Response:', data);
                if (!Array.isArray(data)) {
                    console.error('Réponse API invalide:', data);
                    return { results: [] };
                }
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.code,
                            text: item.description,
                            code: item.code
                        };
                    })
                };
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', status, error);
                console.error('Réponse:', xhr.responseText);
            },
            cache: true
        },
        language: {
            inputTooShort: function() { return "Saisissez au moins 2 caractères..."; },
            noResults: function() { return "Aucun lieu trouvé"; },
            searching: function() { return "Recherche en cours..."; }
        }
    });
}

// ===== CHARGER LES ÉQUIPEMENTS ENFANTS =====
function loadEquipementsEnfants(parentCode, targetSelectId) {
    var $select = $('#' + targetSelectId);
    $select.html('<option value="">Chargement...</option>');
    
    $.ajax({
        url: '{{ route("api.equipements-enfants") }}',
        method: 'GET',
        data: { parent_code: parentCode },
        success: function(data) {
            var options = '<option value="">Sélectionner une ligne...</option>';
            data.forEach(function(item) {
                options += '<option value="' + item.code + '" data-description="' + item.description + '">' + item.description + '</option>';
            });
            $select.html(options);
        },
        error: function(xhr, status, error) {
            console.error('Erreur chargement équipements enfants:', error);
            $select.html('<option value="">Erreur de chargement</option>');
            showNotification('Erreur lors du chargement des équipements', 'error');
        }
    });
}

// ===== PRÉ-SÉLECTIONNER UNE LIGNE =====
function preselectLigne(lieuCode, lieuDescription, targetSelectId) {
    var $select = $('#' + targetSelectId);
    // Ajouter la ligne comme option et la pré-sélectionner
    $select.html('<option value="' + lieuCode + '" selected>' + lieuDescription + '</option>');
    
    // Mettre à jour le champ caché avec le code
    var hiddenFieldId = targetSelectId + '_code';
    $('#' + hiddenFieldId).val(lieuCode);
    
    // Réinitialiser Select2 avec la valeur
    $select.select2({
        width: '100%',
        allowClear: true
    });
}

// ===== CHARGER TOUTES LES LIGNES =====
function loadAllLignes(targetSelectId, preselectCode) {
    var $select = $('#' + targetSelectId);
    $select.html('<option value="">Chargement des lignes...</option>');
    
    $.ajax({
        url: '{{ route("api.all-lignes") }}',
        method: 'GET',
        success: function(data) {
            console.log('Toutes les lignes:', data);
            var options = '<option value="">Sélectionner une ligne...</option>';
            data.forEach(function(item) {
                var selected = (item.code === preselectCode) ? ' selected' : '';
                options += '<option value="' + item.code + '" data-description="' + item.description + '"' + selected + '>' + item.description + '</option>';
            });
            $select.html(options);
            
            // Mettre à jour le champ caché si une ligne est pré-sélectionnée
            if (preselectCode) {
                var hiddenFieldId = targetSelectId + '_code';
                $('#' + hiddenFieldId).val(preselectCode);
            }
            
            // Réinitialiser Select2
            $select.select2({
                width: '100%',
                placeholder: 'Sélectionner une ligne...',
                allowClear: true
            });
        },
        error: function(xhr, status, error) {
            console.error('Erreur chargement lignes:', error);
            $select.html('<option value="">Erreur de chargement</option>');
            showNotification('Erreur lors du chargement des lignes', 'error');
        }
    });
}

// ===== CHARGER LES SOUS-ÉQUIPEMENTS D'UNE LIGNE =====
function loadSousEquipementsLigne(ligneCode, targetSelectId) {
    var $select = $('#' + targetSelectId);
    $select.html('<option value="">Chargement...</option>');
    
    $.ajax({
        url: '{{ route("api.equipements-enfants") }}',
        method: 'GET',
        data: { parent_code: ligneCode },
        success: function(data) {
            console.log('Sous-équipements chargés:', data);
            var options = '';
            if (data.length === 0) {
                options = '<option value="">Aucun sous-équipement disponible</option>';
            } else {
                data.forEach(function(item) {
                    options += '<option value="' + item.code + '">' + item.description + '</option>';
                });
            }
            $select.html(options);
            $select.select2({
                width: '100%',
                placeholder: 'Sélectionner les sous-équipements...',
                allowClear: true
            });
        },
        error: function(xhr, status, error) {
            console.error('Erreur chargement sous-équipements:', error);
            $select.html('<option value="">Erreur de chargement</option>');
        }
    });
}

// ===== CHARGER LES SOUS-ÉQUIPEMENTS POUR LES DEUX SECTIONS =====
function loadSousEquipementsLigneBoth(ligneCode) {
    $.ajax({
        url: '{{ route("api.equipements-enfants") }}',
        method: 'GET',
        data: { parent_code: ligneCode },
        success: function(data) {
            console.log('Sous-équipements chargés pour les deux sections:', data);
            var options = '';
            if (data.length === 0) {
                options = '<option value="">Aucun sous-équipement disponible</option>';
            } else {
                data.forEach(function(item) {
                    options += '<option value="' + item.code + '">' + item.description + '</option>';
                });
            }
            
            // Appliquer aux deux selects
            $('#ligne_select').html(options);
            $('#ligne_installer_select').html(options);
            
            // Initialiser Select2 sur les deux
            $('#ligne_select, #ligne_installer_select').select2({
                width: '100%',
                placeholder: 'Sélectionner les sous-équipements...',
                allowClear: true
            });
        },
        error: function(xhr, status, error) {
            console.error('Erreur chargement sous-équipements:', error);
            $('#ligne_select, #ligne_installer_select').html('<option value="">Erreur de chargement</option>');
        }
    });
}

// ===== CHARGER LIGNES AVEC DONNÉES EXISTANTES (MODE ÉDITION) =====
function loadAllLignesWithExisting(targetSelectId, preselectCode, existingData) {
    var $select = $('#' + targetSelectId);
    $select.html('<option value="">Chargement des lignes...</option>');
    
    // Extraire les codes existants
    var existingCodes = [];
    if (existingData && Array.isArray(existingData)) {
        existingData.forEach(function(item) {
            var code = (typeof item === 'object') ? (item.code || item) : item;
            if (code) existingCodes.push(code);
        });
    }
    
    $.ajax({
        url: '{{ route("api.all-lignes") }}',
        method: 'GET',
        success: function(data) {
            var options = '<option value="">Sélectionner une ligne...</option>';
            data.forEach(function(item) {
                var selected = (item.code === preselectCode) ? ' selected' : '';
                options += '<option value="' + item.code + '" data-description="' + item.description + '"' + selected + '>' + item.description + '</option>';
            });
            $select.html(options);
            
            $select.select2({
                width: '100%',
                placeholder: 'Sélectionner une ligne...',
                allowClear: true
            });
            
            // Charger les sous-équipements si un lieu est sélectionné
            if (preselectCode) {
                var sousEquipSelectId = targetSelectId.replace('ligne_disponible_', 'ligne_').replace('_consigner', '_select').replace('_installer', '_installer_select');
                if (sousEquipSelectId === 'ligne_select' || sousEquipSelectId === 'ligne_installer_select') {
                    // Rien, on gère ailleurs
                } else {
                    sousEquipSelectId = targetSelectId.includes('installer') ? 'ligne_installer_select' : 'ligne_select';
                }
                loadSousEquipementsLigneWithExisting(preselectCode, sousEquipSelectId, existingCodes);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur chargement lignes:', error);
        }
    });
}

// ===== CHARGER SOUS-ÉQUIPEMENTS AVEC PRÉ-SÉLECTION =====
function loadSousEquipementsLigneWithExisting(ligneCode, targetSelectId, existingCodes) {
    var $select = $('#' + targetSelectId);
    $select.html('<option value="">Chargement...</option>');
    
    $.ajax({
        url: '{{ route("api.equipements-enfants") }}',
        method: 'GET',
        data: { parent_code: ligneCode },
        success: function(data) {
            var options = '';
            data.forEach(function(item) {
                var selected = existingCodes.includes(item.code) ? ' selected' : '';
                options += '<option value="' + item.code + '"' + selected + '>' + item.description + '</option>';
            });
            $select.html(options);
            $select.select2({
                width: '100%',
                placeholder: 'Sélectionner les sous-équipements...',
                allowClear: true
            });
        },
        error: function(xhr, status, error) {
            console.error('Erreur chargement sous-équipements:', error);
        }
    });
}

// ===== CHARGER ÉQUIPEMENTS HIÉRARCHIQUES AVEC DONNÉES EXISTANTES =====
function loadEquipementsWithExisting(type, lieuCode, existingData) {
    console.log('loadEquipementsWithExisting:', type, lieuCode, existingData);
    
    // Créer le premier niveau
    createEquipementLevel(type, 1, lieuCode);
    
    // Si pas de données existantes, arrêter
    if (!existingData || Object.keys(existingData).length === 0) return;
    
    // Attendre que le premier niveau soit chargé, puis pré-sélectionner récursivement
    setTimeout(function() {
        preselectEquipementsRecursively(type, existingData, 1);
    }, 800);
}

// ===== PRÉ-SÉLECTIONNER LES ÉQUIPEMENTS RÉCURSIVEMENT =====
function preselectEquipementsRecursively(type, existingData, level) {
    var levelKey = 'equipements_' + type + '_level_' + level;
    var levelData = existingData[levelKey];
    
    console.log('preselectEquipementsRecursively niveau', level, ':', levelKey, levelData);
    
    if (!levelData || !Array.isArray(levelData) || levelData.length === 0) {
        console.log('Pas de données pour le niveau', level);
        return;
    }
    
    // Extraire les codes
    var codes = levelData.map(function(item) {
        return (typeof item === 'object') ? (item.code || '') : item;
    }).filter(function(code) { return code !== ''; });
    
    console.log('Codes à sélectionner au niveau', level, ':', codes);
    
    if (codes.length === 0) return;
    
    // Trouver le select du niveau actuel
    var $select = $('#equipement_' + type + '_level_' + level);
    
    if ($select.length === 0) {
        console.log('Select non trouvé pour le niveau', level, ', on attend...');
        // Le select n'existe pas encore, réessayer
        setTimeout(function() {
            preselectEquipementsRecursively(type, existingData, level);
        }, 500);
        return;
    }
    
    // Vérifier que les options existent
    var availableOptions = $select.find('option').map(function() { return $(this).val(); }).get();
    var validCodes = codes.filter(function(code) { return availableOptions.includes(code); });
    
    console.log('Options disponibles:', availableOptions);
    console.log('Codes valides:', validCodes);
    
    if (validCodes.length === 0) {
        console.log('Aucun code valide trouvé au niveau', level);
        return;
    }
    
    // Pré-sélectionner les valeurs
    $select.val(validCodes);
    
    // Déclencher le changement pour que Select2 se mette à jour
    $select.trigger('change');
    
    // Vérifier s'il y a un niveau suivant à charger
    var nextLevel = level + 1;
    var nextLevelKey = 'equipements_' + type + '_level_' + nextLevel;
    
    if (nextLevel <= 6 && existingData[nextLevelKey] && existingData[nextLevelKey].length > 0) {
        // Charger le niveau suivant directement (sans déclencher select2:select pour éviter les doublons)
        // On appelle createEquipementLevel une seule fois avec tous les codes sélectionnés
        // Le 4ème paramètre `true` indique de ne pas supprimer le niveau s'il existe déjà
        createEquipementLevel(type, nextLevel, validCodes, false);
        
        // Attendre que le niveau soit créé puis continuer la pré-sélection
        setTimeout(function() {
            preselectEquipementsRecursively(type, existingData, nextLevel);
        }, 600);
    }
}

// ===== AFFICHER LE RÉCAPITULATIF DES DONNÉES EXISTANTES =====
function displayExistingRecap(equipConsigner, equipInstaller, lignesConsigner, lignesInstaller) {
    // Récap à consigner
    var htmlConsigner = '';
    
    // Équipements
    if (equipConsigner && Object.keys(equipConsigner).length > 0) {
        for (var levelKey in equipConsigner) {
            var levelData = equipConsigner[levelKey];
            if (levelData && Array.isArray(levelData) && levelData.length > 0) {
                var levelNum = levelKey.replace(/[^0-9]/g, '') || '1';
                htmlConsigner += '<div class="mb-2"><span class="font-medium text-senelec-orange">Niveau ' + levelNum + ':</span>';
                htmlConsigner += '<ul class="ml-4 mt-1 space-y-0.5">';
                levelData.forEach(function(item) {
                    var desc = (typeof item === 'object') ? (item.description || item.code || item) : item;
                    htmlConsigner += '<li class="text-gray-700"><span class="text-senelec-green">•</span> ' + escapeHtml(desc) + '</li>';
                });
                htmlConsigner += '</ul></div>';
            }
        }
    }
    
    // Lignes
    if (lignesConsigner && Array.isArray(lignesConsigner) && lignesConsigner.length > 0) {
        htmlConsigner += '<div class="mb-2"><span class="font-medium text-senelec-orange">Lignes:</span>';
        htmlConsigner += '<ul class="ml-4 mt-1 space-y-0.5">';
        lignesConsigner.forEach(function(item) {
            var desc = (typeof item === 'object') ? (item.description || item.code || item) : item;
            htmlConsigner += '<li class="text-gray-700"><span class="text-senelec-green">•</span> ' + escapeHtml(desc) + '</li>';
        });
        htmlConsigner += '</ul></div>';
    }
    
    if (!htmlConsigner) {
        htmlConsigner = '<p class="text-gray-400 italic">Aucun équipement sélectionné</p>';
    }
    $('#recap-consigner-content').html(htmlConsigner);
    
    // Récap à installer
    var htmlInstaller = '';
    
    // Équipements
    if (equipInstaller && Object.keys(equipInstaller).length > 0) {
        for (var levelKey in equipInstaller) {
            var levelData = equipInstaller[levelKey];
            if (levelData && Array.isArray(levelData) && levelData.length > 0) {
                var levelNum = levelKey.replace(/[^0-9]/g, '') || '1';
                htmlInstaller += '<div class="mb-2"><span class="font-medium text-senelec-teal">Niveau ' + levelNum + ':</span>';
                htmlInstaller += '<ul class="ml-4 mt-1 space-y-0.5">';
                levelData.forEach(function(item) {
                    var desc = (typeof item === 'object') ? (item.description || item.code || item) : item;
                    htmlInstaller += '<li class="text-gray-700"><span class="text-senelec-green">•</span> ' + escapeHtml(desc) + '</li>';
                });
                htmlInstaller += '</ul></div>';
            }
        }
    }
    
    // Lignes
    if (lignesInstaller && Array.isArray(lignesInstaller) && lignesInstaller.length > 0) {
        htmlInstaller += '<div class="mb-2"><span class="font-medium text-senelec-teal">Lignes:</span>';
        htmlInstaller += '<ul class="ml-4 mt-1 space-y-0.5">';
        lignesInstaller.forEach(function(item) {
            var desc = (typeof item === 'object') ? (item.description || item.code || item) : item;
            htmlInstaller += '<li class="text-gray-700"><span class="text-senelec-green">•</span> ' + escapeHtml(desc) + '</li>';
        });
        htmlInstaller += '</ul></div>';
    }
    
    if (!htmlInstaller) {
        htmlInstaller = '<p class="text-gray-400 italic">Aucun équipement sélectionné</p>';
    }
    $('#recap-installer-content').html(htmlInstaller);
    
    // Afficher la section récap
    $('#recap-equipements').show();
}

// ===== DÉTECTER SI LE LIEU EST UNE LIGNE OU UN POSTE =====
function detectIfLigne(code, description) {
    // Logique de détection basée sur le code ou la description
    // Les lignes commencent généralement par "L" ou contiennent "LIGNE"
    // Les postes commencent généralement par "P" ou contiennent "POSTE"
    
    var codeUpper = code.toUpperCase();
    var descUpper = description.toUpperCase();
    
    // Vérifier si c'est une ligne
    if (codeUpper.startsWith('L') || 
        descUpper.includes('LIGNE') || 
        descUpper.includes('LGN') ||
        descUpper.includes('DÉPART') ||
        descUpper.includes('DEPART')) {
        return true; // C'est une ligne
    }
    
    // Vérifier si c'est un poste
    if (codeUpper.startsWith('P') || 
        descUpper.includes('POSTE') || 
        descUpper.includes('PST') ||
        descUpper.includes('TRANSFO')) {
        return false; // C'est un poste
    }
    
    // Par défaut, considérer comme ligne
    return true;
}

// ===== METTRE À JOUR LES OPTIONS DE TYPE D'OUVRAGE =====
function updateOuvrageTypeBasedOnLieu(isLigne) {
    var $radioLigne = $('#radio_ligne');
    var $radioPoste = $('#radio_poste');
    var $radioLigneInstaller = $('#radio_ligne_installer');
    var $radioPosteInstaller = $('#radio_poste_installer');
    
    // Labels pour les styles visuels
    var $labelLigne = $radioLigne.closest('label');
    var $labelPoste = $radioPoste.closest('label');
    var $labelLigneInstaller = $radioLigneInstaller.closest('label');
    var $labelPosteInstaller = $radioPosteInstaller.closest('label');
    
    if (isLigne) {
        // C'est une ligne - cocher Ligne et griser Poste
        $radioLigne.prop('checked', true).prop('disabled', false);
        $radioPoste.prop('checked', false).prop('disabled', true);
        $radioLigneInstaller.prop('checked', true).prop('disabled', false);
        $radioPosteInstaller.prop('checked', false).prop('disabled', true);
        
        // Styles visuels
        $labelLigne.removeClass('opacity-50 cursor-not-allowed').addClass('cursor-pointer');
        $labelPoste.addClass('opacity-50 cursor-not-allowed').removeClass('cursor-pointer');
        $labelLigneInstaller.removeClass('opacity-50 cursor-not-allowed').addClass('cursor-pointer');
        $labelPosteInstaller.addClass('opacity-50 cursor-not-allowed').removeClass('cursor-pointer');
        
        // Afficher les bonnes sections
        $('#ligne_section').show();
        $('#poste_section').hide();
        $('#ligne_section_installer').show();
        $('#poste_section_installer').hide();
    } else {
        // C'est un poste - cocher Poste et griser Ligne
        $radioLigne.prop('checked', false).prop('disabled', true);
        $radioPoste.prop('checked', true).prop('disabled', false);
        $radioLigneInstaller.prop('checked', false).prop('disabled', true);
        $radioPosteInstaller.prop('checked', true).prop('disabled', false);
        
        // Styles visuels
        $labelLigne.addClass('opacity-50 cursor-not-allowed').removeClass('cursor-pointer');
        $labelPoste.removeClass('opacity-50 cursor-not-allowed').addClass('cursor-pointer');
        $labelLigneInstaller.addClass('opacity-50 cursor-not-allowed').removeClass('cursor-pointer');
        $labelPosteInstaller.removeClass('opacity-50 cursor-not-allowed').addClass('cursor-pointer');
        
        // Afficher les bonnes sections
        console.log('Affichage section poste');
        $('#ligne_section').hide();
        $('#poste_section').show();
        $('#oracle-equipements-consigner').show();
        $('#equipement-levels-consigner').show();
        $('#ligne_section_installer').hide();
        $('#poste_section_installer').show();
        $('#oracle-equipements-installer').show();
        $('#equipement-levels-installer').show();
        
        console.log('poste_section visible:', $('#poste_section').is(':visible'));
        console.log('equipement-levels-consigner contenu:', $('#equipement-levels-consigner').html());
    }
}

// ===== RÉINITIALISER LES OPTIONS DE TYPE D'OUVRAGE =====
function resetOuvrageTypeOptions() {
    var $radioLigne = $('#radio_ligne');
    var $radioPoste = $('#radio_poste');
    var $radioLigneInstaller = $('#radio_ligne_installer');
    var $radioPosteInstaller = $('#radio_poste_installer');
    
    // Réactiver tous les radio buttons
    $radioLigne.prop('disabled', false);
    $radioPoste.prop('disabled', false);
    $radioLigneInstaller.prop('disabled', false);
    $radioPosteInstaller.prop('disabled', false);
    
    // Réinitialiser les styles
    var $allLabels = $radioLigne.closest('label')
        .add($radioPoste.closest('label'))
        .add($radioLigneInstaller.closest('label'))
        .add($radioPosteInstaller.closest('label'));
    
    $allLabels.removeClass('opacity-50 cursor-not-allowed').addClass('cursor-pointer');
    
    // Remettre Ligne par défaut
    $radioLigne.prop('checked', true);
    $radioLigneInstaller.prop('checked', true);
    
    // Afficher les sections ligne
    toggleOuvrageSections();
    toggleOuvrageSectionsInstaller();
}

// ===== CRÉER UN NIVEAU D'ÉQUIPEMENT (HIÉRARCHIQUE) =====
function createEquipementLevel(type, level, parentCode, skipRemove) {
    console.log('createEquipementLevel appelé:', type, level, parentCode, 'skipRemove:', skipRemove);
    
    var containerId = 'equipement-levels-' + type;
    var $container = $('#' + containerId);
    
    console.log('Container trouvé:', $container.length > 0, containerId);
    
    // Vérifier si ce niveau existe déjà (pour éviter les doublons)
    var existingLevel = $container.find('.niveau-equipement[data-level="' + level + '"]');
    if (existingLevel.length > 0 && skipRemove) {
        console.log('Niveau', level, 'existe déjà, on skip');
        return;
    }
    
    // Supprimer les niveaux égaux ou supérieurs (sauf si skipRemove est true)
    if (!skipRemove) {
        $container.find('.niveau-equipement').each(function() {
            if (parseInt($(this).data('level')) >= level) {
                $(this).remove();
            }
        });
    }
    
    if (!parentCode) {
        console.log('Pas de parentCode, arrêt');
        return;
    }
    
    // Convertir en tableau si c'est un seul code
    var parentCodes = Array.isArray(parentCode) ? parentCode : [parentCode];
    
    // Charger les équipements enfants de TOUS les parents
    loadMultipleParentsChildren(type, level, parentCodes, $container);
}

// ===== CHARGER LES ENFANTS DE PLUSIEURS PARENTS =====
function loadMultipleParentsChildren(type, level, parentCodes, $container) {
    var childrenByParent = {}; // Grouper les enfants par parent
    var parentDescriptions = {}; // Stocker les descriptions des parents
    var loadedCount = 0;
    
    parentCodes.forEach(function(parentCode) {
        $.ajax({
            url: '{{ route("api.equipements-enfants") }}',
            method: 'GET',
            data: { parent_code: parentCode },
            success: function(data) {
                if (data.length > 0) {
                    childrenByParent[parentCode] = data.map(function(item) {
                        item.parentCode = parentCode;
                        return item;
                    });
                    // Récupérer la description du parent depuis le niveau précédent
                    var $prevSelect = $('#equipement_' + type + '_level_' + (level - 1));
                    if ($prevSelect.length > 0) {
                        var $option = $prevSelect.find('option[value="' + parentCode + '"]');
                        parentDescriptions[parentCode] = $option.data('description') || $option.text() || parentCode;
                    } else {
                        parentDescriptions[parentCode] = parentCode;
                    }
                }
                
                loadedCount++;
                
                // Quand tous les parents ont été chargés
                if (loadedCount === parentCodes.length) {
                    renderEquipementLevelGrouped(type, level, childrenByParent, parentDescriptions, $container);
                }
            },
            error: function() {
                loadedCount++;
                if (loadedCount === parentCodes.length) {
                    renderEquipementLevelGrouped(type, level, childrenByParent, parentDescriptions, $container);
                }
            }
        });
    });
}

// ===== AFFICHER UN NIVEAU D'ÉQUIPEMENT GROUPÉ PAR PARENT =====
function renderEquipementLevelGrouped(type, level, childrenByParent, parentDescriptions, $container) {
    // Compter le total d'enfants
    var totalChildren = 0;
    Object.keys(childrenByParent).forEach(function(parentCode) {
        totalChildren += childrenByParent[parentCode].length;
    });
    
    console.log('Équipements enfants groupés:', childrenByParent, 'Total:', totalChildren);
    
    if (totalChildren === 0) {
        console.log('Aucun équipement enfant');
        return;
    }
    
    var levelClass = 'niveau-' + Math.min(level, 6);
    var selectId = 'equipement_' + type + '_level_' + level;
    var parentCount = Object.keys(childrenByParent).length;
    
    var html = '<div class="niveau-equipement ' + levelClass + '" data-level="' + level + '">';
    html += '<span class="niveau-badge">Niveau ' + level + '</span>';
    html += '<label class="block text-sm font-medium text-gray-700 mb-2">Équipements niveau ' + level + ' <span class="text-xs text-gray-400">(' + totalChildren + ' éléments de ' + parentCount + ' parent(s))</span></label>';
    html += '<select class="input-senelec select2-dynamic" id="' + selectId + '" name="equipements_' + type + '_level_' + level + '[]" multiple data-type="' + type + '" data-level="' + level + '">';
    
    // Grouper par parent avec optgroup
    Object.keys(childrenByParent).forEach(function(parentCode) {
        var parentDesc = parentDescriptions[parentCode] || parentCode;
        html += '<optgroup label="📁 ' + parentDesc + '">';
        
        childrenByParent[parentCode].forEach(function(item) {
            html += '<option value="' + item.code + '" data-description="' + item.description + '" data-parent="' + parentCode + '">' + item.description + '</option>';
        });
        
        html += '</optgroup>';
    });
    
    html += '</select></div>';
    
    $container.append(html);
    console.log('HTML ajouté au container');
    
    // Initialiser Select2 sur le nouveau select
    var $newSelect = $('#' + selectId);
    $newSelect.select2({
        width: '100%',
        placeholder: 'Sélectionner les équipements...',
        allowClear: true
    });
    
    // Écouter les changements pour charger le niveau suivant
    $newSelect.on('select2:select', function(e) {
        var selectedCode = e.params.data.id;
        console.log('Équipement sélectionné:', selectedCode);
        
        // Récupérer TOUS les codes sélectionnés
        var allSelectedCodes = $newSelect.val() || [];
        console.log('Tous les équipements sélectionnés:', allSelectedCodes);
        
        // Charger le niveau suivant avec TOUS les parents sélectionnés
        createEquipementLevel(type, level + 1, allSelectedCodes);
        
        // Synchroniser avec installer si c'est consigner
        if (type === 'consigner') {
            // Copier la sélection vers installer
            var $installerSelect = $('#equipement_installer_level_' + level);
            if ($installerSelect.length > 0) {
                // Ajouter l'option si elle n'existe pas et la sélectionner
                if ($installerSelect.find('option[value="' + selectedCode + '"]').length > 0) {
                    var currentVals = $installerSelect.val() || [];
                    if (!currentVals.includes(selectedCode)) {
                        currentVals.push(selectedCode);
                        $installerSelect.val(currentVals).trigger('change');
                    }
                }
            }
            // Charger le niveau suivant pour installer aussi avec TOUS les parents
            createEquipementLevel('installer', level + 1, allSelectedCodes);
        }
        
        // Mettre à jour le récapitulatif
        updateRecapEquipements();
    });
    
    // Écouter aussi les désélections
    $newSelect.on('select2:unselect', function(e) {
        var unselectedCode = e.params.data.id;
        console.log('Équipement désélectionné:', unselectedCode);
        
        // Récupérer les codes restants sélectionnés
        var remainingCodes = $newSelect.val() || [];
        
        if (remainingCodes.length === 0) {
            // Plus aucun sélectionné, supprimer les niveaux enfants
            $container.find('.niveau-equipement').each(function() {
                if (parseInt($(this).data('level')) > level) {
                    $(this).remove();
                }
            });
        } else {
            // Recharger le niveau suivant avec les parents restants
            createEquipementLevel(type, level + 1, remainingCodes);
        }
        
        // Synchroniser avec installer
        if (type === 'consigner') {
            var $installerSelect = $('#equipement_installer_level_' + level);
            if ($installerSelect.length > 0) {
                // Désélectionner aussi côté installer
                var installerVals = $installerSelect.val() || [];
                var idx = installerVals.indexOf(unselectedCode);
                if (idx > -1) {
                    installerVals.splice(idx, 1);
                    $installerSelect.val(installerVals).trigger('change');
                }
            }
            
            if (remainingCodes.length === 0) {
                // Supprimer niveaux enfants côté installer
                $('#equipement-levels-installer .niveau-equipement').each(function() {
                    if (parseInt($(this).data('level')) > level) {
                        $(this).remove();
                    }
                });
            } else {
                createEquipementLevel('installer', level + 1, remainingCodes);
            }
        }
        
        // Mettre à jour le récapitulatif
        updateRecapEquipements();
    });
}

// ===== SYNCHRONISER ÉQUIPEMENTS VERS INSTALLER =====
function syncEquipementToInstaller(level, selectedCode) {
    // Charger le même niveau pour installer
    createEquipementLevel('installer', level + 1, selectedCode);
}

// ===== COPIER TOUTES LES SÉLECTIONS CONSIGNER VERS INSTALLER =====
function copyAllSelectionsToInstaller() {
    // Parcourir tous les selects de consigner et copier vers installer
    $('#equipement-levels-consigner .niveau-equipement select').each(function() {
        var $consignerSelect = $(this);
        var level = $consignerSelect.closest('.niveau-equipement').data('level');
        var $installerSelect = $('#equipement_installer_level_' + level);
        
        if ($installerSelect.length > 0) {
            var selectedVals = $consignerSelect.val();
            if (selectedVals && selectedVals.length > 0) {
                $installerSelect.val(selectedVals).trigger('change');
            }
        }
    });
}

// ===== RÉCAPITULATIF MODE MANUEL =====
function updateRecapManuel() {
    var consignerText = $('#ouvrages_consigner_manuel').val().trim();
    var installerText = $('#ouvrages_installer_manuel').val().trim();
    var lieuManuel = $('#lieu_execution_manuel').val().trim();
    
    // Récap consigner
    var htmlConsigner = '';
    if (lieuManuel) {
        htmlConsigner += '<div class="mb-2"><span class="font-medium text-senelec-orange">Lieu d\'exécution:</span>';
        htmlConsigner += '<p class="text-gray-700 ml-2 mt-1">' + escapeHtml(lieuManuel) + '</p></div>';
    }
    if (consignerText) {
        htmlConsigner += '<div class="mb-2"><span class="font-medium text-senelec-orange">Ouvrages:</span>';
        htmlConsigner += '<p class="text-gray-700 ml-2 mt-1 whitespace-pre-line">' + escapeHtml(consignerText) + '</p></div>';
    }
    if (!htmlConsigner) {
        htmlConsigner = '<p class="text-gray-400 italic">Aucun ouvrage saisi</p>';
    }
    $('#recap-consigner-manuel-content').html(htmlConsigner);
    
    // Récap installer
    var htmlInstaller = '';
    if (lieuManuel) {
        htmlInstaller += '<div class="mb-2"><span class="font-medium text-senelec-teal">Lieu d\'exécution:</span>';
        htmlInstaller += '<p class="text-gray-700 ml-2 mt-1">' + escapeHtml(lieuManuel) + '</p></div>';
    }
    if (installerText) {
        htmlInstaller += '<div class="mb-2"><span class="font-medium text-senelec-teal">Ouvrages:</span>';
        htmlInstaller += '<p class="text-gray-700 ml-2 mt-1 whitespace-pre-line">' + escapeHtml(installerText) + '</p></div>';
    }
    if (!htmlInstaller) {
        htmlInstaller = '<p class="text-gray-400 italic">Aucun ouvrage saisi</p>';
    }
    $('#recap-installer-manuel-content').html(htmlInstaller);
    
    // Afficher/masquer le récap
    if (consignerText || installerText || lieuManuel) {
        $('#recap-equipements-manuel').show();
    } else {
        $('#recap-equipements-manuel').hide();
    }
}

// Fonction utilitaire pour échapper le HTML
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===== RÉCAPITULATIF DES ÉQUIPEMENTS =====
function updateRecapEquipements() {
    var recapConsigner = [];
    var recapInstaller = [];
    
    // Récupérer le lieu d'exécution
    var lieuExec = $('#lieu_execution option:selected').text();
    if (lieuExec && lieuExec !== 'Rechercher un lieu...' && lieuExec !== '') {
        recapConsigner.push({
            level: 0,
            label: 'Lieu d\'exécution',
            items: [lieuExec]
        });
        recapInstaller.push({
            level: 0,
            label: 'Lieu d\'exécution',
            items: [lieuExec]
        });
    }
    
    // Vérifier si Ligne ou Poste est sélectionné côté consigner
    var isLigneConsigner = $('#radio_ligne').is(':checked');
    var isPosteConsigner = $('#radio_poste').is(':checked');
    
    // Vérifier si Ligne ou Poste est sélectionné côté installer
    var isLigneInstaller = $('#radio_ligne_installer').is(':checked');
    var isPosteInstaller = $('#radio_poste_installer').is(':checked');
    
    // Récupérer la ligne sélectionnée côté consigner (seulement si Ligne est coché)
    if (isLigneConsigner) {
        var ligneConsigner = $('#ligne_disponible_consigner option:selected').text();
        if (ligneConsigner && ligneConsigner.trim() !== 'Choisir une ligne...' && ligneConsigner.trim() !== 'Sélectionner une ligne...' && ligneConsigner.trim() !== '') {
            recapConsigner.push({
                level: 1,
                label: 'Ligne',
                items: [ligneConsigner.trim()]
            });
        }
        
        // Récupérer les sous-équipements lignes consigner
        var sousEquipConsigner = [];
        $('#ligne_select option:selected').each(function() {
            sousEquipConsigner.push($(this).text());
        });
        if (sousEquipConsigner.length > 0) {
            recapConsigner.push({
                level: 2,
                label: 'Sous-équipements',
                items: sousEquipConsigner
            });
        }
    }
    
    // Récupérer la ligne sélectionnée côté installer (seulement si Ligne est coché)
    if (isLigneInstaller) {
        var ligneInstaller = $('#ligne_disponible_installer option:selected').text();
        if (ligneInstaller && ligneInstaller.trim() !== 'Choisir une ligne...' && ligneInstaller.trim() !== 'Sélectionner une ligne...' && ligneInstaller.trim() !== '') {
            recapInstaller.push({
                level: 1,
                label: 'Ligne',
                items: [ligneInstaller.trim()]
            });
        }
        
        // Récupérer les sous-équipements lignes installer
        var sousEquipInstaller = [];
        $('#ligne_installer_select option:selected').each(function() {
            sousEquipInstaller.push($(this).text());
        });
        if (sousEquipInstaller.length > 0) {
            recapInstaller.push({
                level: 2,
                label: 'Sous-équipements',
                items: sousEquipInstaller
            });
        }
    }
    
    // Récupérer les équipements par niveau (postes)
    for (var level = 1; level <= 6; level++) {
        // Consigner
        var $selectConsigner = $('#equipement_consigner_level_' + level);
        if ($selectConsigner.length > 0) {
            var itemsConsigner = [];
            $selectConsigner.find('option:selected').each(function() {
                var parentCode = $(this).data('parent');
                var text = $(this).text();
                if (parentCode) {
                    // Trouver le nom du parent
                    var parentName = '';
                    var $parentSelect = $('#equipement_consigner_level_' + (level - 1));
                    if ($parentSelect.length > 0) {
                        parentName = $parentSelect.find('option[value="' + parentCode + '"]').data('description') || parentCode;
                    }
                    itemsConsigner.push({text: text, parent: parentName});
                } else {
                    itemsConsigner.push({text: text, parent: null});
                }
            });
            if (itemsConsigner.length > 0) {
                recapConsigner.push({
                    level: level,
                    label: 'Niveau ' + level,
                    items: itemsConsigner
                });
            }
        }
        
        // Installer
        var $selectInstaller = $('#equipement_installer_level_' + level);
        if ($selectInstaller.length > 0) {
            var itemsInstaller = [];
            $selectInstaller.find('option:selected').each(function() {
                var parentCode = $(this).data('parent');
                var text = $(this).text();
                if (parentCode) {
                    var parentName = '';
                    var $parentSelect = $('#equipement_installer_level_' + (level - 1));
                    if ($parentSelect.length > 0) {
                        parentName = $parentSelect.find('option[value="' + parentCode + '"]').data('description') || parentCode;
                    }
                    itemsInstaller.push({text: text, parent: parentName});
                } else {
                    itemsInstaller.push({text: text, parent: null});
                }
            });
            if (itemsInstaller.length > 0) {
                recapInstaller.push({
                    level: level,
                    label: 'Niveau ' + level,
                    items: itemsInstaller
                });
            }
        }
    }
    
    // Afficher le récap consigner
    var htmlConsigner = '';
    if (recapConsigner.length === 0) {
        htmlConsigner = '<p class="text-gray-400 italic">Aucun équipement sélectionné</p>';
    } else {
        recapConsigner.forEach(function(group) {
            htmlConsigner += '<div class="mb-2">';
            htmlConsigner += '<span class="font-medium text-senelec-orange">' + group.label + ':</span>';
            htmlConsigner += '<ul class="ml-4 mt-1 space-y-0.5">';
            group.items.forEach(function(item) {
                if (typeof item === 'object') {
                    htmlConsigner += '<li class="text-gray-700 flex items-start gap-1">';
                    htmlConsigner += '<span class="text-senelec-green">•</span> ' + item.text;
                    if (item.parent) {
                        htmlConsigner += ' <span class="text-xs text-gray-400">(← ' + item.parent + ')</span>';
                    }
                    htmlConsigner += '</li>';
                } else {
                    htmlConsigner += '<li class="text-gray-700"><span class="text-senelec-green">•</span> ' + item + '</li>';
                }
            });
            htmlConsigner += '</ul></div>';
        });
    }
    $('#recap-consigner-content').html(htmlConsigner);
    
    // Afficher le récap installer
    var htmlInstaller = '';
    if (recapInstaller.length === 0) {
        htmlInstaller = '<p class="text-gray-400 italic">Aucun équipement sélectionné</p>';
    } else {
        recapInstaller.forEach(function(group) {
            htmlInstaller += '<div class="mb-2">';
            htmlInstaller += '<span class="font-medium text-senelec-teal">' + group.label + ':</span>';
            htmlInstaller += '<ul class="ml-4 mt-1 space-y-0.5">';
            group.items.forEach(function(item) {
                if (typeof item === 'object') {
                    htmlInstaller += '<li class="text-gray-700 flex items-start gap-1">';
                    htmlInstaller += '<span class="text-senelec-green">•</span> ' + item.text;
                    if (item.parent) {
                        htmlInstaller += ' <span class="text-xs text-gray-400">(← ' + item.parent + ')</span>';
                    }
                    htmlInstaller += '</li>';
                } else {
                    htmlInstaller += '<li class="text-gray-700"><span class="text-senelec-green">•</span> ' + item + '</li>';
                }
            });
            htmlInstaller += '</ul></div>';
        });
    }
    $('#recap-installer-content').html(htmlInstaller);
    
    // Afficher/masquer le récap
    var hasAnySelection = recapConsigner.length > 0 || recapInstaller.length > 0;
    if (hasAnySelection) {
        $('#recap-equipements').show();
    } else {
        $('#recap-equipements').hide();
    }
}

// ===== FONCTIONS DMRP =====
function toggleDmrpInput() {
    const value = $('#dmrp_select').val();
    if (value === 'time') {
        $('#dmrp_time_container').show();
    } else {
        $('#dmrp_time_container').hide();
        $('#dmrp_time').val('');
    }
}

// ===== FORMATAGE DATE =====
function formatDateFr(dateStr) {
    if (!dateStr) return '-';
    var parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    var mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    var jour = parseInt(parts[2], 10);
    var moisIndex = parseInt(parts[1], 10) - 1;
    var annee = parts[0];
    return jour + ' ' + mois[moisIndex] + ' ' + annee;
}

// ===== MODAL APERÇU =====
function showPreviewModal() {
    // Validation rapide
    if (!$('#designation').val().trim()) {
        showNotification('Veuillez remplir la désignation des travaux', 'error');
        $('#designation').focus();
        return;
    }
    if (!$('#ddp').val() || !$('#dfp').val()) {
        showNotification('Veuillez remplir les dates de la période', 'error');
        return;
    }
    
    // Générer le récapitulatif complet
    let content = '<div class="space-y-4">';
    
    // Section 1: Informations générales
    content += '<div class="border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="text-sm font-semibold text-senelec-purple mb-3 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-senelec-purple text-white flex items-center justify-center text-xs">1</span> Informations générales</h4>';
    content += '<div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">';
    content += '<div class="p-2 bg-gray-50 rounded"><span class="text-gray-500 text-xs block">Date</span><strong>' + formatDateFr($('#date').val()) + '</strong></div>';
    content += '<div class="p-2 bg-gray-50 rounded"><span class="text-gray-500 text-xs block">Destinataire</span><strong>' + ($('#destinataire').val() || '-') + '</strong></div>';
    var lieuExec = $('#hidden_lieu_execution').val() || $('#lieu_execution_manuel').val() || '-';
    content += '<div class="p-2 bg-gray-50 rounded col-span-2"><span class="text-gray-500 text-xs block">Lieu d\'exécution</span><strong>' + lieuExec + '</strong></div>';
    content += '</div></div>';
    
    // Section 2: Période et désignation
    content += '<div class="border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="text-sm font-semibold text-senelec-purple mb-3 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-senelec-purple text-white flex items-center justify-center text-xs">2</span> Période et travaux</h4>';
    content += '<div class="grid grid-cols-2 gap-3 text-sm mb-3">';
    content += '<div class="p-2 bg-senelec-purple/5 rounded border border-senelec-purple/20"><span class="text-senelec-purple text-xs block">📅 Début proposé</span><strong>' + formatDateFr($('#ddp').val()) + ' à ' + $('#hdp').val() + '</strong></div>';
    content += '<div class="p-2 bg-senelec-teal/5 rounded border border-senelec-teal/20"><span class="text-senelec-teal text-xs block">📅 Fin proposée</span><strong>' + formatDateFr($('#dfp').val()) + ' à ' + $('#hfp').val() + '</strong></div>';
    content += '</div>';
    content += '<div class="p-2 bg-gray-50 rounded text-sm"><span class="text-gray-500 text-xs block">Désignation des travaux</span><strong>' + ($('#designation').val() || '-') + '</strong></div>';
    content += '</div>';
    
    // Section 3: Ouvrages
    content += '<div class="border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="text-sm font-semibold text-senelec-purple mb-3 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-senelec-purple text-white flex items-center justify-center text-xs">3</span> Ouvrages</h4>';
    content += '<div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">';
    
    // Ouvrages à consigner
    content += '<div class="p-2 bg-senelec-orange/5 rounded border border-senelec-orange/20">';
    content += '<span class="text-senelec-orange text-xs font-medium block mb-1">🔒 À consigner</span>';
    var ouvragesConsigner = '';
    if ($('#radio_mode_gmao').is(':checked')) {
        // Mode GMAO
        var isLigneConsigner = $('#radio_ligne').is(':checked');
        if (isLigneConsigner) {
            var ligneConsigner = $('#ligne_disponible_consigner option:selected').text();
            if (ligneConsigner && ligneConsigner.trim() !== 'Choisir une ligne...' && ligneConsigner.trim() !== 'Sélectionner une ligne...' && ligneConsigner.trim() !== '') {
                ouvragesConsigner += '<div>Ligne: ' + ligneConsigner.trim() + '</div>';
            }
            var sousEquipConsigner = [];
            $('#ligne_select option:selected').each(function() { sousEquipConsigner.push($(this).text()); });
            if (sousEquipConsigner.length > 0) {
                ouvragesConsigner += '<div>Équipements: ' + sousEquipConsigner.join(', ') + '</div>';
            }
        }
        // Équipements postes
        for (var i = 1; i <= 6; i++) {
            var equips = [];
            $('#equipement_consigner_level_' + i + ' option:selected').each(function() { equips.push($(this).text()); });
            if (equips.length > 0) {
                ouvragesConsigner += '<div>Niveau ' + i + ': ' + equips.join(', ') + '</div>';
            }
        }
    } else {
        ouvragesConsigner = $('#ouvrages_consigner_manuel').val() || '-';
    }
    content += '<div class="text-gray-700">' + (ouvragesConsigner || '-') + '</div>';
    content += '</div>';
    
    // Ouvrages travaux
    content += '<div class="p-2 bg-senelec-teal/5 rounded border border-senelec-teal/20">';
    content += '<span class="text-senelec-teal text-xs font-medium block mb-1">⚙️ Travaux à réaliser sur</span>';
    var ouvragesTravaux = '';
    if ($('#radio_mode_gmao').is(':checked')) {
        var isLigneInstaller = $('#radio_ligne_installer').is(':checked');
        if (isLigneInstaller) {
            var ligneInstaller = $('#ligne_disponible_installer option:selected').text();
            if (ligneInstaller && ligneInstaller.trim() !== 'Choisir une ligne...' && ligneInstaller.trim() !== 'Sélectionner une ligne...' && ligneInstaller.trim() !== '') {
                ouvragesTravaux += '<div>Ligne: ' + ligneInstaller.trim() + '</div>';
            }
            var sousEquipInstaller = [];
            $('#ligne_installer_select option:selected').each(function() { sousEquipInstaller.push($(this).text()); });
            if (sousEquipInstaller.length > 0) {
                ouvragesTravaux += '<div>Équipements: ' + sousEquipInstaller.join(', ') + '</div>';
            }
        }
        for (var i = 1; i <= 6; i++) {
            var equips = [];
            $('#equipement_installer_level_' + i + ' option:selected').each(function() { equips.push($(this).text()); });
            if (equips.length > 0) {
                ouvragesTravaux += '<div>Niveau ' + i + ': ' + equips.join(', ') + '</div>';
            }
        }
    } else {
        ouvragesTravaux = $('#ouvrages_installer_manuel').val() || '-';
    }
    content += '<div class="text-gray-700">' + (ouvragesTravaux || '-') + '</div>';
    content += '</div>';
    content += '</div></div>';
    
    // Section 4: Intervenants
    content += '<div class="border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="text-sm font-semibold text-senelec-purple mb-3 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-senelec-purple text-white flex items-center justify-center text-xs">4</span> Intervenants</h4>';
    content += '<div class="grid grid-cols-2 gap-3 text-sm">';
    content += '<div class="p-2 bg-gray-50 rounded"><span class="text-gray-500 text-xs block">Demandeur</span><strong>' + $.trim($('#demandeur option:selected').text()) + '</strong>';
    if ($('#matricule').val()) content += '<br><span class="text-xs text-gray-400">Mat: ' + $('#matricule').val() + '</span>';
    if ($('#telephoned').val()) content += '<br><span class="text-xs text-gray-400">Tél: ' + $('#telephoned').val() + '</span>';
    content += '</div>';
    content += '<div class="p-2 bg-gray-50 rounded"><span class="text-gray-500 text-xs block">Chargé des travaux</span><strong>' + $.trim($('#charge_travaux option:selected').text() || '-') + '</strong>';
    if ($('#telephone').val()) content += '<br><span class="text-xs text-gray-400">Tél: ' + $('#telephone').val() + '</span>';
    if ($('#entreprise').val()) content += '<br><span class="text-xs text-gray-400">' + $('#entreprise').val() + '</span>';
    content += '</div>';
    content += '</div></div>';
    
    // Section 5: Options de sécurité
    var mte = $('input[name="mte"]:checked').val() || '-';
    var mcce = $('input[name="mcce"]:checked').val() || '-';
    var etape = $('input[name="etape"]:checked').val() === 'ue' ? 'Une étape' : 'Deux étapes';
    var dmrp = $('#dmrp_select').val();
    var dmrpText = (dmrp === 'aucun' || dmrp === 'non_applicable') ? 'Non Applicable' : (dmrp === 'time' ? ($('#dmrp_time').val() || '-') : dmrp);
    var restituerSoir = $('#dmrp_soir').is(':checked');
    
    content += '<div class="border border-red-200 rounded-lg p-4 bg-red-50/50">';
    content += '<h4 class="text-sm font-semibold text-red-800 mb-3 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs">5</span> ⚠️ Options de sécurité</h4>';
    content += '<div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">';
    content += '<div class="p-2 bg-white rounded border"><span class="text-gray-500 text-xs block">MTE</span><strong class="' + (mte === 'oui' ? 'text-red-600' : 'text-gray-700') + '">' + mte.toUpperCase() + '</strong></div>';
    content += '<div class="p-2 bg-white rounded border"><span class="text-gray-500 text-xs block">MCCE</span><strong class="' + (mcce === 'oui' ? 'text-red-600' : 'text-gray-700') + '">' + mcce.toUpperCase() + '</strong></div>';
    content += '<div class="p-2 bg-white rounded border"><span class="text-gray-500 text-xs block">Consignation</span><strong>' + etape + '</strong></div>';
    content += '<div class="p-2 bg-white rounded border"><span class="text-gray-500 text-xs block">Délai max restitution</span><strong>' + dmrpText + '</strong></div>';
    content += '<div class="p-2 bg-white rounded border"><span class="text-gray-500 text-xs block">Restituer le soir</span><strong class="' + (restituerSoir ? 'text-green-600' : 'text-gray-700') + '">' + (restituerSoir ? '✅ OUI' : 'NON') + '</strong></div>';
    content += '</div></div>';
    
    // Section 6: Documents
    var schema = $('#schema')[0].files[0];
    if (schema) {
        content += '<div class="border border-gray-200 rounded-lg p-4">';
        content += '<h4 class="text-sm font-semibold text-senelec-purple mb-3 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-senelec-purple text-white flex items-center justify-center text-xs">6</span> Document joint</h4>';
        content += '<div class="text-sm p-2 bg-gray-50 rounded"><span class="text-gray-500">📎</span> ' + schema.name + ' <span class="text-gray-400">(' + (schema.size / 1024).toFixed(1) + ' Ko)</span></div>';
        
        // Aperçu de l'image si c'est une image
        var isImage = schema.type.startsWith('image/');
        if (isImage) {
            content += '<div class="mt-3 text-center" id="schema-preview-container">';
            content += '<img id="schema-preview-img" class="max-h-48 mx-auto rounded-lg border border-gray-300 shadow-sm" alt="Aperçu du schéma">';
            content += '</div>';
        }
        content += '</div>';
    }
    
    content += '</div>';
    
    $('#recapContent').html(content);
    
    // Charger l'aperçu de l'image après insertion du HTML
    if (schema && schema.type.startsWith('image/')) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#schema-preview-img').attr('src', e.target.result);
        };
        reader.readAsDataURL(schema);
    }
    
    $('#recapModal').removeClass('hidden').addClass('flex');
}

function closePreviewModal() {
    $('#recapModal').removeClass('flex').addClass('hidden');
}

function submitForm() {
    closePreviewModal();
    $('<input>').attr({ type: 'hidden', name: 'statut', value: 'créée' }).appendTo('#demande-form');
    
    // Gérer le charge_travaux_id avant soumission
    var ctValue = $('#charge_travaux').val();
    if (ctValue && ctValue.toString().startsWith('ext_new_')) {
        // C'est un NOUVEAU CT externe (ajouté via modal)
        // Les champs ct_externe_nom, ct_externe_telephone, etc. sont déjà remplis par saveCT()
        // Ne pas toucher à charge_travaux_externe_id (doit rester vide pour la création)
        $('#charge_travaux_externe_id').val('');
        
        // Vider le charge_travaux_id pour éviter la validation exists:users
        $('#charge_travaux').val('');
    } else if (ctValue && ctValue.toString().startsWith('ext_')) {
        // C'est un CT externe EXISTANT, extraire l'ID numérique
        var externeId = ctValue.replace('ext_', '');
        var selectedOption = $('#charge_travaux option:selected');
        
        // Remplir le champ caché avec l'ID existant
        $('#charge_travaux_externe_id').val(externeId);
        
        // Vider les champs de nouveau CT (on utilise l'ID existant)
        $('#ct_externe_nom').val('');
        $('#ct_externe_telephone').val('');
        $('#ct_externe_entreprise').val('');
        $('#ct_externe_service').val('');
        
        // Vider le charge_travaux_id pour éviter la validation exists:users
        $('#charge_travaux').val('');
    } else {
        // C'est un CT interne, vider les champs externes
        $('#charge_travaux_externe_id').val('');
        $('#ct_externe_nom').val('');
        $('#ct_externe_telephone').val('');
        $('#ct_externe_entreprise').val('');
        $('#ct_externe_service').val('');
    }
    
    // Afficher un indicateur de chargement persistant
    showLoadingOverlay('Envoi de la demande en cours...');
    
    // Soumettre via AJAX
    var formData = new FormData($('#demande-form')[0]);
    
    $.ajax({
        url: $('#demande-form').attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            hideLoadingOverlay();
            if (response.success) {
                showNotification(response.message || 'Demande envoyée avec succès !', 'success');
                // Rediriger après un court délai
                setTimeout(function() {
                    window.location.href = response.redirect || '{{ route("demandeur.demandes.index") }}';
                }, 1500);
            } else {
                showNotification(response.message || 'Erreur lors de l\'envoi', 'error');
            }
        },
        error: function(xhr) {
            hideLoadingOverlay();
            var errorMsg = 'Erreur lors de l\'envoi de la demande';
            // Afficher les erreurs de validation détaillées
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = Object.values(xhr.responseJSON.errors).flat();
                errorMsg = errors.join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showNotification(errorMsg, 'error');
        }
    });
}

// ===== MODAL CT EXTERNE =====
function openModalCT() {
    $('#modalAjouterCT').removeClass('hidden').addClass('flex');
}

function closeModalCT() {
    $('#modalAjouterCT').removeClass('flex').addClass('hidden');
    $('#formAjouterCT')[0].reset();
}

function saveCT() {
    const nom = $('#nom_externe').val().trim();
    const telephone = $('#telephone_externe').val().trim();
    const entreprise = $('#entreprise_externe').val().trim();
    const service = $('#service_externe').val().trim();
    
    if (!nom || !telephone || !entreprise) {
        showNotification('Veuillez remplir tous les champs obligatoires.', 'error');
        return;
    }
    
    // Générer un ID temporaire unique pour le nouveau CT externe
    const tempId = 'ext_new_' + Date.now();
    
    // Ajouter l'option au select dans le groupe Externes
    const newOption = new Option(nom + ' (' + entreprise + ')', tempId, true, true);
    $(newOption).attr({
        'data-type': 'externe',
        'data-nom': nom,
        'data-telephone': telephone,
        'data-entreprise': entreprise,
        'data-service': service
    });
    
    // Chercher l'optgroup Externes ou le créer
    var optgroupExterne = $('#charge_travaux optgroup[label*="Externes"]');
    if (optgroupExterne.length === 0) {
        optgroupExterne = $('<optgroup label="🏢 Externes"></optgroup>');
        $('#charge_travaux').append(optgroupExterne);
    }
    optgroupExterne.append(newOption);
    $('#charge_travaux').val(tempId).trigger('change');
    
    // Remplir les champs cachés pour le CT externe
    $('#ct_externe_nom').val(nom);
    $('#ct_externe_telephone').val(telephone);
    $('#ct_externe_entreprise').val(entreprise);
    $('#ct_externe_service').val(service);
    
    // Mettre à jour les champs visibles
    $('#telephone').val(telephone);
    $('#entreprise').val(entreprise + (service ? ' - ' + service : ''));
    $('#matricule_charge').val('');
    $('#fonctionct').val('Externe');
    $('#matricule_charge_group').hide();
    
    closeModalCT();
    showNotification('Chargé des travaux externe ajouté avec succès', 'success');
}

// ===== LOADING OVERLAY =====
function showLoadingOverlay(message) {
    var overlay = '<div id="loadingOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center">' +
        '<div class="bg-white rounded-xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-sm mx-4">' +
        '<div class="relative">' +
        '<div class="w-16 h-16 border-4 border-senelec-purple/20 rounded-full"></div>' +
        '<div class="w-16 h-16 border-4 border-senelec-purple border-t-transparent rounded-full absolute top-0 left-0 animate-spin"></div>' +
        '</div>' +
        '<p class="text-gray-700 font-medium text-center">' + message + '</p>' +
        '<p class="text-gray-400 text-sm">Veuillez patienter...</p>' +
        '</div></div>';
    $('body').append(overlay);
}

function hideLoadingOverlay() {
    $('#loadingOverlay').fadeOut(300, function() { $(this).remove(); });
}

// ===== NOTIFICATIONS =====
function showNotification(message, type) {
    var bgColor = 'bg-gray-700';
    var icon = '📢';
    var duration = 5000; // 5 secondes par défaut
    
    switch(type) {
        case 'success':
            bgColor = 'bg-green-600';
            icon = '✅';
            duration = 6000;
            break;
        case 'error':
            bgColor = 'bg-red-600';
            icon = '❌';
            duration = 10000; // 10 secondes pour les erreurs
            break;
        case 'warning':
            bgColor = 'bg-amber-500';
            icon = '⚠️';
            duration = 5000;
            break;
        case 'info':
            bgColor = 'bg-blue-600';
            icon = 'ℹ️';
            duration = 4000;
            break;
    }
    
    var $notification = $('<div class="fixed top-4 right-4 ' + bgColor + ' text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 transform transition-all duration-300">' +
        '<span class="text-xl">' + icon + '</span><span class="font-medium">' + message + '</span></div>');
    
    $('body').append($notification);
    
    // Animation d'entrée
    $notification.css({ opacity: 0, transform: 'translateX(100px)' });
    setTimeout(function() {
        $notification.css({ opacity: 1, transform: 'translateX(0)' });
    }, 10);
    
    // Disparition après le délai
    setTimeout(function() {
        $notification.css({ opacity: 0, transform: 'translateX(100px)' });
        setTimeout(function() { $notification.remove(); }, 300);
    }, duration);
}
</script>
@endpush
