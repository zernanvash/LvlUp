<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'title',
        'level',
        'xp',
        'total_xp',
        'rank',
        'is_public',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'date',
        'password' => 'hashed',
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

    // Gamification Methods
    public function addXP(int $amount): void
    {
        $this->xp += $amount;
        $this->total_xp += $amount;
        
        // Level up logic - calculate XP needed before modifying level
        while ($this->xp >= ($xpNeeded = $this->xpNeededForNextLevel())) {
            $this->xp -= $xpNeeded;
            $this->level++;
            
            // Update rank based on level
            $this->updateRank();
        }
        
        $this->save();
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
        return true;
    }

    public function toggleVisibility(): void
    {
        $this->is_public = !$this->is_public;
        $this->save();
    }

    public function getPublicUrl(): string
    {
        return route('profile.public', ['username' => $this->name]);
    }
}
