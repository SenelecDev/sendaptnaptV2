<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait pour les recherches insensibles à la casse et aux accents.
 */
trait SearchableTrait
{
    /**
     * Normalise un terme : minuscules, sans accents.
     */
    protected function normalizeSearchTerm(string $term): string
    {
        $term = mb_strtolower(trim($term));
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'œ' => 'oe',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y', 'ñ' => 'n', 'ç' => 'c', 'ß' => 's',
        ];
        return strtr($term, $accents);
    }

    /**
     * Vérifie si l'extension unaccent est disponible (PostgreSQL).
     */
    protected function hasUnaccentExtension(): bool
    {
        try {
            return DB::selectOne("SELECT 1 FROM pg_extension WHERE extname = 'unaccent'") !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Applique une recherche par mots, insensible à la casse et aux accents.
     *
     * @param Builder $query
     * @param string $search
     * @param array $columns Colonnes à rechercher (ex: ['name', 'nom', 'email'])
     * @param array $relations Relations avec colonnes (ex: ['demandeur' => ['name', 'matricule']])
     * @param bool $wordByWord Si true, chaque mot doit matcher (recommandé pour noms)
     */
    protected function applySearch(Builder $query, string $search, array $columns, array $relations = [], bool $wordByWord = true): void
    {
        $terms = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
        $terms = array_filter(array_map([$this, 'normalizeSearchTerm'], $terms));
        if (empty($terms)) {
            return;
        }

        $driver = DB::getDriverName();
        $useUnaccent = $driver === 'pgsql' && $this->hasUnaccentExtension();

        $query->where(function ($q) use ($terms, $columns, $relations, $useUnaccent, $driver) {
            foreach ($terms as $term) {
                $pattern = '%' . $term . '%';
                $q->where(function ($q2) use ($pattern, $columns, $relations, $useUnaccent, $driver) {
                    foreach ($columns as $col) {
                        $tableCol = str_contains($col, '.') ? $col : $col;
                        if ($useUnaccent) {
                            $q2->orWhereRaw("unaccent(LOWER(COALESCE({$tableCol},''))) ILIKE unaccent(?)", [$pattern]);
                        } else {
                            $q2->orWhereRaw("LOWER(COALESCE({$tableCol},'')) LIKE ?", [$pattern]);
                        }
                    }
                    foreach ($relations as $relation => $relCols) {
                        $q2->orWhereHas($relation, function ($q3) use ($pattern, $relCols, $useUnaccent, $driver) {
                            foreach ($relCols as $col) {
                                if ($useUnaccent) {
                                    $q3->orWhereRaw("unaccent(LOWER(COALESCE({$col},''))) ILIKE unaccent(?)", [$pattern]);
                                } else {
                                    $q3->orWhereRaw("LOWER(COALESCE({$col},'')) LIKE ?", [$pattern]);
                                }
                            }
                        });
                    }
                });
            }
        });
    }

    /**
     * Applique une recherche simple (un seul terme, pas de découpage par mots).
     *
     * @param callable|null $extraCallback Callback recevant ($q, $pattern) pour conditions additionnelles
     */
    protected function applySimpleSearch(Builder $query, string $search, array $columns, array $relations = [], ?callable $extraCallback = null): void
    {
        $term = $this->normalizeSearchTerm($search);
        if (empty($term)) {
            return;
        }
        $pattern = '%' . $term . '%';
        $driver = DB::getDriverName();
        $useUnaccent = $driver === 'pgsql' && $this->hasUnaccentExtension();

        $query->where(function ($q) use ($pattern, $columns, $relations, $useUnaccent, $driver, $extraCallback) {
            foreach ($columns as $col) {
                if ($useUnaccent) {
                    $q->orWhereRaw("unaccent(LOWER(COALESCE({$col},''))) ILIKE unaccent(?)", [$pattern]);
                } else {
                    $q->orWhereRaw("LOWER(COALESCE({$col},'')) LIKE ?", [$pattern]);
                }
            }
            foreach ($relations as $relation => $relCols) {
                $q->orWhereHas($relation, function ($q2) use ($pattern, $relCols, $useUnaccent, $driver) {
                    foreach ($relCols as $col) {
                        if ($useUnaccent) {
                            $q2->orWhereRaw("unaccent(LOWER(COALESCE({$col},''))) ILIKE unaccent(?)", [$pattern]);
                        } else {
                            $q2->orWhereRaw("LOWER(COALESCE({$col},'')) LIKE ?", [$pattern]);
                        }
                    }
                });
            }
            if ($extraCallback) {
                $extraCallback($q, $pattern);
            }
        });
    }
}
