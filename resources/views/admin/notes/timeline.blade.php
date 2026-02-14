@extends('layouts.app')

@section('title', 'Timeline NAPT - ' . $note->numero_note)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.notes.show', $note) }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Timeline - {{ $note->numero_note }}</h1>
            <p class="text-gray-600">{{ $note->renseignementN ?? $note->motif ?? 'NAPT' }}</p>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card-senelec">
        <div class="flow-root">
            <ul class="-mb-8">
                @foreach($events as $index => $event)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    @switch($event['color'])
                                        @case('gray')
                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </span>
                                            @break
                                        @case('blue')
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                            </span>
                                            @break
                                        @case('teal')
                                            <span class="h-8 w-8 rounded-full bg-senelec-teal flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            @break
                                        @case('green')
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                            @break
                                        @case('purple')
                                            <span class="h-8 w-8 rounded-full bg-senelec-purple flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                                </svg>
                                            </span>
                                            @break
                                        @case('orange')
                                            <span class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                            </span>
                                            @break
                                        @case('red')
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </span>
                                            @break
                                        @default
                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                    @endswitch
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $event['action'] }}</p>
                                        <p class="text-sm text-gray-500">Par {{ $event['user'] }}</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        <time datetime="{{ $event['date'] }}">
                                            {{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }}
                                        </time>
                                        <br>
                                        <span class="text-xs">
                                            {{ \Carbon\Carbon::parse($event['date'])->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Durées -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Durées de traitement</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $dureeEtude = null;
                $dureeTravaux = null;
                $dureeTotale = null;
                
                // Durée entre création et remise étude
                if ($note->dre) {
                    $dureeEtude = $note->created_at->diffInDays($note->dre);
                }
                // Durée des travaux (ddt à drex ou dft)
                if ($note->ddt && $note->drex) {
                    $dureeTravaux = $note->ddt->diffInDays($note->drex);
                } elseif ($note->ddt && $note->dft) {
                    $dureeTravaux = $note->ddt->diffInDays($note->dft);
                }
                // Durée totale (création à exécution)
                if ($note->drex) {
                    $dureeTotale = $note->created_at->diffInDays($note->drex);
                }
            @endphp
            
            <div class="text-center p-4 bg-blue-100 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $dureeEtude !== null ? $dureeEtude . 'j' : '-' }}
                </div>
                <div class="text-sm text-gray-600">Durée étude</div>
            </div>
            
            <div class="text-center p-4 bg-senelec-teal/10 rounded-lg">
                <div class="text-2xl font-bold text-senelec-teal">
                    {{ $dureeTravaux !== null ? $dureeTravaux . 'j' : '-' }}
                </div>
                <div class="text-sm text-gray-600">Durée travaux</div>
            </div>
            
            <div class="text-center p-4 bg-senelec-purple/10 rounded-lg">
                <div class="text-2xl font-bold text-senelec-purple">
                    {{ $dureeTotale !== null ? $dureeTotale . 'j' : '-' }}
                </div>
                <div class="text-sm text-gray-600">Durée totale</div>
            </div>
        </div>
    </div>

    <!-- Statut actuel -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut actuel</h3>
        <div class="flex items-center gap-4">
            @switch($note->statut)
                @case('brouillon')
                    <span class="status-brouillon text-lg px-4 py-2">Brouillon</span>
                    <span class="text-gray-600">La note est en cours de rédaction</span>
                    @break
                @case('en étude')
                    <span class="status-en-etude text-lg px-4 py-2">En étude</span>
                    <span class="text-gray-600">La note est en cours d'étude</span>
                    @break
                @case('en attente de vérification')
                    <span class="status-en-attente text-lg px-4 py-2">En attente</span>
                    <span class="text-gray-600">En attente de vérification par le vérificateur</span>
                    @break
                @case('vérifiée')
                    <span class="status-verifiee text-lg px-4 py-2">Vérifiée</span>
                    <span class="text-gray-600">Vérifiée, en attente de validation</span>
                    @break
                @case('validée')
                    <span class="status-validee text-lg px-4 py-2">Validée</span>
                    <span class="text-gray-600">Validée, prête pour exécution</span>
                    @break
                @case('en cours d\'exécution')
                    <span class="status-en-execution text-lg px-4 py-2">En exécution</span>
                    <span class="text-gray-600">Exécution en cours par l'opérateur</span>
                    @break
                @case('exécutée')
                    <span class="status-executee text-lg px-4 py-2">Exécutée</span>
                    <span class="text-gray-600">Note complètement exécutée</span>
                    @break
                @case('retournée')
                    <span class="status-retournee text-lg px-4 py-2">Retournée</span>
                    <span class="text-gray-600">Note retournée pour correction</span>
                    @break
                @case('annulée')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-red-100 text-red-800">Annulée</span>
                    <span class="text-gray-600">Note annulée</span>
                    @break
            @endswitch
        </div>
    </div>
</div>
@endsection
