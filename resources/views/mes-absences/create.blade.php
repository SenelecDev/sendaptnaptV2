@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #374151;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6366f1;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] .user-meta {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] .user-name {
        color: #fff !important;
    }
    .select2-dropdown {
        border-color: #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6 max-w-2xl mx-auto">
        <a href="{{ route('mes-absences.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Déclarer une absence</h1>
    </div>

    <div class="card-senelec max-w-2xl mx-auto">
        <form method="POST" action="{{ route('mes-absences.store') }}">
            @csrf

            <div class="mb-4">
                <label for="interim_id" class="label">Qui vous remplacera ? <span class="text-red-500">*</span></label>
                <select name="interim_id" id="interim_id" class="select2-users @error('interim_id') border-red-500 @enderror" required>
                    <option value="">-- Choisir un intérimaire --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                                data-matricule="{{ $user->matricule }}"
                                data-service="{{ $user->service }}"
                                data-photo="{{ $user->photo_url }}"
                                data-initials="{{ $user->initials }}"
                                {{ old('interim_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ $user->matricule ? '('.$user->matricule.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('interim_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Cette personne aura accès à vos fonctions pendant votre absence.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="date_debut" class="label">Date de début <span class="text-red-500">*</span></label>
                    <input type="date" name="date_debut" id="date_debut" 
                           value="{{ old('date_debut', now()->format('Y-m-d')) }}" 
                           min="{{ now()->format('Y-m-d') }}"
                           class="input w-full @error('date_debut') border-red-500 @enderror" required>
                    @error('date_debut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_fin" class="label">Date de fin (incluse) <span class="text-red-500">*</span></label>
                    <input type="date" name="date_fin" id="date_fin" 
                           value="{{ old('date_fin') }}" 
                           min="{{ now()->format('Y-m-d') }}"
                           class="input w-full @error('date_fin') border-red-500 @enderror" required>
                    @error('date_fin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">L'intérim sera actif jusqu'à cette date incluse.</p>
                </div>
            </div>

            <div class="mb-4">
                <label for="role" class="label">Rôle à déléguer</label>
                <select name="role" id="role" class="input w-full @error('role') border-red-500 @enderror">
                    <option value="">-- Tous mes rôles --</option>
                    @foreach($mesRoles as $role)
                        @if($role !== 'admin')
                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">
                    Choisissez un rôle spécifique ou laissez vide pour déléguer tous vos rôles.
                </p>
            </div>

            <div class="mb-6">
                <label for="motif" class="label">Motif (optionnel)</label>
                <textarea name="motif" id="motif" rows="2" 
                          class="input w-full @error('motif') border-red-500 @enderror" 
                          placeholder="Congés, mission, formation...">{{ old('motif') }}</textarea>
                @error('motif')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Résumé -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-700 mb-2">Résumé</h3>
                <p class="text-sm text-gray-600" id="summary">
                    Complétez le formulaire pour voir le résumé.
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('mes-absences.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-plus mr-2"></i> Déclarer l'absence
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Template personnalisé pour afficher les utilisateurs
    function formatUser(user) {
        if (!user.id) return user.text;
        
        var matricule = $(user.element).data('matricule') || '';
        var service = $(user.element).data('service') || '';
        var photo = $(user.element).data('photo') || '';
        var initials = $(user.element).data('initials') || user.text.charAt(0).toUpperCase();
        
        var avatarHtml;
        if (photo) {
            avatarHtml = '<span class="avatar-wrapper inline-flex"><img src="' + photo + '" class="w-8 h-8 rounded-full object-cover" alt="" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'"><span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold" style="display:none">' + initials + '</span></span>';
        } else {
            avatarHtml = '<div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">' + initials + '</div>';
        }
        
        var $container = $(
            '<div class="flex items-center gap-2 py-1">' +
                avatarHtml +
                '<div>' +
                    '<div class="font-medium user-name">' + user.text.split(' (')[0] + '</div>' +
                    '<div class="text-xs text-gray-500 user-meta">' + 
                        (matricule ? matricule : '') + 
                        (service ? ' • ' + service : '') +
                    '</div>' +
                '</div>' +
            '</div>'
        );
        
        return $container;
    }
    
    function formatUserSelection(user) {
        if (!user.id) return user.text;
        return user.text;
    }
    
    // Fonction de recherche personnalisée (nom, prénom, matricule)
    function matchCustom(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        
        if (typeof data.text === 'undefined') {
            return null;
        }
        
        var searchTerm = params.term.toLowerCase();
        var text = data.text.toLowerCase();
        var matricule = ($(data.element).data('matricule') || '').toString().toLowerCase();
        var service = ($(data.element).data('service') || '').toString().toLowerCase();
        
        // Rechercher dans le nom complet, matricule ou service
        if (text.indexOf(searchTerm) > -1 || 
            matricule.indexOf(searchTerm) > -1 ||
            service.indexOf(searchTerm) > -1) {
            return data;
        }
        
        // Recherche par mots séparés (nom ET prénom)
        var searchWords = searchTerm.split(/\s+/);
        var allWordsMatch = searchWords.every(function(word) {
            return text.indexOf(word) > -1 || 
                   matricule.indexOf(word) > -1 ||
                   service.indexOf(word) > -1;
        });
        
        if (allWordsMatch) {
            return data;
        }
        
        return null;
    }
    
    // Initialiser Select2
    $('.select2-users').select2({
        placeholder: 'Rechercher par nom, prénom ou matricule...',
        allowClear: true,
        templateResult: formatUser,
        templateSelection: formatUserSelection,
        matcher: matchCustom,
        minimumResultsForSearch: 0,
        language: {
            noResults: function() {
                return "Aucun utilisateur trouvé";
            },
            searching: function() {
                return "Recherche...";
            }
        }
    });
    
    // Focus automatique sur le champ de recherche à l'ouverture
    $(document).on('select2:open', function() {
        document.querySelector('.select2-search__field').focus();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const interimSelect = document.getElementById('interim_id');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const roleSelect = document.getElementById('role');
    const summary = document.getElementById('summary');
    
    function updateSummary() {
        const interim = interimSelect.options[interimSelect.selectedIndex]?.text || '';
        const debut = dateDebut.value ? new Date(dateDebut.value).toLocaleDateString('fr-FR') : '';
        const fin = dateFin.value ? new Date(dateFin.value).toLocaleDateString('fr-FR') : '';
        const role = roleSelect.value ? roleSelect.options[roleSelect.selectedIndex]?.text : 'Tous vos rôles';
        
        if (interim && debut && fin) {
            summary.innerHTML = `<strong>${interim.split(' (')[0]}</strong> vous remplacera du <strong>${debut}</strong> au <strong>${fin}</strong> pour <strong>${role}</strong>.`;
        } else {
            summary.textContent = 'Complétez le formulaire pour voir le résumé.';
        }
    }
    
    // Écouter les changements Select2
    $('#interim_id').on('select2:select select2:clear', updateSummary);
    dateDebut.addEventListener('change', updateSummary);
    dateFin.addEventListener('change', updateSummary);
    roleSelect.addEventListener('change', updateSummary);
    
    // Synchroniser date fin minimum avec date début
    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
        if (dateFin.value && dateFin.value < this.value) {
            dateFin.value = this.value;
        }
    });
});
</script>
@endpush
@endsection
