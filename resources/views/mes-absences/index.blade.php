@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mes Absences & Intérims</h1>
        <a href="{{ route('mes-absences.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Déclarer une absence
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mes Absences (je suis absent) -->
        <div class="card-senelec">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-times"></i>
                </span>
                Mes absences déclarées
            </h2>
            
            @if($mesAbsences->isEmpty())
                <p class="text-gray-500 text-center py-4">Aucune absence déclarée.</p>
            @else
                <div class="space-y-3">
                    @foreach($mesAbsences as $absence)
                        @php
                            $isActive = $absence->isActive();
                            $isFuture = $absence->isFuture();
                            $isPast = $absence->isPast();
                        @endphp
                        <div class="border rounded-lg p-4 {{ $isActive ? 'border-red-300 bg-red-50' : ($isFuture ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50') }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($isActive)
                                            <span class="badge badge-danger">En cours</span>
                                        @elseif($isFuture)
                                            <span class="badge badge-info">À venir</span>
                                        @else
                                            <span class="badge badge-secondary">Passée</span>
                                        @endif
                                        @if($absence->role)
                                            <span class="badge badge-warning">{{ ucfirst($absence->role) }}</span>
                                        @else
                                            <span class="badge badge-success">Tous rôles</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Du <strong>{{ $absence->date_debut->format('d/m/Y') }}</strong> 
                                        au <strong>{{ $absence->date_fin->format('d/m/Y') }}</strong>
                                    </p>
                                    <p class="text-sm text-gray-700 mt-1">
                                        <i class="fas fa-user-check text-green-600 mr-1"></i>
                                        Remplacé par : <strong>{{ $absence->interim->name ?? 'N/A' }}</strong>
                                    </p>
                                    @if($absence->motif)
                                        <p class="text-sm text-gray-500 mt-1 italic">{{ $absence->motif }}</p>
                                    @endif
                                </div>
                                @if(!$isPast)
                                    <div class="flex gap-1">
                                        <a href="{{ route('mes-absences.edit', $absence) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded hover:bg-blue-200" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('mes-absences.destroy', $absence) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer cette absence ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded hover:bg-red-200" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Intérims que j'assure -->
        <div class="card-senelec">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-clock"></i>
                </span>
                Intérims que j'assure
            </h2>
            
            @if($mesInterims->isEmpty())
                <p class="text-gray-500 text-center py-4">Aucun intérim en cours ou à venir.</p>
            @else
                <div class="space-y-3">
                    @foreach($mesInterims as $interim)
                        @php
                            $isActive = $interim->isActive();
                            $isFuture = $interim->isFuture();
                            $isPast = $interim->isPast();
                        @endphp
                        <div class="border rounded-lg p-4 {{ $isActive ? 'border-amber-300 bg-amber-50' : ($isFuture ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50') }}">
                            <div class="flex items-center gap-2 mb-1">
                                @if($isActive)
                                    <span class="badge badge-warning">En cours</span>
                                @elseif($isFuture)
                                    <span class="badge badge-info">À venir</span>
                                @else
                                    <span class="badge badge-secondary">Passé</span>
                                @endif
                                @if($interim->role)
                                    <span class="badge badge-primary">{{ ucfirst($interim->role) }}</span>
                                @else
                                    <span class="badge badge-success">Tous rôles</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">
                                Du <strong>{{ $interim->date_debut->format('d/m/Y') }}</strong> 
                                au <strong>{{ $interim->date_fin->format('d/m/Y') }}</strong>
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                <i class="fas fa-user text-blue-600 mr-1"></i>
                                Je remplace : <strong>{{ $interim->user->name ?? 'N/A' }}</strong>
                            </p>
                            @if($interim->motif)
                                <p class="text-sm text-gray-500 mt-1 italic">{{ $interim->motif }}</p>
                            @endif
                            
                            @if($isActive)
                                <div class="mt-2 p-2 bg-amber-100 rounded text-xs text-amber-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Vous avez actuellement accès aux fonctionnalités de 
                                    @if($interim->role)
                                        {{ ucfirst($interim->role) }}
                                    @else
                                        tous les rôles de {{ $interim->user->name }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Aide -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-2">
            <i class="fas fa-question-circle mr-1"></i> Comment fonctionne le système d'intérim ?
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• <strong>Déclarez votre absence</strong> : Indiquez les dates et choisissez qui vous remplacera.</li>
            <li>• <strong>Choisissez le rôle</strong> : Sélectionnez un rôle spécifique ou "Tous mes rôles".</li>
            <li>• <strong>Votre intérimaire</strong> : Aura automatiquement accès à vos fonctions pendant votre absence.</li>
            <li>• <strong>Distinction</strong> : Un badge "INTÉRIM" apparaît dans le menu pour distinguer les rôles temporaires.</li>
        </ul>
    </div>
</div>
@endsection
