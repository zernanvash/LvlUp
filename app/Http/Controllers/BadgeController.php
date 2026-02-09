<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all badges grouped by category
        $badges = Badge::orderBy('rarity')
            ->orderBy('category')
            ->get()
            ->groupBy('category');
        
        // Get user's earned badges
        $earnedBadges = $user->badges->pluck('id')->toArray();
        
        return view('achievements.index', compact('badges', 'earnedBadges'));
    }
    
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
        
        // Toggle display status
        $user->badges()->updateExistingPivot($badge->id, [
            'is_displayed' => !$user->badges->find($badge->id)->pivot->is_displayed
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Badge display toggled successfully.',
        ]);
    }
}
