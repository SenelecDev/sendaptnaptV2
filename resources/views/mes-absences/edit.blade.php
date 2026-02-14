@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6 max-w-2xl mx-auto">
        <a href="{{ route('mes-absences.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Modifier l'absence</h1>
    </div>

    <div class="card-senelec max-w-2xl mx-auto">
        <form method="POST" action="{{ route('mes-absences.update', $absence) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="interim_id" class="label">Qui vous remplacera ? <span class="text-red-500">*</span></label>
                <select name="interim_id" id="interim_id" class="input w-full @error('interim_id') border-red-500 @enderror" required>
                    <option value="">-- Choisir un intérimaire --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('interim_id', $absence->interim_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ $user->matricule ? '('.$user->matricule.')' : '' }}
                            @if($user->service) - {{ $user->service }} @endif
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
                           value="{{ old('date_debut', $absence->date_debut->format('Y-m-d')) }}" 
                           class="input w-full @error('date_debut') border-red-500 @enderror" required>
                    @error('date_debut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_fin" class="label">Date de fin (incluse) <span class="text-red-500">*</span></label>
                    <input type="date" name="date_fin" id="date_fin" 
                           value="{{ old('date_fin', $absence->date_fin->format('Y-m-d')) }}" 
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
                            <option value="{{ $role }}" {{ old('role', $absence->role) == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="motif" class="label">Motif (optionnel)</label>
                <textarea name="motif" id="motif" rows="2" 
                          class="input w-full @error('motif') border-red-500 @enderror" 
                          placeholder="Congés, mission, formation...">{{ old('motif', $absence->motif) }}</textarea>
                @error('motif')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($absence->isActive())
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-amber-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Cette absence est actuellement <strong>en cours</strong>. 
                        Les modifications seront appliquées immédiatement.
                    </p>
                </div>
            @endif

            <div class="flex justify-end space-x-3">
                <a href="{{ route('mes-absences.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
