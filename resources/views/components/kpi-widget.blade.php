@php
    use App\Models\Demande;
    use App\Models\Note;
    use Carbon\Carbon;
    
    $user = auth()->user();
    $startOfMonth = Carbon::now()->startOfMonth();
    $startOfWeek = Carbon::now()->startOfWeek();
    
    // KPIs globaux
    $totalDaptMois = Demande::where('created_at', '>=', $startOfMonth)->count();
    $totalNaptMois = Note::where('created_at', '>=', $startOfMonth)->count();
    $daptAcceptees = Demande::where('statut', 'acceptée')->where('updated_at', '>=', $startOfMonth)->count();
    $naptExecutees = Note::where('statut', 'executée')->where('updated_at', '>=', $startOfMonth)->count();
    
    // Temps moyen de traitement (en jours)
    $avgTraitementDapt = Demande::where('statut', 'acceptée')
        ->whereNotNull('updated_at')
        ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
        ->value('avg_days') ?? 0;
        
    // NAPT en retard (date_fin dépassée et pas exécutée)
    $naptEnRetard = Note::whereDate('dateF', '<', now())
        ->whereNotIn('statut', ['executée', 'annulée'])
        ->count();
    
    // Taux de validation cette semaine
    $naptSemaine = Note::where('created_at', '>=', $startOfWeek)->count();
    $naptValideesSemaine = Note::where('created_at', '>=', $startOfWeek)
        ->where('statut', 'validée')
        ->count();
    $tauxValidation = $naptSemaine > 0 ? round(($naptValideesSemaine / $naptSemaine) * 100) : 0;
@endphp

<div class="card-senelec">
    <div class="p-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Indicateurs du mois
        </h3>
    </div>
    
    <div class="p-4 grid grid-cols-2 gap-4">
        <!-- DAPT ce mois -->
        <div class="bg-gradient-to-br from-senelec-purple/5 to-senelec-purple/10 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">DAPT créées</span>
                <span class="text-xs text-senelec-purple">ce mois</span>
            </div>
            <p class="text-2xl font-bold text-senelec-purple mt-1">{{ $totalDaptMois }}</p>
            <p class="text-xs text-green-600">{{ $daptAcceptees }} acceptées</p>
        </div>
        
        <!-- NAPT ce mois -->
        <div class="bg-gradient-to-br from-senelec-orange/5 to-senelec-orange/10 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">NAPT créées</span>
                <span class="text-xs text-senelec-orange">ce mois</span>
            </div>
            <p class="text-2xl font-bold text-senelec-orange mt-1">{{ $totalNaptMois }}</p>
            <p class="text-xs text-green-600">{{ $naptExecutees }} exécutées</p>
        </div>
        
        <!-- Temps moyen -->
        <div class="bg-gradient-to-br from-senelec-teal/5 to-senelec-teal/10 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Temps moyen</span>
                <svg class="w-4 h-4 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-senelec-teal mt-1">{{ round($avgTraitementDapt, 1) }}j</p>
            <p class="text-xs text-gray-500">traitement DAPT</p>
        </div>
        
        <!-- En retard -->
        <div class="bg-gradient-to-br {{ $naptEnRetard > 0 ? 'from-red-50 to-red-100' : 'from-green-50 to-green-100' }} rounded-lg p-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">NAPT en retard</span>
                @if($naptEnRetard > 0)
                    <svg class="w-4 h-4 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </div>
            <p class="text-2xl font-bold {{ $naptEnRetard > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $naptEnRetard }}</p>
            <p class="text-xs {{ $naptEnRetard > 0 ? 'text-red-500' : 'text-green-500' }}">
                {{ $naptEnRetard > 0 ? 'À traiter en urgence' : 'Tout est à jour' }}
            </p>
        </div>
    </div>
    
    <!-- Barre de progression -->
    <div class="px-4 pb-4">
        <div class="bg-gray-100 rounded-lg p-3">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-600">Taux de validation (semaine)</span>
                <span class="text-sm font-bold text-senelec-purple">{{ $tauxValidation }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-senelec-purple to-senelec-orange h-2 rounded-full transition-all duration-500" 
                     style="width: {{ $tauxValidation }}%"></div>
            </div>
        </div>
    </div>
</div>
