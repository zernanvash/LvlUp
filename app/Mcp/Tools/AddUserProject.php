<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Creates a new project for the user. Automatically calculates complexity-based XP reward, attaches skill tags, checks badge eligibility, and records activity streak.')]
class AddUserProject extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response|ResponseFactory
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user() ?? auth()->user() ?? User::first();

        if (! $user) {
            return Response::error('No user found in the system.');
        }

        $name = $request->get('name');
        $description = $request->get('description');
        $url = $request->get('url');
        $github_url = $request->get('github_url');
        $language = $request->get('language');
        $project_type = $request->get('project_type') ?? 'web';
        $code_content = $request->get('code_content');
        $tags = $request->get('tags');

        if (empty($name)) {
            return Response::error('The project name is required.');
        }

        if (empty($language)) {
            return Response::error('The primary programming language is required.');
        }

        // Calculate XP reward based on complexity
        $xpReward = 100; // Base XP
        if (! empty($code_content)) {
            $lines = count(explode("\n", $code_content));
            $xpReward += min($lines * 2, 400); // Max +400 XP for code
        }

        // Create the project
        $project = $user->projects()->create([
            'name' => $name,
            'description' => $description,
            'url' => $url,
            'github_url' => $github_url,
            'language' => $language,
            'project_type' => $project_type,
            'xp_reward' => $xpReward,
            'metadata' => [
                'code_snippet' => $code_content,
                'lines_of_code' => $code_content ? count(explode("\n", $code_content)) : 0,
            ],
        ]);

        // Attach skills from tags
        if (! empty($tags)) {
            $tagsArray = is_array($tags) ? $tags : array_map('trim', explode(',', $tags));
            $project->attachSkillsFromTags($tagsArray);
        }

        // Check badges
        $newBadges = $project->fresh()->checkBadgesAndReturn();

        // Record activity streak
        $user->fresh()->recordActivityStreak();

        return Response::structured([
            'success' => true,
            'message' => "Project created successfully! You earned {$xpReward} XP.",
            'project_id' => $project->id,
            'xp_earned' => $xpReward,
            'new_badges' => $newBadges,
        ]);
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('The name of the project.')
                ->required(),
            'description' => $schema->string()
                ->description('A brief description of what the project does.'),
            'url' => $schema->string()
                ->description('Optional live demo URL of the project.'),
            'github_url' => $schema->string()
                ->description('Optional GitHub repository URL of the project.'),
            'language' => $schema->string()
                ->description('Primary programming language (e.g. PHP, JavaScript, Rust).')
                ->required(),
            'project_type' => $schema->string()
                ->description('Type of the project.')
                ->enum(['web', 'backend', 'fullstack', 'mobile', 'devops', 'ai', 'other']),
            'code_content' => $schema->string()
                ->description('Optional snippet of code/files to analyze for auto-suggesting skills and calculating complexity XP bonus.'),
            'tags' => $schema->string()
                ->description('Optional comma-separated skill tags to manually attach (e.g. "React, Tailwind, Docker").'),
        ];
    }
}
