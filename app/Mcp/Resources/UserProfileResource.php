<?php

namespace App\Mcp\Resources;

use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Contracts\HasUriTemplate;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Support\UriTemplate;

#[Description('Access a developer’s public profile by their user ID, returning a formatted Markdown overview of their bio, rank, level, and featured projects.')]
#[MimeType('text/markdown')]
class UserProfileResource extends Resource implements HasUriTemplate
{
    public function uriTemplate(): UriTemplate
    {
        return new UriTemplate('file://users/{userId}/profile');
    }

    public function handle(Request $request): Response
    {
        $userId = $request->get('userId');
        $user = User::find($userId) ?? User::first();

        if (! $user) {
            return Response::error('No user found in the system.');
        }

        $projects = $user->projects()->where('is_featured', true)->get();
        if ($projects->isEmpty()) {
            $projects = $user->projects()->take(3)->get();
        }

        $markdown = "# Developer Profile: {$user->name}\n\n";
        $markdown .= "**Title:** {$user->title}\n";
        $markdown .= "**Rank:** {$user->rank} ({$user->getRankTitle()})\n";
        $markdown .= "**Level:** {$user->level} (XP: {$user->xp} / {$user->xpNeededForNextLevel()})\n";
        $markdown .= "**Streak:** {$user->streak_days} days\n\n";
        $markdown .= "## Bio\n{$user->bio}\n\n";
        $markdown .= "## Technical Skills\n{$user->technical_skills}\n\n";

        if ($projects->isNotEmpty()) {
            $markdown .= "## Featured Projects\n";
            foreach ($projects as $project) {
                $markdown .= "- **{$project->name}** ({$project->language} — {$project->project_type})\n";
                if ($project->description) {
                    $markdown .= "  *{$project->description}*\n";
                }
                if ($project->url) {
                    $markdown .= "  Demo: {$project->url}\n";
                }
                if ($project->github_url) {
                    $markdown .= "  GitHub: {$project->github_url}\n";
                }
            }
        }

        return Response::text($markdown);
    }
}
