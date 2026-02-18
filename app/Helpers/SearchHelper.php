<?php

namespace App\Helpers;

/**
 * Helper pour les recherches insensibles à la casse et aux accents.
 */
class SearchHelper
{
    /**
     * Normalise un terme : minuscules, sans accents.
     */
    public static function normalize(string $term): string
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
     * Découpe la recherche en termes normalisés.
     */
    public static function getSearchTerms(string $search): array
    {
        $terms = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
        return array_filter(array_map([self::class, 'normalize'], $terms));
    }
}
