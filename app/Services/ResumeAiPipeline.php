<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResumeAiPipeline
{
    private const CONTENT_KEYS = [
        'summary',
        'skills',
        'experience',
        'projects',
        'education',
        'certifications',
    ];

    /**
     * Run the three-stage resume pipeline:
     * context analysis, content generation, and layout planning.
     */
    public function generate($user, object $resumeInput, Collection $projects, Collection $skills, Collection $certificates, array $keywords, float $matchScore): array
    {
        $context = $this->buildContext($user, $resumeInput, $projects, $skills, $certificates, $keywords, $matchScore);
        $metadata = [
            'provider' => $this->providerName(),
            'models' => config('resume_ai.models'),
            'stages' => [],
        ];

        $analysis = $this->analyzeContext($context);
        $metadata['stages']['analysis'] = $analysis['metadata'];

        $content = $this->generateContent($user, $resumeInput, $projects, $skills, $certificates, $context, $analysis['data']);
        $metadata['stages']['content'] = $content['metadata'];

        $layout = $this->planLayout($context, $content['data']);
        $metadata['stages']['layout'] = $layout['metadata'];
        $metadata['layout'] = $layout['data'];

        return [
            'content' => $this->withPipelineMetadata($content['data'], $metadata),
            'metadata' => $metadata,
        ];
    }

    private function analyzeContext(array $context): array
    {
        $serialized = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $shouldUseLongContext = strlen($serialized) >= (int) config('resume_ai.long_context_threshold');

        if (! $shouldUseLongContext || ! $this->canUseNvidia()) {
            return [
                'data' => [
                    'focus' => $context['target']['title'],
                    'keywords' => $context['target']['keywords'],
                    'project_ids' => collect($context['projects'])->pluck('id')->values()->all(),
                    'notes' => 'Used local deterministic profile analysis.',
                ],
                'metadata' => [
                    'status' => 'local',
                    'model' => null,
                    'reason' => $shouldUseLongContext ? 'nvidia_not_configured' : 'context_under_threshold',
                ],
            ];
        }

        try {
            $data = $this->callNvidiaJson(config('resume_ai.models.long_context'), [
                [
                    'role' => 'system',
                    'content' => 'Analyze profile data for resume targeting. Return only compact JSON.',
                ],
                [
                    'role' => 'user',
                    'content' => "Return JSON with keys focus, keywords, project_ids, notes.\n\nProfile context:\n{$serialized}",
                ],
            ]);

            return [
                'data' => [
                    'focus' => (string) ($data['focus'] ?? $context['target']['title']),
                    'keywords' => $this->normalizeList($data['keywords'] ?? $context['target']['keywords']),
                    'project_ids' => $this->normalizeList($data['project_ids'] ?? collect($context['projects'])->pluck('id')->all()),
                    'notes' => (string) ($data['notes'] ?? ''),
                ],
                'metadata' => [
                    'status' => 'ai',
                    'model' => config('resume_ai.models.long_context'),
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('ResumeAiPipeline long-context stage failed', ['message' => $e->getMessage()]);

            return [
                'data' => [
                    'focus' => $context['target']['title'],
                    'keywords' => $context['target']['keywords'],
                    'project_ids' => collect($context['projects'])->pluck('id')->values()->all(),
                    'notes' => 'Long-context stage failed; used local analysis.',
                ],
                'metadata' => [
                    'status' => 'fallback',
                    'model' => config('resume_ai.models.long_context'),
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }

    private function generateContent($user, object $resumeInput, Collection $projects, Collection $skills, Collection $certificates, array $context, array $analysis): array
    {
        if (! $this->canUseNvidia()) {
            return [
                'data' => $this->localResumeContent($user, $resumeInput, $projects, $skills, $certificates),
                'metadata' => [
                    'status' => 'local',
                    'model' => null,
                    'reason' => 'nvidia_not_configured',
                ],
            ];
        }

        try {
            $payload = [
                'context' => $context,
                'analysis' => $analysis,
                'required_json_keys' => self::CONTENT_KEYS,
            ];

            $data = $this->callNvidiaJson(config('resume_ai.models.content'), [
                [
                    'role' => 'system',
                    'content' => 'You are an expert ATS resume writer. Return one raw JSON object only. Every value must be a plain string.',
                ],
                [
                    'role' => 'user',
                    'content' => "Generate an ATS-friendly resume tailored to the target role. Required keys: summary, skills, experience, projects, education, certifications.\n\n".json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ],
            ]);

            $this->validateContent($data);

            return [
                'data' => $data,
                'metadata' => [
                    'status' => 'ai',
                    'model' => config('resume_ai.models.content'),
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('ResumeAiPipeline content stage failed', ['message' => $e->getMessage()]);

            return [
                'data' => $this->localResumeContent($user, $resumeInput, $projects, $skills, $certificates),
                'metadata' => [
                    'status' => 'local',
                    'model' => config('resume_ai.models.content'),
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }

    private function planLayout(array $context, array $content): array
    {
        $template = $context['target']['template'] ?? 'modern';
        $localPlan = [
            'template' => $template,
            'renderer' => 'browsershot',
            'page_size' => 'A4',
            'density' => strlen(implode(' ', $content)) > 4500 ? 'compact' : 'standard',
            'notes' => 'Use the selected Laravel Blade PDF template with print-safe spacing.',
        ];

        if (! $this->canUseNvidia()) {
            return [
                'data' => $localPlan,
                'metadata' => [
                    'status' => 'local',
                    'model' => null,
                    'reason' => 'nvidia_not_configured',
                ],
            ];
        }

        try {
            $data = $this->callNvidiaJson(config('resume_ai.models.layout'), [
                [
                    'role' => 'system',
                    'content' => 'You improve resume PDF layout plans for Laravel Blade and Browsershot. Return JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => "Return JSON with template, renderer, page_size, density, notes. Do not return code.\n\nTemplate: {$template}\nContent:\n".json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ],
            ]);

            return [
                'data' => array_merge($localPlan, array_filter([
                    'template' => $data['template'] ?? null,
                    'renderer' => $data['renderer'] ?? null,
                    'page_size' => $data['page_size'] ?? null,
                    'density' => $data['density'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ])),
                'metadata' => [
                    'status' => 'ai',
                    'model' => config('resume_ai.models.layout'),
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('ResumeAiPipeline layout stage failed', ['message' => $e->getMessage()]);

            return [
                'data' => $localPlan,
                'metadata' => [
                    'status' => 'local',
                    'model' => config('resume_ai.models.layout'),
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }

    private function buildContext($user, object $resumeInput, Collection $projects, Collection $skills, Collection $certificates, array $keywords, float $matchScore): array
    {
        return [
            'candidate' => [
                'name' => $user->name,
                'email' => $user->email,
                'title' => $user->resume_job_title ?? $user->title ?? null,
                'location' => collect([$user->city ?? null, $user->country ?? null])->filter()->join(', '),
                'bio' => $user->resume_summary ?? $user->bio ?? null,
                'work_experience' => $user->work_experience,
                'education' => $user->education,
                'technical_skills' => $user->technical_skills,
                'links' => [
                    'github' => $user->github_url ?? null,
                    'linkedin' => $user->linkedin_url ?? null,
                ],
            ],
            'target' => [
                'title' => $resumeInput->job_title,
                'description' => $resumeInput->job_description,
                'keywords' => array_values($keywords),
                'match_score' => round($matchScore),
                'template' => $resumeInput->template ?? 'modern',
            ],
            'skills' => $skills->map(fn ($skill) => [
                'id' => $skill->id ?? null,
                'name' => $skill->name ?? null,
                'rarity' => $skill->rarity ?? null,
            ])->filter(fn ($skill) => ! empty($skill['name']))->values()->all(),
            'projects' => $projects->map(fn ($project) => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'language' => $project->language,
                'type' => $project->project_type ?? null,
                'is_featured' => (bool) $project->is_featured,
                'xp_reward' => $project->xp_reward,
                'skills' => $project->skills?->pluck('name')->values()->all() ?? [],
            ])->values()->all(),
            'certificates' => $certificates->map(fn ($certificate) => [
                'title' => $certificate->title,
                'issuer' => $certificate->issuer,
                'issued_date' => optional($certificate->issued_date)->format('Y-m'),
                'summary' => $certificate->ai_summary,
            ])->values()->all(),
        ];
    }

    private function canUseNvidia(): bool
    {
        return filled(config('resume_ai.nvidia.api_key'))
            && filled(config('resume_ai.nvidia.base_url'));
    }

    private function providerName(): string
    {
        return 'nvidia';
    }

    private function callNvidiaJson(string $model, array $messages): array
    {
        $response = Http::withToken(config('resume_ai.nvidia.api_key'))
            ->acceptJson()
            ->timeout((int) config('resume_ai.nvidia.timeout'))
            ->post(config('resume_ai.nvidia.base_url').'/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.25,
                'response_format' => ['type' => 'json_object'],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('NVIDIA request failed: '.$response->status().' '.Str::limit($response->body(), 500));
        }

        $text = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($text) || trim($text) === '') {
            throw new \RuntimeException('NVIDIA response did not include message content.');
        }

        return $this->decodeJson($text);
    }

    private function decodeJson(string $text): array
    {
        $text = trim(preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $text) ?? $text);
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $text = substr($text, $start, $end - $start + 1);
        }

        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            throw new \RuntimeException('AI JSON decode failed: '.json_last_error_msg());
        }

        return $data;
    }

    private function validateContent(array $data): void
    {
        $missing = array_diff(self::CONTENT_KEYS, array_keys($data));

        if (! empty($missing)) {
            throw new \RuntimeException('AI resume content missing keys: '.implode(', ', $missing));
        }
    }

    private function withPipelineMetadata(array $content, array $metadata): array
    {
        foreach (self::CONTENT_KEYS as $key) {
            $content[$key] = (string) ($content[$key] ?? '');
        }

        $content['_pipeline'] = $metadata;

        return $content;
    }

    private function normalizeList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => $item !== null && $item !== ''));
        }

        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return [];
    }

    private function localResumeContent($user, object $resumeInput, Collection $projects, Collection $skills, Collection $certificates): array
    {
        $skillsList = collect(explode(',', (string) ($user->technical_skills ?? '')))
            ->map(fn ($skill) => trim($skill))
            ->filter()
            ->merge($skills->map(fn ($skill) => $skill->name ?? null)->filter())
            ->unique()
            ->values()
            ->join(', ');

        $projectText = $projects->map(function ($project) {
            $skillList = $project->skills ? $project->skills->pluck('name')->join(', ') : '';
            $description = $project->description ?: 'Portfolio project demonstrating applied software development skills.';

            return "- {$project->name}: {$description}".($skillList ? " ({$skillList})" : '');
        })->join("\n");

        $certificationText = $certificates->map(function ($certificate) {
            $parts = array_filter([
                $certificate->title,
                $certificate->issuer ? "Issuer: {$certificate->issuer}" : null,
                $certificate->issued_date ? 'Issued: '.$certificate->issued_date->format('M Y') : null,
                $certificate->ai_summary,
            ]);

            return '- '.implode(' | ', $parts);
        })->join("\n");

        $role = $resumeInput->job_title ?: ($user->resume_job_title ?? $user->title ?? 'Software Developer');
        $summary = $user->resume_summary ?? $user->bio ?: "Developer targeting {$role} roles with practical project experience and a growing technical portfolio.";

        return [
            'summary' => $summary,
            'skills' => $skillsList ?: 'Software development, problem solving, collaboration, technical documentation',
            'experience' => $user->work_experience ?: 'Add work experience in your profile to generate stronger role-specific experience bullets.',
            'projects' => $projectText ?: 'Add projects to your portfolio to generate project highlights.',
            'education' => $user->education ?: 'Add education details in your profile.',
            'certifications' => $certificationText ?: 'No certifications listed.',
        ];
    }
}
