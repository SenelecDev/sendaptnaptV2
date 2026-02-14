@extends('layouts.app')

@section('title', 'Utilisateur - ' . $user->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="p-2 text-senelec-purple hover:text-senelec-magenta rounded-lg hover:bg-purple-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                <p class="mt-1 text-gray-500 font-mono">{{ $user->matricule ?? 'Sans matricule' }}</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            @if(auth()->user()->hasRole('admin') && !$user->hasRole('admin') && auth()->id() !== $user->id)
                <form action="{{ route('admin.impersonate.start', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Simuler
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.users.edit', $user) }}" class="btn-senelec">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profil -->
        <div class="card-senelec p-6">
            <div class="text-center mb-6">
                @if($user->matricule && file_exists(public_path('profil/' . $user->matricule . '.jpg')))
                    <img src="{{ asset('profil/' . $user->matricule . '.jpg') }}" 
                         alt="{{ $user->full_name }}" 
                         class="w-24 h-24 mx-auto rounded-full object-cover border-4 border-senelec-purple/20 shadow-md">
                @else
                    <div class="w-24 h-24 mx-auto rounded-full bg-senelec-purple flex items-center justify-center text-white text-3xl font-semibold border-4 border-senelec-purple/20 shadow-md">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                    </div>
                @endif
                <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->full_name }}</h3>
                <p class="text-gray-500">{{ $user->poste ?? 'Poste non défini' }}</p>
                <div class="mt-2">
                    @if($user->is_active ?? true)
                        <span class="badge-success">Actif</span>
                    @else
                        <span class="badge-danger">Inactif</span>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Email</span>
                    <a href="mailto:{{ $user->email }}" class="text-senelec-purple hover:underline text-sm">{{ $user->email }}</a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Téléphone</span>
                    <span class="text-gray-900">{{ $user->telephone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Matricule</span>
                    <span class="text-gray-900 font-mono">{{ $user->matricule ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Source</span>
                    <span>
                        @if($user->ldap_guid)
                            <span class="badge badge-info">LDAP</span>
                        @else
                            <span class="badge badge-secondary">Local</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Informations professionnelles -->
        <div class="card-senelec p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5 text-senelec-teal shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Infos professionnelles
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-500">Direction</label>
                    <p class="font-medium text-gray-900">{{ $user->direction ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Département</label>
                    <p class="font-medium text-gray-900">{{ $user->departement ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Service</label>
                    <p class="font-medium text-gray-900">{{ $user->service ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Groupe</label>
                    <p class="font-medium text-gray-900">{{ $user->groupe?->nom ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <label class="text-sm text-gray-500">Rôles</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse($user->roles as $role)
                        @php
                            $roleColors = [
                                'admin' => 'bg-red-100 text-red-700',
                                'demandeur' => 'bg-blue-100 text-blue-700',
                                'desa' => 'bg-purple-100 text-purple-700',
                                'verificateur' => 'bg-indigo-100 text-indigo-700',
                                'valideur' => 'bg-green-100 text-green-700',
                                'operateur' => 'bg-amber-100 text-amber-700',
                                'operateurchef' => 'bg-orange-100 text-orange-700',
                                'directeur' => 'bg-violet-100 text-violet-700',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-sm rounded-full {{ $roleColors[$role->name] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($role->name) }}
                        </span>
                    @empty
                        <span class="text-gray-500 text-sm">Aucun rôle attribué</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-purple-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-senelec-purple">{{ $user->demandes_count ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Demandes créées</div>
                </div>
                <div class="p-4 bg-teal-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-senelec-teal">{{ $user->notes_creees_count ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Notes créées</div>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-senelec-orange">{{ $user->notes_verifiees_count ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Notes vérifiées</div>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-senelec-blue">{{ $user->notes_executees_count ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Notes exécutées</div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Date de création</span>
                    <span class="text-gray-900">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Dernière modification</span>
                    <span class="text-gray-900">{{ $user->updated_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Dernière connexion</span>
                    <span class="text-gray-900">
                        {{ $user->last_activity_at ? $user->last_activity_at->format('d/m/Y H:i') : 'Jamais' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Intérims actifs -->
    @if(($user->absencesAsTitulaire ?? collect())->count() > 0 || ($user->absencesAsInterimaire ?? collect())->count() > 0)
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Intérims
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(($user->absencesAsTitulaire ?? collect())->count() > 0)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-3">En tant que titulaire (absent)</h4>
                        <div class="space-y-2">
                            @foreach($user->absencesAsTitulaire as $absence)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $absence->interimaire->name }}</span>
                                        <span class="text-gray-600 text-sm">assure l'intérim</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $absence->date_debut->format('d/m/Y') }} - {{ $absence->date_fin->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if(($user->absencesAsInterimaire ?? collect())->count() > 0)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-3">En tant qu'intérimaire</h4>
                        <div class="space-y-2">
                            @foreach($user->absencesAsInterimaire as $absence)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                                    <div>
                                        <span class="text-gray-600 text-sm">Remplace</span>
                                        <span class="font-medium text-gray-900">{{ $absence->titulaire->name }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $absence->date_debut->format('d/m/Y') }} - {{ $absence->date_fin->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
