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
Route::get('/', function () {
    return view('welcome');
});

// Public Profile (accessible without authentication)
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.public');

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
    Route::get('/skill-tree/{node}', [SkillTreeController::class, 'show'])->name('skill-tree.show');
    Route::post('/skill-tree/{node}/unlock', [SkillTreeController::class, 'unlock'])->name('skill-tree.unlock');
    Route::get('/skill-tree-progress', [SkillTreeController::class, 'progress'])->name('skill-tree.progress');
    
    // Achievements/Badges
    Route::get('/achievements', [BadgeController::class, 'index'])->name('achievements.index');
    Route::get('/badges/{badge}', [BadgeController::class, 'show'])->name('badges.show');
    Route::post('/badges/{badge}/equip', [BadgeController::class, 'equip'])->name('badges.equip');
    Route::post('/badges/{badge}/unequip', [BadgeController::class, 'unequip'])->name('badges.unequip');
    Route::post('/badges/{badge}/toggle-display', [BadgeController::class, 'toggleDisplay'])->name('badges.toggle-display');
    
    // Resume Builder
    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/create', [ResumeController::class, 'create'])->name('resumes.create');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
    Route::patch('/resumes/{resume}', [ResumeController::class, 'update'])->name('resumes.update');
    Route::post('/resumes/analyze', [ResumeController::class, 'analyze'])->name('resumes.analyze');
    Route::post('/resumes/{resume}/generate', [ResumeController::class, 'generate'])->name('resumes.generate');
    Route::get('/resumes/{resume}/download', [ResumeController::class, 'download'])->name('resumes.download');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/toggle-visibility', [ProfileController::class, 'toggleVisibility'])->name('profile.toggle-visibility');
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
