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
        
        // Get user's projects
        $projects = $user->projects()
            ->with('skills')
            ->latest()
            ->get();
        
        return view('dashboard', compact('projects'));
    }
}
