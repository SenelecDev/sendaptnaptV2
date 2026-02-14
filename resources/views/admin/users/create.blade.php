@extends('layouts.app')

@section('title', 'Nouvel utilisateur')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.users.index') }}" class="p-2 text-senelec-purple hover:text-senelec-magenta rounded-lg hover:bg-purple-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvel utilisateur</h1>
            <p class="text-gray-500">Créer un compte utilisateur manuellement</p>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Informations personnelles -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Informations personnelles
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="matricule" class="block text-sm font-medium text-gray-700 mb-1">
                        Matricule <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="matricule" id="matricule" value="{{ old('matricule') }}" required
                           class="input w-full @error('matricule') border-red-500 @enderror"
                           placeholder="Ex: AGT001">
                    @error('matricule')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="input w-full @error('email') border-red-500 @enderror"
                           placeholder="utilisateur@senelec.sn">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                           class="input w-full @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required
                           class="input w-full @error('prenom') border-red-500 @enderror">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
                        Téléphone
                    </label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}"
                           class="input w-full @error('telephone') border-red-500 @enderror"
                           placeholder="Ex: 77 123 45 67">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Organisation -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Organisation
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="direction" class="block text-sm font-medium text-gray-700 mb-1">
                        Direction
                    </label>
                    <input type="text" name="direction" id="direction" value="{{ old('direction') }}"
                           class="input w-full @error('direction') border-red-500 @enderror"
                           placeholder="Ex: Direction Technique">
                    @error('direction')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="departement" class="block text-sm font-medium text-gray-700 mb-1">
                        Département
                    </label>
                    <input type="text" name="departement" id="departement" value="{{ old('departement') }}"
                           class="input w-full @error('departement') border-red-500 @enderror"
                           placeholder="Ex: Maintenance">
                    @error('departement')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="service" class="block text-sm font-medium text-gray-700 mb-1">
                        Service
                    </label>
                    <input type="text" name="service" id="service" value="{{ old('service') }}"
                           class="input w-full @error('service') border-red-500 @enderror"
                           placeholder="Ex: Électricité">
                    @error('service')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="poste" class="block text-sm font-medium text-gray-700 mb-1">
                        Poste / Fonction
                    </label>
                    <input type="text" name="poste" id="poste" value="{{ old('poste') }}"
                           class="input w-full @error('poste') border-red-500 @enderror"
                           placeholder="Ex: Technicien Électricien">
                    @error('poste')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="groupe_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Groupe
                    </label>
                    <select name="groupe_id" id="groupe_id"
                            class="input w-full @error('groupe_id') border-red-500 @enderror">
                        <option value="">-- Aucun groupe --</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('groupe_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Rôles -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Rôles applicatifs
            </h2>
            
            <p class="text-sm text-gray-500 mb-4">Sélectionnez un ou plusieurs rôles pour cet utilisateur</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($roles as $role)
                    @php
                        $roleColors = [
                            'admin' => 'border-red-300 bg-red-50 hover:bg-red-100',
                            'demandeur' => 'border-blue-300 bg-blue-50 hover:bg-blue-100',
                            'desa' => 'border-purple-300 bg-purple-50 hover:bg-purple-100',
                            'verificateur' => 'border-indigo-300 bg-indigo-50 hover:bg-indigo-100',
                            'valideur' => 'border-green-300 bg-green-50 hover:bg-green-100',
                            'operateur' => 'border-amber-300 bg-amber-50 hover:bg-amber-100',
                            'operateurchef' => 'border-orange-300 bg-orange-50 hover:bg-orange-100',
                            'directeur' => 'border-violet-300 bg-violet-50 hover:bg-violet-100',
                        ];
                        $roleDescriptions = [
                            'admin' => 'Accès complet',
                            'demandeur' => 'Créer des DAPT',
                            'desa' => 'Gérer DAPT/NAPT',
                            'verificateur' => 'Vérifier les NAPT',
                            'valideur' => 'Valider les NAPT',
                            'operateur' => 'Exécuter les NAPT',
                            'operateurchef' => 'Opérateur + Fiche',
                            'directeur' => 'Consultation',
                        ];
                    @endphp
                    <label class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition-all
                                  {{ $roleColors[$role->name] ?? 'border-gray-200 bg-gray-50 hover:bg-gray-100' }}
                                  {{ in_array($role->name, old('roles', [])) ? 'ring-2 ring-senelec-magenta' : '' }}">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                               {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                               class="w-5 h-5 text-senelec-magenta border-gray-300 rounded focus:ring-senelec-magenta">
                        <span class="text-sm font-semibold text-gray-700">{{ ucfirst($role->name) }}</span>
                        <span class="text-xs text-gray-500 text-center">{{ $roleDescriptions[$role->name] ?? '' }}</span>
                    </label>
                @endforeach
            </div>
            @error('roles')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Authentification -->
        <div class="card-senelec p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-senelec-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Authentification
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                           class="input w-full @error('password') border-red-500 @enderror"
                           placeholder="Minimum 8 caractères">
                    <p class="mt-1 text-xs text-gray-500">Sera utilisé uniquement si l'authentification LDAP échoue</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmer le mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="input w-full">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.users.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                Annuler
            </a>
            <button type="submit" class="btn-senelec">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Créer l'utilisateur
            </button>
        </div>
    </form>
</div>
@endsection
