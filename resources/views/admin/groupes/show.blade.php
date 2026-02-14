@extends('layouts.app')

@section('title', 'Détails du groupe')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 4px 8px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #00a650;
        box-shadow: 0 0 0 2px rgba(0, 166, 80, 0.2);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #00a650;
        border: none;
        color: white;
        border-radius: 0.375rem;
        padding: 4px 8px;
        margin: 2px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fecaca;
        background: transparent;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #00a650;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] .user-name,
    .select2-container--default .select2-results__option--highlighted[aria-selected] .user-meta {
        color: #fff !important;
    }
    .select2-dropdown {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 8px;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 4px;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.groupes.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $groupe->nom }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.groupes.edit', $groupe) }}" class="btn-senelec">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <!-- Informations du groupe -->
    <div class="card-senelec">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-16 h-16 bg-senelec-purple/10 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900">{{ $groupe->nom }}</h2>
                <p class="text-gray-600 mt-1">{{ $groupe->description ?? 'Aucune description' }}</p>
                <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
                    <span>Créé le {{ $groupe->created_at->format('d/m/Y') }}</span>
                    <span>•</span>
                    <span>{{ $groupe->users->count() }} membre(s)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des membres -->
    <div class="card-senelec">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Membres du groupe</h3>
            <span class="badge badge-info">{{ $groupe->users->count() }} membre(s)</span>
        </div>
        
        <!-- Formulaire d'ajout de membre -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <label class="label mb-2">Ajouter des membres</label>
            <form id="addUserForm" class="flex gap-3 items-start">
                @csrf
                <div class="flex-1">
                    <select id="user_select" name="user_ids[]" class="w-full" multiple>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}" 
                                    data-matricule="{{ $user->matricule }}"
                                    data-service="{{ $user->service }}"
                                    data-initials="{{ strtoupper(substr($user->name, 0, 2)) }}"
                                    data-photo="{{ $user->matricule && file_exists(public_path('profil/' . $user->matricule . '.jpg')) ? asset('profil/' . $user->matricule . '.jpg') : '' }}">
                                {{ $user->name }} {{ $user->matricule ? '('.$user->matricule.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-senelec whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Ajouter
                </button>
            </form>
        </div>
        
        @if($groupe->users->count() > 0)
            <!-- Recherche des membres -->
            <div class="mb-4">
                <input type="text" id="searchMembers" placeholder="Rechercher un membre..." 
                       class="input w-full">
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="membersTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($groupe->users as $user)
                            <tr class="hover:bg-gray-50 member-row" 
                                data-name="{{ strtolower($user->name) }}" 
                                data-matricule="{{ strtolower($user->matricule) }}"
                                data-email="{{ strtolower($user->email) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->matricule && file_exists(public_path('profil/' . $user->matricule . '.jpg')))
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ asset('profil/' . $user->matricule . '.jpg') }}" 
                                                     alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-senelec-purple flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-900">{{ $user->matricule }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-info">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-senelec-magenta hover:underline">
                                            Voir profil
                                        </a>
                                        <button type="button" 
                                                onclick="removeUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                class="text-red-600 hover:text-red-800" 
                                                title="Retirer du groupe">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p class="mt-2 text-gray-500">Aucun membre dans ce groupe</p>
                <p class="text-sm text-gray-400">Utilisez le formulaire ci-dessus pour ajouter des membres</p>
            </div>
        @endif
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
        var initials = $(user.element).data('initials') || user.text.charAt(0).toUpperCase();
        var photo = $(user.element).data('photo') || '';
        
        var avatarHtml;
        if (photo) {
            avatarHtml = '<img src="' + photo + '" class="w-8 h-8 rounded-full object-cover" alt="">';
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
    
    // Initialiser Select2
    $('#user_select').select2({
        placeholder: 'Rechercher des utilisateurs...',
        allowClear: true,
        templateResult: formatUser,
        closeOnSelect: false,
        language: {
            noResults: function() {
                return "Aucun utilisateur trouvé";
            },
            searching: function() {
                return "Recherche...";
            }
        }
    });
    
    // Formulaire d'ajout
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        var userIds = $('#user_select').val();
        if (!userIds || userIds.length === 0) {
            alert('Veuillez sélectionner au moins un utilisateur');
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.groupes.add-user", $groupe) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_ids: userIds
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.message || 'Une erreur est survenue';
                alert(message);
            }
        });
    });
    
    // Recherche dans la liste des membres
    $('#searchMembers').on('keyup', function() {
        var searchText = $(this).val().toLowerCase().trim();
        
        $('.member-row').each(function() {
            var name = $(this).data('name') || '';
            var matricule = $(this).data('matricule') || '';
            var email = $(this).data('email') || '';
            
            if (name.includes(searchText) || matricule.includes(searchText) || email.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Afficher le nombre de résultats
        var visibleCount = $('.member-row:visible').length;
        var totalCount = $('.member-row').length;
        
        if (searchText && visibleCount !== totalCount) {
            $('#searchResultsInfo').remove();
            $('#membersTable').before('<p id="searchResultsInfo" class="text-sm text-gray-500 mb-2">' + visibleCount + ' résultat(s) sur ' + totalCount + '</p>');
        } else {
            $('#searchResultsInfo').remove();
        }
    });
});

// Fonction pour retirer un utilisateur
function removeUser(userId, userName) {
    if (!confirm('Voulez-vous vraiment retirer ' + userName + ' de ce groupe ?')) {
        return;
    }
    
    $.ajax({
        url: '{{ url("admin/groupes/{$groupe->id}/remove-user") }}/' + userId,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Une erreur est survenue';
            alert(message);
        }
    });
}
</script>
@endpush
