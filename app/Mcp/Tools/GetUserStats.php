<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Gets gamified progress stats for the user (level, XP progress, streak, equipped badges count, and unlocked skills count).')]
class GetUserStats extends Tool
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

        $stats = [
            'name' => $user->name,
            'email' => $user->email,
            'title' => $user->title,
            'level' => $user->level,
            'xp' => $user->xp,
            'xp_needed' => $user->xpNeededForNextLevel(),
            'xp_progress' => $user->xpProgress(),
            'rank' => $user->rank,
            'streak_days' => $user->streak_days,
            'total_projects' => $user->projects()->count(),
            'total_badges' => $user->badges()->count(),
            'unlocked_skills' => $user->unlockedNodes()->count(),
        ];

        return Response::structured($stats);
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
