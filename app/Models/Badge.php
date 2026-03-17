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
            'common'   => '#9ca3af',
            'uncommon' => '#10b981',
            'rare'     => '#3b82f6',
            'epic'     => '#a855f7',
            'legendary'=> '#f59e0b',
            'mythic'   => '#ec4899',
            default    => '#6b7280',
        };
    }

    public function getRarityTailwindAttribute(): string
    {
        return match($this->rarity) {
            'common'   => 'gray',
            'uncommon' => 'emerald',
            'rare'     => 'blue',
            'epic'     => 'purple',
            'legendary'=> 'amber',
            'mythic'   => 'pink',
            default    => 'gray',
        };
    }

    public function checkEligibility(User $user): bool
    {
        // Check if user already has this badge (fresh DB query to avoid stale collection)
        if ($user->badges()->where('badge_id', $this->id)->exists()) {
            return false;
        }

        // Skill-based: projects using a specific skill
        if ($this->required_skill_id) {
            $projectsWithSkill = $user->projects()
                ->whereHas('skills', fn($q) => $q->where('skills.id', $this->required_skill_id))
                ->count();
            return $projectsWithSkill >= $this->threshold;
        }

        return match($this->category) {
            'project'      => $user->projects()->count() >= $this->threshold,
            'level'        => $user->level >= $this->threshold,
            'web'          => $user->projects()->where('project_type', 'web')->count() >= $this->threshold,
            'backend'      => $user->projects()->where('project_type', 'backend')->count() >= $this->threshold,
            'fullstack'    => $user->projects()->where('project_type', 'fullstack')->count() >= $this->threshold,
            'mobile'       => $user->projects()->where('project_type', 'mobile')->count() >= $this->threshold,
            'devops'       => $user->projects()->where('project_type', 'devops')->count() >= $this->threshold,
            'ai'           => $user->projects()->where('project_type', 'ai')->count() >= $this->threshold,
            'streak'       => ($user->streak_days ?? 0) >= $this->threshold,
            'skill_node'   => $user->unlockedNodes()->count() >= $this->threshold,
            default        => false,
        };
    }
}
