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

        // Update login streak on dashboard visit
        $user->updateLoginStreak();

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
