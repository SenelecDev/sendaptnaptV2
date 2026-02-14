@extends('layouts.app')

@section('title', 'Administration - Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Header avec filtres -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-12">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Bonjour, {{ Auth::user()->name }}</h1>
            <div class="flex flex-wrap gap-2 mt-2">
                @if(Auth::user()->groupe)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-senelec-purple text-white">
                        {{ Auth::user()->groupe->nom }}
                    </span>
                @endif
                @if(Auth::user()->roles->count() > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-senelec-orange text-white">
                        {{ Auth::user()->roles->first()->name }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500" style="margin-top: 15px;">{{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}</p>
        </div>
        
        <!-- Filtres -->
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1">
                    <label class="text-xs text-gray-600">Du</label>
                    <input type="date" name="date_debut" value="{{ $dateDebut }}" 
                           class="input-senelec text-xs py-1 px-2">
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs text-gray-600">Au</label>
                    <input type="date" name="date_fin" value="{{ $dateFin }}" 
                           class="input-senelec text-xs py-1 px-2">
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs text-gray-600">Semaine</label>
                    <select name="semaine" class="select-senelec text-xs py-1 px-2">
                        <option value="">Toutes</option>
                        @foreach($semainesDisponibles as $num => $label)
                            <option value="{{ $num }}" {{ $semaine == $num ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-senelec py-1 px-3 text-xs">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
            </form>
            <a href="{{ route('admin.dashboard.export') }}" class="btn-senelec-outline py-1 px-3 text-xs">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter
            </a>
        </div>
    </div>

    <!-- Stats Cards - DAPT -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            DAPTs
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="stat-card-purple">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-senelec-purple/10">
                        <svg class="w-6 h-6 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total DAPT</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['dapt_total']) }}</p>
                        <p class="text-xs text-senelec-purple">{{ $stats['dapt_periode'] }} cette période</p>
                    </div>
                </div>
            </div>

            <div class="stat-card-blue">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Créées</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['dapt_creees'] }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card-orange">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-senelec-orange/10">
                        <svg class="w-6 h-6 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">En cours</p>
                        <p class="text-2xl font-bold text-senelec-orange">{{ $stats['dapt_en_cours'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card-senelec p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Acceptées</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['dapt_acceptees'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card-senelec p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Retournées</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['dapt_retournees'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - NAPT -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            NAPTs
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="stat-card-magenta">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-2xl font-bold text-senelec-magenta">{{ number_format($stats['napt_total']) }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-gray-400">
                <p class="text-sm text-gray-500">Brouillon</p>
                <p class="text-2xl font-bold text-gray-600">{{ $stats['napt_brouillon'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500">En étude</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['napt_en_etude'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">En vérification</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['napt_en_attente_verif'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Vérifiées</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['napt_verifiees'] }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4">
            <div class="card-senelec p-4 border-l-4 border-amber-500">
                <p class="text-sm text-gray-500">En validation</p>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['napt_en_attente_valid'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Validées</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['napt_validees'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500">En exécution</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['napt_en_execution'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Exécutées</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['napt_executees'] }}</p>
            </div>
            <div class="card-senelec p-4 border-l-4 border-red-500">
                <p class="text-sm text-gray-500">Annulées</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['napt_annulees'] }}</p>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique Évolution mensuelle -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Évolution mensuelle
            </h3>
            <div class="h-64">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <!-- Graphique Répartition DAPT par statut -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Répartition DAPT par statut
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="daptStatusChart"></canvas>
            </div>
        </div>

        <!-- Graphique Répartition NAPT par statut -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Répartition NAPT par statut
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="naptStatusChart"></canvas>
            </div>
        </div>

        <!-- Graphique Comparatif DAPT vs NAPT -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Comparatif période
            </h3>
            <div class="h-64">
                <canvas id="comparatifChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Taux de traitement + Référentiels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Taux de traitement -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de traitement</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">DAPT acceptées</span>
                        <span class="font-semibold text-senelec-purple">{{ $tauxTraitement['dapt'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-senelec-purple to-senelec-magenta h-2.5 rounded-full" 
                             style="width: {{ $tauxTraitement['dapt'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">NAPT exécutées</span>
                        <span class="font-semibold text-senelec-teal">{{ $tauxTraitement['napt'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-senelec-teal to-senelec-blue h-2.5 rounded-full" 
                             style="width: {{ $tauxTraitement['napt'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs & Référentiels -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisateurs & Groupes</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-senelec-purple">{{ number_format($stats['users_total']) }}</p>
                    <p class="text-xs text-gray-500">Utilisateurs</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['users_actifs']) }}</p>
                    <p class="text-xs text-gray-500">Actifs</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-senelec-teal">{{ $stats['groupes_count'] }}</p>
                    <p class="text-xs text-gray-500">Groupes</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-senelec-orange">{{ $stats['interims_actifs'] }}</p>
                    <p class="text-xs text-gray-500">Intérims actifs</p>
                </div>
            </div>
        </div>

        <!-- Référentiels NAPT -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Référentiels NAPT</h3>
            <div class="grid grid-cols-3 gap-4">
                <a href="{{ route('admin.chargecons.index') }}" class="text-center p-3 bg-gray-50 rounded-lg hover:bg-senelec-purple/5 transition-colors">
                    <p class="text-2xl font-bold text-senelec-purple">{{ $stats['chargecons_count'] }}</p>
                    <p class="text-xs text-gray-500">Chargés Cons.</p>
                </a>
                <a href="{{ route('admin.correspondants.index') }}" class="text-center p-3 bg-gray-50 rounded-lg hover:bg-senelec-purple/5 transition-colors">
                    <p class="text-2xl font-bold text-senelec-magenta">{{ $stats['correspondants_count'] }}</p>
                    <p class="text-xs text-gray-500">Correspondants</p>
                </a>
                <a href="{{ route('admin.services.index') }}" class="text-center p-3 bg-gray-50 rounded-lg hover:bg-senelec-purple/5 transition-colors">
                    <p class="text-2xl font-bold text-senelec-teal">{{ $stats['servicedests_count'] }}</p>
                    <p class="text-xs text-gray-500">Services Dest.</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Dernières activités -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Dernières DAPT -->
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Dernières DAPT</h3>
                <a href="{{ route('admin.demandes.index') }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium transition-colors duration-200">
                    Voir tout →
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($dernieresDapt as $demande)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $demande->numero_demande }}</p>
                                <p class="text-sm text-gray-500">{{ $demande->demandeur?->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $demande->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="badge status-{{ Str::slug($demande->statut) }}">
                                {{ $demande->statut }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Aucune DAPT récente</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Dernières NAPT -->
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Dernières NAPT</h3>
                <a href="{{ route('admin.notes.index') }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium transition-colors duration-200">
                    Voir tout →
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($dernieresNapt as $note)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $note->numero_note }}</p>
                                <p class="text-sm text-gray-500">{{ $note->etabliPar?->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="badge status-{{ Str::slug($note->statut) }}">
                                {{ $note->statut }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>Aucune NAPT récente</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Observations non traitées -->
    @if($stats['observations_non_traitees'] > 0)
    <div class="card-senelec border-l-4 border-senelec-orange">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-senelec-orange/5">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Observations à traiter
                <span class="ml-2 badge badge-orange">{{ $stats['observations_non_traitees'] }}</span>
            </h3>
            <a href="{{ route('admin.observations.index') }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium transition-colors duration-200">
                Voir tout →
            </a>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($observationsRecentes as $observation)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $observation->sujet }}</p>
                            <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($observation->description, 100) }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs text-gray-400">Par {{ $observation->user?->full_name ?? 'Anonyme' }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-400">{{ $observation->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.observations.show', $observation) }}" class="btn-senelec-outline py-1 px-3 text-xs">
                            Traiter
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Actions rapides -->
    <div class="card-senelec p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="{{ route('admin.users.index') }}" class="quick-action">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Utilisateurs</span>
            </a>
            <a href="{{ route('admin.groupes.index') }}" class="quick-action">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Groupes</span>
            </a>
            <a href="{{ route('admin.chargecons.create') }}" class="quick-action">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Chargé Cons.</span>
            </a>
            <a href="{{ route('admin.correspondants.create') }}" class="quick-action">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Correspondant</span>
            </a>
            <a href="{{ route('admin.services.create') }}" class="quick-action">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Service Dest.</span>
            </a>
            <a href="{{ route('admin.absences.create') }}" class="quick-action">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Intérim</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
    // Données pour les graphiques
    const evolutionData = @json($evolutionMensuelle);
    const daptStats = {
        creees: {{ $stats['dapt_creees'] }},
        en_cours: {{ $stats['dapt_en_cours'] }},
        acceptees: {{ $stats['dapt_acceptees'] }},
        retournees: {{ $stats['dapt_retournees'] }}
    };
    const naptStats = {
        brouillon: {{ $stats['napt_brouillon'] }},
        en_etude: {{ $stats['napt_en_etude'] }},
        en_verif: {{ $stats['napt_en_attente_verif'] }},
        verifiees: {{ $stats['napt_verifiees'] }},
        en_valid: {{ $stats['napt_en_attente_valid'] }},
        validees: {{ $stats['napt_validees'] }},
        en_exec: {{ $stats['napt_en_execution'] }},
        executees: {{ $stats['napt_executees'] }},
        annulees: {{ $stats['napt_annulees'] }}
    };

    // Couleurs SENELEC
    const colors = {
        purple: '#2B1444',
        magenta: '#B3006C',
        teal: '#0A91A3',
        orange: '#E87400',
        blue: '#0D1CB0',
        green: '#10B981',
        red: '#EF4444',
        yellow: '#F59E0B',
        gray: '#6B7280',
        indigo: '#6366F1',
        amber: '#F59E0B',
        emerald: '#10B981'
    };

    // 1. Graphique Évolution mensuelle (Line Chart)
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: evolutionData.labels,
            datasets: [{
                label: 'DAPT',
                data: evolutionData.dapt,
                borderColor: colors.purple,
                backgroundColor: colors.purple + '20',
                tension: 0.4,
                fill: true
            }, {
                label: 'NAPT',
                data: evolutionData.napt,
                borderColor: colors.magenta,
                backgroundColor: colors.magenta + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // 2. Graphique Répartition DAPT (Doughnut Chart)
    new Chart(document.getElementById('daptStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Créées', 'En cours', 'Acceptées', 'Retournées'],
            datasets: [{
                data: [daptStats.creees, daptStats.en_cours, daptStats.acceptees, daptStats.retournees],
                backgroundColor: [colors.blue, colors.orange, colors.green, colors.red],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });

    // 3. Graphique Répartition NAPT (Doughnut Chart)
    new Chart(document.getElementById('naptStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Brouillon', 'En étude', 'En vérif.', 'Vérifiées', 'En valid.', 'Validées', 'En exec.', 'Exécutées', 'Annulées'],
            datasets: [{
                data: [
                    naptStats.brouillon, naptStats.en_etude, naptStats.en_verif, 
                    naptStats.verifiees, naptStats.en_valid, naptStats.validees,
                    naptStats.en_exec, naptStats.executees, naptStats.annulees
                ],
                backgroundColor: [
                    colors.gray, colors.purple, colors.yellow, colors.indigo,
                    colors.amber, colors.emerald, colors.blue, colors.green, colors.red
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 10
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // 4. Graphique Comparatif (Bar Chart)
    new Chart(document.getElementById('comparatifChart'), {
        type: 'bar',
        data: {
            labels: ['Total', 'Cette période'],
            datasets: [{
                label: 'DAPT',
                data: [{{ $stats['dapt_total'] }}, {{ $stats['dapt_periode'] }}],
                backgroundColor: colors.purple,
                borderRadius: 8
            }, {
                label: 'NAPT',
                data: [{{ $stats['napt_total'] }}, {{ $stats['napt_periode'] }}],
                backgroundColor: colors.magenta,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
