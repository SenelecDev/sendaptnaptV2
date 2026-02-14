@extends('layouts.app')

@section('title', 'Tableau de bord - Valideur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ Auth::user()->name }}</h1>
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
            <p class="text-gray-600" style="margin-top: 15px;">Notes d'Arrêt Pour Travaux à valider et suivi</p>
        </div>
        <div class="text-sm text-gray-500">
            {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
        </div>
    </div>

    <!-- Statistiques - 6 compteurs -->
    <div class="flex flex-wrap md:flex-nowrap gap-4 overflow-x-auto pb-2">
        <a href="{{ route('valideur.notes.index', ['statut' => 'vérifiée']) }}" class="card-senelec p-4 border-l-4 border-yellow-500 hover:scale-105 transition-transform cursor-pointer flex-1 min-w-[140px]">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-full">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">À valider</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['en_attente_validation'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'validée']) }}" class="card-senelec p-4 border-l-4 border-green-500 hover:scale-105 transition-transform cursor-pointer flex-1 min-w-[140px]">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Validées</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['validees'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'en cours d\'exécution']) }}" class="card-senelec p-4 border-l-4 border-orange-500 hover:scale-105 transition-transform cursor-pointer flex-1 min-w-[140px]">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-full">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">En exécution</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['en_cours_execution'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'retournée']) }}" class="card-senelec p-4 border-l-4 border-red-500 hover:scale-105 transition-transform cursor-pointer flex-1 min-w-[140px]">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-full">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Retournées</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['retournees'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'annulée']) }}" class="card-senelec p-4 border-l-4 border-gray-500 hover:scale-105 transition-transform cursor-pointer flex-1 min-w-[140px]">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-full">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Annulées</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['annulees'] }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('valideur.notes.index', ['statut' => 'exécutée']) }}" class="card-senelec p-4 border-l-4 border-emerald-500 hover:scale-105 transition-transform cursor-pointer flex-1 min-w-[140px]">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 rounded-full">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Exécutées</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['executees'] }}</p>
                </div>
            </div>
        </a>
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
                <div class="flex gap-2">
                    <a href="{{ route('valideur.dashboard', ['periode' => 'semaine']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'semaine' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semaine
                    </a>
                    <a href="{{ route('valideur.dashboard', ['periode' => 'mois']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'mois' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mois
                    </a>
                    <a href="{{ route('valideur.dashboard', ['periode' => 'annee']) }}" 
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
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Dernières notes à valider -->
    <div class="card-senelec overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Dernières notes à valider</h3>
            <a href="{{ route('valideur.notes.index', ['statut' => 'vérifiée']) }}" class="text-senelec-purple hover:text-purple-700 text-sm font-medium">
                Voir tout →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro NAPT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DAPT Associée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Établie par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vérifiée par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date travaux</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dernieresNotes as $note)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-senelec-purple">{{ $note->numero_note }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($note->demande)
                                    <span class="text-sm font-mono text-senelec-orange">
                                        {{ $note->demande->numero_demande }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">
                                    {{ $note->etabliPar->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">
                                    {{ $note->verifiePar->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($note->ddt)
                                        {{ \Carbon\Carbon::parse($note->ddt)->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </div>
                                @if($note->dft)
                                    <div class="text-xs text-gray-500">
                                        au {{ \Carbon\Carbon::parse($note->dft)->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('valideur.notes.show', $note) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-senelec-purple text-white text-xs font-medium rounded-md hover:bg-purple-700 transition-colors" title="Valider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <p class="mt-2 text-lg font-medium">Aucune note à valider</p>
                                <p class="mt-1 text-sm">Toutes les notes ont été traitées.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour les graphiques
        const graphData = @json($graphData);
        
        // Couleurs
        const colors = {
            en_attente_validation: { bg: 'rgba(234, 179, 8, 0.2)', border: 'rgb(234, 179, 8)' },
            validees: { bg: 'rgba(34, 197, 94, 0.2)', border: 'rgb(34, 197, 94)' },
            en_cours_execution: { bg: 'rgba(249, 115, 22, 0.2)', border: 'rgb(249, 115, 22)' },
            executees: { bg: 'rgba(16, 185, 129, 0.2)', border: 'rgb(16, 185, 129)' },
            retournees: { bg: 'rgba(239, 68, 68, 0.2)', border: 'rgb(239, 68, 68)' },
            annulees: { bg: 'rgba(107, 114, 128, 0.2)', border: 'rgb(107, 114, 128)' },
        };
        
        const statusLabels = {
            en_attente_validation: 'À valider',
            validees: 'Validées',
            en_cours_execution: 'En exécution',
            executees: 'Exécutées',
            retournees: 'Retournées',
            annulees: 'Annulées',
        };
        
        // Graphique Evolution (Line)
        const evolutionCtx = document.getElementById('evolutionChart');
        if (evolutionCtx) {
            new Chart(evolutionCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: graphData.labels,
                    datasets: Object.keys(graphData.data).map(key => ({
                        label: statusLabels[key],
                        data: graphData.data[key],
                        borderColor: colors[key].border,
                        backgroundColor: colors[key].bg,
                        tension: 0.4,
                        fill: false,
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12 } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }
        
        // Graphique Doughnut
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const stats = @json($stats);
            new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(stats).map(key => statusLabels[key] || key),
                    datasets: [{
                        data: Object.values(stats),
                        backgroundColor: Object.keys(stats).map(key => colors[key]?.border || 'rgb(156, 163, 175)'),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12 } }
                    }
                }
            });
        }
    });
</script>
@endpush
