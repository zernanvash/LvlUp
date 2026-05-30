<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Gets all projects created by the user (with name, description, URLs, language, type, XP earned, and attached skills).')]
class GetUserProjects extends Tool
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

        $projects = $user->projects()->with('skills')->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'url' => $project->url,
                'github_url' => $project->github_url,
                'language' => $project->language,
                'project_type' => $project->project_type,
                'xp_reward' => $project->xp_reward,
                'is_featured' => $project->is_featured,
                'skills' => $project->skills->map(fn ($s) => [
                    'name' => $s->name,
                    'proficiency' => $s->pivot->proficiency,
                ])->values()->all(),
            ];
        });

        return Response::structured($projects->toArray());
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
