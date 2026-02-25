<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class AiResumeWriter
{
    public function generate($user, $resume, $projects, $skills): array
    {
        $projectsList = $projects->map(
            fn($p) => "- {$p->name}: {$p->description}"
        )->join("\n");

        $skillsList = $skills->pluck('name')->join(', ');

        $prompt = <<<PROMPT
You are an expert resume writer.

Generate a professional ATS-optimized resume.

Return STRICT JSON:

{
 "summary": "...",
 "skills": "...",
 "projects": "...",
 "experience": "...",
 "education": "..."
}

Candidate:
Name: {$user->name}
Email: {$user->email}

Target Role:
{$resume->job_title}

Job Description:
{$resume->job_description}

Keywords:
{$resume->target_keywords}

Projects:
{$projectsList}

Skills:
{$skillsList}
PROMPT;

        try {

            $response = Gemini::generativeModel('gemini-1.5-flash')
                ->generateContent($prompt);

            $text = trim($response->text() ?? '');

            return json_decode($text, true);

        } catch (\Throwable $e) {

            Log::error('AI Resume Generation Failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'summary' => 'AI summary unavailable',
                'skills' => $skillsList,
                'projects' => $projectsList,
                'experience' => '',
                'education' => ''
            ];
        }
    }
}