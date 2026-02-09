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
        
        // Check and update daily login
        $user->checkDailyLogin();
        
        // Get user's projects
        $projects = $user->projects()
            ->with('skills')
            ->latest()
            ->get();
        
        return view('dashboard', compact('projects'));
    }
    
    public function claimDailyReward(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canClaimDailyReward()) {
            return redirect()->back()->with('error', 'Daily reward already claimed!');
        }
        
        $reward = $user->claimDailyReward();
        
        if ($reward) {
            return redirect()->back()->with('success', 
                "Daily reward claimed! +{$reward->xp_earned} XP, +{$reward->gacha_currency_earned} Primogems"
            );
        }
        
        return redirect()->back()->with('error', 'Failed to claim reward');
    }
}
