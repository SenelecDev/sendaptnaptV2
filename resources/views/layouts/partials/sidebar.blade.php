<div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4 scrollbar-sidebar" style="background-color: #2B1444;">
    @php
        $user = auth()->user();
        $rolesInterim = $user->getRolesInterimActifs();
        $hasAnyInterim = count($rolesInterim) > 0;
    @endphp

    <!-- Logo -->
    <div class="flex h-20 shrink-0 items-center justify-center border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/logo.png') }}" alt="SENDAPTNAPT" class="w-12 h-12 object-contain">
            <div class="text-white">
                <span class="text-lg font-bold font-['Rajdhani'] tracking-wide">SENDAPTNAPT</span>
                <p class="text-xs text-white/60">Gestion DAPT/NAPT</p>
            </div>
        </a>
    </div>

    <!-- Indicateur Intérim Actif -->
    @if($hasAnyInterim)
    <div class="bg-amber-500/20 border border-amber-400/50 rounded-lg p-3">
        <div class="flex items-center gap-2 text-amber-300 text-xs font-medium mb-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            INTÉRIM ACTIF
        </div>
        @foreach($rolesInterim as $roleInfo)
        <div class="text-white/80 text-xs mb-1">
            <span class="text-amber-300">{{ ucfirst($roleInfo['role']) }}</span> 
            <span class="text-white/50">pour</span> 
            {{ $roleInfo['titulaire']->name }}
        </div>
        @endforeach
    </div>
    @endif
    
    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <!-- Demandeur -->
            @if($user->hasRoleOrInterim('demandeur') && !$user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    Demandeur
                    @if(!$user->hasRole('demandeur') && $user->estInterimaireA('demandeur'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('demandeur.dashboard') }}" 
                           class="{{ request()->routeIs('demandeur.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('demandeur.demandes.index') }}" 
                           class="{{ request()->routeIs('demandeur.demandes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Demandes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-absences.index') }}" 
                           class="{{ request()->routeIs('mes-absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Absences
                            @if($hasAnyInterim)
                                <span class="ml-auto bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">{{ count($rolesInterim) }}</span>
                            @endif
                        </a>
                    </li>                    
                    <li>
                        <a href="{{ route('mes-observations.index') }}" 
                           class="{{ request()->routeIs('mes-observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- DESA -->
            @if($user->hasRoleOrInterim('desa') && !$user->hasRole('admin'))
            <li x-data="{ openDemandes: {{ request()->is('desa/demandes*') ? 'true' : 'false' }}, openNotes: {{ request()->is('desa/notes*') ? 'true' : 'false' }} }">
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    EDITEUR
                    @if(!$user->hasRole('desa') && $user->estInterimaireA('desa'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Tableau de bord -->
                    <li>
                        <a href="{{ route('desa.dashboard') }}" 
                           class="{{ request()->routeIs('desa.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>

                    <!-- Diffusion Hebdomadaire -->
                    <li>
                        <a href="{{ route('desa.diffusion') }}" 
                           class="{{ request()->routeIs('desa.diffusion*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Diffusions
                        </a>
                    </li>
                    
                    <!-- Demandes (collapsible) -->
                    <li>
                        <button @click="openDemandes = !openDemandes" 
                                class="sidebar-link w-full justify-between {{ request()->is('desa/demandes*') ? 'text-white' : '' }}">
                            <span class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Demandes
                            </span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': openDemandes }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <ul x-show="openDemandes" x-collapse class="mt-1 ml-6 space-y-1">
                            <li>
                                <a href="{{ route('desa.demandes.index') }}" 
                                   class="{{ request()->routeIs('desa.demandes.index') && !request('statut') ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Gestion des DAPTs
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.demandes.index', ['statut' => 'creee']) }}" 
                                   class="{{ request('statut') === 'creee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Reçues
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.demandes.index', ['statut' => 'en_cours']) }}" 
                                   class="{{ request('statut') === 'en_cours' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    En cours de traitement
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.demandes.index', ['statut' => 'retournee']) }}" 
                                   class="{{ request('statut') === 'retournee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Retournées
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.demandes.index', ['statut' => 'acceptee']) }}" 
                                   class="{{ request('statut') === 'acceptee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Acceptées
                                </a>
                            </li>
                        </ul>
                    </li>                 
                    
                    
                    <!-- Notes (collapsible) -->
                    <li>
                        <button @click="openNotes = !openNotes" 
                                class="sidebar-link w-full justify-between {{ request()->is('desa/notes*') ? 'text-white' : '' }}">
                            <span class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Notes
                            </span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': openNotes }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <ul x-show="openNotes" x-collapse class="mt-1 ml-6 space-y-1">
                            <li>
                                <a href="{{ route('desa.notes.index') }}" 
                                   class="{{ request()->routeIs('desa.notes.index') && !request('statut') ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Gestion des napts
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'brouillon']) }}" 
                                   class="{{ request('statut') === 'brouillon' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Brouillons
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'en_etude']) }}" 
                                   class="{{ request('statut') === 'en_etude' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    En étude
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'en_attente_verification']) }}" 
                                   class="{{ request('statut') === 'en_attente_verification' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    À vérifier
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'retournee']) }}" 
                                   class="{{ request('statut') === 'retournee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Retournées
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'verifiee']) }}" 
                                   class="{{ request('statut') === 'verifiee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Vérifiées
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'validee']) }}" 
                                   class="{{ request('statut') === 'validee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Validées
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'en_cours_execution']) }}" 
                                   class="{{ request('statut') === 'en_cours_execution' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    En exécution
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'executee']) }}" 
                                   class="{{ request('statut') === 'executee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Exécutées
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('desa.notes.index', ['statut' => 'annulee']) }}" 
                                   class="{{ request('statut') === 'annulee' ? 'sidebar-sublink-active' : 'sidebar-sublink' }}">
                                    Annulées
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Absences -->
                    <li>
                        <a href="{{ route('mes-absences.index') }}" 
                           class="{{ request()->routeIs('mes-absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Absences
                        </a>
                    </li>
                    
                    <!-- Observations -->
                    <li>
                        <a href="{{ route('mes-observations.index') }}" 
                           class="{{ request()->routeIs('mes-observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Vérificateur -->
            @if($user->hasRoleOrInterim('verificateur') && !$user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    Vérificateur
                    @if(!$user->hasRole('verificateur') && $user->estInterimaireA('verificateur'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('verificateur.dashboard') }}" 
                           class="{{ request()->routeIs('verificateur.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('verificateur.notes.index') }}" 
                           class="{{ request()->routeIs('verificateur.notes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Notes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-absences.index') }}" 
                           class="{{ request()->routeIs('mes-absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Absences
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-observations.index') }}" 
                           class="{{ request()->routeIs('mes-observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Valideur -->
            @if($user->hasRoleOrInterim('valideur') && !$user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    Valideur
                    @if(!$user->hasRole('valideur') && $user->estInterimaireA('valideur'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('valideur.dashboard') }}" 
                           class="{{ request()->routeIs('valideur.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('valideur.notes.index') }}" 
                           class="{{ request()->routeIs('valideur.notes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Notes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-absences.index') }}" 
                           class="{{ request()->routeIs('mes-absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Absences
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-observations.index') }}" 
                           class="{{ request()->routeIs('mes-observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Opérateur Chef -->
            @if($user->hasRoleOrInterim('operateurchef') && !$user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    Opérateur Chef
                    @if(!$user->hasRole('operateurchef') && $user->estInterimaireA('operateurchef'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('operateurchef.dashboard') }}" 
                           class="{{ request()->routeIs('operateurchef.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('operateurchef.notes.index') }}" 
                           class="{{ request()->routeIs('operateurchef.notes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Notes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-absences.index') }}" 
                           class="{{ request()->routeIs('mes-absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Absences
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mes-observations.index') }}" 
                           class="{{ request()->routeIs('mes-observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Opérateur -->
            @if($user->hasRoleOrInterim('operateur') && !$user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    Opérateur
                    @if(!$user->hasRole('operateur') && $user->estInterimaireA('operateur'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('operateur.dashboard') }}" 
                           class="{{ request()->routeIs('operateur.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('operateur.notes.index') }}" 
                           class="{{ request()->routeIs('operateur.notes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Notes
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('mes-absences.index') }}" 
                           class="{{ request()->routeIs('mes-absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Absences
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('mes-observations.index') }}" 
                           class="{{ request()->routeIs('mes-observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Directeur -->
            @if($user->hasRoleOrInterim('directeur') && !$user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2 flex items-center gap-2">
                    Directeur
                    @if(!$user->hasRole('directeur') && $user->estInterimaireA('directeur'))
                        <span class="bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded">INTÉRIM</span>
                    @endif
                </div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('directeur.dashboard') }}" 
                           class="{{ request()->routeIs('directeur.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('directeur.dapt') }}" 
                           class="{{ request()->routeIs('directeur.dapt*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            DAPT
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('directeur.napt') }}" 
                           class="{{ request()->routeIs('directeur.napt*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            NAPT
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('directeur.feedback') }}" 
                           class="{{ request()->routeIs('directeur.feedback*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Feedback
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Administration -->
            @if($user->hasRole('admin'))
            <li>
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2">Administration</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" 
                           class="{{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Utilisateurs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.groupes.index') }}" 
                           class="{{ request()->routeIs('admin.groupes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Groupes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.chargecons.index') }}" 
                           class="{{ request()->routeIs('admin.chargecons.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Chargés Consignation
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.correspondants.index') }}" 
                           class="{{ request()->routeIs('admin.correspondants.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                            Correspondants
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.services.index') }}" 
                           class="{{ request()->routeIs('admin.services.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Services Destinataires
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.observations.index') }}" 
                           class="{{ request()->routeIs('admin.observations.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Observations
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.absences.index') }}" 
                           class="{{ request()->routeIs('admin.absences.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Intérims
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.demandes.index') }}" 
                           class="{{ request()->routeIs('admin.demandes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Gestion DAPT
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.notes.index') }}" 
                           class="{{ request()->routeIs('admin.notes.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Gestion NAPT
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            
            <!-- Outils -->
            <li class="pt-4 border-t border-white/10">
                <div class="text-xs font-semibold leading-6 text-white/40 uppercase tracking-wider mb-2">Outils</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('calendrier') }}" 
                           class="{{ request()->routeIs('calendrier') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Calendrier
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('exports.index') }}" 
                           class="{{ request()->routeIs('exports.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exports Excel
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('documentation.index') }}" 
                           class="{{ request()->routeIs('documentation.*') ? 'sidebar-link-active' : 'sidebar-link' }}" style="background: linear-gradient(to right, {{ request()->routeIs('documentation.*') ? '#8F0056, #B3006C' : '#E85D04, #F77F00' }});">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Documentation
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Déconnexion -->
            <li class="mt-auto pt-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-3 py-2.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 hover:border-red-500/50 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="font-medium">Déconnexion</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>
