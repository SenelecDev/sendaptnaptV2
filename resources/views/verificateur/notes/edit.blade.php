@extends('layouts.app')

@section('title', 'Vérifier NAPT ' . $note->numero_note)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Vérifier NAPT {{ $note->numero_note }}</h1>
                <p class="text-gray-600 mt-1">Vérifiez les informations et validez ou retournez la note.</p>
            </div>
            <a href="{{ route('verificateur.notes.index') }}" class="btn-senelec-outline">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la liste
            </a>
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
                        <dt class="text-sm font-medium text-gray-500">Demande associée</dt>
                        <dd class="mt-1 text-gray-900">{{ $note->demande->numero_demande ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Semaine</dt>
                        <dd class="mt-1 text-gray-900">S{{ $note->numero_semaine }}</dd>
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

            <!-- Renseignements -->
            @if($note->renseignementN)
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Renseignements complémentaires</h3>
                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $note->renseignementN }}</p>
            </div>
            @endif

            <!-- Document joint -->
            @if($note->document)
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Document joint (Étude)</h3>
                <a href="{{ $note->document_url }}" target="_blank" class="inline-flex items-center text-senelec-purple hover:underline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger le document d'étude
                </a>
            </div>
            @endif
        </div>

        <!-- Sidebar - Actions de vérification -->
        <div class="space-y-6">
            <!-- Contacts -->
            <div class="card-senelec p-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Contacts</h3>
                <div class="space-y-3 text-sm">
                    @if($note->chargecons && $note->chargecons->count() > 0)
                    <div>
                        <dt class="text-gray-500 font-medium">Chargés de consignation</dt>
                        <dd class="mt-1">
                            @foreach($note->chargecons as $cc)
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

                    @if($note->services && $note->services->count() > 0)
                    <div>
                        <dt class="text-gray-500 font-medium">Destinataires</dt>
                        <dd class="mt-1">
                            @foreach($note->services as $service)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $service->nom }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action: Vérifier -->
            <div class="card-senelec p-6 bg-green-50 border-green-200">
                <h3 class="text-md font-semibold text-green-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Valider la vérification
                </h3>
                <p class="text-sm text-green-700 mb-4">Confirmez que toutes les informations sont correctes.</p>
                <form action="{{ route('verificateur.notes.update', $note) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="verifier">
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Vérifier la note
                    </button>
                </form>
            </div>

            <!-- Action: Retourner -->
            <div class="card-senelec p-6 bg-red-50 border-red-200">
                <h3 class="text-md font-semibold text-red-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Retourner la note
                </h3>
                <p class="text-sm text-red-700 mb-4">Indiquez le motif du retour.</p>
                <form action="{{ route('verificateur.notes.update', $note) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="retourner">
                    <div class="mb-4">
                        <label for="motif" class="block text-sm font-medium text-red-800 mb-1">Motif du retour *</label>
                        <textarea name="motif" id="motif" rows="3" 
                                  class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                  placeholder="Expliquez pourquoi la note doit être corrigée..." required>{{ old('motif') }}</textarea>
                        @error('motif')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Retourner la note
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
