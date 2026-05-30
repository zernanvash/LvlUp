<?php

namespace App\Mcp\Prompts;

use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('optimize-resume')]
#[Title('Optimize Resume Prompt')]
#[Description('Critiques and optimizes a user\'s resume details and portfolio projects for a target job description.')]
class OptimizeResumePrompt extends Prompt
{
    /**
     * Get the prompt's arguments.
     *
     * @return array<int, \Laravel\Mcp\Server\Prompts\Argument>
     */
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'job_description',
                description: 'The target job description to optimize the resume for.',
                required: true
            ),
        ];
    }

    /**
     * Handle the prompt request.
     */
    public function handle(Request $request): array
    {
        $validated = $request->validate([
            'job_description' => 'required|string',
        ]);

        $jobDesc = $validated['job_description'];

        /** @var \App\Models\User|null $user */
        $user = $request->user() ?? auth()->user() ?? User::first();

        if (! $user) {
            return [
                Response::text('No user context found. Cannot optimize resume.')->asAssistant(),
            ];
        }

        // Build a summary of the user's current profile/resume details
        $jobTitle = $user->resume_job_title ?? $user->title ?? 'Developer';
        $profileSummary = "Name: {$user->name}\n";
        $profileSummary .= "Job Title: {$jobTitle}\n";
        $profileSummary .= "Bio: {$user->bio}\n";
        $profileSummary .= "Technical Skills: {$user->technical_skills}\n";
        $profileSummary .= "Work Experience:\n{$user->work_experience}\n\n";
        $profileSummary .= "Education:\n{$user->education}\n\n";

        $projects = $user->projects()->with('skills')->get();
        if ($projects->isNotEmpty()) {
            $profileSummary .= "Portfolio Projects:\n";
            foreach ($projects as $project) {
                $skillsList = $project->skills->pluck('name')->implode(', ');
                $profileSummary .= "- Name: {$project->name}\n";
                $profileSummary .= "  Language: {$project->language}\n";
                $profileSummary .= "  Type: {$project->project_type}\n";
                $profileSummary .= "  Description: {$project->description}\n";
                $profileSummary .= "  Skills: {$skillsList}\n";
            }
        }

        $systemMessage = "You are a professional technical recruiter and resume writer.
Your job is to analyze the user's portfolio and resume details, compare them to the target job description, and provide actionable feedback on:
1. Keyword alignment (what skills/keywords are missing or should be highlighted).
2. Project framing (how to describe their projects to better highlight relevant experience).
3. Experience and education positioning.

Here is the developer's current profile and portfolio:
{$profileSummary}";

        $userMessage = "Please review my profile and projects for the following target job description and show me how to optimize my resume for it:\n\n{$jobDesc}";

        return [
            Response::text($systemMessage)->asAssistant(),
            Response::text($userMessage),
        ];
    }
}
