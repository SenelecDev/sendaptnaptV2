@extends('layouts.app')

@section('title', 'Dashboard - Opérateur Chef')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
            <p class="text-gray-600">Bienvenu, {{ Auth::user()->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-senelec-purple text-white">
                Opérateur Chef
            </span>
            <span class="text-sm text-gray-500">
                {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </span>
        </div>
    </div>

    <!-- Semaines S-1, en cours et S+1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Semaine S-1 -->
        <div class="rounded-2xl shadow-lg overflow-hidden border border-gray-100 bg-white">
            <div class="px-4 py-3" style="background: linear-gradient(to right, #6b7280, #9ca3af);">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        <span class="font-semibold text-sm">Semaine S-1</span>
                    </div>
                    <span class="text-xs opacity-90">{{ $debutSemaineM1->format('d/m') }} - {{ $finSemaineM1->format('d/m') }}</span>
                </div>
            </div>
            <div class="p-3">
                <div class="grid grid-cols-5 gap-1" style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 0.25rem;">
                    <a href="{{ route('operateurchef.notes.index', ['date_debut' => $debutSemaineM1->format('Y-m-d'), 'date_fin' => $finSemaineM1->format('Y-m-d')]) }}" class="text-center p-2 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                        <p class="text-lg font-bold text-gray-900">{{ $statsSemaineM1['total'] }}</p>
                        <p class="text-[10px] text-gray-500">Total</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'validée', 'date_debut' => $debutSemaineM1->format('Y-m-d'), 'date_fin' => $finSemaineM1->format('Y-m-d')]) }}" class="text-center p-2 bg-green-50 rounded hover:bg-green-100 transition-colors">
                        <p class="text-lg font-bold text-green-600">{{ $statsSemaineM1['validees'] }}</p>
                        <p class="text-[10px] text-gray-500">Valid.</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => "en cours d'exécution", 'date_debut' => $debutSemaineM1->format('Y-m-d'), 'date_fin' => $finSemaineM1->format('Y-m-d')]) }}" class="text-center p-2 bg-orange-50 rounded hover:bg-orange-100 transition-colors">
                        <p class="text-lg font-bold text-orange-600">{{ $statsSemaineM1['en_cours'] }}</p>
                        <p class="text-[10px] text-gray-500">En cours</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'exécutée', 'date_debut' => $debutSemaineM1->format('Y-m-d'), 'date_fin' => $finSemaineM1->format('Y-m-d')]) }}" class="text-center p-2 bg-emerald-50 rounded hover:bg-emerald-100 transition-colors">
                        <p class="text-lg font-bold text-emerald-600">{{ $statsSemaineM1['executees'] }}</p>
                        <p class="text-[10px] text-gray-500">Exec.</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'annulée', 'date_debut' => $debutSemaineM1->format('Y-m-d'), 'date_fin' => $finSemaineM1->format('Y-m-d')]) }}" class="text-center p-2 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                        <p class="text-lg font-bold text-gray-600">{{ $statsSemaineM1['annulees'] }}</p>
                        <p class="text-[10px] text-gray-500">Annul.</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Semaine en cours -->
        <div class="rounded-2xl shadow-lg overflow-hidden border border-gray-100 bg-white">
            <div class="px-4 py-3" style="background: linear-gradient(to right, #16a34a, #22c55e);">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-semibold text-sm">Semaine en cours</span>
                    </div>
                    <span class="text-xs opacity-90">{{ $debutSemaine->format('d/m') }} - {{ $finSemaine->format('d/m') }}</span>
                </div>
            </div>
            <div class="p-3">
                <div class="grid grid-cols-5 gap-1" style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 0.25rem;">
                    <a href="{{ route('operateurchef.notes.index', ['date_debut' => $debutSemaine->format('Y-m-d'), 'date_fin' => $finSemaine->format('Y-m-d')]) }}" class="text-center p-2 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                        <p class="text-lg font-bold text-gray-900">{{ $statsSemaineCourante['total'] }}</p>
                        <p class="text-[10px] text-gray-500">Total</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'validée', 'date_debut' => $debutSemaine->format('Y-m-d'), 'date_fin' => $finSemaine->format('Y-m-d')]) }}" class="text-center p-2 bg-green-50 rounded hover:bg-green-100 transition-colors">
                        <p class="text-lg font-bold text-green-600">{{ $statsSemaineCourante['validees'] }}</p>
                        <p class="text-[10px] text-gray-500">Valid.</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => "en cours d'exécution", 'date_debut' => $debutSemaine->format('Y-m-d'), 'date_fin' => $finSemaine->format('Y-m-d')]) }}" class="text-center p-2 bg-orange-50 rounded hover:bg-orange-100 transition-colors">
                        <p class="text-lg font-bold text-orange-600">{{ $statsSemaineCourante['en_cours'] }}</p>
                        <p class="text-[10px] text-gray-500">En cours</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'exécutée', 'date_debut' => $debutSemaine->format('Y-m-d'), 'date_fin' => $finSemaine->format('Y-m-d')]) }}" class="text-center p-2 bg-emerald-50 rounded hover:bg-emerald-100 transition-colors">
                        <p class="text-lg font-bold text-emerald-600">{{ $statsSemaineCourante['executees'] }}</p>
                        <p class="text-[10px] text-gray-500">Exec.</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'annulée', 'date_debut' => $debutSemaine->format('Y-m-d'), 'date_fin' => $finSemaine->format('Y-m-d')]) }}" class="text-center p-2 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                        <p class="text-lg font-bold text-gray-600">{{ $statsSemaineCourante['annulees'] }}</p>
                        <p class="text-[10px] text-gray-500">Annul.</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Semaine S+1 -->
        <div class="rounded-2xl shadow-lg overflow-hidden border border-gray-100 bg-white">
            <div class="px-4 py-3" style="background: linear-gradient(to right, #3b82f6, #2563eb);">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <span class="font-semibold text-sm">Semaine S+1</span>
                    </div>
                    <span class="text-xs opacity-90">{{ $debutSemaineS1->format('d/m') }} - {{ $finSemaineS1->format('d/m') }}</span>
                </div>
            </div>
            <div class="p-3">
                <div class="grid grid-cols-5 gap-1" style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 0.25rem;">
                    <a href="{{ route('operateurchef.notes.index', ['date_debut' => $debutSemaineS1->format('Y-m-d'), 'date_fin' => $finSemaineS1->format('Y-m-d')]) }}" class="text-center p-2 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                        <p class="text-lg font-bold text-gray-900">{{ $statsSemaineS1['total'] }}</p>
                        <p class="text-[10px] text-gray-500">Total</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'validée', 'date_debut' => $debutSemaineS1->format('Y-m-d'), 'date_fin' => $finSemaineS1->format('Y-m-d')]) }}" class="text-center p-2 bg-green-50 rounded hover:bg-green-100 transition-colors">
                        <p class="text-lg font-bold text-green-600">{{ $statsSemaineS1['validees'] }}</p>
                        <p class="text-[10px] text-gray-500">Valid.</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => "en cours d'exécution", 'date_debut' => $debutSemaineS1->format('Y-m-d'), 'date_fin' => $finSemaineS1->format('Y-m-d')]) }}" class="text-center p-2 bg-orange-50 rounded hover:bg-orange-100 transition-colors">
                        <p class="text-lg font-bold text-orange-600">{{ $statsSemaineS1['en_cours'] }}</p>
                        <p class="text-[10px] text-gray-500">En cours</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'exécutée', 'date_debut' => $debutSemaineS1->format('Y-m-d'), 'date_fin' => $finSemaineS1->format('Y-m-d')]) }}" class="text-center p-2 bg-emerald-50 rounded hover:bg-emerald-100 transition-colors">
                        <p class="text-lg font-bold text-emerald-600">{{ $statsSemaineS1['executees'] }}</p>
                        <p class="text-[10px] text-gray-500">Exec.</p>
                    </a>
                    <a href="{{ route('operateurchef.notes.index', ['statut' => 'annulée', 'date_debut' => $debutSemaineS1->format('Y-m-d'), 'date_fin' => $finSemaineS1->format('Y-m-d')]) }}" class="text-center p-2 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                        <p class="text-lg font-bold text-gray-600">{{ $statsSemaineS1['annulees'] }}</p>
                        <p class="text-[10px] text-gray-500">Annul.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparaison des semaines -->
    <div class="card-senelec">
        <div class="card-header">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="font-semibold text-gray-900">Comparaison des semaines</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Validées</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Exécutées</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Annulées</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux d'exécution</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-500">Semaine S-1</div>
                            <div class="text-sm text-gray-400">{{ $debutSemaineM1->format('d/m') }} - {{ $finSemaineM1->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-700">{{ $statsSemaineM1['total'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $statsSemaineM1['validees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ $statsSemaineM1['executees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $statsSemaineM1['annulees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold {{ $statsSemaineM1['taux_execution'] >= 50 ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $statsSemaineM1['taux_execution'] }}%
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Semaine en cours</div>
                            <div class="text-sm text-gray-500">{{ $debutSemaine->format('d/m') }} - {{ $finSemaine->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-900">{{ $statsSemaineCourante['total'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $statsSemaineCourante['validees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ $statsSemaineCourante['executees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $statsSemaineCourante['annulees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold {{ $statsSemaineCourante['taux_execution'] >= 50 ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $statsSemaineCourante['taux_execution'] }}%
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">Semaine S+1</div>
                            <div class="text-sm text-gray-500">{{ $debutSemaineS1->format('d/m') }} - {{ $finSemaineS1->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-900">{{ $statsSemaineS1['total'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $statsSemaineS1['validees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ $statsSemaineS1['executees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $statsSemaineS1['annulees'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold {{ $statsSemaineS1['taux_execution'] >= 50 ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $statsSemaineS1['taux_execution'] }}%
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Graphique évolution -->
    <div class="card-senelec">
        <div class="card-header">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                <span class="font-semibold text-gray-900">Évolution des NAPT par semaine</span>
            </div>
        </div>
        <div class="p-6">
            <div style="height: 300px;">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['S-1\n({{ $debutSemaineM1->format("d/m") }} - {{ $finSemaineM1->format("d/m") }})', 'Semaine en cours\n({{ $debutSemaine->format("d/m") }} - {{ $finSemaine->format("d/m") }})', 'S+1\n({{ $debutSemaineS1->format("d/m") }} - {{ $finSemaineS1->format("d/m") }})'],
            datasets: [
                {
                    label: 'Total',
                    data: [{{ $statsSemaineM1['total'] }}, {{ $statsSemaineCourante['total'] }}, {{ $statsSemaineS1['total'] }}],
                    backgroundColor: 'rgba(107, 114, 128, 0.8)',
                    borderColor: 'rgba(107, 114, 128, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Validées',
                    data: [{{ $statsSemaineM1['validees'] }}, {{ $statsSemaineCourante['validees'] }}, {{ $statsSemaineS1['validees'] }}],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                },
                {
                    label: 'En cours',
                    data: [{{ $statsSemaineM1['en_cours'] }}, {{ $statsSemaineCourante['en_cours'] }}, {{ $statsSemaineS1['en_cours'] }}],
                    backgroundColor: 'rgba(249, 115, 22, 0.8)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Exécutées',
                    data: [{{ $statsSemaineM1['executees'] }}, {{ $statsSemaineCourante['executees'] }}, {{ $statsSemaineS1['executees'] }}],
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Annulées',
                    data: [{{ $statsSemaineM1['annulees'] }}, {{ $statsSemaineCourante['annulees'] }}, {{ $statsSemaineS1['annulees'] }}],
                    backgroundColor: 'rgba(156, 163, 175, 0.8)',
                    borderColor: 'rgba(156, 163, 175, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
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
});
</script>
@endpush
@endsection
