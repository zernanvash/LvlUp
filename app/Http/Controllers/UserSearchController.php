<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $rankFilter = $request->get('rank', '');
        $sortBy = $request->get('sort', 'level');

        $users = User::where('is_public', true)
            ->where('id', '!=', auth()->id())
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('title', 'like', "%{$query}%")
                      ->orWhere('bio', 'like', "%{$query}%")
                      ->orWhere('technical_skills', 'like', "%{$query}%");
                });
            })
            ->when($rankFilter, fn($q) => $q->where('rank', $rankFilter))
            ->when($sortBy === 'level', fn($q) => $q->orderByDesc('level')->orderByDesc('total_xp'))
            ->when($sortBy === 'projects', fn($q) => $q->withCount('projects')->orderByDesc('projects_count'))
            ->when($sortBy === 'badges', fn($q) => $q->withCount('badges')->orderByDesc('badges_count'))
            ->when($sortBy === 'name', fn($q) => $q->orderBy('name'))
            ->with(['equippedBadges' => fn($q) => $q->limit(3)])
            ->withCount(['projects', 'badges'])
            ->paginate(12)
            ->withQueryString();

        $ranks = ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Master'];

        return view('users.index', compact('users', 'query', 'rankFilter', 'sortBy', 'ranks'));
    }
}
