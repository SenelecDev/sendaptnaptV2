@php
    use App\Support\NaptQueryFilters;
    $typeOuvrageOptions = NaptQueryFilters::TYPE_OUVRAGE_OPTIONS;
@endphp

{{-- Filtres NAPT partagés (liste DESA + export Excel) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div>
        <label class="label">Recherche générale</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="N° NAPT, N° DAPT, lieu..."
               class="{{ $inputClass ?? 'input-senelec w-full' }}">
    </div>
    <div>
        <label class="label">Demandeur</label>
        <input type="text" name="demandeur" value="{{ request('demandeur') }}"
               placeholder="Nom ou matricule"
               class="{{ $inputClass ?? 'input-senelec w-full' }}">
    </div>
    <div>
        <label class="label">Ouvrage</label>
        <input type="text" name="ouvrage" value="{{ request('ouvrage') }}"
               placeholder="Poste, ligne, réactance..."
               class="{{ $inputClass ?? 'input-senelec w-full' }}">
    </div>
    <div>
        <label class="label">Type d'ouvrage</label>
        <select name="type_ouvrage" class="{{ $inputClass ?? 'input-senelec w-full' }}">
            <option value="">Tous les types</option>
            @foreach($typeOuvrageOptions as $value => $label)
                <option value="{{ $value }}" @selected(request('type_ouvrage') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Date début (à partir de)</label>
        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
               class="{{ $inputClass ?? 'input-senelec w-full' }}">
    </div>
    <div>
        <label class="label">Date fin (jusqu'au)</label>
        <input type="date" name="date_fin" value="{{ request('date_fin') }}"
               class="{{ $inputClass ?? 'input-senelec w-full' }}">
    </div>
    <div>
        <label class="label">Semaine</label>
        <select name="{{ $semaineFieldName ?? 'semaine' }}" class="{{ $inputClass ?? 'input-senelec w-full' }}">
            <option value="">Toutes</option>
            @for($i = 1; $i <= 53; $i++)
                <option value="{{ $i }}" @selected(request($semaineFieldName ?? 'semaine', request('numero_semaine')) == $i)>Semaine {{ $i }}</option>
            @endfor
        </select>
    </div>
    <div>
        <label class="label">Année</label>
        <select name="annee" class="{{ $inputClass ?? 'input-senelec w-full' }}">
            <option value="">Toutes</option>
            @foreach([date('Y'), date('Y') - 1, date('Y') - 2] as $year)
                <option value="{{ $year }}" @selected((string) request('annee') === (string) $year)>{{ $year }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Statut</label>
        <select name="statut" class="{{ $inputClass ?? 'input-senelec w-full' }}">
            <option value="">Tous les statuts</option>
            @foreach([
                'brouillon' => 'Brouillon',
                'en_etude' => 'En étude',
                'en_attente_verification' => 'À vérifier',
                'verifiee' => 'Vérifiée',
                'en_attente_validation' => 'À valider',
                'validee' => 'Validée',
                'en_cours_execution' => 'En exécution',
                'executee' => 'Exécutée',
                'retournee' => 'Retournée',
                'annulee' => 'Annulée',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(request('statut') === $value || request('statut') === str_replace('_', ' ', $value))>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    @if($showGroupe ?? true)
    <div>
        <label class="label">Groupe demandeur</label>
        <select name="groupe_id" class="{{ $inputClass ?? 'input-senelec w-full' }}">
            <option value="">Tous les groupes</option>
            @foreach(($groupes ?? \App\Models\Groupe::orderBy('nom')->get()) as $groupe)
                <option value="{{ $groupe->id }}" @selected((string) request('groupe_id') === (string) $groupe->id)>{{ $groupe->nom }}</option>
            @endforeach
        </select>
    </div>
    @endif
</div>
