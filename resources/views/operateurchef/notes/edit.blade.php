@extends('layouts.app')

@section('title', 'Fiche manœuvre - NAPT ' . $note->numero_note)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div>
        <a href="{{ route('operateurchef.notes.show', $note) }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la note
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $note->fiche_manoeuvre ? 'Modifier' : 'Ajouter' }} la fiche de manœuvre
        </h1>
        <p class="text-gray-600">NAPT {{ $note->numero_note }} - Semaine {{ $note->numero_semaine }}</p>
    </div>

    <!-- Info DAPT -->
    @if($note->demande)
    <div class="card-senelec p-6 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500">DAPT</p>
                <p class="font-medium">{{ $note->demande->numero_demande }}</p>
            </div>
            <div>
                <p class="text-gray-500">Lieu d'exécution</p>
                <p class="font-medium">{{ $note->demande->lieu_execution ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Désignation</p>
                <p class="font-medium">{{ Str::limit($note->demande->designation, 80) ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Fiche actuelle -->
    @if($note->fiche_manoeuvre)
    <div class="card-senelec p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Fiche actuelle</h3>
        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="font-medium text-green-800">Fiche de manœuvre jointe</p>
                    <p class="text-sm text-green-600">{{ basename($note->fiche_manoeuvre) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ Storage::url($note->fiche_manoeuvre) }}" target="_blank" class="btn-senelec-sm">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Voir
                </a>
                <form action="{{ route('operateurchef.notes.destroy-fiche', $note) }}" method="POST" 
                      onsubmit="return confirm('Supprimer cette fiche de manœuvre ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-senelec-sm bg-red-600 hover:bg-red-700">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire -->
    <form action="{{ route('operateurchef.notes.update', $note) }}" method="POST" enctype="multipart/form-data" class="card-senelec p-6">
        @csrf
        @method('PUT')
        
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            {{ $note->fiche_manoeuvre ? 'Remplacer la fiche' : 'Joindre la fiche de manœuvre' }}
        </h3>
        
        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <div class="space-y-6">
            <!-- Upload -->
            <div>
                <label for="fiche_manoeuvre" class="label">
                    Fiche de manœuvre <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-senelec-purple transition-colors"
                         x-data="{ fileName: '' }">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="fiche_manoeuvre" class="relative cursor-pointer rounded-md font-medium text-senelec-purple hover:text-senelec-magenta">
                                    <span>Sélectionner un fichier</span>
                                    <input id="fiche_manoeuvre" name="fiche_manoeuvre" type="file" class="sr-only" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           @change="fileName = $event.target.files[0]?.name || ''"
                                           required>
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG ou PNG jusqu'à 10 Mo</p>
                            <p x-show="fileName" x-text="fileName" class="text-sm font-medium text-senelec-purple mt-2"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Information</p>
                        <p>Une fois la fiche de manœuvre jointe, l'opérateur pourra démarrer l'exécution de cette NAPT.</p>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end gap-4 pt-4 border-t">
                <a href="{{ route('operateurchef.notes.show', $note) }}" class="btn-senelec-outline">
                    Annuler
                </a>
                <button type="submit" class="btn-senelec">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ $note->fiche_manoeuvre ? 'Remplacer la fiche' : 'Joindre la fiche' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
