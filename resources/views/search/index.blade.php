@extends('layouts.app')

@section('title', 'Recherche - ' . $query)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">
                <svg class="w-7 h-7 inline-block mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Résultats de recherche
            </h1>
            @if($query)
                <p class="text-gray-600 mt-1">
                    {{ $totalResults }} résultat(s) pour "<span class="font-semibold text-senelec-purple">{{ $query }}</span>"
                </p>
            @endif
        </div>
    </div>

    <!-- Formulaire de recherche -->
    <div class="card-senelec p-4">
        <form action="{{ route('search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="search" 
                       name="q" 
                       value="{{ $query }}" 
                       placeholder="Rechercher par numéro, désignation, demandeur..." 
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-senelec-purple focus:border-senelec-purple"
                       autofocus>
            </div>
            <button type="submit" class="btn-senelec px-6">
                Rechercher
            </button>
        </form>
    </div>

    @if($query)
        <!-- Résultats DAPT -->
        @if($results['demandes']->count() > 0)
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-senelec-purple/10 to-transparent">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    DAPT ({{ $results['demandes']->count() }})
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($results['demandes'] as $demande)
                <a href="{{ route('demandeur.demandes.show', $demande) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-senelec-purple">{{ $demande->numero_demande }}</span>
                                @switch($demande->statut)
                                    @case('créée')
                                        <span class="badge badge-info">Créée</span>
                                        @break
                                    @case('en cours de traitement')
                                        <span class="badge badge-warning">En cours</span>
                                        @break
                                    @case('acceptée')
                                        <span class="badge badge-success">Acceptée</span>
                                        @break
                                    @case('retournée')
                                        <span class="badge badge-danger">Retournée</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $demande->statut }}</span>
                                @endswitch
                            </div>
                            <p class="text-sm text-gray-600 mt-1 truncate">{{ $demande->designation }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                Par {{ $demande->demandeur->name ?? 'N/A' }} • {{ $demande->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Résultats NAPT -->
        @if($results['notes']->count() > 0)
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-senelec-orange/10 to-transparent">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    NAPT ({{ $results['notes']->count() }})
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($results['notes'] as $note)
                <a href="{{ route('desa.notes.show', $note) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-senelec-orange">{{ $note->numero_note }}</span>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">S{{ $note->numero_semaine }}</span>
                                @switch($note->statut)
                                    @case('validée')
                                        <span class="badge badge-success">Validée</span>
                                        @break
                                    @case('executée')
                                        <span class="badge badge-success">Exécutée</span>
                                        @break
                                    @case('en attente de vérification')
                                        <span class="badge badge-warning">À vérifier</span>
                                        @break
                                    @case('retournée')
                                        <span class="badge badge-danger">Retournée</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $note->statut }}</span>
                                @endswitch
                            </div>
                            <p class="text-sm text-gray-600 mt-1 truncate">
                                DAPT: {{ $note->demande->numero_demande ?? 'N/A' }} - {{ $note->demande->designation ?? '' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Établie par {{ $note->etabliPar->name ?? 'N/A' }} • {{ $note->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Résultats Utilisateurs (admin) -->
        @if($results['users']->count() > 0)
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-senelec-teal/10 to-transparent">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Utilisateurs ({{ $results['users']->count() }})
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($results['users'] as $user)
                <a href="{{ route('admin.users.show', $user) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-4">
                        @if($user->photo_url)
                            <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-senelec-purple/10 flex items-center justify-center text-senelec-purple font-bold">
                                {{ $user->initials }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->matricule }} • {{ $user->email }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Aucun résultat -->
        @if($totalResults === 0)
        <div class="card-senelec p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun résultat trouvé</h3>
            <p class="text-gray-500 mb-4">Aucun résultat pour "{{ $query }}"</p>
            <p class="text-sm text-gray-400">Essayez avec d'autres mots-clés ou vérifiez l'orthographe.</p>
        </div>
        @endif
    @else
        <!-- Pas de recherche -->
        <div class="card-senelec p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Rechercher dans l'application</h3>
            <p class="text-gray-500">Entrez un numéro de DAPT, NAPT, un nom de demandeur ou une désignation.</p>
        </div>
    @endif
</div>
@endsection
