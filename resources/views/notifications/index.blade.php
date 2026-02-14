@extends('layouts.app')

@section('title', 'Mes Notifications')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes Notifications</h1>
            <p class="text-gray-600">{{ $stats['non_lues'] }} notification(s) non lue(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($stats['non_lues'] > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-senelec-outline text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tout marquer comme lu
                </button>
            </form>
            @endif
            @if($stats['lues'] > 0)
            <form action="{{ route('notifications.destroy-read') }}" method="POST" class="inline" onsubmit="return confirm('Supprimer toutes les notifications lues ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn text-sm bg-red-100 text-red-700 hover:bg-red-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer les lues
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-senelec mb-6">
        <div class="p-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('notifications.index', ['statut' => 'tous']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $statut === 'tous' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Toutes ({{ $stats['total'] }})
                </a>
                <a href="{{ route('notifications.index', ['statut' => 'non_lues']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $statut === 'non_lues' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Non lues ({{ $stats['non_lues'] }})
                </a>
                <a href="{{ route('notifications.index', ['statut' => 'lues']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $statut === 'lues' ? 'bg-senelec-purple text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Lues ({{ $stats['lues'] }})
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des notifications -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $type = $data['type'] ?? 'info';
                $isUnread = is_null($notification->read_at);
                
                // Couleurs selon le type
                $colors = [
                    'green' => 'bg-green-100 text-green-800 border-green-200',
                    'red' => 'bg-red-100 text-red-800 border-red-200',
                    'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];
                $color = \App\Notifications\WorkflowNotification::getColor($type);
                $colorClass = $colors[$color] ?? $colors['gray'];
                
                // Icônes
                $iconMap = [
                    'document-text' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    'clipboard-document-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                    'user-group' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                    'chat-bubble-left-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
                    'bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                ];
                $iconName = \App\Notifications\WorkflowNotification::getIcon($type);
                $iconPath = $iconMap[$iconName] ?? $iconMap['bell'];
            @endphp
            
            <div class="card-senelec {{ $isUnread ? 'border-l-4 border-l-senelec-purple bg-purple-50/30' : '' }}">
                <div class="p-4">
                    <div class="flex items-start gap-4">
                        <!-- Icône -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $iconPath !!}
                            </svg>
                        </div>
                        
                        <!-- Contenu -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 {{ $isUnread ? '' : 'font-normal' }}">
                                        {{ $data['title'] ?? 'Notification' }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $data['message'] ?? '' }}</p>
                                </div>
                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-3 mt-3">
                                @if(isset($data['action_url']))
                                    <a href="{{ route('notifications.mark-read', $notification->id) }}" 
                                       class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium">
                                        {{ $data['action_text'] ?? 'Voir les détails' }}
                                    </a>
                                @endif
                                
                                @if($isUnread && !isset($data['action_url']))
                                    <a href="{{ route('notifications.mark-read', $notification->id) }}" 
                                       class="text-sm text-gray-500 hover:text-gray-700">
                                        Marquer comme lu
                                    </a>
                                @endif
                                
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card-senelec">
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucune notification</h3>
                    <p class="text-gray-500">Vous n'avez pas de notifications pour le moment.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
