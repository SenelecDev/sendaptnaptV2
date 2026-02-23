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
            <div class="flex gap-2">
                <a href="{{ route('desa.dashboard.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exporter Excel
                </a>
                <a href="{{ route('desa.notes.create') }}" class="btn-senelec">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouvelle NAPT
        </a>
            </div>
        </div>
    </div>

    <!-- Compteurs DAPT -->
    <div class="card-senelec p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            DAPTs
            @if($dateDebut && $dateFin)
                <span class="ml-2 text-xs font-normal text-gray-500">({{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }})</span>
            @endif
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
            @if($dateDebut && $dateFin)
                <span class="ml-2 text-xs font-normal text-gray-500">({{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }})</span>
            @endif
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

    <!-- Filtres par période et groupe -->
    <div class="card-senelec p-4">
        <form method="GET" action="{{ route('desa.dashboard') }}" class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
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
                        <option value="{{ $i }}" {{ $mois == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('fr')->translatedFormat('F') }}
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
                <label class="label">Groupe(s)</label>
                <select name="groupe_ids[]" id="groupe-select" class="w-full" multiple>
                    @foreach($groupes as $groupe)
                        <option value="{{ $groupe->id }}" {{ in_array($groupe->id, $groupeIds ?? []) ? 'selected' : '' }}>{{ $groupe->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">&nbsp;</label>
                <button type="submit" class="btn-senelec w-full">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
            </div>
            <!-- Hidden field for periode (graphiques) -->
            <input type="hidden" name="periode" value="{{ $periode }}">
        </form>
        @if($dateDebut && $dateFin || !empty($groupeIds))
        <p class="text-sm text-gray-500 mt-3">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Filtres actifs : 
            @if($dateDebut && $dateFin)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-senelec-purple/10 text-senelec-purple">
                    {{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }}
                </span>
            @endif
            @if(!empty($groupeIds))
                @foreach($groupeIds as $gId)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-senelec-orange/10 text-senelec-orange ml-1">
                        {{ $groupes->find($gId)->nom ?? 'N/A' }}
                    </span>
                @endforeach
            @endif
        </p>
        @endif
    </div>

    <!-- Comparaison des groupes (si plusieurs sélectionnés) -->
    @if($compareData && count($compareData) > 1)
    <div class="card-senelec p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Comparaison des groupes
        </h2>
        
        <!-- Tableau de comparaison DAPT -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-700 mb-3">DAPT (Demandes)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Créées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">En cours</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acceptées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Retournées</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($compareData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ ['#2B1444', '#B3006C', '#0A91A3', '#E87400', '#0D1CB0', '#10B981'][$loop->index % 6] }}"></div>
                                    <span class="font-medium text-gray-900">{{ $item['groupe']->nom }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-senelec-purple">{{ $item['demandes']['total'] }}</td>
                            <td class="px-4 py-3 text-center text-blue-600">{{ $item['demandes']['creees'] }}</td>
                            <td class="px-4 py-3 text-center text-orange-600">{{ $item['demandes']['en_cours'] }}</td>
                            <td class="px-4 py-3 text-center text-green-600">{{ $item['demandes']['acceptees'] }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ $item['demandes']['retournees'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tableau de comparaison NAPT -->
        <div>
            <h3 class="text-md font-semibold text-gray-700 mb-3">NAPT (Notes)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">En étude</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Vérification</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Vérifiées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Validées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Exécutées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Retournées</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($compareData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ ['#2B1444', '#B3006C', '#0A91A3', '#E87400', '#0D1CB0', '#10B981'][$loop->index % 6] }}"></div>
                                    <span class="font-medium text-gray-900">{{ $item['groupe']->nom }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-senelec-orange">{{ $item['notes']['total'] }}</td>
                            <td class="px-4 py-3 text-center text-blue-600">{{ $item['notes']['en_etude'] }}</td>
                            <td class="px-4 py-3 text-center text-orange-600">{{ $item['notes']['en_verification'] }}</td>
                            <td class="px-4 py-3 text-center text-teal-600">{{ $item['notes']['verifiees'] }}</td>
                            <td class="px-4 py-3 text-center text-green-600">{{ $item['notes']['validees'] }}</td>
                            <td class="px-4 py-3 text-center text-emerald-600">{{ $item['notes']['executees'] }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ $item['notes']['retournees'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Graphique de comparaison -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-md font-semibold text-gray-700">Graphique comparatif</h3>
                <button type="button" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" data-chart-download="compareChart" title="Télécharger en PNG">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
            </div>
            <div class="h-72">
                <canvas id="compareChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistiques Groupes avec DAPT Retournées -->
    @if($topGroupesRetournees->count() > 0)
    <div class="card-senelec p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Groupes avec DAPT retournées
                @if($dateDebut && $dateFin)
                    <span class="ml-2 text-xs font-normal text-gray-500">({{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }})</span>
                @endif
            </h2>
            <span class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Total : {{ $topGroupesRetournees->sum('demandes_retournees_count') }} DAPT retournées</span>
                <button type="button" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" data-chart-download="retourneesChart" title="Télécharger graphique en PNG">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
            </span>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tableau -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Groupe</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Retournées</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nb renvois</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $totalRetournees = $topGroupesRetournees->sum('demandes_retournees_count'); @endphp
                        @foreach($topGroupesRetournees as $index => $groupe)
                        <tr class="hover:bg-red-50/50 {{ $index < 3 ? 'bg-red-50/30' : '' }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-red-500 text-white' : ($index === 1 ? 'bg-red-400 text-white' : ($index === 2 ? 'bg-red-300 text-white' : 'bg-gray-100 text-gray-600')) }} font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900">{{ $groupe->nom }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold {{ $index < 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $groupe->demandes_retournees_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-orange-100 text-orange-800">
                                    {{ $groupe->total_renvois ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">
                                {{ $totalRetournees > 0 ? round(($groupe->demandes_retournees_count / $totalRetournees) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Graphique -->
            <div>
                <div class="h-72">
                    <canvas id="retourneesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                    @if($compareGraphData)
                        Comparaison NAPT par groupe
                    @else
                        Évolution des NAPT
                    @endif
                    @if($dateDebut && $dateFin)
                        <span class="ml-2 text-xs font-normal text-gray-500">({{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }})</span>
                    @endif
                </h3>
                @if($filtre === 'tout' && !$compareGraphData)
                <!-- Sélecteur de période graphique (visible seulement si pas de filtre) -->
                <div class="flex gap-2">
                    <a href="{{ route('desa.dashboard', array_merge(request()->except('periode'), ['periode' => 'semaine'])) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'semaine' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semaine
                    </a>
                    <a href="{{ route('desa.dashboard', array_merge(request()->except('periode'), ['periode' => 'mois'])) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'mois' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mois
                    </a>
                    <a href="{{ route('desa.dashboard', array_merge(request()->except('periode'), ['periode' => 'annee'])) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'annee' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Année
                    </a>
                </div>
                @endif
                <button type="button" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" data-chart-download="evolutionChart" title="Télécharger en PNG">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
            </div>
            <div class="h-72">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <!-- Graphique Répartition NAPT par statut (Doughnut) ou Statuts par groupe (comparaison) -->
        <div class="card-senelec p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    @if($compareGraphData)
                        Statuts NAPT par groupe
                    @else
                        Répartition NAPT par statut
                    @endif
                    @if($dateDebut && $dateFin)
                        <span class="ml-2 text-xs font-normal text-gray-500">({{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }})</span>
                    @endif
                </h3>
                @if($filtre === 'tout')
                <!-- Sélecteur de période graphique (visible seulement si pas de filtre) -->
                <div class="flex gap-2">
                    <a href="{{ route('desa.dashboard', array_merge(request()->except('periode'), ['periode' => 'semaine'])) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'semaine' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semaine
                    </a>
                    <a href="{{ route('desa.dashboard', array_merge(request()->except('periode'), ['periode' => 'mois'])) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'mois' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mois
                    </a>
                    <a href="{{ route('desa.dashboard', array_merge(request()->except('periode'), ['periode' => 'annee'])) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-all {{ $periode === 'annee' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Année
                    </a>
                </div>
                @endif
                <button type="button" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" data-chart-download="statusChart" title="Télécharger en PNG">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
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
                    @if($dateDebut && $dateFin)
                        {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
                    @elseif($periode === 'semaine')
                        7 derniers jours
                    @elseif($periode === 'mois')
                        4 dernières semaines
                    @else
                        12 derniers mois
                    @endif
                </h3>
                <button type="button" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors" data-chart-download="barChart" title="Télécharger en PNG">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
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
                    Top Groupes
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
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold {{ $index === 0 ? 'text-yellow-600' : 'text-senelec-purple' }}">{{ $groupe->demandes_count }}</span>
                            <p class="text-xs text-gray-500">DAPT acceptées</p>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.25rem;
        min-height: 42px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #2B1444;
        border: none;
        color: white;
        border-radius: 0.375rem;
        padding: 2px 8px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 18px;
        padding-right: 15px;
        border: none !important;
        border-right: none !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ff6b6b;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding-left: 8px;
    }
    .select2-dropdown {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2B1444;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 5px;
    }
</style>
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

    // 1. Graphique Évolution (Line Chart) - Adapté pour comparaison multi-groupes
    @if($compareGraphData)
    // Mode comparaison: une ligne par groupe
    const compareGraphData = @json($compareGraphData);
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: compareGraphData.labels,
            datasets: compareGraphData.datasets.map(ds => ({
                label: ds.label,
                data: ds.data,
                borderColor: ds.borderColor,
                backgroundColor: ds.backgroundColor,
                tension: 0.4,
                fill: false,
                borderWidth: 3
            }))
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
                },
                title: {
                    display: true,
                    text: 'Nombre total de NAPT par groupe',
                    font: { size: 14 }
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
    @else
    // Mode normal: par statut
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
    @endif

    // 2. Graphique Répartition par statut (Doughnut ou Bar pour comparaison)
    @if($compareGraphData)
    // Mode comparaison: bar chart avec statuts par groupe
    const compareDataForStatus = @json($compareData);
    const statusLabels = ['En étude', 'Vérification', 'Vérifiées', 'Validées', 'Exécutées', 'Retournées'];
    const statusColors = [colors.blue, colors.orange, colors.teal, colors.green, colors.emerald, colors.red];
    
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: compareDataForStatus.map(item => item.groupe.nom),
            datasets: [
                {
                    label: 'En étude',
                    data: compareDataForStatus.map(item => item.notes.en_etude),
                    backgroundColor: colors.blue,
                    borderRadius: 4
                },
                {
                    label: 'Vérification',
                    data: compareDataForStatus.map(item => item.notes.en_verification),
                    backgroundColor: colors.orange,
                    borderRadius: 4
                },
                {
                    label: 'Vérifiées',
                    data: compareDataForStatus.map(item => item.notes.verifiees),
                    backgroundColor: colors.teal,
                    borderRadius: 4
                },
                {
                    label: 'Validées',
                    data: compareDataForStatus.map(item => item.notes.validees),
                    backgroundColor: colors.green,
                    borderRadius: 4
                },
                {
                    label: 'Exécutées',
                    data: compareDataForStatus.map(item => item.notes.executees),
                    backgroundColor: colors.emerald,
                    borderRadius: 4
                },
                {
                    label: 'Retournées',
                    data: compareDataForStatus.map(item => item.notes.retournees),
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
                        boxWidth: 10,
                        padding: 8,
                        font: { size: 9 }
                    }
                },
                title: {
                    display: true,
                    text: 'Statuts NAPT par groupe',
                    font: { size: 12 }
                }
            },
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
    @else
    // Mode normal: doughnut par statut
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
    @endif

    // 3. Graphique Barres groupées
    @if($compareGraphData)
    // Mode comparaison: barres empilées par groupe et par période
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: compareGraphData.labels,
            datasets: compareGraphData.datasets.map(ds => ({
                label: ds.label,
                data: ds.data,
                backgroundColor: ds.borderColor,
                borderRadius: 4
            }))
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
                },
                title: {
                    display: true,
                    text: 'Comparaison des NAPT par groupe et par période',
                    font: { size: 14 }
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
    @else
    // Mode normal: par statut
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
    @endif
    
    // 4. Graphique de comparaison (si plusieurs groupes sélectionnés)
    @if($compareData && count($compareData) > 1)
    const compareData = @json($compareData);
    const compareColors = ['#2B1444', '#B3006C', '#0A91A3', '#E87400', '#0D1CB0', '#10B981'];
    
    new Chart(document.getElementById('compareChart'), {
        type: 'bar',
        data: {
            labels: compareData.map(item => item.groupe.nom),
            datasets: [
                {
                    label: 'DAPT Total',
                    data: compareData.map(item => item.demandes.total),
                    backgroundColor: compareData.map((_, i) => compareColors[i % compareColors.length] + '80'),
                    borderColor: compareData.map((_, i) => compareColors[i % compareColors.length]),
                    borderWidth: 2,
                    borderRadius: 4
                },
                {
                    label: 'NAPT Total',
                    data: compareData.map(item => item.notes.total),
                    backgroundColor: compareData.map((_, i) => compareColors[i % compareColors.length] + '40'),
                    borderColor: compareData.map((_, i) => compareColors[i % compareColors.length]),
                    borderWidth: 2,
                    borderRadius: 4,
                    borderDash: [5, 5]
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
                    display: true,
                    text: 'Comparaison DAPT vs NAPT par groupe'
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
    @endif
    
    // Graphique DAPT Retournées par groupe
    @if($topGroupesRetournees->count() > 0)
    const retourneesData = @json($topGroupesRetournees);
    const retourneesColors = [
        '#EF4444', '#F87171', '#FCA5A5', '#FECACA', '#FEE2E2',
        '#DC2626', '#B91C1C', '#991B1B', '#7F1D1D', '#450A0A'
    ];
    
    new Chart(document.getElementById('retourneesChart'), {
        type: 'bar',
        data: {
            labels: retourneesData.map(g => g.nom),
            datasets: [{
                label: 'DAPT Retournées',
                data: retourneesData.map(g => g.demandes_retournees_count),
                backgroundColor: retourneesData.map((_, i) => retourneesColors[i % retourneesColors.length]),
                borderColor: retourneesData.map((_, i) => retourneesColors[Math.max(0, i - 1) % retourneesColors.length]),
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Classement des groupes par DAPT retournées',
                    font: { size: 14, weight: 'bold' },
                    color: '#EF4444'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = retourneesData.reduce((a, b) => a + b.demandes_retournees_count, 0);
                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return `${context.raw} DAPT retournées (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Nombre de DAPT retournées'
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
    @endif
</script>

<!-- Export graphiques en PNG -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-chart-download]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-chart-download');
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const chart = Chart.getChart(canvas);
            if (chart) {
                const url = chart.toDataURL('image/png');
                const a = document.createElement('a');
                a.href = url;
                a.download = 'graphique-' + canvasId + '-' + new Date().toISOString().slice(0,10) + '.png';
                a.click();
            }
        });
    });
});
</script>

<!-- jQuery + Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#groupe-select').select2({
            placeholder: 'Sélectionnez un ou plusieurs groupes',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Aucun groupe trouvé";
                },
                searching: function() {
                    return "Recherche...";
                }
            }
        });
    });
</script>
@endpush
