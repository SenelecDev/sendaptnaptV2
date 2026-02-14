@extends('layouts.app')

@section('title', 'Tableau de bord - Directeur')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tableau de bord Directeur</h1>
            <p class="text-gray-600">Vue d'ensemble des activités DAPT/NAPT</p>
        </div>
    </div>

    <!-- Filtres temporels -->
    <div class="card-senelec p-4">
        <form method="GET" action="{{ route('directeur.dashboard') }}" class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="label">Période</label>
                <select name="filtre" class="input-senelec w-full" onchange="this.form.submit()">
                    <option value="tout" {{ $filtre == 'tout' ? 'selected' : '' }}>Toutes les données</option>
                    <option value="semaine" {{ $filtre == 'semaine' ? 'selected' : '' }}>Par semaine</option>
                    <option value="mois" {{ $filtre == 'mois' ? 'selected' : '' }}>Par mois</option>
                    <option value="annee" {{ $filtre == 'annee' ? 'selected' : '' }}>Par année</option>
                </select>
            </div>
            @if($filtre == 'semaine')
            <div>
                <label class="label">Semaine</label>
                <select name="semaine" class="input-senelec w-full">
                    @foreach($semainesDisponibles as $num => $label)
                        <option value="{{ $num }}" {{ $semaine == $num ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($filtre == 'mois')
            <div>
                <label class="label">Mois</label>
                <select name="mois" class="input-senelec w-full">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('mois', date('m')) == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            @endif
            @if($filtre != 'tout')
            <div>
                <label class="label">Année</label>
                <select name="annee" class="input-senelec w-full">
                    @foreach($anneesDisponibles as $an)
                        <option value="{{ $an }}" {{ $annee == $an ? 'selected' : '' }}>{{ $an }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <button type="submit" class="btn-senelec w-full">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
            </div>
        </form>
        @if($dateDebut && $dateFin)
        <p class="text-sm text-gray-500 mt-2">
            Période : {{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }}
        </p>
        @endif
    </div>

    <!-- Statistiques DAPT -->
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Statistiques DAPT (Demandes)
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-senelec-purple">{{ $statsDapt['total'] }}</p>
                <p class="text-sm text-gray-500">Total</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $statsDapt['creees'] }}</p>
                <p class="text-sm text-gray-500">Créées</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $statsDapt['en_cours'] }}</p>
                <p class="text-sm text-gray-500">En traitement</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $statsDapt['acceptees'] }}</p>
                <p class="text-sm text-gray-500">Acceptées</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-orange-600">{{ $statsDapt['retournees'] }}</p>
                <p class="text-sm text-gray-500">Retournées</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-gray-400">{{ $statsDapt['brouillon'] }}</p>
                <p class="text-sm text-gray-500">Brouillons</p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Taux d'acceptation :</span>
                <div class="flex-1 bg-gray-200 rounded-full h-4 max-w-xs">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-500" style="width: {{ $tauxDapt }}%"></div>
                </div>
                <span class="text-sm font-semibold text-green-600">{{ $tauxDapt }}%</span>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique DAPT -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Répartition des DAPT
            </h3>
            <div class="relative h-64">
                <canvas id="chartDapt"></canvas>
            </div>
        </div>

        <!-- Graphique NAPT -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Répartition des NAPT
            </h3>
            <div class="relative h-64">
                <canvas id="chartNapt"></canvas>
            </div>
        </div>
    </div>

    <!-- Statistiques NAPT -->
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Statistiques NAPT (Notes)
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-senelec-magenta">{{ $statsNapt['total'] }}</p>
                <p class="text-sm text-gray-500">Total</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-gray-400">{{ $statsNapt['en_etude'] }}</p>
                <p class="text-sm text-gray-500">En étude</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $statsNapt['en_verification'] }}</p>
                <p class="text-sm text-gray-500">En vérification</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-indigo-600">{{ $statsNapt['verifiees'] }}</p>
                <p class="text-sm text-gray-500">Vérifiées</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-purple-600">{{ $statsNapt['validees'] }}</p>
                <p class="text-sm text-gray-500">Validées</p>
            </div>
            <div class="card-senelec p-4 text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $statsNapt['en_execution'] }}</p>
                <p class="text-sm text-gray-500">En exécution</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div class="card-senelec p-4 text-center border-l-4 border-green-500">
                <p class="text-2xl font-bold text-green-600">{{ $statsNapt['executees'] }}</p>
                <p class="text-sm text-gray-500">Exécutées</p>
            </div>
            <div class="card-senelec p-4 text-center border-l-4 border-orange-500">
                <p class="text-2xl font-bold text-orange-600">{{ $statsNapt['retournees'] }}</p>
                <p class="text-sm text-gray-500">Retournées</p>
            </div>
            <div class="card-senelec p-4 text-center border-l-4 border-red-500">
                <p class="text-2xl font-bold text-red-600">{{ $statsNapt['annulees'] }}</p>
                <p class="text-sm text-gray-500">Annulées</p>
            </div>
            <div class="card-senelec p-4 text-center border-l-4 border-gray-300">
                <p class="text-2xl font-bold text-gray-400">{{ $statsNapt['brouillon'] }}</p>
                <p class="text-sm text-gray-500">Brouillons</p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Taux d'exécution :</span>
                <div class="flex-1 bg-gray-200 rounded-full h-4 max-w-xs">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-500" style="width: {{ $tauxNapt }}%"></div>
                </div>
                <span class="text-sm font-semibold text-green-600">{{ $tauxNapt }}%</span>
            </div>
        </div>
    </div>

    <!-- Statistiques Utilisateurs et Groupes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique Utilisateurs par rôle -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Utilisateurs par rôle
            </h3>
            <div class="relative h-64">
                <canvas id="chartUsers"></canvas>
            </div>
            <div class="mt-4 flex justify-between text-sm">
                <span class="text-gray-600">Total: <strong class="text-senelec-purple">{{ $statsUsers['total'] }}</strong></span>
                <span class="text-gray-600">Actifs: <strong class="text-green-600">{{ $statsUsers['actifs'] }}</strong></span>
            </div>
        </div>

        <!-- Statistiques par groupe -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top groupes par activité</h3>
            @if($statsGroupes->count() > 0)
            <div class="space-y-3">
                @foreach($statsGroupes as $item)
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-sm font-medium text-gray-900">{{ $item['groupe']->nom }}</span>
                        <span class="text-xs text-gray-500 ml-2">({{ $item['users_count'] }} utilisateurs)</span>
                    </div>
                    <span class="px-2 py-1 bg-senelec-purple/10 text-senelec-purple rounded text-sm font-medium">
                        {{ $item['demandes_count'] }} demandes
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
            @endif
        </div>
    </div>

    <!-- Accès rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('directeur.dapt') }}" class="card-senelec p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-senelec-purple/10 rounded-full group-hover:bg-senelec-purple/20 transition-colors">
                    <svg class="w-8 h-8 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Consulter les DAPT</h3>
                    <p class="text-sm text-gray-500">Voir toutes les demandes</p>
                </div>
            </div>
        </a>
        <a href="{{ route('directeur.napt') }}" class="card-senelec p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-senelec-magenta/10 rounded-full group-hover:bg-senelec-magenta/20 transition-colors">
                    <svg class="w-8 h-8 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Consulter les NAPT</h3>
                    <p class="text-sm text-gray-500">Voir toutes les notes</p>
                </div>
            </div>
        </a>
        <a href="{{ route('directeur.feedback') }}" class="card-senelec p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 rounded-full group-hover:bg-blue-200 transition-colors">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Envoyer un feedback</h3>
                    <p class="text-sm text-gray-500">Contacter les admins</p>
                </div>
            </div>
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration commune
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.plugins.legend.position = 'bottom';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 15;

    // Graphique DAPT (Pie)
    const ctxDapt = document.getElementById('chartDapt');
    if (ctxDapt) {
        new Chart(ctxDapt, {
            type: 'pie',
            data: {
                labels: ['Créées', 'En traitement', 'Acceptées', 'Retournées', 'Brouillons'],
                datasets: [{
                    data: [
                        {{ $statsDapt['creees'] }},
                        {{ $statsDapt['en_cours'] }},
                        {{ $statsDapt['acceptees'] }},
                        {{ $statsDapt['retournees'] }},
                        {{ $statsDapt['brouillon'] }}
                    ],
                    backgroundColor: [
                        '#3B82F6', // blue
                        '#F59E0B', // amber
                        '#10B981', // green
                        '#F97316', // orange
                        '#9CA3AF'  // gray
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.raw / total) * 100) : 0;
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique NAPT (Doughnut)
    const ctxNapt = document.getElementById('chartNapt');
    if (ctxNapt) {
        new Chart(ctxNapt, {
            type: 'doughnut',
            data: {
                labels: ['En étude', 'Vérification', 'Validées', 'En exécution', 'Exécutées', 'Retournées', 'Annulées'],
                datasets: [{
                    data: [
                        {{ $statsNapt['en_etude'] }},
                        {{ $statsNapt['en_verification'] }},
                        {{ $statsNapt['validees'] }},
                        {{ $statsNapt['en_execution'] }},
                        {{ $statsNapt['executees'] }},
                        {{ $statsNapt['retournees'] }},
                        {{ $statsNapt['annulees'] }}
                    ],
                    backgroundColor: [
                        '#6366F1', // indigo
                        '#3B82F6', // blue
                        '#8B5CF6', // purple
                        '#F59E0B', // amber
                        '#10B981', // green
                        '#F97316', // orange
                        '#EF4444'  // red
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 8,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.raw / total) * 100) : 0;
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Utilisateurs (Bar horizontal)
    const ctxUsers = document.getElementById('chartUsers');
    if (ctxUsers) {
        new Chart(ctxUsers, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_map('ucfirst', array_keys($statsUsers['par_role']))) !!},
                datasets: [{
                    label: 'Utilisateurs',
                    data: {!! json_encode(array_values($statsUsers['par_role'])) !!},
                    backgroundColor: [
                        '#2B1444', // purple (admin)
                        '#B3006C', // magenta (demandeur)
                        '#6366F1', // indigo (desa)
                        '#3B82F6', // blue (verificateur)
                        '#8B5CF6', // purple (valideur)
                        '#F59E0B', // amber (operateurchef)
                        '#10B981', // green (operateur)
                        '#06B6D4'  // cyan (directeur)
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
