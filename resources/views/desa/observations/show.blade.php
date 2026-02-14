@extends('layouts.app')

@php
    $routePrefix = explode('.', request()->route()->getName())[0] ?? 'desa';
@endphp

@section('title', 'Observation - ' . $observation->sujet)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route($routePrefix . '.observations.index') }}" class="text-senelec-purple hover:text-senelec-magenta text-sm flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour aux observations
            </a>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Détail de l'observation</h1>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card-senelec">
        <div class="card-header flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">{{ $observation->sujet }}</h2>
            <div class="flex gap-2">
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
            </div>
        </div>
        
        <div class="card-body space-y-6">
            <!-- Informations -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="text-sm text-gray-500">Utilisateur</span>
                    <p class="font-medium text-gray-900">{{ $observation->user->name ?? 'N/A' }}</p>
                    @if($observation->user?->matricule)
                        <p class="text-sm text-gray-500">{{ $observation->user->matricule }}</p>
                    @endif
                </div>
                <div>
                    <span class="text-sm text-gray-500">Date de création</span>
                    <p class="font-medium text-gray-900">{{ $observation->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Statut</span>
                    <p class="mt-1">
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
                    </p>
                </div>
            </div>

            <!-- Description -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <div class="prose prose-sm max-w-none text-gray-900 bg-gray-50 rounded-lg p-4">
                    {!! nl2br(e($observation->description)) !!}
                </div>
            </div>

            <!-- Pièce jointe -->
            @if($observation->piece_jointe)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Pièce jointe</h3>
                    <a href="{{ Storage::url($observation->piece_jointe) }}" target="_blank" 
                       class="inline-flex items-center gap-2 text-senelec-purple hover:text-senelec-magenta">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Voir le fichier
                    </a>
                </div>
            @endif

            <!-- Réponse DESA -->
            @if($observation->reponse)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Réponse</h3>
                    <div class="prose prose-sm max-w-none text-gray-900 bg-green-50 border border-green-200 rounded-lg p-4">
                        {!! nl2br(e($observation->reponse)) !!}
                    </div>
                    @if($observation->date_reponse)
                        <p class="text-xs text-gray-400 mt-2">
                            Répondu le {{ $observation->date_reponse->format('d/m/Y à H:i') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end">
        <a href="{{ route($routePrefix . '.observations.index') }}" class="btn-senelec-outline py-2 px-4">
            Retour à la liste
        </a>
    </div>
</div>
@endsection
