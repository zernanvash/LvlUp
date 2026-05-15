<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BadgeController extends Controller
{
    /**
     * Display all badges with earned status and progress
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('badges', 'projects.skills', 'unlockedNodes');

        $earnedBadges = $user->badges->keyBy('id');
        $progressCounts = $this->progressCountsFor($user);
        $allBadges = Cache::remember('badges.with-required-skill', now()->addMinutes(10), function () {
            return Badge::with('requiredSkill')
                ->orderBy('category')
                ->orderBy('threshold')
                ->get();
        });

        $badges = $allBadges->map(function ($badge) use ($earnedBadges, $progressCounts) {
            $earnedBadge = $earnedBadges->get($badge->id);
            $earned = $earnedBadge !== null;
            $progress = $earned ? 100 : $this->calculateProgress($badge, $progressCounts);

            return [
                'badge'       => $badge,
                'earned'      => $earned,
                'progress'    => round($progress, 1),
                'earned_at'   => $earned ? $earnedBadge->pivot->earned_at : null,
                'is_displayed'=> $earned ? (bool) $earnedBadge->pivot->is_displayed : false,
            ];
        });

        $badgesByCategory = $badges->groupBy('badge.category');
        $equippedBadges = $user->badges
            ->where('pivot.is_displayed', true)
            ->sortBy('pivot.updated_at')
            ->values();
        $equippedCount = $equippedBadges->count();
        $equippedJs = $equippedBadges->map(fn($b) => [
            'id'     => $b->id,
            'title'  => $b->title,
            'icon'   => $b->icon,
            'rarity' => $b->rarity,
            'color'  => match($b->rarity) {
                'uncommon'  => 'green',
                'rare'      => 'blue',
                'epic'      => 'purple',
                'legendary' => 'amber',
                'mythic'    => 'pink',
                default     => 'gray',
            },
        ])->values()->toArray();

        return view('achievements.index', compact('badgesByCategory', 'equippedBadges', 'equippedCount', 'equippedJs'));
    }

    private function progressCountsFor($user): array
    {
        $projects = $user->projects;
        $projectsByType = $projects->groupBy('project_type')->map->count();
        $projectsBySkill = [];

        foreach ($projects as $project) {
            foreach ($project->skills->unique('id') as $skill) {
                $projectsBySkill[$skill->id] = ($projectsBySkill[$skill->id] ?? 0) + 1;
            }
        }

        return [
            'projects' => $projects->count(),
            'level' => $user->level,
            'project_types' => $projectsByType,
            'streak' => $user->streak_days ?? 0,
            'skill_nodes' => $user->unlockedNodes->count(),
            'skill_projects' => $projectsBySkill,
        ];
    }

    private function calculateProgress(Badge $badge, array $counts): float
    {
        $current = match(true) {
            (bool) $badge->required_skill_id => $counts['skill_projects'][$badge->required_skill_id] ?? 0,
            $badge->category === 'project' => $counts['projects'],
            $badge->category === 'level' => $counts['level'],
            in_array($badge->category, ['web', 'backend', 'fullstack', 'mobile', 'devops', 'ai'], true) => $counts['project_types']->get($badge->category, 0),
            $badge->category === 'streak' => $counts['streak'],
            $badge->category === 'skill_node' => $counts['skill_nodes'],
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
