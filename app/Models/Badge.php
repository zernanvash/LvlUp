<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'icon',
        'rarity',
        'category',
        'required_skill_id',
        'threshold',
        'xp_reward',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at', 'is_displayed')
                    ->withTimestamps();
    }

    public function requiredSkill()
    {
        return $this->belongsTo(Skill::class, 'required_skill_id');
    }

    public function getRarityColorAttribute(): string
    {
        return match($this->rarity) {
            'common' => '#9ca3af',
            'uncommon' => '#10b981',
            'rare' => '#3b82f6',
            'epic' => '#a855f7',
            'legendary' => '#f59e0b',
            'mythic' => '#ec4899',
            default => '#6b7280',
        };
    }

    public function checkEligibility(User $user): bool
    {
        // Check if user already has this badge
        if ($user->badges->contains($this->id)) {
            return false;
        }

        // Check skill-based badges
        if ($this->required_skill_id) {
            $projectsWithSkill = $user->projects()
                ->whereHas('skills', fn($q) => $q->where('skills.id', $this->required_skill_id))
                ->count();
            return $projectsWithSkill >= $this->threshold;
        }

        // Check project count badges
        if ($this->category === 'project') {
            return $user->projects()->count() >= $this->threshold;
        }

        // Check level badges
        if ($this->category === 'level') {
            return $user->level >= $this->threshold;
        }

        return false;
    }
}
