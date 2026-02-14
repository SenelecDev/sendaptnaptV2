@extends('layouts.app')

@section('title', 'Feedback - Directeur')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div>
        <a href="{{ route('directeur.dashboard') }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour au tableau de bord
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Feedback aux administrateurs</h1>
        <p class="text-gray-600">Envoyez vos remarques et suggestions à l'équipe d'administration</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="list-disc list-inside text-red-700 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Formulaire de feedback -->
    <div class="card-senelec">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Nouveau feedback</h3>
        </div>
        <form action="{{ route('directeur.feedback.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="type" class="label">Type de feedback</label>
                <select id="type" name="type" class="input-senelec w-full" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>Suggestion d'amélioration</option>
                    <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>Signalement de bug</option>
                    <option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>Question</option>
                    <option value="remarque" {{ old('type') == 'remarque' ? 'selected' : '' }}>Remarque générale</option>
                </select>
            </div>
            <div>
                <label for="sujet" class="label">Sujet</label>
                <input type="text" id="sujet" name="sujet" value="{{ old('sujet') }}" 
                       class="input-senelec w-full" placeholder="Objet de votre feedback" required>
            </div>
            <div>
                <label for="contenu" class="label">Message</label>
                <textarea id="contenu" name="contenu" rows="5" 
                          class="input-senelec w-full" placeholder="Décrivez votre feedback en détail..." required>{{ old('contenu') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn-senelec">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Envoyer le feedback
                </button>
            </div>
        </form>
    </div>

    <!-- Historique des feedbacks -->
    <div class="card-senelec">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Mes feedbacks envoyés</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($observations as $observation)
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @php
                                $typeColors = [
                                    'suggestion' => 'bg-blue-100 text-blue-700',
                                    'bug' => 'bg-red-100 text-red-700',
                                    'question' => 'bg-yellow-100 text-yellow-700',
                                    'remarque' => 'bg-gray-100 text-gray-700',
                                ];
                                $typeLabels = [
                                    'suggestion' => 'Suggestion',
                                    'bug' => 'Bug',
                                    'question' => 'Question',
                                    'remarque' => 'Remarque',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $typeColors[$observation->type] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $typeLabels[$observation->type] ?? 'Autre' }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $observation->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <h4 class="font-medium text-gray-900 mb-1">{{ $observation->sujet }}</h4>
                        <p class="text-sm text-gray-600">{{ $observation->description }}</p>
                    </div>
                    <div>
                        @if($observation->statut === 'résolu' || $observation->statut === 'fermé')
                            <span class="badge-success">Traité</span>
                        @else
                            <span class="badge-warning">En attente</span>
                        @endif
                    </div>
                </div>
                @if($observation->reponse_admin)
                <div class="mt-4 pl-4 border-l-2 border-senelec-purple">
                    <p class="text-xs text-gray-500 mb-1">Réponse de l'administration</p>
                    <p class="text-sm text-gray-700">{{ $observation->reponse_admin }}</p>
                </div>
                @endif
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-lg font-medium">Aucun feedback envoyé</p>
                <p class="text-sm">Utilisez le formulaire ci-dessus pour envoyer votre premier feedback.</p>
            </div>
            @endforelse
        </div>
        
        @if($observations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $observations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
