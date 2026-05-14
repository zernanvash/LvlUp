<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class NvidiaResumeService
{
    public function generateResumeJson(array $profile, ?string $targetRole = null): array
    {
        if (!filled(config('resume_ai.nvidia.api_key'))) {
            throw new RuntimeException('NVIDIA_API_KEY is missing.');
        }

        $response = Http::withToken(config('resume_ai.nvidia.api_key'))
            ->acceptJson()
            ->timeout((int) config('resume_ai.nvidia.timeout', 45))
            ->post(config('resume_ai.nvidia.base_url') . '/chat/completions', [
                'model' => config('resume_ai.models.content'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an ATS resume assistant. Return valid JSON only. Do not invent experience, awards, or certifications.',
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'task' => 'Generate an ATS-friendly resume JSON.',
                            'target_role' => $targetRole,
                            'profile' => $profile,
                            'required_json_shape' => [
                                'summary' => '',
                                'skills' => [],
                                'projects' => [],
                                'experience' => [],
                                'education' => [],
                                'certifications' => [],
                            ],
                        ], JSON_UNESCAPED_SLASHES),
                    ],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2000,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('NVIDIA API request failed: ' . $response->status() . ' ' . Str::limit($response->body(), 500));
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException('NVIDIA response did not include resume JSON.');
        }

        $json = json_decode($this->extractJson($content), true);

        if (!is_array($json)) {
            throw new RuntimeException('NVIDIA response was not valid JSON.');
        }

        return $json;
    }

    private function extractJson(string $content): string
    {
        $content = trim(preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $content) ?? $content);
        $start = strpos($content, '{');
        $end = strrpos($content, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($content, $start, $end - $start + 1);
        }

        return $content;
    }
}
