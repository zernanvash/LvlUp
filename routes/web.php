<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillTreeController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Api\UserSnapshotController;
use App\Http\Controllers\ShortcutController;
use App\Http\Controllers\UserSearchController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/new', [ShortcutController::class, 'newProject']);
    
    // Skill Tree
    Route::get('/skill-tree', [SkillTreeController::class, 'index'])->name('skill-tree.index');
    Route::get('/skill-tree/progress', [SkillTreeController::class, 'progress'])->name('skill-tree.progress');
    Route::get('/skill-tree/{node}', [SkillTreeController::class, 'show'])->name('skill-tree.show');
    Route::post('/skill-tree/{node}/unlock', [SkillTreeController::class, 'unlock'])->name('skill-tree.unlock');
    
    // Achievements/Badges
    Route::get('/achievements', [BadgeController::class, 'index'])->name('achievements.index');
    Route::post('/badges/{badge}/equip', [BadgeController::class, 'equip'])->name('badges.equip');
    Route::post('/badges/{badge}/unequip', [BadgeController::class, 'unequip'])->name('badges.unequip');
    Route::post('/badges/{badge}/toggle-display', [BadgeController::class, 'toggleDisplay'])->name('badges.toggle-display');
    
    // Resume Builder (single page at /resume)
    Route::get('/resume', [ResumeController::class, 'index'])->name('resume.index');
    Route::post('/resume/generate', [ResumeController::class, 'generate'])->name('resume.generate');
    Route::post('/resume/analyze', [ResumeController::class, 'analyze'])->name('resume.analyze');
    Route::get('/resume/download', [ResumeController::class, 'download'])->name('resume.download');
    Route::get('/resume/preview', [ResumeController::class, 'preview'])->name('resume.preview');

    // Certificates (uploaded to profile, used in resume)
    Route::post('/resume/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::post('/resume/certificates/{certificate}/summary', [CertificateController::class, 'regenerateSummary'])->name('certificates.regenerate-summary');
    Route::delete('/resume/certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/visibility', [ProfileController::class, 'updateVisibility'])->name('profile.visibility');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::patch('/profile/toggle-visibility', [ProfileController::class, 'toggleVisibility'])->name('profile.toggle-visibility');
    
    // Discover / User Search
    Route::get('/users', [UserSearchController::class, 'index'])->name('users.index');

});

// Public Profile
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.public');

// API Routes (Optional - for future mobile app)
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserSnapshotController::class, 'profile']);
    Route::get('/stats', [UserSnapshotController::class, 'stats']);
});
