<?php

namespace App\Support;

use App\Models\Note;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NaptQueryFilters
{
    use SearchableTrait;

    public const TYPE_OUVRAGE_OPTIONS = [
        'poste' => 'Poste',
        'ligne' => 'Ligne',
        'reactance' => 'Réactance',
        'transformateur' => 'Transformateur',
        'coupe_circuit' => 'Coupe-circuit / Disjoncteur',
        'cable' => 'Câble',
    ];

    /** @var array<string, string[]> */
    public const TYPE_OUVRAGE_KEYWORDS = [
        'poste' => ['poste', 'pst'],
        'ligne' => ['ligne', 'lign'],
        'reactance' => ['reactance', 'réactance', 'react'],
        'transformateur' => ['transformateur', 'transfo', 'trf'],
        'coupe_circuit' => ['coupe-circuit', 'coupe circuit', 'disjoncteur', 'cc '],
        'cable' => ['cable', 'câble'],
    ];

    /**
     * @param  bool  $joined  Requête avec join demandes + users (préfixe notes./demandes./users.)
     */
    public function apply(Builder $query, Request $request, bool $joined = false): Builder
    {
        if ($joined) {
            $this->applyJoinedFilters($query, $request);
        } else {
            $this->applyRelationFilters($query, $request);
        }

        return $query;
    }

    protected function applyJoinedFilters(Builder $query, Request $request): void
    {
        if ($request->filled('search')) {
            $this->applySimpleSearch(
                $query,
                $request->search,
                [
                    'notes.numero_note',
                    'demandes.numero_demande',
                    'demandes.lieu_execution',
                    'demandes.ouvrages_consigner_manuel',
                    'users.name',
                    'users.nom',
                    'users.prenom',
                    'users.matricule',
                ],
                [],
                function ($q, $pattern) {
                    $this->applyDemandeJsonSearch($q, $pattern, 'demandes.');
                }
            );
        }

        if ($request->filled('demandeur')) {
            $this->applySimpleSearch(
                $query,
                $request->demandeur,
                ['users.name', 'users.nom', 'users.prenom', 'users.matricule']
            );
        }

        $this->applyOuvrageFilters($query, $request, joined: true);
        $this->applyCommonFilters($query, $request, joined: true);
    }

    protected function applyRelationFilters(Builder $query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where(function ($outer) use ($request) {
                $outer->where(function ($q) use ($request) {
                    $this->applySimpleSearch($q, $request->search, ['numero_note']);
                });
                $outer->orWhereHas('demande', function ($dq) use ($request) {
                    $this->applySimpleSearch(
                        $dq,
                        $request->search,
                        ['numero_demande', 'lieu_execution', 'ouvrages_consigner_manuel'],
                        ['demandeur' => ['name', 'nom', 'prenom', 'matricule']],
                        function ($sub, $pattern) {
                            $this->applyDemandeJsonSearch($sub, $pattern);
                        }
                    );
                });
            });
        }

        if ($request->filled('demandeur')) {
            $query->whereHas('demande.demandeur', function ($q) use ($request) {
                $this->applySimpleSearch($q, $request->demandeur, ['name', 'nom', 'prenom', 'matricule']);
            });
        }

        $this->applyOuvrageFilters($query, $request, joined: false);
        $this->applyCommonFilters($query, $request, joined: false);
    }

    protected function applyOuvrageFilters(Builder $query, Request $request, bool $joined): void
    {
        if ($request->filled('ouvrage')) {
            $term = $request->ouvrage;
            if ($joined) {
                $this->applySimpleSearch(
                    $query,
                    $term,
                    ['demandes.ouvrages_consigner_manuel', 'demandes.lieu_execution'],
                    [],
                    function ($q, $pattern) {
                        $this->applyDemandeJsonSearch($q, $pattern, 'demandes.');
                    }
                );
            } else {
                $query->whereHas('demande', function ($dq) use ($term) {
                    $this->applySimpleSearch(
                        $dq,
                        $term,
                        ['ouvrages_consigner_manuel', 'lieu_execution'],
                        [],
                        function ($sub, $pattern) {
                            $this->applyDemandeJsonSearch($sub, $pattern);
                        }
                    );
                });
            }
        }

        if ($request->filled('type_ouvrage')) {
            $keywords = self::TYPE_OUVRAGE_KEYWORDS[$request->type_ouvrage] ?? [];
            if (! empty($keywords)) {
                $query->where(function ($q) use ($keywords, $joined) {
                    foreach ($keywords as $keyword) {
                        $pattern = '%'.$this->normalizeSearchTerm($keyword).'%';
                        if ($joined) {
                            $q->orWhere(function ($sub) use ($pattern) {
                                $sub->whereRaw('LOWER(demandes.ouvrages_consigner_manuel) LIKE ?', [$pattern])
                                    ->orWhereRaw('LOWER(demandes.lieu_execution) LIKE ?', [$pattern]);
                                $this->applyDemandeJsonSearch($sub, $pattern, 'demandes.');
                            });
                        } else {
                            $q->orWhereHas('demande', function ($dq) use ($pattern) {
                                $dq->where(function ($sub) use ($pattern) {
                                    $sub->whereRaw('LOWER(ouvrages_consigner_manuel) LIKE ?', [$pattern])
                                        ->orWhereRaw('LOWER(lieu_execution) LIKE ?', [$pattern]);
                                    $this->applyDemandeJsonSearch($sub, $pattern);
                                });
                            });
                        }
                    }
                });
            }
        }
    }

    /**
     * Recherche dans les champs JSON ouvrages de la table demandes
     * (lignes_oracle / equipements_oracle / ouvrages_consigner_gmao).
     */
    protected function applyDemandeJsonSearch($query, string $pattern, string $prefix = ''): void
    {
        $driver = DB::connection()->getDriverName();
        $columns = [
            $prefix.'ouvrages_consigner_gmao',
            $prefix.'lignes_oracle',
            $prefix.'equipements_oracle',
        ];

        foreach ($columns as $column) {
            if ($driver === 'mysql') {
                $query->orWhereRaw('LOWER(CAST(COALESCE('.$column.', "[]") AS CHAR)) LIKE ?', [$pattern]);
            } elseif ($driver === 'pgsql') {
                $query->orWhereRaw('LOWER(COALESCE('.$column.'::text, \'[]\')) LIKE ?', [$pattern]);
            }
        }
    }

    protected function applyCommonFilters(Builder $query, Request $request, bool $joined): void
    {
        $notesTable = $joined ? 'notes.' : '';
        $usersTable = $joined ? 'users.' : '';

        if ($request->filled('statut')) {
            $statut = $this->resolveStatut($request->statut);
            $query->where($notesTable.'statut', $statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate($notesTable.'ddt', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate($notesTable.'dft', '<=', $request->date_fin);
        }

        $semaine = $request->input('semaine', $request->input('numero_semaine'));
        if ($request->filled('semaine') || $request->filled('numero_semaine')) {
            $query->where($notesTable.'numero_semaine', $semaine);
        }

        if ($request->filled('annee')) {
            $query->whereYear($notesTable.'ddt', $request->annee);
        }

        if ($request->filled('groupe_id')) {
            if ($joined) {
                $query->where($usersTable.'groupe_id', $request->groupe_id);
            } else {
                $query->whereHas('demande.demandeur', function ($q) use ($request) {
                    $q->where('groupe_id', $request->groupe_id);
                });
            }
        }
    }

    public function resolveStatut(string $statut): string
    {
        $statutMap = [
            'brouillon' => Note::STATUT_BROUILLON,
            'en_etude' => Note::STATUT_EN_ETUDE,
            'en étude' => Note::STATUT_EN_ETUDE,
            'en_attente_verification' => Note::STATUT_EN_ATTENTE_VERIFICATION,
            'en attente de vérification' => Note::STATUT_EN_ATTENTE_VERIFICATION,
            'verifiee' => Note::STATUT_VERIFIEE,
            'vérifiée' => Note::STATUT_VERIFIEE,
            'en_attente_validation' => Note::STATUT_EN_ATTENTE_VALIDATION,
            'en attente de validation' => Note::STATUT_EN_ATTENTE_VALIDATION,
            'validee' => Note::STATUT_VALIDEE,
            'validée' => Note::STATUT_VALIDEE,
            'en_cours_execution' => Note::STATUT_EN_COURS_EXECUTION,
            'en cours d\'exécution' => Note::STATUT_EN_COURS_EXECUTION,
            'executee' => Note::STATUT_EXECUTEE,
            'exécutée' => Note::STATUT_EXECUTEE,
            'retournee' => Note::STATUT_RETOURNEE,
            'retournée' => Note::STATUT_RETOURNEE,
            'annulee' => Note::STATUT_ANNULEE,
            'annulée' => Note::STATUT_ANNULEE,
            'établie' => Note::STATUT_BROUILLON,
        ];

        return $statutMap[$statut] ?? $statut;
    }
}
