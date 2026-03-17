<?php

namespace App\Http\Controllers;

use App\Models\SkillNode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillTreeController extends Controller
{
    /**
     * Display skill tree with user's progress
     * Requirements: 4.2, 4.3, 10.1
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all skill nodes with their relationships
        $nodes = SkillNode::with(['skill', 'parent', 'children'])
            ->orderBy('tier')
            ->orderBy('y_position')
            ->get();
        
        // Get user's unlocked node IDs for quick lookup
        $unlockedNodeIds = $user->unlockedNodes->pluck('id')->toArray();
        
        return view('skill-tree.index', compact('nodes', 'unlockedNodeIds'));
    }
    
    /**
     * Show node details, requirements, and progress
     * Requirements: 10.1, 10.3
     */
    public function show(SkillNode $node)
    {
        $user = auth()->user();
        
        $node->load(['skill', 'parent', 'children']);
        
        $isUnlocked = $node->isUnlockedBy($user);
        $canUnlock = $node->canBeUnlockedBy($user);
        $taskProgress = $node->calculateTaskProgress($user);
        
        // Determine node state
        $state = 'locked';
        if ($isUnlocked) {
            $state = 'unlocked';
        } elseif ($canUnlock) {
            $state = 'available';
        }
        
        // Get requirements details
        $requirements = [
            'level' => [
                'required' => $node->required_level,
                'current' => $user->level,
                'met' => $user->level >= $node->required_level,
            ],
            'parent' => null,
            'tasks' => $taskProgress,
        ];
        
        // Check parent requirement
        if ($node->parent_node_id) {
            $parentUnlocked = $node->parent->isUnlockedBy($user);
            $requirements['parent'] = [
                'node' => $node->parent,
                'title' => $node->parent->title,
                'unlocked' => $parentUnlocked,
                'met' => $parentUnlocked,
            ];
        }
        
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'node' => $node,
                'state' => $state,
                'is_unlocked' => $isUnlocked,
                'can_unlock' => $canUnlock,
                'requirements' => $requirements,
            ]);
        }
        
        return view('skill-tree.show', compact('node', 'state', 'isUnlocked', 'canUnlock', 'requirements', 'user'));
    }
    
    /**
     * Attempt to unlock node (validate all requirements)
     * Requirements: 4.4, 4.5, 4.6, 10.5
     */
    public function unlock(SkillNode $node)
    {
        $user = auth()->user();

        // Check if already unlocked
        if ($node->isUnlockedBy($user)) {
            $msg = 'You have already unlocked this skill node.';
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // Check if node can be unlocked
        if (!$node->canBeUnlockedBy($user)) {
            $msg = $this->getUnlockFailureReason($node, $user);
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        try {
            $user->unlockedNodes()->attach($node->id, ['unlocked_at' => now()]);

            // Collect any badges earned (via XP from skill unlock)
            // We check badges manually here since no Project event fires
            $newBadges = $this->checkSkillNodeBadges($user);

            // Check if leveled up (addXP may have been called via badge rewards)
            $levelUpData = session()->get('level_up');
            session()->forget('level_up'); // consume it — we'll return it in JSON

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success'    => true,
                    'title'      => $node->title,
                    'tier'       => $node->tier,
                    'icon'       => $node->skill->icon ?? 'fas fa-code',
                    'new_badges' => $newBadges,
                    'level_up'   => $levelUpData,
                ]);
            }

            // Fallback for non-AJAX (shouldn't happen with new frontend)
            session()->flash('node_unlocked', [
                'title' => $node->title,
                'tier'  => $node->tier,
                'icon'  => $node->skill->icon ?? 'fas fa-code',
            ]);
            if (!empty($newBadges)) {
                session()->flash('new_badges', $newBadges);
            }

            return back()->with('success', "Skill node '{$node->title}' unlocked successfully!");
        } catch (\Exception $e) {
            $msg = 'Failed to unlock skill node. Please try again.';
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return back()->with('error', $msg);
        }
    }

    /**
     * Check and award badges related to skill node unlocks
     */
    private function checkSkillNodeBadges($user): array
    {
        $user = \App\Models\User::with('badges')->find($user->id);
        $newlyEarned = [];

        $badges = \App\Models\Badge::whereNull('required_skill_id')
            ->whereIn('category', ['skill_node', 'level'])
            ->get();

        foreach ($badges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }
            if ($badge->checkEligibility($user)) {
                $user->badges()->attach($badge->id, [
                    'earned_at'    => now(),
                    'is_displayed' => false,
                ]);
                $user->addXP($badge->xp_reward);
                $user->load('badges');
                $newlyEarned[] = [
                    'title'        => $badge->title,
                    'icon'         => $badge->icon,
                    'rarity'       => $badge->rarity,
                    'rarity_color' => $badge->rarity_color,
                    'xp_reward'    => $badge->xp_reward,
                ];
            }
        }

        return $newlyEarned;
    }
    
    /**
     * Return JSON of user's unlock progress and task progress
     * Requirements: 10.3
     */
    public function progress()
    {
        $user = auth()->user();
        
        // Get all nodes with progress information
        $nodes = SkillNode::with(['skill', 'parent'])->get();
        
        $progress = $nodes->map(function ($node) use ($user) {
            $isUnlocked = $node->isUnlockedBy($user);
            $canUnlock = $node->canBeUnlockedBy($user);
            $taskProgress = $node->calculateTaskProgress($user);
            
            // Determine node state
            $state = 'locked';
            if ($isUnlocked) {
                $state = 'unlocked';
            } elseif ($canUnlock) {
                $state = 'available';
            }
            
            return [
                'id' => $node->id,
                'title' => $node->title,
                'tier' => $node->tier,
                'state' => $state,
                'is_unlocked' => $isUnlocked,
                'can_unlock' => $canUnlock,
                'unlocked_at' => $isUnlocked ? $user->unlockedNodes->find($node->id)->pivot->unlocked_at : null,
                'requirements' => [
                    'level' => [
                        'required' => $node->required_level,
                        'current' => $user->level,
                        'met' => $user->level >= $node->required_level,
                    ],
                    'parent' => $node->parent_node_id ? [
                        'id' => $node->parent_node_id,
                        'title' => $node->parent->title,
                        'unlocked' => $node->parent->isUnlockedBy($user),
                    ] : null,
                    'tasks' => $taskProgress,
                ],
            ];
        });
        
        return response()->json([
            'success' => true,
            'user' => [
                'level' => $user->level,
                'xp' => $user->xp,
                'total_xp' => $user->total_xp,
                'rank' => $user->rank,
            ],
            'nodes' => $progress,
            'total_nodes' => $nodes->count(),
            'unlocked_count' => $user->unlockedNodes->count(),
            'available_count' => $progress->where('state', 'available')->count(),
        ]);
    }
    
    /**
     * Helper method to determine why a node cannot be unlocked
     */
    private function getUnlockFailureReason(SkillNode $node, $user): string
    {
        // Check level requirement
        if ($user->level < $node->required_level) {
            return "You need to reach level {$node->required_level} to unlock this node. You are currently level {$user->level}.";
        }
        
        // Check parent requirement
        if ($node->parent_node_id && !$node->parent->isUnlockedBy($user)) {
            return "You must unlock '{$node->parent->title}' before unlocking this node.";
        }
        
        // Check task requirements
        $taskProgress = $node->checkTaskRequirements($user);
        foreach ($taskProgress as $task) {
            if (!$task['completed']) {
                return "Task requirement not met: {$task['description']} ({$task['current']}/{$task['required']})";
            }
        }
        
        return 'Cannot unlock this skill node. Please check all requirements.';
    }
}
