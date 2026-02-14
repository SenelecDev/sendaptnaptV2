@extends('layouts.app')

@section('title', 'Modifier chargé de consignation')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.chargecons.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier chargé de consignation</h1>
            <p class="text-gray-600">{{ $chargecon->nom }}</p>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card-senelec">
        <form action="{{ route('admin.chargecons.update', $chargecon) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label for="nom" class="label">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $chargecon->nom) }}" 
                           class="input @error('nom') border-red-500 @enderror" required>
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Matricule -->
                <div>
                    <label for="matricule" class="label">Matricule</label>
                    <input type="text" name="matricule" id="matricule" value="{{ old('matricule', $chargecon->matricule) }}" 
                           class="input @error('matricule') border-red-500 @enderror">
                    @error('matricule')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fonction -->
                <div>
                    <label for="fonction" class="label">Fonction</label>
                    <input type="text" name="fonction" id="fonction" value="{{ old('fonction', $chargecon->fonction) }}" 
                           class="input @error('fonction') border-red-500 @enderror">
                    @error('fonction')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="telephone" class="label">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $chargecon->telephone) }}" 
                           class="input @error('telephone') border-red-500 @enderror">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label for="adresse" class="label">Adresse</label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $chargecon->adresse) }}" 
                           class="input @error('adresse') border-red-500 @enderror">
                    @error('adresse')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.chargecons.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Annuler
                </a>
                <button type="submit" class="btn-senelec">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
