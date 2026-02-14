@extends('layouts.app')

@section('title', 'Modifier service destinataire')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.services.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier service destinataire</h1>
            <p class="text-gray-600">{{ $service->nom }}</p>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card-senelec">
        <form action="{{ route('admin.services.update', $service) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label for="nom" class="label">Nom du service <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $service->nom) }}" 
                           class="input @error('nom') border-red-500 @enderror" required>
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsable" class="label">Responsable</label>
                    <input type="text" name="responsable" id="responsable" value="{{ old('responsable', $service->responsable) }}" 
                           class="input @error('responsable') border-red-500 @enderror">
                    @error('responsable')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $service->email) }}" 
                           class="input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.services.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
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
