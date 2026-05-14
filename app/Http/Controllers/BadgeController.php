<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Display all badges with earned status and progress
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('badges', 'projects');

        $badges = Badge::with('requiredSkill')->get()->map(function ($badge) use ($user) {
            $earned = $user->badges->contains($badge->id);
            $progress = $earned ? 100 : $this->calculateProgress($badge, $user);

            return [
                'badge'       => $badge,
                'earned'      => $earned,
                'progress'    => round($progress, 1),
                'earned_at'   => $earned ? $user->badges->find($badge->id)->pivot->earned_at : null,
                'is_displayed'=> $earned ? (bool) $user->badges->find($badge->id)->pivot->is_displayed : false,
            ];
        });

        $badgesByCategory = $badges->groupBy('badge.category');

        return view('achievements.index', compact('badgesByCategory'));
    }

    private function calculateProgress(Badge $badge, $user): float
    {
        if ($badge->required_skill_id) {
            $count = $user->projects()
                ->whereHas('skills', fn($q) => $q->where('skills.id', $badge->required_skill_id))
                ->count();
            return min(100, ($count / max(1, $badge->threshold)) * 100);
        }

        $current = match($badge->category) {
            'project'   => $user->projects()->count(),
            'level'     => $user->level,
            'web'       => $user->projects()->where('project_type', 'web')->count(),
            'backend'   => $user->projects()->where('project_type', 'backend')->count(),
            'fullstack' => $user->projects()->where('project_type', 'fullstack')->count(),
            'mobile'    => $user->projects()->where('project_type', 'mobile')->count(),
            'devops'    => $user->projects()->where('project_type', 'devops')->count(),
            'ai'        => $user->projects()->where('project_type', 'ai')->count(),
            'streak'    => $user->streak_days ?? 0,
            'skill_node'=> $user->unlockedNodes()->count(),
            default     => 0,
        };

        return min(100, ($current / max(1, $badge->threshold)) * 100);
    }
    
    /**
     * Display badge details
     */
    public function show(Badge $badge)
    {
        $user = auth()->user();
        
        $earned = $user->badges->contains($badge->id);
        $progress = 0;
        
        if (!$earned) {
            // Calculate progress toward earning this badge
            if ($badge->required_skill_id) {
                $projectsWithSkill = $user->projects()
                    ->whereHas('skills', fn($q) => $q->where('skills.id', $badge->required_skill_id))
                    ->count();
                $progress = min(100, ($projectsWithSkill / max(1, $badge->threshold)) * 100);
            } elseif ($badge->category === 'project') {
                $projectCount = $user->projects()->count();
                $progress = min(100, ($projectCount / max(1, $badge->threshold)) * 100);
            } elseif ($badge->category === 'level') {
                $progress = min(100, ($user->level / max(1, $badge->threshold)) * 100);
            }
        } else {
            $progress = 100;
        }
        
        $earnedAt = $earned ? $user->badges->find($badge->id)->pivot->earned_at : null;
        $isDisplayed = $earned ? $user->badges->find($badge->id)->pivot->is_displayed : false;
        
        return view('achievements.show', compact('badge', 'earned', 'progress', 'earnedAt', 'isDisplayed'));
    }
    
    /**
     * Equip badge to profile (validate limit)
     */
    public function equip(Badge $badge)
    {
        $user = auth()->user();
        
        // Check if user has earned this badge
        if (!$user->badges->contains($badge->id)) {
            return back()->with('error', 'You have not earned this badge yet.');
        }
        
        // Attempt to equip the badge (User model handles limit validation)
        $success = $user->equipBadge($badge->id);
        
        if (!$success) {
            return back()->with('error', 'You can only equip up to 6 badges. Please unequip a badge first.');
        }
        
        return back()->with('success', 'Badge equipped successfully!');
    }
    
    /**
     * Remove badge from profile
     */
    public function unequip(Badge $badge)
    {
        $user = auth()->user();
        
        // Check if user has earned this badge
        if (!$user->badges->contains($badge->id)) {
            return back()->with('error', 'You have not earned this badge yet.');
        }
        
        // Unequip the badge
        $success = $user->unequipBadge($badge->id);
        
        if (!$success) {
            return back()->with('error', 'Failed to unequip badge.');
        }
        
        return back()->with('success', 'Badge unequipped successfully!');
    }
    
    /**
     * Toggle display status (kept for backward compatibility with existing routes)
     */
    public function toggleDisplay(Request $request, Badge $badge)
    {
        $user = auth()->user();
        
        // Check if user has this badge
        if (!$user->badges->contains($badge->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have not earned this badge yet.',
            ], 403);
        }
        
        $currentStatus = $user->badges->find($badge->id)->pivot->is_displayed;
        
        // If trying to equip, check limit
        if (!$currentStatus) {
            $equippedCount = $user->badges()->wherePivot('is_displayed', true)->count();
            if ($equippedCount >= 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only equip up to 6 badges. Please unequip a badge first.',
                ], 422);
            }
        }
        
        // Toggle display status
        $user->badges()->updateExistingPivot($badge->id, [
            'is_displayed' => !$currentStatus,
            'updated_at' => now()
        ]);
        $user->clearFastUiCaches();
        
        return response()->json([
            'success' => true,
            'message' => 'Badge display toggled successfully.',
        ]);
    }
}
