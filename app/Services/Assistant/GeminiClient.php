<?php

namespace App\Services\Assistant;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiClient
{
    /**
     * Modèles de repli (quota / saturation / modèle retiré pour nouveaux comptes).
     *
     * @var list<string>
     */
    private const FALLBACK_MODELS = [
        'gemini-3.1-flash-lite',
        'gemini-3.5-flash',
        'gemini-flash-lite-latest',
        'gemini-flash-latest',
        'gemini-2.0-flash',
    ];

    /** Nombre max de modèles tentés (principal + replis) pour rester sous les timeouts proxy. */
    private const MAX_MODEL_ATTEMPTS = 4;

    public function isConfigured(): bool
    {
        $key = config('assistant.gemini.api_key');

        return is_string($key) && trim($key) !== '';
    }

    /**
     * @param  list<array{role: string, parts: list<array<string, mixed>>}>  $contents
     * @param  list<array<string, mixed>>  $functionDeclarations
     * @return array<string, mixed>
     */
    public function generate(string $systemInstruction, array $contents, array $functionDeclarations = []): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Clé Gemini non configurée.');
        }

        $primary = (string) config('assistant.gemini.model', 'gemini-3.1-flash-lite');
        $models = array_values(array_unique(array_filter([
            $primary,
            ...self::FALLBACK_MODELS,
        ])));
        $models = array_slice($models, 0, self::MAX_MODEL_ATTEMPTS);

        $lastError = 'Échec Gemini inconnu.';

        foreach ($models as $index => $model) {
            try {
                return $this->generateWithModel($model, $systemInstruction, $contents, $functionDeclarations);
            } catch (RuntimeException $e) {
                $lastError = $e->getMessage();
                $code = $e->getCode();
                $lower = mb_strtolower($lastError);
                $retryable = in_array($code, [404, 429, 503], true)
                    || str_contains($lower, 'timeout')
                    || str_contains($lower, 'indisponible')
                    || str_contains($lower, 'high demand')
                    || str_contains($lower, 'unavailable')
                    || str_contains($lower, 'resource_exhausted')
                    || str_contains($lower, 'quota')
                    || str_contains($lower, 'no longer available')
                    || str_contains($lower, 'not found');

                if (! $retryable || $index === count($models) - 1) {
                    throw $e;
                }

                Log::warning('Gemini modèle indisponible/quota, essai du suivant', [
                    'failed_model' => $model,
                    'next_model' => $models[$index + 1] ?? null,
                    'error' => $lastError,
                ]);
            }
        }

        throw new RuntimeException($lastError);
    }

    /**
     * @param  list<array{role: string, parts: list<array<string, mixed>>}>  $contents
     * @param  list<array<string, mixed>>  $functionDeclarations
     * @return array<string, mixed>
     */
    private function generateWithModel(
        string $model,
        string $systemInstruction,
        array $contents,
        array $functionDeclarations = []
    ): array {
        $base = rtrim((string) config('assistant.gemini.base_url'), '/');
        $key = (string) config('assistant.gemini.api_key');
        $timeout = max(10, min(45, (int) config('assistant.gemini.timeout', 25)));

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 2048,
            ],
        ];

        if ($functionDeclarations !== []) {
            $payload['tools'] = [
                ['functionDeclarations' => $functionDeclarations],
            ];
        }

        $url = "{$base}/models/{$model}:generateContent";

        try {
            $response = Http::connectTimeout(8)
                ->timeout($timeout)
                ->acceptJson()
                ->asJson()
                ->withHeaders([
                    'x-goog-api-key' => $key,
                ])
                ->post($url, $payload);
        } catch (\Throwable $e) {
            $safe = $this->sanitizeErrorMessage($e->getMessage());
            Log::warning('Gemini network error', [
                'model' => $model,
                'error' => $safe,
            ]);

            throw new RuntimeException(
                'Réseau Gemini indisponible (timeout/connexion) sur '.$model.' : '.$safe,
                0,
                $e
            );
        }

        if (! $response->successful()) {
            return $this->handleHttpError($model, $response);
        }

        return $response->json() ?? [];
    }

    /**
     * @return never
     */
    private function handleHttpError(string $model, Response $response): array
    {
        $status = $response->status();
        $body = mb_substr($response->body(), 0, 400);
        $message = data_get($response->json(), 'error.message', $body);

        Log::warning('Gemini API error', [
            'status' => $status,
            'model' => $model,
            'body' => $this->sanitizeErrorMessage($body),
        ]);

        $code = in_array($status, [404, 429, 503], true) ? $status : 0;

        throw new RuntimeException(
            'Échec Gemini HTTP '.$status.' ('.$model.') : '.$this->sanitizeErrorMessage((string) $message),
            $code
        );
    }

    private function sanitizeErrorMessage(string $message): string
    {
        $message = preg_replace('/([?&]key=)[^&\s"\']+/i', '$1***', $message) ?? $message;
        $message = preg_replace('/(AQ\.[A-Za-z0-9_\-]+)/', 'AQ.***', $message) ?? $message;

        return mb_substr($message, 0, 220);
    }

    /**
     * @param  array<string, mixed>  $response
     */
    public function extractText(array $response): string
    {
        $parts = $response['candidates'][0]['content']['parts'] ?? [];
        $texts = [];

        foreach ($parts as $part) {
            if (isset($part['text']) && is_string($part['text'])) {
                $texts[] = $part['text'];
            }
        }

        return trim(implode("\n", $texts));
    }
}
