<?php

namespace App\Mcp\Tools;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Gets all skills registered in the system along with the user’s unlocked node counts, project average proficiency, and composite level progress score (0 to 100).')]
class GetUserSkills extends Tool
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

        $skills = Skill::withCount('nodes')->get()->map(function ($skill) use ($user) {
            $total = $skill->nodes_count;
            $unlocked = $user->unlockedNodes()->where('skill_id', $skill->id)->count();
            $nodeProgress = $total > 0 ? ($unlocked / $total) * 100 : 0;

            $projectProficiency = $skill->calculateUserProficiency($user); // 0-5
            $proficiencyProgress = ($projectProficiency / 5) * 100;

            // Composite score is the weighted average of node progress (60%) and project proficiency (40%)
            $score = ($nodeProgress * 0.6) + ($proficiencyProgress * 0.4);
            $score = min(100, max(0, round($score)));

            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'slug' => $skill->slug,
                'category' => $skill->category,
                'rarity' => $skill->rarity,
                'total_nodes' => $total,
                'unlocked_nodes' => $unlocked,
                'project_proficiency' => round($projectProficiency, 2),
                'composite_score' => $score,
            ];
        });

        return Response::structured($skills->toArray());
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
