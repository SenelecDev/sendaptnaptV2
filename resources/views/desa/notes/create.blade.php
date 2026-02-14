@extends('layouts.app')

@section('title', 'Créer une NAPT')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .fieldset-section {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background-color: #fff;
    }
    .fieldset-section legend {
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        padding: 0 0.5rem;
        margin-left: -0.25rem;
    }
    .form-label-required::after {
        content: ' *';
        color: #ef4444;
    }
    /* Select2 Custom Styles */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        min-height: 42px;
        padding: 4px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #7c3aed;
        border: none;
        border-radius: 0.375rem;
        color: white;
        padding: 4px 12px;
        margin: 2px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 12px;
        border: none;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fca5a5;
        background: transparent;
    }
    .select2-dropdown {
        border-color: #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #7c3aed;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 6px;
    }
    /* Single select styles */
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
        padding: 6px 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 0;
        color: #374151;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Créer une NAPT</h1>
                @if($demande)
                    <p class="mt-1 text-sm text-gray-500">
                        Pour la demande <span class="font-semibold text-senelec-purple">{{ $demande->numero_demande }}</span>
                    </p>
                @endif
            </div>
            <a href="{{ route('desa.demandes.index') }}" class="btn-senelec-outline">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-red-700 font-medium">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="note-form" action="{{ route('desa.notes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($demande)
            <input type="hidden" name="demande_id" value="{{ $demande->id }}">
        @endif

        <!-- Informations sur la Demande -->
        @if($demande)
        <div class="card-senelec">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informations sur la Demande
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Demandeur - Service</dt>
                        <dd class="mt-1 text-gray-900">{{ $demande->demandeur->name ?? '-' }} - {{ $demande->demandeur->service ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Chargé de travaux</dt>
                        <dd class="mt-1 text-gray-900">
                            @if($demande->charge_travaux_info)
                                {{ $demande->charge_travaux_info->nom }}
                                @if($demande->charge_travaux_info->type === 'externe')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 ml-1">Externe</span>
                                @endif
                                @if($demande->charge_travaux_info->telephone)
                                    <span class="text-gray-500 text-sm ml-1">({{ $demande->charge_travaux_info->telephone }})</span>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Lieu d'exécution</dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $demande->lieu_execution ?? $demande->lieu_execution_manuel ?? '-' }}
                            @if($demande->lieu_code)
                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $demande->lieu_code }}</span>
                            @endif
                        </dd>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Ouvrages à consigner -->
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-orange-800 mb-3">Ouvrages à consigner</h4>
                        @if($demande->mode_saisie === 'manuel')
                            <div class="text-sm text-gray-700">
                                {!! nl2br(e($demande->ouvrages_consigner_manuel ?? 'Non renseigné')) !!}
                            </div>
                        @else
                            @php
                                $equipementsData = $demande->equipements_oracle ? json_decode($demande->equipements_oracle, true) : [];
                                $lignesData = $demande->lignes_oracle ? json_decode($demande->lignes_oracle, true) : [];
                            @endphp
                            @if(!empty($lignesData))
                                <ul class="text-sm text-gray-700 space-y-1">
                                    @foreach($lignesData as $ligne)
                                        <li class="flex items-center">
                                            <span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span>
                                            {{ is_array($ligne) ? ($ligne['description'] ?? $ligne['code'] ?? '-') : $ligne }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(!empty($equipementsData))
                                <ul class="text-sm text-gray-700 space-y-1 mt-2">
                                    @foreach($equipementsData as $level => $data)
                                        @if(is_array($data))
                                            @foreach($data as $eq)
                                                <li class="flex items-center">
                                                    <span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span>
                                                    {{ is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '-') : $eq }}
                                                </li>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            @if(empty($lignesData) && empty($equipementsData))
                                <span class="text-gray-500 text-sm">Non spécifié</span>
                            @endif
                        @endif
                    </div>

                    <!-- Ouvrages à réaliser -->
                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-teal-800 mb-3">Ouvrages sur lesquels les travaux sont à réaliser</h4>
                        @if($demande->mode_saisie === 'manuel')
                            <div class="text-sm text-gray-700">
                                {!! nl2br(e($demande->ouvrages_installer_manuel ?? 'Non renseigné')) !!}
                            </div>
                        @else
                            @php
                                $equipementsInstallerData = $demande->equipements_installer_oracle ? json_decode($demande->equipements_installer_oracle, true) : [];
                                $lignesInstallerData = $demande->lignes_installer_oracle ? json_decode($demande->lignes_installer_oracle, true) : [];
                            @endphp
                            @if(!empty($lignesInstallerData))
                                <ul class="text-sm text-gray-700 space-y-1">
                                    @foreach($lignesInstallerData as $ligne)
                                        <li class="flex items-center">
                                            <span class="w-2 h-2 bg-teal-400 rounded-full mr-2"></span>
                                            {{ is_array($ligne) ? ($ligne['description'] ?? $ligne['code'] ?? '-') : $ligne }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(!empty($equipementsInstallerData))
                                <ul class="text-sm text-gray-700 space-y-1 mt-2">
                                    @foreach($equipementsInstallerData as $level => $data)
                                        @if(is_array($data))
                                            @foreach($data as $eq)
                                                <li class="flex items-center">
                                                    <span class="w-2 h-2 bg-teal-400 rounded-full mr-2"></span>
                                                    {{ is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '-') : $eq }}
                                                </li>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            @if(empty($lignesInstallerData) && empty($equipementsInstallerData))
                                <span class="text-gray-500 text-sm">Non spécifié</span>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Désignation des travaux</dt>
                    <dd class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $demande->designation ?? '-' }}</dd>
                </div>
            </div>
        </div>
        @else
        <!-- Sélection de la demande si pas de demande pré-sélectionnée -->
        <div class="card-senelec p-6">
            <label for="demande_id" class="label form-label-required">Sélectionner une demande acceptée</label>
            <select name="demande_id" id="demande_id" class="select2 w-full" required>
                <option value="">-- Choisir une demande --</option>
                @foreach($demandesAcceptees as $dem)
                    <option value="{{ $dem->id }}">{{ $dem->numero_demande }} - {{ Str::limit($dem->designation, 60) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <!-- Informations NAPT -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Informations NAPT
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="numero_note" class="label form-label-required">Numéro NAPT</label>
                    <input type="text" name="numero_note" id="numero_note" 
                           class="input-senelec w-full @error('numero_note') border-red-500 @enderror" 
                           placeholder="Ex: 00001-2026" 
                           value="{{ old('numero_note') }}" required>
                    <p class="mt-1 text-xs text-gray-500">Format: XXXXX-YYYY</p>
                    @error('numero_note')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="date" class="label form-label-required">Date de saisie</label>
                    <input type="date" name="date" id="date" 
                           class="input-senelec w-full bg-gray-100" 
                           value="{{ old('date', now()->format('Y-m-d')) }}" readonly required>
                </div>
                <div>
                    <label for="numero_semaine" class="label form-label-required">Numéro de semaine</label>
                    <input type="number" name="numero_semaine" id="numero_semaine" 
                           class="input-senelec w-full bg-gray-100" 
                           value="{{ old('numero_semaine') }}" readonly required>
                </div>
            </div>
        </div>

        <!-- Périodes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Période travaux -->
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Période travaux
                </h3>
                <div class="space-y-4">
                    @php
                        $ddtValue = '';
                        $dftValue = '';
                        // Utiliser les dates acceptées si disponibles, sinon les dates proposées
                        if ($demande) {
                            $dateDebutSource = $demande->dda ?? $demande->ddp;
                            $heureDebutSource = $demande->hda ?? $demande->hdp;
                            $dateFinSource = $demande->dfa ?? $demande->dfp;
                            $heureFinSource = $demande->hfa ?? $demande->hfp;
                            
                            if ($dateDebutSource) {
                                $dateDebut = \Carbon\Carbon::parse($dateDebutSource);
                                if ($heureDebutSource) {
                                    $timeParts = explode(':', $heureDebutSource);
                                    $dateDebut->setTime((int)($timeParts[0] ?? 0), (int)($timeParts[1] ?? 0));
                                }
                                $ddtValue = $dateDebut->format('Y-m-d\TH:i');
                            }
                            if ($dateFinSource) {
                                $dateFin = \Carbon\Carbon::parse($dateFinSource);
                                if ($heureFinSource) {
                                    $timeParts = explode(':', $heureFinSource);
                                    $dateFin->setTime((int)($timeParts[0] ?? 0), (int)($timeParts[1] ?? 0));
                                }
                                $dftValue = $dateFin->format('Y-m-d\TH:i');
                            }
                        }
                    @endphp
                    <div>
                        <label for="ddt" class="label form-label-required">Date début des travaux</label>
                        <input type="datetime-local" name="ddt" id="ddt" 
                               class="input-senelec w-full"
                               value="{{ old('ddt', $ddtValue) }}" required>
                    </div>
                    <div>
                        <label for="dft" class="label form-label-required">Date fin des travaux</label>
                        <input type="datetime-local" name="dft" id="dft" 
                               class="input-senelec w-full"
                               value="{{ old('dft', $dftValue) }}" required>
                    </div>
                </div>
            </div>

            <!-- Période exploitation -->
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Période exploitation
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="dre" class="label form-label-required">Date retrait de l'exploitation</label>
                        <input type="datetime-local" name="dre" id="dre" 
                               class="input-senelec w-full"
                               value="{{ old('dre') }}" required>
                    </div>
                    <div>
                        <label for="drex" class="label form-label-required">Date remise de l'exploitation</label>
                        <input type="datetime-local" name="drex" id="drex" 
                               class="input-senelec w-full"
                               value="{{ old('drex') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacts -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Contacts
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="charges_consignation" class="label">Chargé(s) de consignation</label>
                    <select name="charges_consignation[]" id="charges_consignation" class="select2 w-full" multiple>
                        <option value="0">N/A</option>
                        @foreach($chargecons as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->nom }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3">
                        <label for="adresse_charges_consignation" class="label text-xs">Adresse(s)</label>
                        <textarea name="adresse_charges_consignation" id="adresse_charges_consignation" 
                                  class="input-senelec w-full" rows="2" 
                                  placeholder="Séparer les adresses par des virgules">{{ old('adresse_charges_consignation') }}</textarea>
                    </div>
                </div>
                <div>
                    <label for="correspondants" class="label">Correspondants</label>
                    <select name="correspondants[]" id="correspondants" class="select2 w-full" multiple>
                        <option value="0">N/A</option>
                        @foreach($correspondants as $corr)
                            <option value="{{ $corr->id }}">{{ $corr->nom }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3">
                        <label for="adresse_correspondants" class="label text-xs">Adresse(s)</label>
                        <textarea name="adresse_correspondants" id="adresse_correspondants" 
                                  class="input-senelec w-full" rows="2" 
                                  placeholder="Séparer les adresses par des virgules">{{ old('adresse_correspondants') }}</textarea>
                    </div>
                </div>
                <div>
                    <label for="services" class="label">Destinataires</label>
                    <select name="services[]" id="services" class="select2 w-full" multiple>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Renseignements complémentaires -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Renseignements complémentaires
            </h2>
            <textarea name="renseignementN" id="renseignementN" class="input-senelec w-full" rows="4" 
                      placeholder="Informations complémentaires...">{{ old('renseignementN', $demande->renseignement ?? '') }}</textarea>
        </div>

        <!-- Étude approfondie -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Étude approfondie
            </h2>
            
            <div class="mb-4">
                <label class="label form-label-required">Cette demande nécessite-t-elle une étude approfondie ?</label>
                <div class="mt-2 flex">
                    <label class="inline-flex items-center" style="margin-right: 10px;">
                        <input type="radio" name="etude" value="oui" id="etude_oui" 
                               class="form-radio text-senelec-purple" onclick="toggleFileInput()">
                        <span class="ml-2">Oui</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="etude" value="non" id="etude_non" 
                               class="form-radio text-senelec-purple" onclick="toggleFileInput()" checked>
                        <span class="ml-2">Non</span>
                    </label>
                </div>
            </div>
            
            <div id="file-input" class="hidden">
                <label for="document" class="label">Veuillez joindre un fichier</label>
                <input type="file" name="document" id="document" 
                       class="input-senelec w-full" accept=".pdf,.jpg,.jpeg,.png">
                <p class="mt-1 text-xs text-gray-500">Formats acceptés: PDF, JPG, PNG</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="card-senelec p-6">
            <div class="flex flex-wrap gap-4 justify-center">
                <button type="submit" name="action" value="brouillon" id="btn-brouillon"
                        data-loading-text="Enregistrement en brouillon..."
                        class="px-6 py-3 bg-senelec-purple hover:bg-senelec-purple/90 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <svg class="w-5 h-5 mr-2 btn-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="btn-text">Enregistrer en brouillon</span>
                </button>
                
                <button type="submit" name="action" value="en_cours_etude" id="btn-etude"
                        data-loading-text="Mise en cours d'étude..."
                        class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors inline-flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400">
                    <svg class="w-5 h-5 mr-2 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <svg class="w-5 h-5 mr-2 btn-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="btn-text">Mettre en cours d'étude</span>
                </button>
                
                <button type="submit" name="action" value="attente_verification" id="btn-verification"
                        data-loading-text="Envoi en vérification..."
                        class="px-6 py-3 bg-senelec-orange hover:bg-senelec-orange/90 text-white font-semibold rounded-lg transition-colors inline-flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400">
                    <svg class="w-5 h-5 mr-2 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg class="w-5 h-5 mr-2 btn-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="btn-text">Envoyer en vérification</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Fonctions de calcul des dates
    function subtract30Minutes(dateString) {
        let date = new Date(dateString);
        date.setMinutes(date.getMinutes() - 30);
        return formatDateForInput(date);
    }

    function add30Minutes(dateString) {
        let date = new Date(dateString);
        date.setMinutes(date.getMinutes() + 30);
        return formatDateForInput(date);
    }

    function formatDateForInput(date) {
        const offset = date.getTimezoneOffset();
        date.setMinutes(date.getMinutes() - offset);
        return date.toISOString().slice(0, 16);
    }

    function getWeekNumber(date) {
        const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        const dayNum = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - dayNum);
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    }

    function setDefaultValues() {
        const ddtElement = document.getElementById('ddt');
        const dftElement = document.getElementById('dft');
        const dreElement = document.getElementById('dre');
        const drexElement = document.getElementById('drex');
        const numeroSemaineElement = document.getElementById('numero_semaine');

        if (ddtElement.value) {
            dreElement.value = subtract30Minutes(ddtElement.value);
            numeroSemaineElement.value = getWeekNumber(new Date(ddtElement.value));
        }

        if (dftElement.value) {
            drexElement.value = add30Minutes(dftElement.value);
        }
    }

    document.getElementById('ddt').addEventListener('change', function() {
        const ddtValue = this.value;
        if (ddtValue) {
            document.getElementById('dre').value = subtract30Minutes(ddtValue);
            document.getElementById('numero_semaine').value = getWeekNumber(new Date(ddtValue));
        }
    });

    document.getElementById('dft').addEventListener('change', function() {
        const dftValue = this.value;
        if (dftValue) {
            document.getElementById('drex').value = add30Minutes(dftValue);
        }
    });

    function toggleFileInput() {
        const etudeOui = document.getElementById('etude_oui');
        const fileInputDiv = document.getElementById('file-input');
        const btnVerification = document.getElementById('btn-verification');
        const btnEtude = document.getElementById('btn-etude');

        if (etudeOui.checked) {
            fileInputDiv.classList.remove('hidden');
            validateVerificationButton();
            // Activer le bouton "en cours d'étude"
            if (btnEtude) {
                btnEtude.disabled = false;
                btnEtude.title = '';
                btnEtude.style.backgroundColor = '';
                btnEtude.style.cursor = '';
            }
        } else {
            fileInputDiv.classList.add('hidden');
            document.getElementById('document').value = '';
            if (btnVerification) {
                btnVerification.disabled = false;
                btnVerification.title = '';
                btnVerification.style.backgroundColor = '';
                btnVerification.style.cursor = '';
            }
            // Désactiver le bouton "en cours d'étude" si Non
            if (btnEtude) {
                btnEtude.disabled = true;
                btnEtude.title = 'Ce bouton est disponible uniquement pour une étude approfondie';
                btnEtude.style.backgroundColor = '#9ca3af';
                btnEtude.style.cursor = 'not-allowed';
            }
        }
    }

    function validateVerificationButton() {
        const etudeOui = document.getElementById('etude_oui').checked;
        const fileInput = document.getElementById('document');
        const btnVerification = document.getElementById('btn-verification');
        
        if (btnVerification) {
            if (etudeOui) {
                const hasNewFile = fileInput.files.length > 0;
                btnVerification.disabled = !hasNewFile;
                btnVerification.title = hasNewFile ? '' : 'Un fichier joint est obligatoire pour une NAPT nécessitant une étude';
                
                // Appliquer les styles directement
                if (!hasNewFile) {
                    btnVerification.style.backgroundColor = '#9ca3af';
                    btnVerification.style.cursor = 'not-allowed';
                } else {
                    btnVerification.style.backgroundColor = '';
                    btnVerification.style.cursor = '';
                }
            } else {
                btnVerification.disabled = false;
                btnVerification.title = '';
                btnVerification.style.backgroundColor = '';
                btnVerification.style.cursor = '';
            }
        }
    }

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Sélectionnez...",
            allowClear: true,
            width: '100%'
        });
    });

    window.onload = function() {
        toggleFileInput();
        setDefaultValues();
        
        const fileInput = document.getElementById('document');
        if (fileInput) {
            fileInput.addEventListener('change', validateVerificationButton);
        }

        const form = document.getElementById('note-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitter = e.submitter;
                
                // Vérification pour le fichier obligatoire
                if (submitter && submitter.value === 'attente_verification') {
                    const etudeOui = document.getElementById('etude_oui').checked;
                    const fileInput = document.getElementById('document');
                    const hasNewFile = fileInput.files.length > 0;
                    
                    if (etudeOui && !hasNewFile) {
                        e.preventDefault();
                        alert('Un fichier joint est obligatoire pour une NAPT nécessitant une étude avant de l\'envoyer en vérification.');
                        return false;
                    }
                }
                
                // Afficher le spinner sur le bouton cliqué
                if (submitter) {
                    const icon = submitter.querySelector('.btn-icon');
                    const spinner = submitter.querySelector('.btn-spinner');
                    const text = submitter.querySelector('.btn-text');
                    const loadingText = submitter.getAttribute('data-loading-text');
                    
                    if (icon) icon.classList.add('hidden');
                    if (spinner) spinner.classList.remove('hidden');
                    if (text && loadingText) text.textContent = loadingText;
                    
                    // Désactiver tous les autres boutons
                    document.querySelectorAll('button[type="submit"]').forEach(btn => {
                        if (btn !== submitter) {
                            btn.disabled = true;
                            btn.style.opacity = '0.5';
                        }
                    });
                    submitter.style.cursor = 'wait';
                }
            });
        }
    }
</script>
@endpush
@endsection
