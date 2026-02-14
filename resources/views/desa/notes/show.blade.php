@extends('layouts.app')

@section('title', 'NAPT ' . $note->numero_note)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">NAPT {{ $note->numero_note }}</h1>
                <div class="mt-2 flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($note->statut === 'brouillon') bg-gray-100 text-gray-800
                        @elseif($note->statut === 'en étude') bg-blue-100 text-blue-800
                        @elseif($note->statut === 'en attente de vérification') bg-yellow-100 text-yellow-800
                        @elseif($note->statut === 'vérifiée') bg-indigo-100 text-indigo-800
                        @elseif($note->statut === 'en attente de validation') bg-orange-100 text-orange-800
                        @elseif($note->statut === 'validée') bg-green-100 text-green-800
                        @elseif($note->statut === 'en cours d\'exécution') bg-purple-100 text-purple-800
                        @elseif($note->statut === 'executée') bg-green-200 text-green-900
                        @elseif($note->statut === 'retournée') bg-red-100 text-red-800
                        @elseif($note->statut === 'annulée') bg-red-200 text-red-900
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($note->statut) }}
                    </span>
                    @if($note->demande)
                        <span class="text-sm text-gray-500">
                            Demande: <a href="{{ route('desa.demandes.show', $note->demande) }}" class="text-senelec-purple hover:underline">{{ $note->demande->numero_demande }}</a>
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-3">
                @if(in_array($note->statut, ['brouillon', 'en étude', 'retournée']))
                    <a href="{{ route('desa.notes.edit', $note) }}" class="btn-senelec">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                @endif
                @if(!in_array($note->statut, ['annulée', 'executée']))
                    <button type="button" onclick="document.getElementById('modal-annuler').classList.remove('hidden')" class="btn-danger">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Annuler NAPT
                    </button>
                @endif
                <a href="{{ route('desa.notes.index') }}" class="btn-senelec-outline">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenu principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations générales -->
            <div class="card-senelec p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informations générales
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Numéro NAPT</dt>
                        <dd class="mt-1 text-gray-900 font-semibold">{{ $note->numero_note }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de saisie</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->date ? $note->date->format('d/m/Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Semaine</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->numero_semaine }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Établi par</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->etabliPar->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Périodes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card-senelec p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Période travaux
                    </h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Début</dt>
                            <dd class="text-gray-900 font-medium">{{ $note->ddt ? $note->ddt->format('d/m/Y H:i') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Fin</dt>
                            <dd class="text-gray-900 font-medium">{{ $note->dft ? $note->dft->format('d/m/Y H:i') : '-' }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="card-senelec p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Période exploitation
                    </h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Retrait</dt>
                            <dd class="text-gray-900 font-medium">{{ $note->dre ? $note->dre->format('d/m/Y H:i') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Remise</dt>
                            <dd class="text-gray-900 font-medium">{{ $note->drex ? $note->drex->format('d/m/Y H:i') : '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Motif de retour -->
            @if($note->statut === 'retournée' && $note->motif)
            <div class="card-senelec p-6 border-red-200 bg-red-50">
                <h3 class="text-md font-semibold text-red-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Motif du retour
                </h3>
                <p class="text-red-800 bg-red-100 p-4 rounded-lg">{{ $note->motif }}</p>
            </div>
            @endif

            <!-- Renseignements -->
            @if($note->renseignementN)
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Renseignements complémentaires
                </h3>
                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $note->renseignementN }}</p>
            </div>
            @endif

            <!-- Document joint -->
            @if($note->document)
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    Document joint (Étude)
                </h3>
                <a href="{{ $note->document_url }}" target="_blank" class="inline-flex items-center text-senelec-purple hover:underline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger le document
                </a>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Demande associée -->
            @if($note->demande)
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Demande associée
                </h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Numéro</dt>
                        <dd class="text-gray-900 font-medium">{{ $note->demande->numero_demande }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Demandeur</dt>
                        <dd class="text-gray-900">{{ $note->demande->demandeur->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Désignation</dt>
                        <dd class="text-gray-700 text-xs">{{ Str::limit($note->demande->designation, 100) }}</dd>
                    </div>
                </dl>
                @if($note->demande->pdf_path)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ Storage::url($note->demande->pdf_path) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-senelec-purple hover:bg-senelec-purple/90 text-white text-sm font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimer DAPT
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Contacts -->
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Contacts
                </h3>
                <div class="space-y-4 text-sm">
                    @if($note->chargesCons && $note->chargesCons->count() > 0)
                    <div>
                        <dt class="text-gray-500 font-medium">Chargés de consignation</dt>
                        <dd class="mt-1">
                            @foreach($note->chargesCons as $cc)
                                <span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $cc->nom }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif

                    @if($note->correspondants && $note->correspondants->count() > 0)
                    <div>
                        <dt class="text-gray-500 font-medium">Correspondants</dt>
                        <dd class="mt-1">
                            @foreach($note->correspondants as $corr)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $corr->nom }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif

                    @if($note->servicesDest && $note->servicesDest->count() > 0)
                    <div>
                        <dt class="text-gray-500 font-medium">Destinataires</dt>
                        <dd class="mt-1">
                            @foreach($note->servicesDest as $service)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $service->nom }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Étude approfondie -->
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Étude approfondie
                </h3>
                <p class="text-sm">
                    @if($note->etude === 'oui')
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Oui - Étude requise
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Non
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Prévisualisation PDF NAPT -->
    <div class="mt-8">
        <div class="card-senelec overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Prévisualisation NAPT
                </h3>
                <a href="{{ route('pdf.napt.view', $note) }}" target="_blank" class="btn-senelec-outline text-sm py-2 px-3">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ouvrir dans un nouvel onglet
                </a>
            </div>
            <iframe src="{{ route('pdf.napt.view', $note) }}" class="w-full border-0" style="height: 900px;"></iframe>
        </div>
    </div>
</div>

<!-- Modal Annuler NAPT -->
@if(!in_array($note->statut, ['annulée', 'executée']))
<div id="modal-annuler" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Annuler NAPT {{ $note->numero_note }}
                </h3>
                <button type="button" onclick="document.getElementById('modal-annuler').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-red-700">
                    <strong>Attention :</strong> Cette action est irréversible. La NAPT sera définitivement annulée.
                </p>
            </div>
            
            <form action="{{ route('desa.notes.annuler', $note) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="commentanul" class="label">Motif d'annulation <span class="text-red-500">*</span></label>
                    <textarea name="commentanul" id="commentanul" rows="4" 
                              class="input-senelec w-full" 
                              placeholder="Expliquez la raison de l'annulation (minimum 10 caractères)"
                              required minlength="10"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('modal-annuler').classList.add('hidden')" class="btn-senelec-outline">
                        Fermer
                    </button>
                    <button type="submit" class="btn-danger">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
