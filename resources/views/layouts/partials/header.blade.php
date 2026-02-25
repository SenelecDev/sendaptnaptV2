<header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-white/10 px-4 shadow-lg sm:gap-x-6 sm:px-6 lg:px-8" style="background-color: #B3006C;">
    <!-- Mobile menu button -->
    <button type="button" class="-m-2.5 p-2.5 text-white lg:hidden" @click="sidebarOpen = true">
        <span class="sr-only">Ouvrir le menu</span>
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Separator -->
    <div class="h-6 w-px bg-white/20 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <!-- Search -->
        <form class="relative flex flex-1" action="{{ route('search') }}" method="GET">
            <label for="search-field" class="sr-only">Rechercher</label>
            <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="search-field" 
                   class="block h-full w-full border-0 py-0 pl-8 pr-0 text-white placeholder:text-white/60 focus:ring-0 sm:text-sm bg-transparent" 
                   placeholder="Rechercher DAPT, NAPT..." 
                   type="search" 
                   name="q">
        </form>

        <div class="flex items-center gap-x-4 lg:gap-x-6">
            @if(!auth()->user()->hasRole('desa'))
            <!-- Notifications -->
            <div class="relative" x-data="{ 
                open: false, 
                notifications: [], 
                unreadCount: 0, 
                loading: true,
                markAsReadAndRedirect(notifId, actionUrl) {
                    fetch('{{ url('notifications') }}/' + notifId + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).then(() => {
                        if (actionUrl) {
                            window.location.href = actionUrl;
                        } else {
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                            this.notifications = this.notifications.map(n => n.id === notifId ? {...n, read: true} : n);
                        }
                    }).catch(() => {
                        if (actionUrl) window.location.href = actionUrl;
                    });
                }
            }" x-init="
                fetch('{{ route('notifications.latest') }}')
                    .then(r => r.json())
                    .then(data => {
                        notifications = data.notifications;
                        unreadCount = data.unread_count;
                        loading = false;
                    })
                    .catch(() => { loading = false; });
                
                setInterval(() => {
                    fetch('{{ route('notifications.count') }}')
                        .then(r => r.json())
                        .then(data => { unreadCount = data.count; });
                }, 30000);
            ">
                <button type="button" 
                        class="-m-2.5 p-2.5 text-white/80 hover:text-white relative"
                        @click="open = !open; if(open) { 
                            fetch('{{ route('notifications.latest') }}')
                                .then(r => r.json())
                                .then(data => { notifications = data.notifications; unreadCount = data.unread_count; });
                        }">
                    <span class="sr-only">Notifications</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-senelec-orange text-xs font-medium text-white"></span>
                </button>

                <!-- Notifications Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-xl bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                    <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        <a href="{{ route('notifications.index') }}" class="text-xs text-senelec-purple hover:text-senelec-magenta">Voir tout</a>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <template x-if="loading">
                            <div class="p-4 text-center">
                                <svg class="animate-spin h-6 w-6 mx-auto text-senelec-purple" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </template>
                        <template x-if="!loading && notifications.length === 0">
                            <div class="p-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>Aucune notification</p>
                            </div>
                        </template>
                        <template x-if="!loading && notifications.length > 0">
                            <div>
                                <template x-for="notif in notifications" :key="notif.id">
                                    <div @click="markAsReadAndRedirect(notif.id, notif.action_url)" 
                                       class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 last:border-0 cursor-pointer transition-colors"
                                       :class="{ 'bg-senelec-purple/10 border-l-4 border-l-senelec-purple': !notif.read, 'opacity-70': notif.read }">
                                        <div class="flex gap-3">
                                            <!-- Indicateur non lu -->
                                            <div class="flex-shrink-0 flex flex-col items-center gap-1">
                                                <div x-show="!notif.read" class="w-2 h-2 rounded-full bg-senelec-purple animate-pulse"></div>
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                     :class="{
                                                        'bg-green-100 text-green-700': notif.color === 'green',
                                                        'bg-red-100 text-red-700': notif.color === 'red',
                                                        'bg-blue-100 text-blue-700': notif.color === 'blue',
                                                        'bg-purple-100 text-purple-700': notif.color === 'purple',
                                                        'bg-amber-100 text-amber-700': notif.color === 'amber',
                                                        'bg-gray-100 text-gray-700': notif.color === 'gray'
                                                     }">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-900 truncate" :class="{ 'font-semibold': !notif.read, 'font-normal': notif.read }" x-text="notif.title"></p>
                                                <p class="text-xs text-gray-500 line-clamp-2" x-text="notif.message"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="notif.created_at"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="px-4 py-2 border-t border-gray-100 text-center">
                        <a href="{{ route('notifications.index') }}" class="text-sm text-senelec-purple hover:text-senelec-magenta font-medium">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            </div>

            <!-- Separator -->
            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-white/20" aria-hidden="true"></div>
            @endif

            <!-- Profile dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" 
                        class="-m-1.5 flex items-center p-1.5"
                        @click="open = !open">
                    <span class="sr-only">Menu utilisateur</span>
                    <span class="avatar-wrapper inline-flex">
                        @if(auth()->user()->photo_url)
                            <img class="h-9 w-9 rounded-full object-cover shadow-md" 
                                 src="{{ auth()->user()->photo_url }}" 
                                 alt="{{ auth()->user()->full_name }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center text-white text-sm font-bold shadow-md" style="{{ auth()->user()->photo_url ? 'display:none' : '' }}">
                            {{ auth()->user()->initials }}
                        </div>
                    </span>
                    <span class="hidden lg:flex lg:items-center">
                        <span class="ml-4 text-sm font-semibold leading-6 text-white" aria-hidden="true">
                            {{ auth()->user()->prenom ?: auth()->user()->name }}
                        </span>
                        <svg class="ml-2 h-5 w-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </button>

                <!-- Profile Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-xl bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</p>
                        {{-- <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p> --}}
                        <p class="text-xs text-senelec-purple mt-1">{{ auth()->user()->matricule }}</p>
                        @if(auth()->user()->roles->count() > 0)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach(auth()->user()->roles as $role)
                                    <span class="badge badge-purple text-xs">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mon profil
                        </div>
                    </a>
                    <a href="{{ route('profile.signature') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Ma signature
                        </div>
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Se déconnecter
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
