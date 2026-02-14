@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
            <p class="text-gray-600">Gérer vos informations personnelles</p>
        </div>
    </div>

    <!-- Informations du profil -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Informations personnelles</h3>
        
        <div class="flex items-start gap-6">
            <!-- Photo de profil -->
            <div class="flex-shrink-0">
                @if(auth()->user()->photo_url)
                    <img class="h-24 w-24 rounded-full object-cover" 
                         src="{{ auth()->user()->photo_url }}" 
                         alt="{{ auth()->user()->full_name }}">
                @else
                    <div class="h-24 w-24 rounded-full bg-senelec-purple flex items-center justify-center text-white text-2xl font-semibold">
                        {{ auth()->user()->initials }}
                    </div>
                @endif
            </div>

            <!-- Détails -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">Nom complet</label>
                    <p class="text-gray-900 font-medium">{{ auth()->user()->full_name }}</p>
                </div>
                
                <div>
                    <label class="label">Matricule</label>
                    <p class="text-gray-900 font-medium">{{ auth()->user()->matricule ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="label">Email</label>
                    <p class="text-gray-900 font-medium">{{ auth()->user()->email ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="label">Téléphone</label>
                    <p class="text-gray-900 font-medium">{{ auth()->user()->telephone ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="label">Direction</label>
                    <p class="text-gray-900 font-medium">{{ auth()->user()->direction ?? '-' }}</p>
                </div>
                
                <div>
                    <label class="label">Service</label>
                    <p class="text-gray-900 font-medium">{{ auth()->user()->service ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rôles et permissions -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rôles et permissions</h3>
        
        <div class="flex flex-wrap gap-2">
            @forelse(auth()->user()->roles as $role)
                <span class="badge badge-info">{{ ucfirst($role->name) }}</span>
            @empty
                <span class="text-gray-500">Aucun rôle attribué</span>
            @endforelse
        </div>
    </div>

    <!-- Intérims actifs -->
    @php
        $interimsActifs = auth()->user()->absences()
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->with('remplacant')
            ->get();
    @endphp
    
    @if($interimsActifs->count() > 0)
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Intérims en cours</h3>
        
        <div class="space-y-3">
            @foreach($interimsActifs as $absence)
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ $absence->motif }}</p>
                        <p class="text-sm text-gray-500">
                            Du {{ $absence->date_debut->format('d/m/Y') }} 
                            au {{ $absence->date_fin->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Remplacé par</p>
                        <p class="font-medium text-orange-600">{{ $absence->remplacant->full_name }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Signature -->
    <div class="card-senelec">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Signature numérique</h3>
            <a href="{{ route('profile.signature') }}" class="btn-senelec">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Gérer ma signature
            </a>
        </div>
        
        @if(auth()->user()->signature)
            <div class="p-4 bg-gray-50 rounded-lg">
                <img src="{{ auth()->user()->signature_url }}" alt="Signature" class="max-h-20">
            </div>
        @else
            <p class="text-gray-500">Aucune signature enregistrée</p>
        @endif
    </div>

    <!-- Activité récente -->
    <div class="card-senelec">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de connexion</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Dernière connexion</span>
                <p class="font-medium text-gray-900">
                    {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y H:i') : 'Non disponible' }}
                </p>
            </div>
            <div>
                <span class="text-gray-500">Compte créé le</span>
                <p class="font-medium text-gray-900">
                    {{ auth()->user()->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
