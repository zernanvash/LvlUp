<?php

namespace App\Http\Controllers;

use App\Models\SkillNode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SkillTreeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all skill nodes with their relationships
        $nodes = SkillNode::with(['skill', 'parent', 'children'])
            ->orderBy('y_position')
            ->get();
        
        // Get user's unlocked nodes
        $unlockedNodeIds = $user->unlockedNodes->pluck('id')->toArray();
        
        return view('skill-tree.index', compact('nodes', 'unlockedNodeIds'));
    }
    
    public function unlock(Request $request, SkillNode $node)
    {
        $user = Auth::user();
        
        // Check if node can be unlocked
        if (!$node->canBeUnlockedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot unlock this skill node. Check requirements.',
            ], 400);
        }
        
        try {
            // Wrap in transaction to ensure atomicity
            DB::transaction(function () use ($user, $node) {
                // Unlock the node
                $user->unlockedNodes()->attach($node->id, [
                    'unlocked_at' => now(),
                ]);
                
                // Deduct skill points
                $user->skill_points -= $node->skill_point_cost;
                $user->save();
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlock skill node. Please try again.',
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => "Skill '{$node->title}' unlocked!",
            'remaining_points' => $user->skill_points,
        ]);
    }
    
    public function getNodeDetails(SkillNode $node)
    {
        $user = Auth::user();
        
        $node->load('skill', 'parent');
        
        return response()->json([
            'node' => $node,
            'is_unlocked' => $node->isUnlockedBy($user),
            'can_unlock' => $node->canBeUnlockedBy($user),
            'user_level' => $user->level,
            'user_skill_points' => $user->skill_points,
        ]);
    }
}
