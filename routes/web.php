<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillTreeController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::redirect('/', '/login');

// Authentication Routes (Laravel Breeze provides these)
require __DIR__.'/auth.php';

// Protected Routes (Require Authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/daily-reward/claim', [DashboardController::class, 'claimDailyReward'])->name('daily-reward.claim');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    Route::get('/new', function () {
        return redirect()->route('projects.create');
    });
    
    // Skill Tree
    Route::get('/skill-tree', [SkillTreeController::class, 'index'])->name('skill-tree.index');
    Route::post('/skill-tree/{node}/unlock', [SkillTreeController::class, 'unlock'])->name('skill-tree.unlock');
    Route::get('/skill-tree/{node}/details', [SkillTreeController::class, 'getNodeDetails'])->name('skill-tree.details');
    
    // Achievements/Badges
    Route::get('/achievements', [BadgeController::class, 'index'])->name('achievements.index');
    Route::post('/badges/{badge}/toggle-display', [BadgeController::class, 'toggleDisplay'])->name('badges.toggle-display');
    
    // Resume Builder
    Route::get('/resume-builder', [ResumeController::class, 'index'])->name('resume.index');
    Route::post('/resume/analyze', [ResumeController::class, 'analyze'])->name('resume.analyze');
    Route::post('/resume/generate', [ResumeController::class, 'generate'])->name('resume.generate');
    Route::get('/resume/{resume}/download', [ResumeController::class, 'download'])->name('resume.download');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

// API Routes (Optional - for future mobile app)
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['projects', 'badges', 'unlockedNodes']);
    });
    
    Route::get('/stats', function (Request $request) {
        $user = $request->user();
        return [
            'level' => $user->level,
            'xp' => $user->xp,
            'xp_needed' => $user->xpNeededForNextLevel(),
            'xp_progress' => $user->xpProgress(),
            'rank' => $user->rank,
            'primogems' => $user->gacha_currency,
            'skill_points' => $user->skill_points,
            'streak_days' => $user->streak_days,
            'total_projects' => $user->projects()->count(),
            'total_badges' => $user->badges()->count(),
            'unlocked_skills' => $user->unlockedNodes()->count(),
        ];
    });
});
