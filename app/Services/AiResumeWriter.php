<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class AiResumeWriter
{
    /**
     * The Gemini model to use.
     */
    private string $model = 'gemini-1.5-flash';

    /**
     * How many times to retry on failure.
     */
    private int $maxRetries = 2;

    public function generate($user, $resume, $projects, $skills): array
    {
        // 1. Prepare data
        $projectsList = $projects->map(fn($p) => "- {$p->name}: {$p->description}")->join("\n");
        $skillsList   = $skills->pluck('name')->join(', ');

        // 2. Build prompt — candidate data FIRST, schema instructions LAST
        $prompt = $this->buildPrompt($user, $resume, $projectsList, $skillsList);

        // 3. Attempt generation with retries
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $text = $this->callGemini($prompt);

                Log::debug("AiResumeWriter raw response (attempt {$attempt})", ['text' => $text]);

                $cleanJson = $this->extractJson($text);
                $data      = json_decode($cleanJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException(
                        'JSON decode error: ' . json_last_error_msg() . ' | Raw: ' . $text
                    );
                }

                // 4. Validate all expected keys are present
                $this->validateStructure($data);

                return $data;

            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning("AiResumeWriter attempt {$attempt} failed: " . $e->getMessage());

                // Small backoff before retrying
                if ($attempt < $this->maxRetries) {
                    sleep(1);
                }
            }
        }

        // All retries exhausted — log full trace and return fallback
        Log::error('AiResumeWriter: all attempts failed', [
            'message' => $lastException?->getMessage(),
            'trace'   => $lastException?->getTraceAsString(),
        ]);

        return $this->fallback($skillsList, $projectsList);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildPrompt($user, $resume, string $projectsList, string $skillsList): string
    {
        return <<<PROMPT
        You are an expert resume writer specialising in ATS-optimised resumes.

        Below is the candidate's information. Use it to generate professional resume content.

        ---
        Candidate Name   : {$user->name}
        Target Role      : {$resume->job_title}
        Job Description  :
        {$resume->job_description}

        Projects         :
        {$projectsList}

        Skills           : {$skillsList}
        ---

        INSTRUCTIONS:
        - Return ONLY a single, raw JSON object — no markdown, no code fences, no explanations.
        - Do NOT wrap the JSON in ```json ... ``` or any other block.
        - Every value must be a plain string (no nested arrays or objects).
        - Use the EXACT keys shown below.

        Required JSON format:
        {
          "summary": "A 3–5 sentence professional summary tailored to the target role.",
          "skills": "Comma-separated list of relevant technical and soft skills.",
          "projects": "Formatted project highlights relevant to the target role.",
          "experience": "Work experience entries with achievements, quantified where possible.",
          "education": "Degree, institution, and graduation year."
        }
        PROMPT;
    }

    /**
     * Call the Gemini API, preferring JSON response mode when available.
     *
     * @throws \Throwable
     */
    private function callGemini(string $prompt): string
    {
        // Attempt to use response_mime_type for guaranteed JSON output.
        // If the installed package version doesn't support withGenerationConfig,
        // it will throw and we fall back to the plain generateContent call.
        try {
            $response = Gemini::generativeModel($this->model)
                ->withGenerationConfig([
                    'response_mime_type' => 'application/json',
                ])
                ->generateContent($prompt);
        } catch (\BadMethodCallException | \Error $e) {
            // Package version doesn't support withGenerationConfig — fall back
            Log::debug('AiResumeWriter: withGenerationConfig not supported, using plain call');
            $response = Gemini::generativeModel($this->model)->generateContent($prompt);
        }

        $text = $response->text();

        if (empty(trim($text))) {
            throw new \RuntimeException('Gemini returned an empty response.');
        }

        return $text;
    }

    /**
     * Robustly extract a JSON object from the response string.
     * Handles markdown code fences and any surrounding prose.
     *
     * @throws \RuntimeException
     */
    private function extractJson(string $text): string
    {
        // Strategy 1: find the outermost { ... } block
        $start = strpos($text, '{');
        $end   = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($text, $start, $end - $start + 1);
        }

        // Strategy 2: strip markdown fences and try again
        $stripped = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $text);
        $stripped = trim($stripped);

        $start = strpos($stripped, '{');
        $end   = strrpos($stripped, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($stripped, $start, $end - $start + 1);
        }

        throw new \RuntimeException('Could not locate a JSON object in the response: ' . $text);
    }

    /**
     * Ensure the decoded array has all required resume keys.
     *
     * @throws \RuntimeException
     */
    private function validateStructure(array $data): void
    {
        $required = ['summary', 'skills', 'projects', 'experience', 'education'];
        $missing  = array_diff($required, array_keys($data));

        if (!empty($missing)) {
            throw new \RuntimeException(
                'Gemini response is missing keys: ' . implode(', ', $missing)
            );
        }
    }

    /**
     * Safe fallback returned when all attempts fail.
     */
    private function fallback(string $skillsList, string $projectsList): array
    {
        return [
            'summary'    => 'AI summary currently unavailable. Please update manually.',
            'skills'     => $skillsList,
            'projects'   => $projectsList,
            'experience' => 'Please update your experience manually.',
            'education'  => '',
        ];
    }
}
