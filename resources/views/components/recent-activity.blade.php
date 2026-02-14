@php
    $user = auth()->user();
    
    // Récupérer les activités récentes
    $activities = collect();
    
    // Mes demandes récentes (si demandeur)
    if ($user->hasRoleOrInterim('demandeur')) {
        $demandes = \App\Models\Demande::where('demandeur_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($d) {
                return [
                    'type' => 'dapt',
                    'icon' => 'document',
                    'title' => 'DAPT ' . $d->numero_demande,
                    'description' => 'Statut: ' . $d->statut,
                    'date' => $d->updated_at,
                    'url' => route('demandeur.demandes.show', $d->id),
                    'color' => 'purple',
                ];
            });
        $activities = $activities->merge($demandes);
    }
    
    // Mes NAPT établies (si DESA)
    if ($user->hasRoleOrInterim('desa')) {
        $notes = \App\Models\Note::where('etabli_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($n) {
                return [
                    'type' => 'napt',
                    'icon' => 'clipboard',
                    'title' => 'NAPT ' . $n->numero_note,
                    'description' => 'Statut: ' . $n->statut,
                    'date' => $n->updated_at,
                    'url' => route('desa.notes.show', $n->id),
                    'color' => 'orange',
                ];
            });
        $activities = $activities->merge($notes);
    }
    
    // Notifications récentes
    $notifications = $user->notifications()
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($n) {
            return [
                'type' => 'notification',
                'icon' => 'bell',
                'title' => $n->data['title'] ?? 'Notification',
                'description' => \Illuminate\Support\Str::limit($n->data['message'] ?? '', 50),
                'date' => $n->created_at,
                'url' => $n->data['actionUrl'] ?? '#',
                'color' => 'teal',
                'read' => !is_null($n->read_at),
            ];
        });
    $activities = $activities->merge($notifications);
    
    // Trier par date et limiter
    $activities = $activities->sortByDesc('date')->take(10);
@endphp

<div class="card-senelec">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Mon activité récente
        </h3>
        <a href="{{ route('notifications.index') }}" class="text-sm text-senelec-purple hover:underline">Tout voir</a>
    </div>
    
    @if($activities->isEmpty())
    <div class="p-8 text-center text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <p>Aucune activité récente</p>
    </div>
    @else
    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
        @foreach($activities as $activity)
        <a href="{{ $activity['url'] }}" class="block p-3 hover:bg-gray-50 transition-colors {{ isset($activity['read']) && !$activity['read'] ? 'bg-blue-50/50' : '' }}">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center 
                    @switch($activity['color'])
                        @case('purple') bg-senelec-purple/10 text-senelec-purple @break
                        @case('orange') bg-senelec-orange/10 text-senelec-orange @break
                        @case('teal') bg-senelec-teal/10 text-senelec-teal @break
                        @default bg-gray-100 text-gray-600
                    @endswitch">
                    @switch($activity['icon'])
                        @case('document')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            @break
                        @case('clipboard')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            @break
                        @case('bell')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @break
                    @endswitch
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $activity['title'] }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $activity['description'] }}</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $activity['date']->diffForHumans() }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
