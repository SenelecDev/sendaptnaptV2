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
        <a href="{{ route('admin.absences.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nouvelle Absence / Intérim</h1>
    </div>

    <div class="card-senelec max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.absences.store') }}">
            @csrf

            <div class="mb-4">
                <label for="user_id" class="label">Titulaire (absent) <span class="text-red-500">*</span></label>
                <select name="user_id" id="user_id" class="select2-users @error('user_id') border-red-500 @enderror" required>
                    <option value="">-- Sélectionner le titulaire --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                                data-matricule="{{ $user->matricule }}"
                                data-service="{{ $user->service }}"
                                data-photo="{{ $user->photo_url }}"
                                data-initials="{{ $user->initials }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ $user->matricule ? '('.$user->matricule.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="interim_id" class="label">Intérimaire (remplaçant) <span class="text-red-500">*</span></label>
                <select name="interim_id" id="interim_id" class="select2-users @error('interim_id') border-red-500 @enderror" required>
                    <option value="">-- Sélectionner l'intérimaire --</option>
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
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="date_debut" class="label">Date de début <span class="text-red-500">*</span></label>
                    <input type="date" name="date_debut" id="date_debut" 
                           value="{{ old('date_debut') }}" 
                           class="input w-full @error('date_debut') border-red-500 @enderror" required>
                    @error('date_debut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_fin" class="label">Date de fin <span class="text-red-500">*</span></label>
                    <input type="date" name="date_fin" id="date_fin" 
                           value="{{ old('date_fin') }}" 
                           class="input w-full @error('date_fin') border-red-500 @enderror" required>
                    @error('date_fin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="role" class="label">Rôle concerné</label>
                <select name="role" id="role" class="input w-full @error('role') border-red-500 @enderror">
                    <option value="">-- Tous les rôles --</option>
                    <option value="demandeur" {{ old('role') == 'demandeur' ? 'selected' : '' }}>Demandeur</option>
                    <option value="desa" {{ old('role') == 'desa' ? 'selected' : '' }}>DESA</option>
                    <option value="verificateur" {{ old('role') == 'verificateur' ? 'selected' : '' }}>Vérificateur</option>
                    <option value="valideur" {{ old('role') == 'valideur' ? 'selected' : '' }}>Valideur</option>
                    <option value="operateur" {{ old('role') == 'operateur' ? 'selected' : '' }}>Opérateur</option>
                    <option value="operateurchef" {{ old('role') == 'operateurchef' ? 'selected' : '' }}>Opérateur Chef</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Laissez vide si l'intérim couvre tous les rôles du titulaire.</p>
            </div>

            <div class="mb-6">
                <label for="motif" class="label">Motif</label>
                <textarea name="motif" id="motif" rows="3" 
                          class="input w-full @error('motif') border-red-500 @enderror" 
                          placeholder="Raison de l'absence (congés, maladie, mission...)">{{ old('motif') }}</textarea>
                @error('motif')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.absences.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

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
        // Si pas de terme de recherche, afficher tout
        if ($.trim(params.term) === '') {
            return data;
        }
        
        // Si pas de texte, ne pas afficher
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
    
    // Initialiser Select2 pour les utilisateurs
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
            },
            inputTooShort: function() {
                return "Saisissez pour rechercher...";
            }
        }
    });
    
    // Focus automatique sur le champ de recherche à l'ouverture
    $(document).on('select2:open', function() {
        document.querySelector('.select2-search__field').focus();
    });
});
</script>
@endpush
