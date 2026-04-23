<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class AiResumeWriter
{
    private string $model = 'gemini-1.5-flash';
    private int $maxRetries = 2;

    /**
     * Generate resume content from all user profile data.
     *
     * @param  \App\Models\User        $user
     * @param  object                  $resume  Has job_title, job_description
     * @param  \Illuminate\Support\Collection $projects
     * @param  \Illuminate\Support\Collection $skills
     * @param  \Illuminate\Support\Collection $certificates
     * @return array
     */
    public function generate($user, $resume, $projects, $skills, $certificates = null): array
    {
        $projectsList = $projects->map(function ($p) {
            $skillList = $p->skills ? $p->skills->pluck('name')->join(', ') : '';
            return "- {$p->name}: {$p->description}" . ($skillList ? " [{$skillList}]" : '');
        })->join("\n");

        $skillsList = $skills->map(fn($s) => $s?->name ?? '')->filter()->join(', ');

        // Include technical_skills from profile
        $technicalSkills = $user->technical_skills ?? '';
        if ($technicalSkills) {
            $skillsList = $technicalSkills . ($skillsList ? ', ' . $skillsList : '');
        }

        // Certificates
        $certsList = '';
        if ($certificates && $certificates->count() > 0) {
            $certsList = $certificates->map(function ($c) {
                $line = "- {$c->title}";
                if ($c->issuer) $line .= " ({$c->issuer})";
                if ($c->issued_date) $line .= " — " . $c->issued_date->format('M Y');
                if ($c->ai_summary) $line .= ": {$c->ai_summary}";
                return $line;
            })->join("\n");
        }

        // Work experience
        $workExperience = $user->work_experience ?? '';

        // Education
        $education = $user->education ?? '';

        // Bio / summary hint
        $bio = $user->bio ?? '';

        $prompt = $this->buildPrompt(
            $user,
            $resume,
            $projectsList,
            $skillsList,
            $certsList,
            $workExperience,
            $education,
            $bio
        );

        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $text = $this->callGemini($prompt);

                Log::debug("AiResumeWriter raw response (attempt {$attempt})", ['text' => $text]);

                $cleanJson = $this->extractJson($text);
                $data      = json_decode($cleanJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('JSON decode error: ' . json_last_error_msg());
                }

                $this->validateStructure($data);
                return $data;

            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning("AiResumeWriter attempt {$attempt} failed: " . $e->getMessage());
                if ($attempt < $this->maxRetries) sleep(1);
            }
        }

        Log::error('AiResumeWriter: all attempts failed', [
            'message' => $lastException?->getMessage(),
        ]);

        return $this->fallback($user, $skillsList, $projectsList, $certsList);
    }

    // -------------------------------------------------------------------------

    private function buildPrompt(
        $user,
        $resume,
        string $projectsList,
        string $skillsList,
        string $certsList,
        string $workExperience,
        string $education,
        string $bio
    ): string {
        $name    = $user->name ?? '';
        $title   = $resume->job_title ?? '';
        $jobDesc = $resume->job_description ?? '';

        return <<<PROMPT
        You are an expert ATS-optimised resume writer. Use the candidate's information below to generate polished, professional resume content tailored to the target role.

        ---
        Candidate Name    : {$name}
        Target Role       : {$title}
        Bio / About Me    : {$bio}
        Job Description   :
        {$jobDesc}

        Projects:
        {$projectsList}

        Skills: {$skillsList}

        Work Experience:
        {$workExperience}

        Education:
        {$education}

        Certificates & Achievements:
        {$certsList}
        ---

        INSTRUCTIONS:
        - Return ONLY a single raw JSON object — no markdown, no code fences, no explanations.
        - Do NOT wrap the JSON in ```json ... ```.
        - Every value must be a plain string.
        - Tailor content to the target role using keywords from the job description.
        - Keep summary to 3–5 sentences. Be specific and quantify achievements where you can infer them.
        - Use the EXACT keys shown below.

        Required JSON format:
        {
          "summary": "Professional 3–5 sentence summary tailored to the target role.",
          "skills": "Comma-separated relevant technical and soft skills.",
          "experience": "Formatted work experience entries with bullet-point achievements.",
          "projects": "Formatted project highlights showing relevant impact.",
          "education": "Degree, institution, and graduation year.",
          "certifications": "Formatted list of certifications with issuers and dates."
        }
        PROMPT;
    }

    private function callGemini(string $prompt): string
    {
        try {
            $response = Gemini::generativeModel($this->model)
                ->withGenerationConfig([
                    'response_mime_type' => 'application/json',
                ])
                ->generateContent($prompt);
        } catch (\BadMethodCallException | \Error $e) {
            Log::debug('AiResumeWriter: withGenerationConfig not supported, plain call');
            $response = Gemini::generativeModel($this->model)->generateContent($prompt);
        }

        $text = $response->text();

        if (empty(trim($text))) {
            throw new \RuntimeException('Gemini returned empty response.');
        }

        return $text;
    }

    private function extractJson(string $text): string
    {
        $start = strpos($text, '{');
        $end   = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($text, $start, $end - $start + 1);
        }

        $stripped = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $text);
        $stripped = trim($stripped);
        $start = strpos($stripped, '{');
        $end   = strrpos($stripped, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($stripped, $start, $end - $start + 1);
        }

        throw new \RuntimeException('Could not locate JSON in Gemini response: ' . $text);
    }

    private function validateStructure(array $data): void
    {
        $required = ['summary', 'skills', 'experience', 'projects', 'education', 'certifications'];
        $missing  = array_diff($required, array_keys($data));

        if (!empty($missing)) {
            throw new \RuntimeException('Gemini response missing keys: ' . implode(', ', $missing));
        }
    }

    private function fallback($user, string $skillsList, string $projectsList, string $certsList): array
    {
        return [
            'summary'        => ($user->bio ?? '') ?: 'AI summary currently unavailable. Please update manually.',
            'skills'         => $skillsList ?: 'Please list your skills.',
            'experience'     => $user->work_experience ?? 'Please add your work experience.',
            'projects'       => $projectsList ?: 'No projects listed.',
            'education'      => $user->education ?? 'Please add your education.',
            'certifications' => $certsList ?: 'No certifications listed.',
        ];
    }
}
