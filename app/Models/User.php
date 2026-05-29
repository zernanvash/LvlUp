<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    private const RANK_TITLES = [
        'Bronze'   => 'Junior Dev',
        'Silver'   => 'Mid Dev',
        'Gold'     => 'Senior Dev',
        'Platinum' => 'Tech Lead',
        'Diamond'  => 'Architect',
        'Master'   => 'Legend',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'title',
        'linkedin_url',
        'github_url',
        'level',
        'xp',
        'total_xp',
        'rank',
        'is_public',
        'last_login',
        'last_activity_date',
        'streak_days',
        // Contact (private / resume-only)
        'phone_number',
        'home_address',
        'city',
        'country',
        'website_url',
        // Skills
        'technical_skills',
        // Resume details (private)
        'resume_job_title',
        'resume_summary',
        'work_experience',
        'education',
        'certifications',
        'languages',
        // Visibility toggles
        'visibility_settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'date',
        'last_activity_date' => 'date',
        'is_public' => 'boolean',
        'password' => 'hashed',
        'visibility_settings' => 'array',
    ];

    // Relationships
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at', 'is_displayed')
                    ->withTimestamps();
    }

    public function equippedBadges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at', 'is_displayed')
                    ->withTimestamps()
                    ->wherePivot('is_displayed', true)
                    ->orderBy('user_badges.updated_at', 'asc');
    }

    public function unlockedNodes()
    {
        return $this->belongsToMany(SkillNode::class, 'user_skill_nodes')
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class)->latest();
    }

    // Gamification Methods
    public function addXP(int $amount): void
    {
        $this->xp += $amount;
        $this->total_xp += $amount;

        if ($this->xp < 0) {
            $this->xp = 0;
        }
        if ($this->total_xp < 0) {
            $this->total_xp = 0;
        }

        $leveledUp = false;
        $previousRank = $this->rank;

        // Level up logic - calculate XP needed before modifying level
        while ($this->xp >= ($xpNeeded = $this->xpNeededForNextLevel())) {
            $this->xp -= $xpNeeded;
            $this->level++;
            $this->updateRank();
            $leveledUp = true;
        }

        if ($leveledUp) {
            session()->flash('level_up', [
                'new_level'  => $this->level,
                'rank_title' => $this->getRankTitle(),
                'old_rank'   => $previousRank,
                'new_rank'   => $this->rank,
                'is_rank_up' => $previousRank !== $this->rank,
            ]);
        }

        $this->save();
        $this->clearFastUiCaches();
    }

    public function getRankTitle(): string
    {
        return self::RANK_TITLES[$this->rank] ?? $this->rank ?? 'Junior Dev';
    }

    public static function rankTitleMap(): array
    {
        return self::RANK_TITLES;
    }

    public function streakBonusMultiplier(): float
    {
        $tiers = (int) floor(($this->streak_days ?? 0) / 3);
        return min(1.0 + ($tiers * 0.1), 2.0);
    }

    public function streakBonusActive(): bool
    {
        return ($this->streak_days ?? 0) >= 3;
    }

    public function shouldShowMilestoneBanner(int $threshold = 100): bool
    {
        $gap = $this->xpNeededForNextLevel() - $this->xp;
        return $gap <= $threshold && $gap > 0;
    }

    public function xpToNextLevel(): int
    {
        return max(0, $this->xpNeededForNextLevel() - $this->xp);
    }

    /**
     * Called when a user completes a meaningful activity (project created/updated,
     * skill node unlocked). Increments streak by at most 1 per calendar day.
     */
    public function recordActivityStreak(): void
    {
        $today = now()->toDateString();
        $lastActivity = $this->last_activity_date
            ? $this->last_activity_date->toDateString()
            : null;

        // Already recorded activity today — no change
        if ($lastActivity === $today) {
            return;
        }

        $yesterday = now()->subDay()->toDateString();
        if ($lastActivity === $yesterday) {
            $this->streak_days = ($this->streak_days ?? 0) + 1;
        } else {
            // Missed a day (or first ever activity) — reset to 1
            $this->streak_days = 1;
        }

        $this->last_activity_date = $today;
        $this->save();
        $this->clearFastUiCaches();
    }

    public function xpNeededForNextLevel(): int
    {
        // Formula: 100 * level^1.5
        return (int) (100 * pow($this->level, 1.5));
    }

    public function xpProgress(): float
    {
        $needed = $this->xpNeededForNextLevel();
        if ($needed == 0) {
            return 0;
        }
        return ($this->xp / $needed) * 100;
    }

    private function updateRank(): void
    {
        $ranks = [
            1 => 'Bronze',
            10 => 'Silver',
            25 => 'Gold',
            50 => 'Platinum',
            75 => 'Diamond',
            100 => 'Master',
        ];

        foreach (array_reverse($ranks, true) as $level => $rank) {
            if ($this->level >= $level) {
                $this->rank = $rank;
                break;
            }
        }
    }

    // Badge Management
    public function equipBadge(int $badgeId): bool
    {
        // Check if user owns the badge
        if (!$this->badges()->where('badge_id', $badgeId)->exists()) {
            return false;
        }

        // Check if badge is already equipped
        $badge = $this->badges()->where('badge_id', $badgeId)->first();
        if ($badge && $badge->pivot->is_displayed) {
            return true; // Already equipped, no action needed
        }

        // Check if already at limit (6 badges)
        $equippedCount = $this->badges()->wherePivot('is_displayed', true)->count();
        if ($equippedCount >= 6) {
            return false;
        }

        // Equip the badge - update timestamps to track equip order
        $this->badges()->updateExistingPivot($badgeId, [
            'is_displayed' => true,
            'updated_at' => now()
        ]);
        $this->clearFastUiCaches();
        return true;
    }

    public function unequipBadge(int $badgeId): bool
    {
        // Check if user owns the badge
        if (!$this->badges()->where('badge_id', $badgeId)->exists()) {
            return false;
        }

        // Unequip the badge
        $this->badges()->updateExistingPivot($badgeId, ['is_displayed' => false]);
        $this->clearFastUiCaches();
        return true;
    }

    public function toggleVisibility(): void
    {
        $this->is_public = !$this->is_public;
        $this->save();
    }

    /**
     * Check if a specific field is visible on the public profile.
     * Defaults to true if not explicitly set.
     */
    public function isVisible(string $field): bool
    {
        $settings = $this->visibility_settings ?? [];
        return $settings[$field] ?? true;
    }

    public function getPublicUrl(): string
    {
        return route('profile.public', ['username' => $this->name]);
    }

    public function clearFastUiCaches(): void
    {
        Cache::forget("dashboard.projects.{$this->id}");
        Cache::forget("public-profile.{$this->id}");
        Cache::forget("api.stats.{$this->id}");
    }
}
