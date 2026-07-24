<?php

namespace App\Services\Assistant;

use App\Models\User;

class OfflineResponder
{
    public function __construct(
        private readonly KnowledgeBaseService $knowledge,
        private readonly AssistantToolService $tools,
    ) {
    }

    /**
     * @param  list<array{role: string, content: string}>  $history
     * @return array{reply: string, mode: string, tools: list<string>}
     */
    public function respond(string $question, User $user, array $history = [], ?string $geminiError = null): array
    {
        $q = mb_strtolower($question);
        $used = [];
        $parts = [];

        $roles = $user->getRoleNames()->implode(', ') ?: 'demandeur';
        $hasGeminiKey = filled(config('assistant.gemini.api_key'));

        if ($this->wantsData($q, ['dapt', 'demande'])) {
            $result = $this->tools->execute('get_my_demandes', ['limit' => 10], $user, $this->knowledge);
            $used[] = 'get_my_demandes';
            $parts[] = $this->formatDemandes($result);
        }

        if ($this->wantsData($q, ['napt', 'note'])) {
            $result = $this->tools->execute('get_my_notes', ['limit' => 10], $user, $this->knowledge);
            $used[] = 'get_my_notes';
            $parts[] = $this->formatNotes($result);
        }

        if ($this->wantsData($q, ['attente', 'pending', 'file', 'à faire', 'a faire', 'queue', 'en attente'])) {
            $result = $this->tools->execute('get_pending_queue', ['limit' => 10], $user, $this->knowledge);
            $used[] = 'get_pending_queue';
            $parts[] = $this->formatPending($result);
        }

        if ($this->wantsData($q, ['stat', 'compteur', 'combien'])) {
            $result = $this->tools->execute('get_napt_stats', [], $user, $this->knowledge);
            $used[] = 'get_napt_stats';
            $parts[] = $this->formatStats($result);
        }

        if ($parts === [] || $this->wantsHelp($q)) {
            $help = $this->knowledge->search($question, 2200);
            $parts[] = $this->formatHelp($question, $help);
            $used[] = 'search_help';
        }

        if ($hasGeminiKey) {
            $header = "Je réponds en mode local (Gemini injoignable). Rôle : {$roles}.";
            if (filled($geminiError)) {
                $header .= ' Cause : '.mb_substr($geminiError, 0, 220);
            } else {
                $header .= ' Domaine HTTPS : generativelanguage.googleapis.com.';
            }
        } else {
            $header = "Je réponds en mode local. Rôle : {$roles}.";
        }

        return [
            'reply' => $header."\n\n".implode("\n\n", array_filter($parts)),
            'mode' => 'offline',
            'tools' => array_values(array_unique($used)),
        ];
    }

    private function wantsHelp(string $q): bool
    {
        return $this->wantsData($q, [
            'comment', 'aide', 'tutoriel', 'tuto', 'fonctionne', 'validation', 'vérif', 'verif',
            'workflow', 'étape', 'etape', 'quoi', 'créer', 'creer', 'retour', 'annul',
            'manœuvre', 'manoeuvre', 'intérim', 'interim', 'export', 'signature',
            'diffusion', 'gmao', 'calendrier', 'observation', 'feedback', 'directeur',
            'admin', 'sync', 'ldap', 'oracle', 'imperson', 'notification', 'brouillon',
            'consignation', 'correspondant', 'restitution', 'profil', 'mte', 'mcce',
            'glossaire', 'fiche', 'soumettre', 'groupe',
        ]);
    }

