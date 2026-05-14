<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Resume AI Pipeline Provider
    |--------------------------------------------------------------------------
    |
    | The resume pipeline uses an OpenAI-compatible NVIDIA endpoint for AI
    | generation. If no key is configured, it uses deterministic local content
    | so the builder remains usable in development.
    |
    */
    'nvidia' => [
        'api_key' => env('NVIDIA_API_KEY'),
        'base_url' => rtrim(env('NVIDIA_BASE_URL', 'https://integrate.api.nvidia.com/v1'), '/'),
        'timeout' => (int) env('RESUME_AI_TIMEOUT', 45),
    ],

    'models' => [
        'content' => env('RESUME_AI_CONTENT_MODEL', 'mistral-medium-3.5-128b'),
        'layout' => env('RESUME_AI_LAYOUT_MODEL', 'mistral-small-4-119b-2603'),
        'long_context' => env('RESUME_AI_LONG_CONTEXT_MODEL', 'nemotron-3-super-120b-a12b'),
    ],

    'long_context_threshold' => (int) env('RESUME_AI_LONG_CONTEXT_THRESHOLD', 9000),
];
