@extends('layouts.app')

@section('title', 'Modifier la demande ' . $demande->numero_demande)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('demandeur.demandes.show', $demande) }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">
                Modifier la demande <span class="text-senelec-purple">{{ $demande->numero_demande }}</span>
            </h1>
            <p class="text-gray-600">Modification de la Demande d'Arrêt Pour Travaux</p>
        </div>
    </div>

    @if($demande->statut === 'retournée')
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-medium">Cette demande vous a été retournée</p>
                <p class="text-sm">Veuillez effectuer les corrections nécessaires et soumettre à nouveau.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('demandeur.demandes.update', $demande) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Informations générales -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informations générales
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="date">Date de la demande <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', $demande->date) }}" 
                           class="input-senelec w-full" required>
                </div>
                <div>
                    <label class="label" for="destinataire">Destinataire <span class="text-red-500">*</span></label>
                    <input type="text" id="destinataire" name="destinataire" 
                           value="{{ old('destinataire', $demande->destinataire) }}" 
                           class="input-senelec w-full" placeholder="Service destinataire" required>
                </div>
                <div class="md:col-span-2">
                    <label class="label" for="designation">Désignation des travaux <span class="text-red-500">*</span></label>
                    <textarea id="designation" name="designation" rows="3" 
                              class="input-senelec w-full" placeholder="Description détaillée des travaux" required>{{ old('designation', $demande->designation) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Lieu d'exécution -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Lieu d'exécution
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="lieu_execution">Lieu d'exécution <span class="text-red-500">*</span></label>
                    <input type="text" id="lieu_execution" name="lieu_execution" 
                           value="{{ old('lieu_execution', $demande->lieu_execution) }}" 
                           class="input-senelec w-full" placeholder="Localisation des travaux" required>
                </div>
                <div>
                    <label class="label" for="ouvrage_type">Type d'ouvrage <span class="text-red-500">*</span></label>
                    <select id="ouvrage_type" name="ouvrage_type" class="select-senelec w-full" required>
                        <option value="ligne" {{ old('ouvrage_type', $demande->ouvrage_type) == 'ligne' ? 'selected' : '' }}>Ligne</option>
                        <option value="poste" {{ old('ouvrage_type', $demande->ouvrage_type) == 'poste' ? 'selected' : '' }}>Poste</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Mode de saisie des ouvrages -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Ouvrages à consigner / installer
            </h2>
            
            <div class="mb-4">
                <label class="label">Mode de saisie <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="mode_saisie" value="manuel" 
                               {{ old('mode_saisie', $demande->mode_saisie ?? 'manuel') == 'manuel' ? 'checked' : '' }}
                               class="text-senelec-purple focus:ring-senelec-purple">
                        <span class="ml-2">Saisie manuelle</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="mode_saisie" value="gmao" 
                               {{ old('mode_saisie', $demande->mode_saisie) == 'gmao' ? 'checked' : '' }}
                               class="text-senelec-purple focus:ring-senelec-purple">
                        <span class="ml-2">Depuis GMAO</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="ouvrages_consigner_manuel">Ouvrages à consigner</label>
                    <textarea id="ouvrages_consigner_manuel" name="ouvrages_consigner_manuel" rows="4" 
                              class="input-senelec w-full" placeholder="Liste des ouvrages à consigner...">{{ old('ouvrages_consigner_manuel', $demande->ouvrages_consigner_manuel) }}</textarea>
                </div>
                <div>
                    <label class="label" for="ouvrages_installer_manuel">Ouvrages à installer</label>
                    <textarea id="ouvrages_installer_manuel" name="ouvrages_installer_manuel" rows="4" 
                              class="input-senelec w-full" placeholder="Liste des ouvrages à installer...">{{ old('ouvrages_installer_manuel', $demande->ouvrages_installer_manuel) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Dates et heures prévues -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Dates et heures prévues
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="label" for="ddp">Date début prévue <span class="text-red-500">*</span></label>
                    <input type="date" id="ddp" name="ddp" value="{{ old('ddp', $demande->ddp) }}" 
                           class="input-senelec w-full" required>
                </div>
                <div>
                    <label class="label" for="hdp">Heure début <span class="text-red-500">*</span></label>
                    <input type="time" id="hdp" name="hdp" value="{{ old('hdp', $demande->hdp) }}" 
                           class="input-senelec w-full" required>
                </div>
                <div>
                    <label class="label" for="dfp">Date fin prévue <span class="text-red-500">*</span></label>
                    <input type="date" id="dfp" name="dfp" value="{{ old('dfp', $demande->dfp) }}" 
                           class="input-senelec w-full" required>
                </div>
                <div>
                    <label class="label" for="hfp">Heure fin <span class="text-red-500">*</span></label>
                    <input type="time" id="hfp" name="hfp" value="{{ old('hfp', $demande->hfp) }}" 
                           class="input-senelec w-full" required>
                </div>
            </div>
            
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="dmrp" value="1" {{ old('dmrp', $demande->dmrp) ? 'checked' : '' }}
                           class="rounded text-senelec-purple focus:ring-senelec-purple">
                    <span class="ml-2 text-sm">Demande de mise en régime particulier</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="dmrp_restitution" value="1" {{ old('dmrp_restitution', $demande->dmrp_restitution) ? 'checked' : '' }}
                           class="rounded text-senelec-purple focus:ring-senelec-purple">
                    <span class="ml-2 text-sm">Restitution DMRP</span>
                </label>
            </div>
        </div>

        <!-- Options supplémentaires -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                Options supplémentaires
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label" for="etape">Type d'étape <span class="text-red-500">*</span></label>
                    <select id="etape" name="etape" class="select-senelec w-full" required>
                        <option value="ue" {{ old('etape', $demande->etape) == 'ue' ? 'selected' : '' }}>Une étape (UE)</option>
                        <option value="de" {{ old('etape', $demande->etape) == 'de' ? 'selected' : '' }}>Deux étapes (DE)</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="schema">Schéma (optionnel)</label>
                    <input type="file" id="schema" name="schema" accept=".pdf,.png,.jpg,.jpeg"
                           class="input-senelec w-full">
                    @if($demande->schema)
                        <p class="text-xs text-gray-500 mt-1">
                            Schéma actuel: <a href="{{ Storage::url($demande->schema) }}" target="_blank" class="text-senelec-purple hover:underline">Voir</a>
                        </p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, PNG, JPG</p>
                    @endif
                </div>
            </div>
            
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="mte" value="1" {{ old('mte', $demande->mte) ? 'checked' : '' }}
                           class="rounded text-senelec-purple focus:ring-senelec-purple">
                    <span class="ml-2 text-sm">Mise à la terre (MTE)</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="mcce" value="1" {{ old('mcce', $demande->mcce) ? 'checked' : '' }}
                           class="rounded text-senelec-purple focus:ring-senelec-purple">
                    <span class="ml-2 text-sm">MCCE</span>
                </label>
            </div>
            
            <div class="mt-4">
                <label class="label" for="renseignement">Renseignements complémentaires</label>
                <textarea id="renseignement" name="renseignement" rows="3" 
                          class="input-senelec w-full" placeholder="Informations supplémentaires...">{{ old('renseignement', $demande->renseignement) }}</textarea>
            </div>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('demandeur.demandes.show', $demande) }}" class="btn-senelec-outline py-2 px-6">
                Annuler
            </a>
            <button type="submit" class="btn-senelec py-2 px-6">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