    /**
     * @param  list<string>  $needles
     */
    private function wantsData(string $q, array $needles): bool
    {
        foreach ($needles as $n) {
            if (str_contains($q, $n)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array{ok: bool, data?: mixed, message?: string}  $result
     */
    private function formatDemandes(array $result): string
    {
        if (! ($result['ok'] ?? false)) {
            return $result['message'] ?? 'Impossible de lire les DAPT.';
        }

        $rows = $result['data']['demandes'] ?? [];
        if ($rows === []) {
            return "Aucune DAPT trouvée.\nPour en créer une : Demandeur → Mes DAPT → Nouvelle demande (schéma obligatoire).";
        }

        $lines = ['Voici les DAPT récentes :'];
        foreach ($rows as $r) {
            $lines[] = sprintf(
                '• %s — %s (statut : %s)',
                $r['numero'] ?? '#'.$r['id'],
                $r['designation'] ?: 'Sans désignation',
                $r['statut'] ?? '-'
            );
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array{ok: bool, data?: mixed, message?: string}  $result
     */
    private function formatNotes(array $result): string
    {
        if (! ($result['ok'] ?? false)) {
            return $result['message'] ?? 'Impossible de lire les NAPT.';
        }

        $rows = $result['data']['notes'] ?? [];
        if ($rows === []) {
            return "Aucune NAPT dans votre périmètre.\nLe DESA crée la NAPT à partir d'une DAPT (« Faire NAPT »).";
        }

        $lines = ['Voici les NAPT récentes :'];
        foreach ($rows as $r) {
            $fiche = ! empty($r['fiche_manoeuvre']) ? ' — fiche manœuvre OK' : '';
            $lines[] = sprintf(
                '• %s (DAPT %s) — statut : %s%s',
                $r['numero'] ?? '#'.$r['id'],
                $r['dapt'] ?? '?',
                $r['statut'] ?? '-',
                $fiche
            );
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array{ok: bool, data?: mixed, message?: string}  $result
     */
    private function formatPending(array $result): string
    {
        if (! ($result['ok'] ?? false)) {
            return $result['message'] ?? 'Impossible de lire la file d’attente.';
        }

        $rows = $result['data']['file'] ?? [];
        if ($rows === []) {
            return 'Rien en attente d’action pour votre rôle pour le moment.';
        }

        $lines = ['Éléments en attente :'];
        foreach ($rows as $r) {
            $lines[] = sprintf(
                '• [%s] %s — %s (%s)',
                $r['type'] ?? '?',
                $r['numero'] ?? '?',
                $r['libelle'] ?? '',
                $r['statut'] ?? '-'
            );
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array{ok: bool, data?: mixed, message?: string}  $result
     */
    private function formatStats(array $result): string
    {
        if (! ($result['ok'] ?? false)) {
            return $result['message'] ?? 'Statistiques non disponibles pour votre rôle.';
        }

        $par = $result['data']['par_statut'] ?? [];
        if ($par === []) {
            return 'Aucune NAPT en base.';
        }

        $lines = ['Répartition des NAPT par statut :'];
        foreach ($par as $statut => $total) {
            $lines[] = sprintf('• %s : %d', $statut, (int) $total);
        }
        $lines[] = 'Total : '.((int) ($result['data']['total'] ?? 0));

        return implode("\n", $lines);
    }

    private function formatHelp(string $question, string $help): string
    {
        $q = mb_strtolower($question);

        if (str_contains($q, 'diffusion')) {
            return "Diffusion hebdomadaire :\n1. Menu Éditeur (DESA) → Diffusions\n2. Semaine + année (+ filtre statut optionnel)\n3. Sélectionner les groupes\n4. Prévisualiser puis Envoyer (email + PDF combiné)\n\n".$help;
        }

        if (str_contains($q, 'gmao') || (str_contains($q, 'ouvrage') && str_contains($q, 'comment'))) {
            return "Mode GMAO sur une DAPT :\n1. Choisir mode de saisie GMAO\n2. Sélectionner le lieu d’exécution\n3. Ajouter ouvrages à consigner / installer\nSinon : mode manuel (texte libre).\n\n".$help;
        }

        if (str_contains($q, 'créer') || str_contains($q, 'creer') || (str_contains($q, 'dapt') && str_contains($q, 'comment'))) {
            return "Pour créer une DAPT :\n1. Menu Demandeur → Demandes → Nouvelle demande\n2. Période, désignation, schéma (obligatoire)\n3. Ouvrages (GMAO ou manuel) + chargé de travaux\n4. Enregistrer brouillon OU Valider et soumettre (statut créée)\n\n".$help;
        }

        if (str_contains($q, 'fiche') || str_contains($q, 'manœuvre') || str_contains($q, 'manoeuvre')) {
            return "Fiche manœuvre :\n1. Opérateur Chef ouvre une NAPT validée\n2. Joint un PDF/JPG/PNG (max 10 Mo)\n3. Sans fiche, l’opérateur ne peut pas démarrer l’exécution\n\n".$help;
        }

        if (str_contains($q, 'intérim') || str_contains($q, 'interim') || str_contains($q, 'absence')) {
            return "Intérim :\n1. Menu Absences → Nouvelle absence\n2. Dates + rôle(s) à déléguer\n3. Choisir l’intérimaire\nBadge INTÉRIM dans la sidebar pendant la période.\n\n".$help;
        }

        if (str_contains($q, 'directeur') || str_contains($q, 'feedback')) {
            return "Directeur : menu Dashboard, DAPT, NAPT (stats), Feedback.\nAccès consultation / supervision — pas d’édition du workflow.\n\n".$help;
        }

        if (str_contains($q, 'admin') || str_contains($q, 'sync') || str_contains($q, 'imperson') || str_contains($q, 'ldap') || str_contains($q, 'oracle')) {
            return "Admin : utilisateurs, groupes, référentiels NAPT, observations, intérims, journal, gestion DAPT/NAPT.\nSuper admin : Sync Oracle/LDAP/photos (depuis Utilisateurs) + impersonation (Simuler).\n\n".$help;
        }

        if (str_contains($q, 'calendrier')) {
            return "Calendrier NAPT : menu Outils → Calendrier NAPT — vue planifiée des notes.\n\n".$help;
        }

        if (str_contains($q, 'signature') || str_contains($q, 'profil')) {
            return "Signature : Profil → Ma signature.\nFormat PNG/JPG recommandé (~300×200), max 2 Mo. Utilisée sur les PDF NAPT.\n\n".$help;
        }

        if (str_contains($q, 'restitution') || str_contains($q, 'créneau') || str_contains($q, 'creneau')) {
            return "Si la DAPT a « restitution le soir », l’opérateur termine l’exécution par créneaux (slots) plutôt qu’une seule plage.\n\n".$help;
        }

        if ((str_contains($q, 'export') || str_contains($q, 'excel') || str_contains($q, 'pdf')) && ! str_contains($q, 'dapt pdf unitaire')) {
            return "Exports : menu Outils → Export Excel (/exports).\n• DAPT Excel (dates, statut, groupe)\n• NAPT Excel + PDF (filtres avancés)\nAussi : DESA/Admin listes & dashboards ; diffusion = PDF email.\n\n".$help;
        }

        return "D’après la documentation SENDAPTNAPT :\n\n".$help;
    }
}
