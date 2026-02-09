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
        'skill_points',
        'rank',
        'gacha_currency',
        'streak_days',
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

    public function unlockedNodes()
    {
        return $this->belongsToMany(SkillNode::class, 'user_skill_nodes')
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }

    public function dailyRewards()
    {
        return $this->hasMany(DailyReward::class);
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
        
        // Level up logic
        while ($this->xp >= $this->xpNeededForNextLevel()) {
            $this->xp -= $this->xpNeededForNextLevel();
            $this->level++;
            $this->skill_points += 3; // Gain skill points on level up
            
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

    public function checkDailyLogin(): void
    {
        $today = Carbon::today();
        
        if ($this->last_login === null || !$this->last_login->isToday()) {
            $yesterday = Carbon::yesterday();
            
            if ($this->last_login && $this->last_login->isSameDay($yesterday)) {
                // Continue streak
                $this->streak_days++;
            } else {
                // Reset streak
                $this->streak_days = 1;
            }
            
            $this->last_login = $today;
            $this->save();
        }
    }

    public function canClaimDailyReward(): bool
    {
        return !$this->dailyRewards()
                    ->whereDate('claimed_date', Carbon::today())
                    ->exists();
    }

    public function claimDailyReward(): ?DailyReward
    {
        if (!$this->canClaimDailyReward()) {
            return null;
        }

        $dayNumber = $this->streak_days;
        $baseXP = 50;
        $baseCurrency = 20;
        
        // Bonus rewards for streaks (every 7 days)
        $multiplier = floor($dayNumber / 7) + 1;
        
        $reward = $this->dailyRewards()->create([
            'claimed_date' => Carbon::today(),
            'day_number' => $dayNumber,
            'xp_earned' => $baseXP * $multiplier,
            'gacha_currency_earned' => $baseCurrency * $multiplier,
        ]);

        $this->addXP($reward->xp_earned);
        $this->gacha_currency += $reward->gacha_currency_earned;
        $this->save();

        return $reward;
    }
}
