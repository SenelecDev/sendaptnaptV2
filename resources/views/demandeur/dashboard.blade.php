@extends('layouts.app')

@section('title', 'Tableau de bord - Demandeur')

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
            <p class="text-gray-600" style="margin-top: 15px;">Vue d'ensemble des DAPT</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <div class="text-sm text-gray-500">
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
            </div>
            <a href="{{ route('demandeur.demandes.create') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle demande
        </a>
        </div>
    </div>

    <!-- Compteurs par statut -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <a href="{{ route('demandeur.demandes.index') }}" class="stat-card-purple hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">DAPTs</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'brouillon']) }}" class="stat-card-gray hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value">{{ $stats['brouillons'] }}</div>
            <div class="stat-label">Brouillons</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'créée']) }}" class="stat-card-blue hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value">{{ $stats['creees'] }}</div>
            <div class="stat-label">Créées</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'en cours de traitement']) }}" class="stat-card-orange hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'acceptée']) }}" class="stat-card-green hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value">{{ $stats['acceptees'] }}</div>
            <div class="stat-label">Acceptées</div>
        </a>
        <a href="{{ route('demandeur.demandes.index', ['statut' => 'retournée']) }}" class="card-senelec p-4 border-l-4 border-red-500 hover:scale-105 transition-transform cursor-pointer">
            <div class="stat-value text-red-600">{{ $stats['retournees'] }}</div>
            <div class="stat-label">Retournées</div>
        </a>
    </div>

    <!-- Section Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique Évolution temporelle -->
        <div class="card-senelec p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Évolution des DAPT
                </h3>
                <!-- Sélecteur de période -->
                <div class="flex gap-2" x-data="{ periode: '{{ $periode }}' }">
                    <a href="{{ route('demandeur.dashboard', ['periode' => 'semaine']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'semaine' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semaine
                    </a>
                    <a href="{{ route('demandeur.dashboard', ['periode' => 'mois']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'mois' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mois
                    </a>
                    <a href="{{ route('demandeur.dashboard', ['periode' => 'annee']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'annee' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Année
                    </a>
                </div>
            </div>
            <div class="h-72">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <!-- Graphique Répartition par statut (Doughnut) -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Répartition par statut
            </h3>
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
                    Détail par période - 
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

    <!-- Dernières demandes -->
    <div class="card-senelec">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dernières demandes
            </h3>
            <a href="{{ route('demandeur.demandes.index') }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium transition-colors duration-200">
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
                                    @case('brouillon')
                                        <span class="badge badge-secondary">Brouillon</span>
                                        @break
                                    @default
                                        <span class="badge">{{ $demande->statut }}</span>
                                @endswitch
                            </div>
                            <p class="text-sm text-gray-600 mt-1 truncate">{{ $demande->designation }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $demande->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('demandeur.demandes.show', $demande) }}" 
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
                    <p>Aucune demande récente</p>
                    <a href="{{ route('demandeur.demandes.create') }}" class="mt-4 inline-flex btn-senelec">
                        Créer ma première demande
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Top demandeurs du groupe -->
    @if($groupe && count($topDemandeurs) > 0)
    <div class="card-senelec">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Top demandeurs du groupe {{ $groupe->nom }}
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($topDemandeurs as $index => $demandeur)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-200 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-500')) }} font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <span class="inline-flex">
                            @if($demandeur->photo_url)
                                <img src="{{ $demandeur->photo_url }}" alt="{{ $demandeur->name }}" class="w-10 h-10 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="w-10 h-10 rounded-full bg-senelec-purple/10 flex items-center justify-center text-senelec-purple font-bold" style="{{ $demandeur->photo_url ? 'display:none' : '' }}">
                                {{ $demandeur->initials }}
                            </div>
                        </span>
                        <div>
                            <p class="font-medium text-gray-900 {{ $demandeur->id === Auth::id() ? 'text-senelec-purple' : '' }}">
                                {{ $demandeur->name }}
                                @if($demandeur->id === Auth::id())
                                    <span class="text-xs text-senelec-magenta">(vous)</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">{{ $demandeur->matricule }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold {{ $index === 0 ? 'text-yellow-600' : 'text-senelec-purple' }}">{{ $demandeur->demandes_count }}</span>
                        <p class="text-xs text-gray-500">demandes</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
    // Données pour les graphiques
    const graphData = @json($graphData);
    const stats = {
        brouillons: {{ $stats['brouillons'] }},
        creees: {{ $stats['creees'] }},
        en_cours: {{ $stats['en_cours'] }},
        acceptees: {{ $stats['acceptees'] }},
        retournees: {{ $stats['retournees'] }}
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
        gray: '#6B7280'
    };

    // 1. Graphique Évolution (Line Chart)
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: graphData.labels,
            datasets: [
                {
                    label: 'Créées',
                    data: graphData.datasets.creees,
                    borderColor: colors.blue,
                    backgroundColor: colors.blue + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'En cours',
                    data: graphData.datasets.en_cours,
                    borderColor: colors.orange,
                    backgroundColor: colors.orange + '20',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Acceptées',
                    data: graphData.datasets.acceptees,
                    borderColor: colors.green,
                    backgroundColor: colors.green + '20',
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

    // 2. Graphique Répartition par statut (Doughnut)
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Brouillons', 'Créées', 'En cours', 'Acceptées', 'Retournées'],
            datasets: [{
                data: [stats.brouillons, stats.creees, stats.en_cours, stats.acceptees, stats.retournees],
                backgroundColor: [colors.gray, colors.blue, colors.orange, colors.green, colors.red],
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
                        padding: 15
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
                    data: graphData.datasets.brouillons,
                    backgroundColor: colors.gray,
                    borderRadius: 4
                },
                {
                    label: 'Créées',
                    data: graphData.datasets.creees,
                    backgroundColor: colors.blue,
                    borderRadius: 4
                },
                {
                    label: 'En cours',
                    data: graphData.datasets.en_cours,
                    backgroundColor: colors.orange,
                    borderRadius: 4
                },
                {
                    label: 'Acceptées',
                    data: graphData.datasets.acceptees,
                    backgroundColor: colors.green,
                    borderRadius: 4
                },
                {
                    label: 'Retournées',
                    data: graphData.datasets.retournees,
                    backgroundColor: colors.red,
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
