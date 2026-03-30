@extends('layouts.app')

@section('title', 'Exécuter NAPT ' . $note->numero_note)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div>
        <a href="{{ route('operateur.notes.show', $note) }}" class="text-senelec-purple hover:text-senelec-magenta text-sm mb-2 inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la note
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Exécuter NAPT {{ $note->numero_note }}</h1>
        <p class="text-gray-600">Semaine {{ $note->numero_semaine }} - {{ $note->date?->format('d/m/Y') }}</p>
    </div>

    <!-- Info DAPT -->
    @if($note->demande)
    <div class="card-senelec p-6 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500">DAPT</p>
                <p class="font-medium">{{ $note->demande->numero_demande }}</p>
            </div>
            <div>
                <p class="text-gray-500">Lieu d'exécution</p>
                <p class="font-medium">{{ $note->demande->lieu_execution ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Désignation</p>
                <p class="font-medium">{{ Str::limit($note->demande->designation, 80) ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statut actuel -->
    <div class="card-senelec p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut actuel</h3>
        @php
            $statusColors = [
                'validée' => 'bg-blue-100 text-blue-800 border-blue-200',
                'en cours d\'exécution' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            ];
            $colorClass = $statusColors[$note->statut] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        @endphp
        <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full border {{ $colorClass }}">
            {{ ucfirst($note->statut) }}
        </span>
        
        @if($note->fiche_manoeuvre)
        <div class="mt-4 flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm text-green-800">Fiche de manœuvre jointe</span>
            </div>
            <a href="{{ Storage::url($note->fiche_manoeuvre) }}" target="_blank" class="text-green-700 hover:text-green-900 text-sm underline">
                Voir la fiche
            </a>
        </div>
        @endif
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Actions selon le statut -->
    @if($note->statut === 'validée')
        <!-- Démarrer l'exécution -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Démarrer l'exécution</h3>
            <p class="text-gray-600 mb-6">
                En démarrant l'exécution, saisissez la date/heure réelle de début.
            </p>
            
            <form action="{{ route('operateur.notes.update', $note) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="demarrer">

                <div class="mb-6">
                    <label for="dre_reel" class="label">
                        Date/heure réelle début d'exécution <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="dre_reel" name="dre_reel"
                           value="{{ old('dre_reel', now()->format('Y-m-d\TH:i')) }}"
                           class="input-senelec w-full md:w-96" required>
                </div>
                
                <div class="flex justify-end gap-4">
                    <a href="{{ route('operateur.notes.show', $note) }}" class="btn-senelec-outline">
                        Annuler
                    </a>
                    <button type="submit" class="btn-senelec bg-yellow-600 hover:bg-yellow-700">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Démarrer l'exécution
                    </button>
                </div>
            </form>
        </div>
    @elseif($note->statut === 'en cours d\'exécution')
        <!-- Terminer l'exécution -->
        <div class="card-senelec p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Terminer l'exécution</h3>
            <p class="text-gray-600 mb-6">
                Veuillez indiquer les dates réelles de début et fin des travaux.
            </p>
            
            <form action="{{ route('operateur.notes.update', $note) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="terminer">
                
                @if($note->demande && $note->demande->dmrp_restitution)
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">Créneaux journaliers (restitution le soir)</h4>
                        <p class="text-sm text-blue-700 mb-3">
                            Saisissez un début/fin d'exécution pour chaque journée concernée.
                        </p>

                        <div id="slots-container" class="space-y-3">
                            @php
                                $oldSlots = old('execution_slots', $note->execution_slots ?? []);
                                if (empty($oldSlots)) {
                                    $oldSlots = [['start' => '', 'end' => '']];
                                }
                            @endphp
                            @foreach($oldSlots as $i => $slot)
                                @php
                                    $slotStart = isset($slot['start']) ? str_replace(' ', 'T', substr($slot['start'], 0, 16)) : '';
                                    $slotEnd = isset($slot['end']) ? str_replace(' ', 'T', substr($slot['end'], 0, 16)) : '';
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 slot-row">
                                    <input type="datetime-local" name="execution_slots[{{ $i }}][start]"
                                           value="{{ $slotStart }}" class="input-senelec w-full" placeholder="Début">
                                    <div class="flex gap-2">
                                        <input type="datetime-local" name="execution_slots[{{ $i }}][end]"
                                               value="{{ $slotEnd }}" class="input-senelec w-full" placeholder="Fin">
                                        <button type="button" class="px-3 py-2 border rounded text-red-600 remove-slot">-</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-slot" class="mt-3 px-3 py-2 border rounded text-blue-700 hover:bg-blue-100">
                            + Ajouter un créneau
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="ddt" class="label">
                                Date/heure début des travaux <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="ddt" name="ddt" 
                                   value="{{ old('ddt', $note->dre?->format('Y-m-d\TH:i')) }}" 
                                   class="input-senelec w-full" required>
                        </div>
                        <div>
                            <label for="dft" class="label">
                                Date/heure fin des travaux <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="dft" name="dft" 
                                   value="{{ old('dft') }}" 
                                   class="input-senelec w-full" required>
                        </div>
                    </div>
                @endif
                
                <div class="flex justify-end gap-4">
                    <a href="{{ route('operateur.notes.show', $note) }}" class="btn-senelec-outline">
                        Annuler
                    </a>
                    <button type="submit" class="btn-senelec bg-green-600 hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terminer l'exécution
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

@if($note->demande && $note->demande->dmrp_restitution)
<script>
    (function () {
        const container = document.getElementById('slots-container');
        const addBtn = document.getElementById('add-slot');
        if (!container || !addBtn) return;

        const buildRow = (index) => {
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 md:grid-cols-2 gap-3 slot-row';
            row.innerHTML = `
                <input type="datetime-local" name="execution_slots[${index}][start]" class="input-senelec w-full" placeholder="Début">
                <div class="flex gap-2">
                    <input type="datetime-local" name="execution_slots[${index}][end]" class="input-senelec w-full" placeholder="Fin">
                    <button type="button" class="px-3 py-2 border rounded text-red-600 remove-slot">-</button>
                </div>
            `;
            return row;
        };

        addBtn.addEventListener('click', () => {
            const idx = container.querySelectorAll('.slot-row').length;
            container.appendChild(buildRow(idx));
        });

        container.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-slot')) return;
            const rows = container.querySelectorAll('.slot-row');
            if (rows.length <= 1) return;
            e.target.closest('.slot-row')?.remove();
        });
    })();
</script>
@endif
@endsection
