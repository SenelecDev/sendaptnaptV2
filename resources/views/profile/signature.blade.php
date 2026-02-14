@extends('layouts.app')

@section('title', 'Ma signature')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ma signature</h1>
            <p class="text-gray-600">Gérer votre signature numérique</p>
        </div>
    </div>

    <!-- Signature actuelle -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Signature actuelle</h3>
        
        @if(auth()->user()->signature)
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 text-center">
                <img src="{{ auth()->user()->signature_url }}" alt="Signature" class="max-h-24 mx-auto">
            </div>
            
            <form action="{{ route('profile.signature.delete') }}" method="POST" class="mt-4"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre signature ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer la signature
                </button>
            </form>
        @else
            <div class="p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <p class="text-gray-500">Aucune signature enregistrée</p>
            </div>
        @endif
    </div>

    <!-- Upload nouvelle signature -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            {{ auth()->user()->signature ? 'Remplacer la signature' : 'Ajouter une signature' }}
        </h3>
        
        <form action="{{ route('profile.signature.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-6">
                    <label class="label">Fichier image (PNG, JPG)</label>
                    <input type="file" name="signature" accept="image/png,image/jpeg" 
                           class="block w-full text-sm text-gray-500
                                  border border-gray-300 rounded-lg p-2
                                  file:mr-6 file:py-2 file:px-4
                                  file:rounded file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-green-100 file:text-green-700
                                  hover:file:bg-green-200
                                  cursor-pointer" required>
                    <p class="text-xs text-gray-500">
                        Format recommandé : PNG avec fond transparent, <strong>300 x 200 pixels</strong>, max 2 Mo.<br>
                        Les images plus grandes seront redimensionnées et les fonds blancs rendus transparents automatiquement.
                    </p>
                    @error('signature')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn-senelec">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Enregistrer la signature
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Instructions -->
    <div class="card-senelec bg-blue-50 border border-blue-200">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Conseils
        </h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Utilisez une image de signature scannée ou créée numériquement</li>
            <li>• Les fonds blancs (JPEG/PNG) sont automatiquement rendus transparents</li>
            <li>• La signature sera utilisée sur les documents PDF générés (NAPT)</li>
            <li>• <strong>Dimensions recommandées : 300 x 200 pixels</strong></li>
            <li>• Les images trop grandes seront automatiquement redimensionnées</li>
        </ul>
    </div>
</div>
@endsection
