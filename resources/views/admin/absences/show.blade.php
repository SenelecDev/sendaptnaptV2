@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6 gap-4">
            <a href="{{ route('admin.absences.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Détails de l'Absence / Intérim</h1>
        </div>

        @php
            $now = now();
            $isActive = $absence->date_debut <= $now && $absence->date_fin >= $now;
            $isFuture = $absence->date_debut > $now;
            $isPast = $absence->date_fin < $now;
        @endphp

        <div class="card-senelec p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    @if($isActive)
                        <span class="badge badge-success text-lg">En cours</span>
                    @elseif($isFuture)
                        <span class="badge badge-info text-lg">À venir</span>
                    @else
                        <span class="badge badge-secondary text-lg">Passée</span>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.absences.edit', $absence) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                    <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" class="inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash mr-1"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Titulaire -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Titulaire (absent)</h3>
                    <div class="flex items-center gap-6">
                        <span class="inline-flex flex-shrink-0">
                            @if($absence->user->photo_url)
                                <img src="{{ $absence->user->photo_url }}" alt="{{ $absence->user->name }}" class="w-14 h-14 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="w-14 h-14 rounded-full bg-blue-500 text-white flex items-center justify-center" style="{{ $absence->user->photo_url ? 'display:none' : '' }}">
                                <span class="text-lg font-bold">{{ $absence->user->initials ?? 'U' }}</span>
                            </div>
                        </span>
                        <div class="space-y-1">
                            <p class="font-semibold text-gray-900 text-lg">{{ $absence->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $absence->user->matricule ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $absence->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Intérimaire -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Intérimaire</h3>
                    <div class="flex items-center gap-6">
                        <span class="inline-flex flex-shrink-0">
                            @if($absence->interim->photo_url)
                                <img src="{{ $absence->interim->photo_url }}" alt="{{ $absence->interim->name }}" class="w-14 h-14 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="w-14 h-14 rounded-full bg-green-500 text-white flex items-center justify-center" style="{{ $absence->interim->photo_url ? 'display:none' : '' }}">
                                <span class="text-lg font-bold">{{ $absence->interim->initials ?? 'U' }}</span>
                            </div>
                        </span>
                        <div class="space-y-1">
                            <p class="font-semibold text-gray-900 text-lg">{{ $absence->interim->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $absence->interim->matricule ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $absence->interim->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Date de début</h3>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($absence->date_debut)->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Date de fin</h3>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($absence->date_fin)->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Durée</h3>
                @php
                    $debut = \Carbon\Carbon::parse($absence->date_debut);
                    $fin = \Carbon\Carbon::parse($absence->date_fin);
                    $duree = $debut->diffInDays($fin) + 1;
                @endphp
                <p class="text-lg font-semibold text-gray-900">{{ $duree }} jour(s)</p>
            </div>

            @if($absence->role)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Rôle concerné</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($absence->role) }}</p>
                </div>
            @endif

            @if($absence->motif)
                <div class="mt-6 mb-4">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Motif</h3>
                    <p class="text-gray-700">{{ $absence->motif }}</p>
                </div>
            @endif

            <hr class="my-6">

            <div class="text-sm text-gray-500 space-y-1 mt-4">
                <p>Créé le : {{ $absence->created_at?->format('d/m/Y à H:i') ?? '-' }}</p>
                <p>Modifié le : {{ $absence->updated_at?->format('d/m/Y à H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
