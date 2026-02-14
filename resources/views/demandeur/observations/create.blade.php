@extends('layouts.app')

@section('title', 'Nouvelle Observation')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('demandeur.observations.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Nouvelle Observation</h1>
            <p class="text-gray-600">Envoyez un retour ou une suggestion à l'administration</p>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card-senelec p-6">
        <form action="{{ route('demandeur.observations.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sujet -->
                <div class="md:col-span-2">
                    <label for="sujet" class="label">Sujet <span class="text-red-500">*</span></label>
                    <input type="text" name="sujet" id="sujet" value="{{ old('sujet') }}" 
                           class="input-senelec w-full @error('sujet') border-red-500 @enderror"
                           placeholder="Résumé de votre observation" required>
                    @error('sujet')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="label">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" class="select-senelec w-full @error('type') border-red-500 @enderror" required>
                        <option value="">Sélectionner un type</option>
                        <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>Bug / Problème technique</option>
                        <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>Suggestion d'amélioration</option>
                        <option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>Question</option>
                        <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priorité -->
                <div>
                    <label for="priorite" class="label">Priorité <span class="text-red-500">*</span></label>
                    <select name="priorite" id="priorite" class="select-senelec w-full @error('priorite') border-red-500 @enderror" required>
                        <option value="">Sélectionner une priorité</option>
                        <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                        <option value="normale" {{ old('priorite', 'normale') == 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                        <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                    @error('priorite')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="label">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="6"
                              class="input-senelec w-full @error('description') border-red-500 @enderror"
                              placeholder="Décrivez votre observation en détail..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('demandeur.observations.index') }}" class="btn-senelec-outline">
                    Annuler
                </a>
                <button type="submit" class="btn-senelec">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Envoyer l'observation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
