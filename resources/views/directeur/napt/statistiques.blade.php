@extends('layouts.app')

@section('title', 'Statistiques NAPT - Directeur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('directeur.napt') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Statistiques NAPT</h1>
                <p class="text-gray-600">Analyse des Notes d'Arrêt Pour Travaux - {{ $annee }}</p>
            </div>
        </div>
        <div>
            <form method="GET" class="flex items-center gap-2">
                <select name="annee" onchange="this.form.submit()" class="input py-2">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <!-- Indicateurs clés -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card-senelec text-center">
            <div class="text-4xl font-bold text-senelec-purple">
                {{ $delaiMoyen?->delai_moyen ? number_format($delaiMoyen->delai_moyen, 1) : '0' }}
            </div>
            <div class="text-gray-600 mt-2">Délai moyen (jours)</div>
            <div class="text-xs text-gray-500">Création → Exécution</div>
        </div>
        <div class="card-senelec text-center">
            <div class="text-4xl font-bold text-senelec-teal">
                {{ $parMois->sum('total') }}
            </div>
            <div class="text-gray-600 mt-2">Total notes {{ $annee }}</div>
        </div>
        <div class="card-senelec text-center">
            <div class="text-4xl font-bold text-green-600">{{ $tauxReussite }}%</div>
            <div class="text-gray-600 mt-2">Taux de réussite</div>
            <div class="text-xs text-gray-500">Exécutées / Soumises</div>
        </div>
        <div class="card-senelec text-center">
            <div class="text-4xl font-bold text-senelec-orange">
                {{ $parMois->sum('executees') }}
            </div>
            <div class="text-gray-600 mt-2">Notes exécutées</div>
        </div>
    </div>

    <!-- Répartition GMAO / Manuel -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-senelec">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mode de saisie</h3>
            <div class="flex items-center justify-around py-8">
                <div class="text-center">
                    <div class="w-24 h-24 rounded-full bg-senelec-blue/20 flex items-center justify-center mx-auto mb-2">
                        <span class="text-3xl font-bold text-senelec-blue">{{ $parMode['gmao'] ?? 0 }}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-700">GMAO</div>
                    @php
                        $totalMode = ($parMode['gmao'] ?? 0) + ($parMode['manuel'] ?? 0);
                        $pctGmao = $totalMode > 0 ? round((($parMode['gmao'] ?? 0) / $totalMode) * 100) : 0;
                    @endphp
                    <div class="text-xs text-gray-500">{{ $pctGmao }}%</div>
                </div>
                <div class="text-center">
                    <div class="w-24 h-24 rounded-full bg-senelec-orange/20 flex items-center justify-center mx-auto mb-2">
                        <span class="text-3xl font-bold text-senelec-orange">{{ $parMode['manuel'] ?? 0 }}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-700">Manuel</div>
                    <div class="text-xs text-gray-500">{{ 100 - $pctGmao }}%</div>
                </div>
            </div>
        </div>

        <!-- Évolution mensuelle mini -->
        <div class="card-senelec">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution mensuelle</h3>
            <div class="flex items-end justify-between h-32 gap-1 px-4">
                @php
                    $moisCourts = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
                    $maxMois = $parMois->max('total') ?: 1;
                @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $stat = $parMois->firstWhere('mois', $m);
                        $total = $stat?->total ?? 0;
                        $height = ($total / $maxMois) * 100;
                    @endphp
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-full bg-senelec-teal rounded-t transition-all duration-300" 
                             style="height: {{ max($height, 4) }}%" 
                             title="{{ $total }} notes"></div>
                        <span class="text-xs text-gray-500 mt-1">{{ $moisCourts[$m - 1] }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Tableau détaillé par mois -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Détail mensuel {{ $annee }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exécutées</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retournées</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux réussite</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $moisNoms = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                    @endphp
                    @foreach($parMois as $stat)
                        @php
                            $taux = $stat->total > 0 ? round(($stat->executees / $stat->total) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $moisNoms[$stat->mois] ?? 'Mois ' . $stat->mois }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                {{ $stat->total }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $stat->executees }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $stat->retournees }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $taux }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ $taux }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top intervenants -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Top Vérificateurs -->
        <div class="card-senelec">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Vérificateurs</h3>
            @if($parVerificateur->isNotEmpty())
                <div class="space-y-3">
                    @foreach($parVerificateur->take(5) as $index => $stat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-senelec-teal/20 text-senelec-teal text-xs flex items-center justify-center font-semibold">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm text-gray-900">{{ $stat->verifie?->full_name ?? 'Inconnu' }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $stat->total }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-4">Aucune donnée</p>
            @endif
        </div>

        <!-- Top Valideurs -->
        <div class="card-senelec">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Valideurs</h3>
            @if($parValideur->isNotEmpty())
                <div class="space-y-3">
                    @foreach($parValideur->take(5) as $index => $stat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 text-xs flex items-center justify-center font-semibold">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm text-gray-900">{{ $stat->valide?->full_name ?? 'Inconnu' }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $stat->total }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-4">Aucune donnée</p>
            @endif
        </div>

        <!-- Top Opérateurs -->
        <div class="card-senelec">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Opérateurs</h3>
            @if($parOperateur->isNotEmpty())
                <div class="space-y-3">
                    @foreach($parOperateur->take(5) as $index => $stat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-senelec-purple/20 text-senelec-purple text-xs flex items-center justify-center font-semibold">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm text-gray-900">{{ $stat->execute?->full_name ?? 'Inconnu' }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $stat->total }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-4">Aucune donnée</p>
            @endif
        </div>
    </div>
</div>
@endsection
