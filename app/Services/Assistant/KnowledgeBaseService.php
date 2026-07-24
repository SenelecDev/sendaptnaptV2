<?php

namespace App\Services\Assistant;

class KnowledgeBaseService
{
    public function fullText(): string
    {
        $path = config('assistant.knowledge_path');

        if (! is_string($path) || ! is_file($path)) {
            return 'Documentation SENDAPTNAPT indisponible.';
        }

        return (string) file_get_contents($path);
    }

    /**
     * Extrait les sections les plus pertinentes pour une question (mode offline / contexte LLM).
     */
    public function search(string $question, int $maxChars = 3500): string
    {
        $full = $this->fullText();
        $normalized = mb_strtolower($question);
        $sections = preg_split('/\n(?=##\s)/', $full) ?: [$full];

        $keywords = $this->keywords($normalized);
        $scored = [];

        foreach ($sections as $section) {
            $hay = mb_strtolower($section);
            $score = 0;
            foreach ($keywords as $kw) {
                if ($kw !== '' && str_contains($hay, $kw)) {
                    $score += 2;
                }
            }
            if ($score > 0) {
                $scored[] = ['score' => $score, 'text' => trim($section)];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        if ($scored === []) {
            $fallback = implode("\n\n", array_slice($sections, 0, 3));

            return mb_substr($fallback, 0, $maxChars);
        }

        $out = '';
        foreach ($scored as $item) {
            $candidate = $out === '' ? $item['text'] : $out."\n\n".$item['text'];
            if (mb_strlen($candidate) > $maxChars) {
                break;
            }
            $out = $candidate;
        }

        return $out !== '' ? $out : mb_substr($full, 0, $maxChars);
    }

    /**
     * @return list<string>
     */
    private function keywords(string $question): array
    {
        $map = [
            'dapt' => ['dapt', 'demande', 'créer', 'creer', 'schema', 'ouvrage', 'brouillon', 'soumettre'],
            'gmao' => ['gmao', 'équipement', 'equipement', 'lieu', 'ligne', 'poste', 'manuel'],
            'napt' => ['napt', 'note', 'édition', 'edition', 'consignation', 'correspondant', 'service'],
            'diffusion' => ['diffusion', 'diffusions', 'hebdo', 'groupe', 'prévisual', 'previsual'],
            'verification' => ['vérif', 'verif', 'vérificateur', 'verificateur'],
            'validation' => ['valid', 'valideur'],
            'execution' => ['exécut', 'execut', 'opérateur', 'operateur', 'manœuvre', 'manoeuvre', 'fiche', 'créneau', 'creneau', 'restitution'],
            'retour' => ['retour', 'annul', 'motif'],
            'export' => ['export', 'excel', 'pdf'],
            'calendrier' => ['calendrier', 'planning', 'agenda'],
            'interim' => ['intérim', 'interim', 'absence', 'délég', 'deleg'],
            'role' => ['rôle', 'role', 'demandeur', 'desa', 'éditeur', 'editeur', 'directeur', 'admin'],
            'directeur' => ['directeur', 'supervision', 'feedback', 'statistique'],
            'admin' => ['admin', 'utilisateur', 'référentiel', 'referentiel', 'sync', 'ldap', 'oracle', 'imperson', 'journal'],
            'notification' => ['notif', 'alerte', 'rappel'],
            'observation' => ['observation', 'bug', 'suggestion'],
            'signature' => ['signature', 'profil', 'cachet'],
            'glossaire' => ['mte', 'mcce', 'glossaire', 'ue', 'de'],
        ];

        $found = [];
        foreach ($map as $group => $words) {
            foreach ($words as $w) {
                if (str_contains($question, $w)) {
                    $found[] = $w;
                    $found[] = $group;
                }
            }
        }

        $tokens = preg_split('/\W+/u', $question, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($tokens as $t) {
            if (mb_strlen($t) >= 4) {
                $found[] = $t;
            }
        }

        return array_values(array_unique($found));
    }
}
