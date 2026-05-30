<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Track last login date (separate from activity streak)
        if (! $user->last_login || $user->last_login->toDateString() !== now()->toDateString()) {
            $user->last_login = now()->toDateString();
            $user->save();
        }

        // Reset streak to 0 if a day was missed
        if ($user->last_activity_date) {
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();
            $lastActivity = $user->last_activity_date->toDateString();

            if ($lastActivity !== $today && $lastActivity !== $yesterday) {
                if ($user->streak_days > 0) {
                    $user->streak_days = 0;
                    $user->save();
                }
            }
        }

        $projects = Cache::remember(
            "dashboard.projects.{$user->id}",
            now()->addSeconds(30),
            fn () => $user->projects()
                ->with('skills')
                ->latest()
                ->get()
        );

        $xpToNextLevel = $user->xpToNextLevel();
        $showMilestoneBanner = $user->shouldShowMilestoneBanner();
        $streakBonusActive = $user->streakBonusActive();
        $streakBonusMultiplier = $user->streakBonusMultiplier();

        // Calculate dynamic activity pulse (heatmap) and weekly chart
        $startDate = now()->subDays(34)->startOfDay();

        $projectDates = $user->projects()
            ->where('created_at', '>=', $startDate)
            ->pluck('created_at')
            ->map(fn ($d) => $d->toDateString())
            ->toArray();

        $nodeDates = $user->unlockedNodes()
            ->wherePivot('unlocked_at', '>=', $startDate)
            ->get()
            ->map(fn ($n) => \Carbon\Carbon::parse($n->pivot->unlocked_at)->toDateString())
            ->toArray();

        $badgeDates = $user->badges()
            ->wherePivot('earned_at', '>=', $startDate)
            ->get()
            ->map(fn ($b) => \Carbon\Carbon::parse($b->pivot->earned_at)->toDateString())
            ->toArray();

        // Combine all activity dates
        $allActivities = array_merge($projectDates, $nodeDates, $badgeDates);
        $activityCounts = array_count_values($allActivities);

        // Generate 35 days array (from 34 days ago until today)
        $heatmap = [];
        for ($i = 34; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = $activityCounts[$date] ?? 0;

            // Map count to level 0-4
            if ($count === 0) {
                $level = 0;
            } elseif ($count === 1) {
                $level = 1;
            } elseif ($count === 2) {
                $level = 2;
            } elseif ($count === 3) {
                $level = 3;
            } else {
                $level = 4;
            }
            $heatmap[] = $level;
        }

        // Weekly chart data (Monday to Sunday of the current week)
        $startOfWeek = now()->startOfWeek();
        $weekActivities = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $weekActivities[$date] = 0;
        }

        foreach ($allActivities as $dateStr) {
            if (isset($weekActivities[$dateStr])) {
                $weekActivities[$dateStr]++;
            }
        }

        $weeklyChart = [];
        foreach ($weekActivities as $date => $count) {
            if ($count === 0) {
                $height = 8;
            } elseif ($count === 1) {
                $height = 35;
            } elseif ($count === 2) {
                $height = 65;
            } else {
                $height = 100;
            }
            $weeklyChart[] = $height;
        }

        // Fetch core skills and compute dynamic radar chart values
        $skills = Skill::whereIn('slug', ['web-dev', 'backend', 'database', 'devops', 'mobile', 'ai', 'fullstack'])
            ->withCount('nodes')
            ->get();
        $radarData = [];

        foreach ($skills as $skill) {
            $total = $skill->nodes_count;
            $unlocked = $user->unlockedNodes()->where('skill_id', $skill->id)->count();
            $nodeProgress = $total > 0 ? ($unlocked / $total) * 100 : 0;

            $projectProficiency = $skill->calculateUserProficiency($user); // 0-5
            $proficiencyProgress = ($projectProficiency / 5) * 100;

            // Composite score is the weighted average of node progress (60%) and project proficiency (40%)
            $score = ($nodeProgress * 0.6) + ($proficiencyProgress * 0.4);
            $score = min(100, max(0, round($score)));

            // Simplify label for chart space
            $shortName = match ($skill->slug) {
                'web-dev' => 'Web Dev',
                'backend' => 'Backend',
                'database' => 'Database',
                'devops' => 'DevOps',
                'mobile' => 'Mobile',
                'ai' => 'AI & ML',
                'fullstack' => 'Full Stack',
                default => $skill->name
            };

            $radarData[] = [
                'label' => $shortName,
                'score' => $score,
            ];
        }

        return view('dashboard', compact(
            'projects',
            'xpToNextLevel',
            'showMilestoneBanner',
            'streakBonusActive',
            'streakBonusMultiplier',
            'heatmap',
            'weeklyChart',
            'radarData'
        ));
    }
}
