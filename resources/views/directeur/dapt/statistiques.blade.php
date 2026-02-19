@extends('layouts.app')

@section('title', 'Statistiques DAPT - Directeur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('directeur.dapt') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Statistiques DAPT</h1>
            <p class="text-gray-600">Analyse des Demandes d'Arrêt Pour Travaux</p>
        </div>
    </div>

    <!-- Indicateurs clés -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-senelec text-center">
            <div class="text-4xl font-bold text-senelec-purple">
                {{ $delaiMoyen?->delai_moyen ? number_format($delaiMoyen->delai_moyen, 1) : '0' }}
            </div>
            <div class="text-gray-600 mt-2">Délai moyen de traitement (jours)</div>
        </div>
        <div class="card-senelec text-center">
            <div class="text-4xl font-bold text-senelec-teal">
                {{ $parMois->sum('total') }}
            </div>
            <div class="text-gray-600 mt-2">Total demandes {{ now()->year }}</div>
        </div>
        <div class="card-senelec text-center">
            @php
                $totalAcceptees = $parMois->sum('acceptees');
                $totalDemandes = $parMois->sum('total');
                $tauxAcceptation = $totalDemandes > 0 ? round(($totalAcceptees / $totalDemandes) * 100, 1) : 0;
            @endphp
            <div class="text-4xl font-bold text-green-600">{{ $tauxAcceptation }}%</div>
            <div class="text-gray-600 mt-2">Taux d'acceptation</div>
        </div>
    </div>

    <!-- Graphique par mois -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution mensuelle {{ now()->year }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acceptées</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retournées</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux acceptation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Graphique</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $moisNoms = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                        $maxTotal = $parMois->max('total') ?: 1;
                    @endphp
                    @foreach($parMois as $stat)
                        @php
                            $taux = $stat->total > 0 ? round(($stat->acceptees / $stat->total) * 100, 1) : 0;
                            $barWidth = ($stat->total / $maxTotal) * 100;
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
                                    {{ $stat->acceptees }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $stat->retournees }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $taux }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-32 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-senelec-purple h-2.5 rounded-full" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if($parMois->isEmpty())
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Aucune donnée disponible pour cette année
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top groupes -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <svg class="w-5 h-5 inline mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Top groupes demandeurs
        </h3>
        @if(isset($parGroupe) && $parGroupe->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acceptées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Retournées</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Répartition</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $maxGroupeTotal = $parGroupe->first()->total; @endphp
                        @foreach($parGroupe as $index => $stat)
                            @php
                                $barWidth = ($stat->total / $maxGroupeTotal) * 100;
                                $tauxAcc = $stat->total > 0 ? round(($stat->acceptees / $stat->total) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full 
                                                 {{ $index < 3 ? 'bg-senelec-purple text-white' : 'bg-gray-200 text-gray-600' }} 
                                                 text-xs font-semibold">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $stat->groupe?->nom ?? 'Sans groupe' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-gray-900">{{ $stat->total }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $stat->acceptees }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $stat->retournees }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-32 bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-senelec-purple h-2.5 rounded-full" style="width: {{ $barWidth }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $tauxAcc }}% acc.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">Aucune donnée disponible</p>
        @endif
    </div>

    <!-- Top demandeurs -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 demandeurs</h3>
        @if($parDemandeur->isNotEmpty())
            <div class="space-y-4">
                @foreach($parDemandeur as $index => $stat)
                    @php
                        $maxDemandeur = $parDemandeur->first()->total;
                        $barWidth = ($stat->total / $maxDemandeur) * 100;
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="w-8 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full 
                                         {{ $index < 3 ? 'bg-senelec-magenta text-white' : 'bg-gray-200 text-gray-600' }} 
                                         text-xs font-semibold">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $stat->demandeur?->name ?? 'Inconnu' }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $stat->total }} demandes</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-senelec-teal h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-8">Aucune donnée disponible</p>
        @endif
    </div>
</div>
@endsection
