<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Badge;
use App\Models\Skill;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Track last login date (separate from activity streak)
        if (!$user->last_login || $user->last_login->toDateString() !== now()->toDateString()) {
            $user->last_login = now()->toDateString();
            $user->save();
        }

        $projects = $user->projects()
            ->with('skills')
            ->latest()
            ->get();

        $xpToNextLevel        = $user->xpToNextLevel();
        $showMilestoneBanner  = $user->shouldShowMilestoneBanner();
        $streakBonusActive    = $user->streakBonusActive();
        $streakBonusMultiplier = $user->streakBonusMultiplier();

        return view('dashboard', compact(
            'projects',
            'xpToNextLevel',
            'showMilestoneBanner',
            'streakBonusActive',
            'streakBonusMultiplier'
        ));
    }
}
