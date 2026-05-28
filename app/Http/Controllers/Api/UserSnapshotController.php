<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserSnapshotController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->load(['projects', 'badges', 'unlockedNodes'])
        );
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(Cache::remember(
            "api.stats.{$user->id}",
            now()->addSeconds(30),
            fn () => [
                'level' => $user->level,
                'xp' => $user->xp,
                'xp_needed' => $user->xpNeededForNextLevel(),
                'xp_progress' => $user->xpProgress(),
                'rank' => $user->rank,
                'primogems' => $user->gacha_currency,
                'skill_points' => $user->skill_points,
                'streak_days' => $user->streak_days,
                'total_projects' => $user->projects()->count(),
                'total_badges' => $user->badges()->count(),
                'unlocked_skills' => $user->unlockedNodes()->count(),
            ]
        ));
    }
}
