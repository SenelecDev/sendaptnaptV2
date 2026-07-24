<?php

namespace App\Services\Assistant;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AssistantOrchestrator
{
    public function __construct(
        private readonly KnowledgeBaseService $knowledge,
        private readonly AssistantToolService $tools,
        private readonly GeminiClient $gemini,
        private readonly OfflineResponder $offline,
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool) config('assistant.enabled', true);
    }

    public function usesGemini(): bool
    {
        return $this->isEnabled() && $this->gemini->isConfigured();
    }

    /**
     * @param  list<array{role: string, content: string}>  $history
     * @return array{reply: string, mode: string, tools: list<string>}
     */
    public function chat(User $user, string $message, array $history = []): array
    {
        $message = trim($message);
        if ($message === '') {
            return [
                'reply' => 'Posez une question sur SENDAPTNAPT (DAPT, NAPT, workflow, exports, intérims…).',
                'mode' => 'offline',
                'tools' => [],
            ];
        }

        $history = array_slice($history, -1 * (int) config('assistant.max_history', 6));

        if (! $this->usesGemini()) {
            return $this->offline->respond($message, $user, $history);
        }

        try {
            return $this->chatWithGemini($user, $message, $history);
        } catch (\Throwable $e) {
            Log::warning('Assistant Gemini fallback offline', [
                'error' => mb_substr(preg_replace('/([?&]key=)[^&\s"\']+/i', '$1***', $e->getMessage()) ?? $e->getMessage(), 0, 220),
            ]);

            return $this->offline->respond($message, $user, $history, $e->getMessage());
        }
    }

    /**
     * @param  list<array{role: string, content: string}>  $history
     * @return array{reply: string, mode: string, tools: list<string>}
     */
    private function chatWithGemini(User $user, string $message, array $history): array
    {
        $roles = $user->getRoleNames()->implode(', ') ?: 'demandeur';
        $doc = $this->knowledge->search($message, 2500);
        [$toolContext, $usedTools] = $this->collectToolContext($user, $message);

        $system = <<<PROMPT
Tu es l'assistant officiel de SENDAPTNAPT (SENELEC — gestion DAPT et NAPT). Réponds toujours en français, de façon claire, concise et utile.
Utilisateur connecté : {$user->full_name} (rôles : {$roles}).
Tu as accès à la documentation et éventuellement à des données métier déjà récupérées.
N'invente pas de données absentes du contexte. Si une info manque, dis-le.
Ne révèle jamais de secrets, mots de passe, clés API.
Rappels métier :
- Une DAPT = une seule NAPT.
- Si étude = oui, le document d'étude est obligatoire avant vérification/validation.
- L'exécution est bloquée sans fiche manœuvre (opérateur chef).
- Page documentation : /documentation ; exports : /exports.

## Documentation applicative
{$doc}

## Données métier disponibles
{$toolContext}
PROMPT;

        $contents = [];
        foreach ($history as $item) {
            $role = ($item['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $text = trim((string) ($item['content'] ?? ''));
            if ($text === '') {
                continue;
            }
            if (str_contains($text, 'Gemini temporairement indisponible')
                || str_contains($text, 'Gemini indisponible')
                || str_contains($text, 'mode local (Gemini')) {
                continue;
            }
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => mb_substr($text, 0, 1500)]],
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        $response = $this->gemini->generate($system, $contents, []);
        $text = $this->gemini->extractText($response);
        if ($text === '') {
            $text = 'Je n’ai pas pu formuler de réponse. Reformulez votre question.';
        }

        return [
            'reply' => $text,
            'mode' => 'gemini',
            'tools' => $usedTools,
        ];
    }

    /**
     * @return array{0: string, 1: list<string>}
     */
    private function collectToolContext(User $user, string $message): array
    {
        $q = mb_strtolower($message);
        $used = [];
        $chunks = [];

        $map = [
            'get_my_demandes' => ['dapt', 'demande'],
            'get_my_notes' => ['napt', 'note'],
            'get_pending_queue' => ['attente', 'pending', 'file', 'à faire', 'a faire', 'queue'],
            'get_napt_stats' => ['stat', 'compteur', 'combien'],
        ];

        $toRun = [];
        foreach ($map as $tool => $needles) {
            foreach ($needles as $n) {
                if (str_contains($q, $n)) {
                    $toRun[$tool] = true;
                    break;
                }
            }
        }

        if ($toRun === [] && (
            str_contains($q, 'comment')
            || str_contains($q, 'aide')
            || str_contains($q, 'fonctionne')
            || str_contains($q, 'workflow')
            || str_contains($q, 'signature')
            || str_contains($q, 'export')
            || str_contains($q, 'intérim')
            || str_contains($q, 'interim')
            || str_contains($q, 'manœuvre')
            || str_contains($q, 'manoeuvre')
            || str_contains($q, 'diffusion')
            || str_contains($q, 'gmao')
            || str_contains($q, 'calendrier')
            || str_contains($q, 'admin')
            || str_contains($q, 'directeur')
            || str_contains($q, 'observation')
            || str_contains($q, 'notification')
            || str_contains($q, 'sync')
            || str_contains($q, 'glossaire')
        )) {
            return ['Aucune donnée métier demandée.', []];
        }

        if ($toRun === []) {
            $toRun = [
                'get_my_demandes' => true,
                'get_my_notes' => true,
                'get_pending_queue' => true,
            ];
        }

        foreach (array_keys($toRun) as $tool) {
            $result = $this->tools->execute($tool, ['limit' => 8], $user, $this->knowledge);
            $used[] = $tool;
            $chunks[] = $tool.': '.json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        $text = implode("\n", $chunks);
        if (mb_strlen($text) > 6000) {
            $text = mb_substr($text, 0, 6000).'…';
        }

        return [$text !== '' ? $text : 'Aucune donnée.', $used];
    }
}
