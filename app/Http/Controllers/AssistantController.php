<?php

namespace App\Http\Controllers;

use App\Services\Assistant\AssistantOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssistantController extends Controller
{
    public function __construct(
        private readonly AssistantOrchestrator $orchestrator,
    ) {
    }

    public function status(): JsonResponse
    {
        $configured = $this->orchestrator->usesGemini();

        return response()->json([
            'enabled' => $this->orchestrator->isEnabled(),
            'configured' => $configured,
            // "gemini" = clé présente ; le chat bascule en offline si le réseau bloque Google AI
            'mode' => $configured ? 'gemini' : 'offline',
            'hint' => $configured
                ? 'Clé Gemini détectée. Si les réponses restent locales, le réseau bloque generativelanguage.googleapis.com.'
                : 'Ajoutez GEMINI_API_KEY dans le .env pour activer Gemini.',
        ]);
    }

    public function chat(Request $request): JsonResponse
    {
        if (! $this->orchestrator->isEnabled()) {
            return response()->json([
                'reply' => 'L’assistant est désactivé (ASSISTANT_ENABLED=false).',
                'mode' => 'disabled',
            ], 503);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:12'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:4000'],
        ]);

        $user = $request->user();
        $history = $validated['history'] ?? [];

        $result = $this->orchestrator->chat($user, $validated['message'], $history);

        Log::info('Assistant chat', [
            'user_id' => $user->id,
            'mode' => $result['mode'],
            'tools' => $result['tools'] ?? [],
        ]);

        return response()->json([
            'reply' => $result['reply'],
            'mode' => $result['mode'],
        ]);
    }
}
