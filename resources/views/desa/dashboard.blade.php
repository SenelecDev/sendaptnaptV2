@extends('layouts.app')

@section('title', 'Tableau de bord - DESA')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
            <p class="text-gray-600" style="margin-top: 15px;">Vue d'ensemble des Demandes et Notes d'Arrêt Pour Travaux</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <div class="text-sm text-gray-500">
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
            </div>
            <a href="{{ route('desa.notes.create') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle NAPT
        </a>
        </div>
    </div>

    <!-- Compteurs DAPT -->
    <div class="card-senelec p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            DAPTs
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('desa.demandes.index', ['statut' => 'creee']) }}" class="stat-card-blue hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $demandesStats['recues'] }}</div>
                <div class="stat-label">Reçues</div>
            </a>
            <a href="{{ route('desa.demandes.index', ['statut' => 'en_cours']) }}" class="stat-card-orange hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $demandesStats['en_cours'] }}</div>
                <div class="stat-label">En cours</div>
            </a>
            <a href="{{ route('desa.demandes.index', ['statut' => 'retournee']) }}" class="card-senelec p-4 border-l-4 border-red-500 hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value text-red-600">{{ $demandesStats['retournees'] }}</div>
                <div class="stat-label">Retournées</div>
            </a>
            <a href="{{ route('desa.demandes.index', ['statut' => 'acceptee']) }}" class="stat-card-green hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $demandesStats['acceptees'] }}</div>
                <div class="stat-label">Acceptées</div>
            </a>
        </div>
    </div>

    <!-- Compteurs NAPT -->
    <div class="card-senelec p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            NAPTs
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            <a href="{{ route('desa.notes.index', ['statut' => 'retournee']) }}" class="card-senelec p-4 border-l-4 border-red-500 hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value text-red-600">{{ $notesStats['retournees'] }}</div>
                <div class="stat-label">Retournées</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'en_attente_verification']) }}" class="stat-card-orange hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $notesStats['en_attente_verification'] }}</div>
                <div class="stat-label">En vérification</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'verifiee']) }}" class="stat-card-teal hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $notesStats['verifiees'] }}</div>
                <div class="stat-label">Vérifiées</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'validee']) }}" class="stat-card-green hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $notesStats['validees'] }}</div>
                <div class="stat-label">Validées</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'en_cours_execution']) }}" class="card-senelec p-4 border-l-4 border-yellow-500 hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value text-yellow-600">{{ $notesStats['en_cours_execution'] }}</div>
                <div class="stat-label">En exécution</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'executee']) }}" class="card-senelec p-4 border-l-4 border-emerald-500 hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value text-emerald-600">{{ $notesStats['executees'] }}</div>
                <div class="stat-label">Exécutées</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'annulee']) }}" class="card-senelec p-4 border-l-4 border-gray-500 hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value text-gray-600">{{ $notesStats['annulees'] }}</div>
                <div class="stat-label">Annulées</div>
            </a>
            <a href="{{ route('desa.notes.index', ['statut' => 'en_etude']) }}" class="stat-card-blue hover:scale-105 transition-transform cursor-pointer">
                <div class="stat-value">{{ $notesStats['en_etude'] }}</div>
                <div class="stat-label">En étude</div>
            </a>
        </div>
    </div>

    <!-- Outils de Gestion -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Outils NAPT -->
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-senelec-orange/10 to-transparent">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="text-xl mr-2">⚡</span>
                    Outils de Gestion NAPT
                </h3>
            </div>
            <div class="p-4 space-y-4">
                <!-- Gestion NAPT -->
                <a href="{{ route('desa.notes.index') }}" class="block p-4 rounded-lg border border-gray-200 hover:border-senelec-orange hover:bg-senelec-orange/5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-senelec-orange/10 flex items-center justify-center group-hover:bg-senelec-orange/20 transition-colors">
                            <svg class="w-5 h-5 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Gestion des NAPT</p>
                            <p class="text-sm text-gray-500">Filtrer par date, semaine, statut ou groupe et imprimer en lot.</p>
                        </div>
                    </div>
                </a>
                
                <!-- Diffusion Hebdomadaire -->
                <a href="{{ route('desa.diffusion') }}" class="block p-4 rounded-lg border border-gray-200 hover:border-senelec-teal hover:bg-senelec-teal/5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-senelec-teal/10 flex items-center justify-center group-hover:bg-senelec-teal/20 transition-colors">
                            <svg class="w-5 h-5 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Diffusion Hebdomadaire</p>
                            <p class="text-sm text-gray-500">Gérer et envoyer la diffusion hebdomadaire des NAPT.</p>
                        </div>
                    </div>
                </a>
                
                <!-- Raccourcis Rapides NAPT -->
                <div class="pt-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">🚀 Raccourcis</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('desa.notes.create') }}" class="px-3 py-1.5 text-sm bg-senelec-orange/10 text-senelec-orange rounded-lg hover:bg-senelec-orange/20 transition-colors">
                            + Nouvelle NAPT
                        </a>
                        <a href="{{ route('desa.notes.index', ['statut' => 'brouillon']) }}" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                            Brouillons
                        </a>
                        <a href="{{ route('desa.notes.index', ['statut' => 'retournee']) }}" class="px-3 py-1.5 text-sm bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                            Retournées
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outils DAPT -->
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-senelec-purple/10 to-transparent">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="text-xl mr-2">⚡</span>
                    Outils de Gestion DAPT
                </h3>
            </div>
            <div class="p-4 space-y-4">
                <!-- Filtrage & Impression DAPT -->
                <a href="{{ route('desa.demandes.index') }}" class="block p-4 rounded-lg border border-gray-200 hover:border-senelec-purple hover:bg-senelec-purple/5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-senelec-purple/10 flex items-center justify-center group-hover:bg-senelec-purple/20 transition-colors">
                            <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Filtrage & Impression DAPT</p>
                            <p class="text-sm text-gray-500">Filtrer les DAPT par date, semaine, statut ou groupe et les imprimer en lot.</p>
                        </div>
                    </div>
                </a>
                
                <!-- DAPT Validées -->
                <a href="{{ route('desa.demandes.index', ['statut' => 'acceptee']) }}" class="block p-4 rounded-lg border border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">DAPT Acceptées</p>
                            <p class="text-sm text-gray-500">Consulter et gérer la liste des DAPT acceptées avec impression sélective.</p>
                        </div>
                    </div>
                </a>
                
                <!-- Raccourcis Rapides DAPT -->
                <div class="pt-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">⚡ Raccourcis Rapides</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('desa.demandes.index', ['statut' => 'creee']) }}" class="px-3 py-1.5 text-sm bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                            À traiter
                        </a>
                        <a href="{{ route('desa.demandes.index', ['statut' => 'en_cours']) }}" class="px-3 py-1.5 text-sm bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition-colors">
                            En cours
                        </a>
                        <a href="{{ route('desa.demandes.index', ['statut' => 'retournee']) }}" class="px-3 py-1.5 text-sm bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                            Retournées
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique Évolution NAPT -->
        <div class="card-senelec p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Évolution des NAPT
                </h3>
                <!-- Sélecteur de période -->
                <div class="flex gap-2">
                    <a href="{{ route('desa.dashboard', ['periode' => 'semaine']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'semaine' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semaine
                    </a>
                    <a href="{{ route('desa.dashboard', ['periode' => 'mois']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'mois' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mois
                    </a>
                    <a href="{{ route('desa.dashboard', ['periode' => 'annee']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'annee' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Année
                    </a>
                </div>
            </div>
            <div class="h-72">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <!-- Graphique Répartition NAPT par statut (Doughnut) -->
        <div class="card-senelec p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    Répartition NAPT par statut
                </h3>
                <!-- Sélecteur de période -->
                <div class="flex gap-2">
                    <a href="{{ route('desa.dashboard', ['periode' => 'semaine']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'semaine' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semaine
                    </a>
                    <a href="{{ route('desa.dashboard', ['periode' => 'mois']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'mois' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mois
                    </a>
                    <a href="{{ route('desa.dashboard', ['periode' => 'annee']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'annee' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Année
                    </a>
                </div>
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Graphique Barres par statut -->
        <div class="card-senelec p-6 lg:col-span-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Détail NAPT par période - 
                    @if($periode === 'semaine')
                        7 derniers jours
                    @elseif($periode === 'mois')
                        4 dernières semaines
                    @else
                        12 derniers mois
                    @endif
                </h3>
            </div>
            <div class="h-80">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Groupes et dernières demandes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Groupes créant des DAPT -->
        @if(count($topGroupes) > 0)
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Top Groupes - DAPT
                </h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($topGroupes as $index => $groupe)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-200 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-500')) }} font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="w-10 h-10 rounded-full bg-senelec-purple/10 flex items-center justify-center text-senelec-purple font-bold">
                                {{ substr($groupe->nom, 0, 2) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $groupe->nom }}</p>
                                <p class="text-xs text-gray-500">{{ $groupe->code ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold {{ $index === 0 ? 'text-yellow-600' : 'text-senelec-purple' }}">{{ $groupe->demandes_count }}</span>
                            <p class="text-xs text-gray-500">demandes</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Dernières demandes reçues -->
        <div class="card-senelec">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Demandes à traiter
                </h3>
                <a href="{{ route('desa.demandes.index', ['statut' => 'creee']) }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium transition-colors duration-200">
                    Voir toutes →
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($dernieresDemandes as $demande)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3">
                                    <p class="font-mono font-medium text-senelec-purple">{{ $demande->numero_demande }}</p>
                                    <span class="badge badge-info">Reçue</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 truncate">{{ $demande->designation }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Par {{ $demande->demandeur->name ?? 'N/A' }} • {{ $demande->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <a href="{{ route('desa.demandes.edit', $demande) }}" 
                               class="ml-4 btn-senelec py-1 px-3 text-sm">
                                Traiter
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Aucune demande en attente</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Dernières notes -->
    <div class="card-senelec">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Dernières notes
            </h3>
            <a href="{{ route('desa.notes.index') }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium transition-colors duration-200">
                Voir toutes →
            </a>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($dernieresNotes as $note)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <p class="font-mono font-medium text-senelec-orange">{{ $note->numero_note }}</p>
                                @switch($note->statut)
                                    @case('brouillon')
                                        <span class="badge badge-secondary">Brouillon</span>
                                        @break
                                    @case('en étude')
                                        <span class="badge badge-info">En étude</span>
                                        @break
                                    @case('en attente de vérification')
                                        <span class="badge badge-warning">À vérifier</span>
                                        @break
                                    @case('vérifiée')
                                        <span class="badge badge-info">Vérifiée</span>
                                        @break
                                    @case('validée')
                                        <span class="badge badge-success">Validée</span>
                                        @break
                                    @case('en cours d\'exécution')
                                        <span class="badge badge-warning">En exécution</span>
                                        @break
                                    @case('executée')
                                        <span class="badge badge-success">Exécutée</span>
                                        @break
                                    @case('retournée')
                                        <span class="badge badge-danger">Retournée</span>
                                        @break
                                    @case('annulée')
                                        <span class="badge badge-secondary">Annulée</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $note->statut }}</span>
                                @endswitch
                            </div>
                            <p class="text-sm text-gray-600 mt-1 truncate">DAPT: {{ $note->demande->numero_demande ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $note->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('desa.notes.show', $note) }}" 
                           class="ml-4 text-senelec-purple hover:text-senelec-magenta transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>Aucune note récente</p>
                    <a href="{{ route('desa.notes.create') }}" class="mt-4 inline-flex btn-senelec">
                        Créer une note
                    </a>
                </div>
            @endforelse
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
    const graphData = @json($graphData);
    const notesStats = {
        brouillon: {{ $notesStats['brouillon'] }},
        en_etude: {{ $notesStats['en_etude'] }},
        en_attente_verification: {{ $notesStats['en_attente_verification'] }},
        verifiees: {{ $notesStats['verifiees'] }},
        validees: {{ $notesStats['validees'] }},
        executees: {{ $notesStats['executees'] }},
        retournees: {{ $notesStats['retournees'] }},
        annulees: {{ $notesStats['annulees'] }}
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
        emerald: '#059669'
    };

    // 1. Graphique Évolution (Line Chart)
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: graphData.labels,
            datasets: [
                {
                    label: 'Brouillons',
                    data: graphData.datasets.brouillon,
                    borderColor: colors.gray,
                    backgroundColor: colors.gray + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'En étude',
                    data: graphData.datasets.en_etude,
                    borderColor: colors.blue,
                    backgroundColor: colors.blue + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'En vérification',
                    data: graphData.datasets.en_attente_verification,
                    borderColor: colors.orange,
                    backgroundColor: colors.orange + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Vérifiées',
                    data: graphData.datasets.verifiees,
                    borderColor: colors.teal,
                    backgroundColor: colors.teal + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'En validation',
                    data: graphData.datasets.en_attente_validation,
                    borderColor: colors.yellow,
                    backgroundColor: colors.yellow + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Validées',
                    data: graphData.datasets.validees,
                    borderColor: colors.green,
                    backgroundColor: colors.green + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'En exécution',
                    data: graphData.datasets.en_cours_execution,
                    borderColor: colors.magenta,
                    backgroundColor: colors.magenta + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Exécutées',
                    data: graphData.datasets.executees,
                    borderColor: colors.emerald,
                    backgroundColor: colors.emerald + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Retournées',
                    data: graphData.datasets.retournees,
                    borderColor: colors.red,
                    backgroundColor: colors.red + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Annulées',
                    data: graphData.datasets.annulees,
                    borderColor: '#9CA3AF',
                    backgroundColor: '#9CA3AF20',
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
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

    // 2. Graphique Répartition par statut (Doughnut) - Basé sur la période
    // Calculer les totaux par période à partir des datasets
    const statusTotals = {
        brouillon: graphData.datasets.brouillon.reduce((a, b) => a + b, 0),
        en_etude: graphData.datasets.en_etude.reduce((a, b) => a + b, 0),
        en_attente_verification: graphData.datasets.en_attente_verification.reduce((a, b) => a + b, 0),
        verifiees: graphData.datasets.verifiees.reduce((a, b) => a + b, 0),
        en_attente_validation: graphData.datasets.en_attente_validation.reduce((a, b) => a + b, 0),
        validees: graphData.datasets.validees.reduce((a, b) => a + b, 0),
        en_cours_execution: graphData.datasets.en_cours_execution.reduce((a, b) => a + b, 0),
        executees: graphData.datasets.executees.reduce((a, b) => a + b, 0),
        retournees: graphData.datasets.retournees.reduce((a, b) => a + b, 0),
        annulees: graphData.datasets.annulees.reduce((a, b) => a + b, 0)
    };
    
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Brouillons', 'En étude', 'À vérifier', 'Vérifiées', 'À valider', 'Validées', 'En exécution', 'Exécutées', 'Retournées', 'Annulées'],
            datasets: [{
                data: [
                    statusTotals.brouillon, 
                    statusTotals.en_etude, 
                    statusTotals.en_attente_verification, 
                    statusTotals.verifiees,
                    statusTotals.en_attente_validation,
                    statusTotals.validees,
                    statusTotals.en_cours_execution,
                    statusTotals.executees, 
                    statusTotals.retournees,
                    statusTotals.annulees
                ],
                backgroundColor: [
                    colors.gray, 
                    colors.blue, 
                    colors.orange, 
                    colors.teal,
                    colors.yellow,
                    colors.green,
                    colors.magenta,
                    colors.emerald,
                    colors.red,
                    '#9CA3AF'
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
                        boxWidth: 10,
                        padding: 8,
                        font: {
                            size: 9
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // 3. Graphique Barres groupées
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: graphData.labels,
            datasets: [
                {
                    label: 'Brouillons',
                    data: graphData.datasets.brouillon,
                    backgroundColor: colors.gray,
                    borderRadius: 4
                },
                {
                    label: 'En étude',
                    data: graphData.datasets.en_etude,
                    backgroundColor: colors.blue,
                    borderRadius: 4
                },
                {
                    label: 'En vérification',
                    data: graphData.datasets.en_attente_verification,
                    backgroundColor: colors.orange,
                    borderRadius: 4
                },
                {
                    label: 'Vérifiées',
                    data: graphData.datasets.verifiees,
                    backgroundColor: colors.teal,
                    borderRadius: 4
                },
                {
                    label: 'En validation',
                    data: graphData.datasets.en_attente_validation,
                    backgroundColor: colors.yellow,
                    borderRadius: 4
                },
                {
                    label: 'Validées',
                    data: graphData.datasets.validees,
                    backgroundColor: colors.green,
                    borderRadius: 4
                },
                {
                    label: 'En exécution',
                    data: graphData.datasets.en_cours_execution,
                    backgroundColor: colors.magenta,
                    borderRadius: 4
                },
                {
                    label: 'Exécutées',
                    data: graphData.datasets.executees,
                    backgroundColor: colors.emerald,
                    borderRadius: 4
                },
                {
                    label: 'Retournées',
                    data: graphData.datasets.retournees,
                    backgroundColor: colors.red,
                    borderRadius: 4
                },
                {
                    label: 'Annulées',
                    data: graphData.datasets.annulees,
                    backgroundColor: '#9CA3AF',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
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
