@extends('layouts.app')

@section('title', 'Observation - ' . Str::limit($observation->sujet, 30))

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.observations.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $observation->sujet }}</h1>
                <p class="text-gray-600">Observation #{{ $observation->id }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($observation->statut !== 'résolu' && $observation->statut !== 'fermé')
                <form action="{{ route('admin.observations.mark-processed', $observation) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn bg-green-100 text-green-700 hover:bg-green-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marquer résolu
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.observations.destroy', $observation) }}" method="POST" class="inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette observation ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn bg-red-100 text-red-700 hover:bg-red-200">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenu principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Description
                </h3>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($observation->description)) !!}
                </div>
            </div>

            <!-- Réponse admin -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-senelec-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Réponse administrateur
                </h3>
                
                @if($observation->reponse_admin)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($observation->reponse_admin)) !!}
                        </div>
                        <div class="mt-3 text-sm text-gray-500">
                            Répondu par {{ $observation->traitePar->full_name ?? 'Admin' }} 
                            le {{ $observation->date_reponse?->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.observations.update', $observation) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label for="reponse_admin" class="label">{{ $observation->reponse_admin ? 'Modifier la réponse' : 'Ajouter une réponse' }}</label>
                            <textarea name="reponse_admin" id="reponse_admin" rows="4" 
                                      class="input @error('reponse_admin') border-red-500 @enderror"
                                      placeholder="Écrivez votre réponse...">{{ old('reponse_admin', $observation->reponse_admin) }}</textarea>
                            @error('reponse_admin')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="statut" class="label">Statut</label>
                            <select name="statut" id="statut" class="input">
                                <option value="ouvert" {{ $observation->statut == 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                                <option value="en cours" {{ $observation->statut == 'en cours' ? 'selected' : '' }}>En cours</option>
                                <option value="résolu" {{ $observation->statut == 'résolu' ? 'selected' : '' }}>Résolu</option>
                                <option value="fermé" {{ $observation->statut == 'fermé' ? 'selected' : '' }}>Fermé</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="btn-senelec">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informations -->
            <div class="card-senelec">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Auteur</dt>
                        <dd class="mt-1 text-gray-900">{{ $observation->user->full_name ?? 'Inconnu' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $observation->getTypeBadgeClass() }}">
                                {{ ucfirst($observation->type) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Priorité</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $observation->getPrioriteBadgeClass() }}">
                                {{ ucfirst($observation->priorite) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $observation->getStatutBadgeClass() }}">
                                {{ ucfirst($observation->statut) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="mt-1 text-gray-900">{{ $observation->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @if($observation->traitePar)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Traité par</dt>
                        <dd class="mt-1 text-gray-900">{{ $observation->traitePar->full_name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
