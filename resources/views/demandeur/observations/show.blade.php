@extends('layouts.app')

@section('title', 'Observation - ' . $observation->sujet)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <a href="{{ route('demandeur.observations.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">{{ $observation->sujet }}</h1>
            <p class="text-gray-600">Envoyée le {{ $observation->created_at->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenu principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="card-senelec p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                <div class="prose prose-sm max-w-none text-gray-700">
                    {!! nl2br(e($observation->description)) !!}
                </div>
            </div>

            <!-- Réponse de l'admin -->
            @if($observation->reponse_admin)
                <div class="card-senelec p-6 border-l-4 border-senelec-teal">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Réponse de l'administration
                    </h3>
                    @if($observation->traitePar)
                        <p class="text-sm text-gray-500 mb-3">
                            Par {{ $observation->traitePar->full_name }} 
                            @if($observation->date_reponse)
                                le {{ $observation->date_reponse->format('d/m/Y à H:i') }}
                            @endif
                        </p>
                    @endif
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($observation->reponse_admin)) !!}
                    </div>
                </div>
            @else
                <div class="card-senelec p-6 bg-gray-50 border border-dashed border-gray-300">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm">En attente de réponse de l'administration</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Informations -->
        <div class="space-y-6">
            <div class="card-senelec p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">
                            @switch($observation->type)
                                @case('bug')
                                    <span class="badge badge-danger">Bug</span>
                                    @break
                                @case('suggestion')
                                    <span class="badge badge-info">Suggestion</span>
                                    @break
                                @case('question')
                                    <span class="badge badge-warning">Question</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">Autre</span>
                            @endswitch
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Priorité</dt>
                        <dd class="mt-1">
                            @switch($observation->priorite)
                                @case('urgente')
                                    <span class="badge badge-danger">Urgente</span>
                                    @break
                                @case('haute')
                                    <span class="badge badge-orange">Haute</span>
                                    @break
                                @case('normale')
                                    <span class="badge badge-info">Normale</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">Basse</span>
                            @endswitch
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            @switch($observation->statut)
                                @case('ouvert')
                                    <span class="badge badge-info">Ouvert</span>
                                    @break
                                @case('en cours')
                                    <span class="badge badge-warning">En cours</span>
                                    @break
                                @case('résolu')
                                    <span class="badge badge-success">Résolu</span>
                                    @break
                                @case('fermé')
                                    <span class="badge badge-secondary">Fermé</span>
                                    @break
                                @default
                                    <span class="badge">{{ $observation->statut }}</span>
                            @endswitch
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $observation->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($observation->updated_at != $observation->created_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $observation->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
