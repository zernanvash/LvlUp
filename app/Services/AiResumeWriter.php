<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiResumeWriter
{
    private string $model = 'gemini-2.5-flash';

    private int $maxRetries = 2;

    // =========================================================================
    // Public API
    // =========================================================================

    public function generate($user, $resume, $projects, $skills): array
    {
        $projectsList = $projects->map(fn($p) => "- {$p->name}: {$p->description}")->join("\n");
        $skillsList   = $skills->pluck('name')->join(', ');
        $prompt       = $this->buildPrompt($user, $resume, $projectsList, $skillsList);

        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $raw  = $this->callGemini($prompt);
                $data = $this->parseJson($raw);
                $this->validateStructure($data);

                // Ensure every value is a plain string — Gemini sometimes
                // returns skills or other fields as arrays.
                $data = $this->castToStrings($data);

                Log::info('AiResumeWriter success', [
                    'attempt' => $attempt,
                    'keys'    => array_keys($data),
                ]);

                return $data;

            } catch (\Throwable $e) {
                $lastException = $e;

                Log::warning("AiResumeWriter attempt {$attempt} failed", [
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                ]);

                if ($attempt < $this->maxRetries) {
                    sleep(2);
                }
            }
        }

        Log::error('AiResumeWriter: all attempts failed — returning fallback', [
            'error' => $lastException?->getMessage(),
            'trace' => $lastException?->getTraceAsString(),
        ]);

        return $this->fallback($skillsList, $projectsList);
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    /**
     * Call Gemini via raw HTTP — same pattern as AiDebugController.
     *
     * @throws \RuntimeException
     */
    private function callGemini(string $prompt): string
    {
        $url = config('gemini.base_url') . $this->model . ':generateContent?key=' . config('gemini.api_key');

        $response = Http::timeout(30)->post($url, [
            'system_instruction' => [
                'parts' => [
                    [
                        'text' =>
                            'You are a senior resume writer with 15 years of experience crafting ' .
                            'ATS-optimised resumes for software engineers and tech professionals. ' .
                            'You write in a confident, accomplishment-focused style. ' .
                            'Never invent facts not present in the candidate data. ' .
                            'Always return ONLY a valid JSON object — no markdown, no code fences, no explanation.',
                    ],
                ],
            ],
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'        => 0.3,
                'maxOutputTokens'    => 4096,
                'response_mime_type' => 'application/json',
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                "Gemini HTTP {$response->status()}: " . $response->body()
            );
        }

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (empty(trim($text ?? ''))) {
            throw new \RuntimeException(
                'Gemini returned an empty response. Full body: ' . $response->body()
            );
        }

        Log::debug('AiResumeWriter raw response', ['preview' => substr($text, 0, 300)]);

        return $text;
    }

    /**
     * Build the user prompt with all candidate data.
     */
    private function buildPrompt($user, $resume, string $projectsList, string $skillsList): string
    {
        $phone      = $resume->phone             ?? ($user->phone      ?? 'Not provided');
        $location   = $resume->location          ?? ($user->location   ?? 'Not provided');
        $linkedIn   = $resume->linked_in         ?? ($user->linked_in  ?? '');
        $github     = $resume->github_url        ?? ($user->github_url ?? '');
        $experience = trim($resume->work_experience   ?? '');
        $education  = trim($resume->education_details ?? '');
        $certs      = trim($resume->certifications    ?? '');
        $languages  = trim($resume->spoken_languages  ?? '');
        $tone       = $resume->tone              ?? 'professional';
        $bio        = trim($resume->bio_seed     ?? '');

        $experienceBlock = $experience ?: 'No work experience provided — infer seniority from the projects listed below.';
        $educationBlock  = $education  ?: 'Not provided.';
        $certsBlock      = $certs      ?: 'None listed.';
        $languagesBlock  = $languages  ?: 'Not specified.';

        return <<<PROMPT
=== CANDIDATE PROFILE ===
Name             : {$user->name}
Email            : {$user->email}
Phone            : {$phone}
Location         : {$location}
LinkedIn         : {$linkedIn}
GitHub/Portfolio : {$github}

=== TARGET ROLE ===
Job Title    : {$resume->job_title}
Writing Tone : {$tone}

=== JOB DESCRIPTION ===
{$resume->job_description}

=== WORK EXPERIENCE ===
{$experienceBlock}

=== EDUCATION ===
{$educationBlock}

=== CERTIFICATIONS ===
{$certsBlock}

=== SPOKEN LANGUAGES ===
{$languagesBlock}

=== PROJECTS ===
{$projectsList}

=== SKILLS ===
{$skillsList}

=== EXTRA CONTEXT ===
{$bio}

=== INSTRUCTIONS ===
Using ALL the information above, produce a complete ATS-optimised resume in {$tone} tone.
Quantify achievements wherever the data allows (e.g. "reduced load time by 40%").
Write every field in full polished prose — never use placeholder text.

Return ONLY this JSON object with exactly these 9 keys:

{
  "headline": "One punchy line: role + key value proposition",
  "summary": "3-5 sentence professional summary tailored to the target role",
  "skills": "Comma-separated list of relevant skills ordered by relevance to the job description",
  "experience": "Formatted work history. Each role on its own line: Company · Title · Dates, followed by 2-4 bullet achievements",
  "projects": "Key project highlights. Each: Project Name — tech stack — impact/outcome",
  "education": "Degree, institution, graduation year. Use 'Not provided' if absent.",
  "certifications": "Certifications with issuing body and year. Use 'None' if absent.",
  "languages": "Spoken languages with proficiency levels. Use 'Not specified' if absent.",
  "achievements": "Awards, publications, open-source contributions, or stand-out accomplishments. Use 'None' if absent."
}
PROMPT;
    }

    /**
     * Extract and decode JSON from the model response.
     *
     * @throws \RuntimeException
     */
    private function parseJson(string $text): array
    {
        // Strip markdown fences if present
        $clean = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $clean = preg_replace('/\s*```$/m', '', $clean);
        $clean = trim($clean);

        // Find the outermost { ... } block
        $start = strpos($clean, '{');
        $end   = strrpos($clean, '}');

        if ($start === false || $end === false || $end <= $start) {
            throw new \RuntimeException(
                'No JSON object found in response. Raw: ' . substr($text, 0, 300)
            );
        }

        $data = json_decode(substr($clean, $start, $end - $start + 1), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'JSON decode failed: ' . json_last_error_msg() . ' | Raw: ' . substr($text, 0, 300)
            );
        }

        return $data;
    }

    /**
     * Ensure all 9 required keys are present.
     *
     * @throws \RuntimeException
     */
    private function validateStructure(array $data): void
    {
        $required = ['headline', 'summary', 'skills', 'experience', 'projects', 'education', 'certifications', 'languages', 'achievements'];
        $missing  = array_diff($required, array_keys($data));

        if (! empty($missing)) {
            throw new \RuntimeException('Gemini response missing keys: ' . implode(', ', $missing));
        }
    }

    /**
     * Cast every value in the AI response to a plain string.
     * Flat arrays become comma-separated strings; nested arrays are JSON-encoded.
     */
    private function castToStrings(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $isFlat = array_keys($value) === range(0, count($value) - 1)
                          && count(array_filter($value, 'is_array')) === 0;

                $data[$key] = $isFlat ? implode(', ', $value) : json_encode($value);

            } elseif (!is_string($value)) {
                $data[$key] = (string) $value;
            }
        }

        return $data;
    }

    /**
     * Fallback returned when every retry fails.
     */
    private function fallback(string $skillsList, string $projectsList): array
    {
        return [
            'headline'       => 'Resume — AI generation temporarily unavailable.',
            'summary'        => 'AI summary unavailable. Please regenerate or update manually.',
            'skills'         => $skillsList ?: 'Please add your skills.',
            'experience'     => 'Please add your experience manually, then regenerate.',
            'projects'       => $projectsList ?: 'No projects listed.',
            'education'      => 'Not provided.',
            'certifications' => 'None.',
            'languages'      => 'Not specified.',
            'achievements'   => 'None.',
        ];
    }
}
