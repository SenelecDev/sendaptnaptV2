@extends('layouts.app')

@section('title', 'Modifier le groupe')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.groupes.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier le groupe</h1>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('admin.groupes.update', $groupe) }}" method="POST" class="card-senelec space-y-6">
        @csrf
        @method('PUT')

        <!-- Nom -->
        <div>
            <label for="nom" class="label">Nom du groupe <span class="text-red-500">*</span></label>
            <input type="text" name="nom" id="nom" value="{{ old('nom', $groupe->nom) }}" required
                   class="input @error('nom') border-red-500 @enderror">
            @error('nom')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="label">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="input @error('description') border-red-500 @enderror">{{ old('description', $groupe->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.groupes.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="btn-senelec">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
