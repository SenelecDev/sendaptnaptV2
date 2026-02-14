@extends('layouts.app')

@section('title', 'Export des données')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">
                <svg class="w-7 h-7 inline-block mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export des données
            </h1>
            <p class="text-gray-600 mt-1">Exportez vos données DAPT et NAPT au format Excel</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Export DAPT -->
        <div class="card-senelec">
            <div class="p-5 border-b border-gray-200 bg-gradient-to-r from-senelec-purple/10 to-transparent">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export DAPT
                </h2>
                <p class="text-sm text-gray-500 mt-1">Demandes d'Autorisation pour Travaux</p>
            </div>
            <form action="{{ route('export.dapt') }}" method="GET" class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" name="date_debut" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-purple focus:border-senelec-purple">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" name="date_fin" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-purple focus:border-senelec-purple">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-purple focus:border-senelec-purple">
                        <option value="">Tous les statuts</option>
                        <option value="créée">Créée</option>
                        <option value="en cours de traitement">En cours de traitement</option>
                        <option value="acceptée">Acceptée</option>
                        <option value="retournée">Retournée</option>
                    </select>
                </div>
                <button type="submit" class="w-full btn-senelec flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exporter DAPT en Excel
                </button>
            </form>
        </div>

        <!-- Export NAPT -->
        <div class="card-senelec">
            <div class="p-5 border-b border-gray-200 bg-gradient-to-r from-senelec-orange/10 to-transparent">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Export NAPT
                </h2>
                <p class="text-sm text-gray-500 mt-1">Notes d'Arrêt pour Travaux</p>
            </div>
            <form action="{{ route('export.napt') }}" method="GET" class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" name="date_debut" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-orange focus:border-senelec-orange">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" name="date_fin" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-orange focus:border-senelec-orange">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semaine</label>
                    <input type="number" name="numero_semaine" min="1" max="53" placeholder="Ex: 12" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-orange focus:border-senelec-orange">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-senelec-orange focus:border-senelec-orange">
                        <option value="">Tous les statuts</option>
                        <option value="établie">Établie</option>
                        <option value="en attente de vérification">En attente de vérification</option>
                        <option value="vérifiée">Vérifiée</option>
                        <option value="validée">Validée</option>
                        <option value="executée">Exécutée</option>
                        <option value="retournée">Retournée</option>
                        <option value="annulée">Annulée</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-senelec-orange text-white px-4 py-2 rounded-lg hover:bg-senelec-orange/90 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exporter NAPT en Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Aide -->
    <div class="card-senelec p-5 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-start gap-4">
            <div class="bg-senelec-teal/10 p-3 rounded-full">
                <svg class="w-6 h-6 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">À propos de l'export</h3>
                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                    <li>• Les fichiers exportés sont au format Excel (.xlsx)</li>
                    <li>• Vous pouvez filtrer les données avant l'export</li>
                    <li>• Si aucun filtre n'est sélectionné, toutes les données seront exportées</li>
                    <li>• Les fichiers sont générés instantanément et téléchargés automatiquement</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
