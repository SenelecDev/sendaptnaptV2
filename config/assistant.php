<?php

return [

    'enabled' => env('ASSISTANT_ENABLED', true),

    'provider' => env('ASSISTANT_PROVIDER', 'gemini'),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        // Nouveaux comptes Google : 2.5 souvent « no longer available » → 3.1 / 3.5.
        'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 25),
    ],

    'knowledge_path' => resource_path('assistant/knowledge.md'),

    'max_history' => 6,

    'max_tool_rows' => 15,

    'rate_limit_per_minute' => 20,

];
