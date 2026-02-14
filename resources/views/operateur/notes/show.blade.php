@extends('layouts.app')

@section('title', 'NAPT ' . $note->numero_note)

@section('content')
<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('operateur.notes.index') }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">NAPT {{ $note->numero_note }}</h1>
            <p class="text-gray-600">Semaine {{ $note->numero_semaine }} - {{ $note->date?->format('d/m/Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(in_array($note->statut, ['validée', 'en cours d\'exécution']) && $note->fiche_manoeuvre)
                <a href="{{ route('operateur.notes.edit', $note) }}" class="btn-senelec">
                    @if($note->statut === 'validée')
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        </svg>
                        Démarrer l'exécution
                    @else
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terminer l'exécution
                    @endif
                </a>
                <button type="button" onclick="document.getElementById('modalAnnuler').classList.remove('hidden')" class="btn-senelec-outline text-red-600 border-red-600 hover:bg-red-50">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Annuler la note
                </button>
            @endif
            <a href="{{ route('pdf.napt.view', $note) }}" target="_blank" class="btn-senelec-outline">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Voir PDF
            </a>
        </div>
    </div>

    <!-- Statut et Fiche manœuvre -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Statut -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut de la note</h3>
            @php
                $statusColors = [
                    'validée' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'en cours d\'exécution' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'executée' => 'bg-green-100 text-green-800 border-green-200',
                    'annulée' => 'bg-red-100 text-red-800 border-red-200',
                ];
                $colorClass = $statusColors[$note->statut] ?? 'bg-gray-100 text-gray-800 border-gray-200';
            @endphp
            <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full border {{ $colorClass }}">
                {{ ucfirst($note->statut) }}
            </span>
            
            <div class="mt-4 space-y-2 text-sm">
                @if($note->validePar)
                    <p><span class="font-medium">Validée par :</span> {{ $note->validePar->full_name }}</p>
                @endif
                @if($note->enCoursExecution)
                    <p><span class="font-medium">Démarrée par :</span> {{ $note->enCoursExecution->full_name }}</p>
                    <p><span class="font-medium">Démarrée le :</span> {{ $note->dre?->format('d/m/Y H:i') }}</p>
                @endif
                @if($note->execute)
                    <p><span class="font-medium">Exécutée par :</span> {{ $note->execute->full_name }}</p>
                    <p><span class="font-medium">Terminée le :</span> {{ $note->drex?->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <!-- Fiche manœuvre -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Fiche de manœuvre</h3>
            @if($note->fiche_manoeuvre)
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-medium text-green-800">Fiche jointe</p>
                            <p class="text-sm text-green-600">Prête pour l'exécution</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($note->fiche_manoeuvre) }}" target="_blank" class="btn-senelec-sm">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Voir
                    </a>
                </div>
            @else
                <div class="flex items-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <svg class="w-8 h-8 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800">En attente de fiche</p>
                        <p class="text-sm text-yellow-600">L'opérateur chef doit d'abord joindre la fiche de manœuvre</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- DAPT Associée -->
    @if($note->demande)
    <div class="card-senelec p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">DAPT Associée</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500">Numéro DAPT</p>
                <p class="font-medium">{{ $note->demande->numero_demande }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Demandeur</p>
                <p class="font-medium">{{ $note->demande->demandeur?->full_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Lieu d'exécution</p>
                <p class="font-medium">{{ $note->demande->lieu_execution ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Désignation</p>
                <p class="font-medium">{{ Str::limit($note->demande->designation, 50) ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Dates d'exécution si terminée -->
    @if($note->statut === 'executée' && ($note->ddt || $note->dft))
    <div class="card-senelec p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates d'exécution</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Début des travaux</p>
                <p class="font-medium">{{ $note->ddt?->format('d/m/Y H:i') ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Fin des travaux</p>
                <p class="font-medium">{{ $note->dft?->format('d/m/Y H:i') ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Aperçu NAPT - Pleine largeur -->
<div class="w-full px-4 pb-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Aperçu du document NAPT</h3>
        </div>
        <iframe src="{{ route('pdf.napt.view', $note) }}?view=operateur" class="border-0" style="width: 100%; height: 900px;"></iframe>
    </div>
</div>

<!-- Modal Annulation -->
<div id="modalAnnuler" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center gap-3 mb-4">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Annuler cette note</h3>
            </div>
            <form action="{{ route('operateur.notes.annuler', $note) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="commentanul" class="block text-sm font-medium text-gray-700 mb-2">Motif d'annulation <span class="text-red-500">*</span></label>
                    <textarea name="commentanul" id="commentanul" rows="4" class="input w-full" placeholder="Indiquez le motif de l'annulation..." required></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="document.getElementById('modalAnnuler').classList.add('hidden')" class="btn-senelec-outline">
                        Fermer
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
