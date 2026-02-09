<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'category',
        'rarity',
        'required_level',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skill')
                    ->withPivot('proficiency')
                    ->withTimestamps();
    }

    public function nodes()
    {
        return $this->hasMany(SkillNode::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class, 'required_skill_id');
    }

    public function getRarityColorAttribute(): string
    {
        return match($this->rarity) {
            'common' => '#9ca3af',
            'rare' => '#3b82f6',
            'epic' => '#a855f7',
            'legendary' => '#f59e0b',
            default => '#6b7280',
        };
    }
}

class SkillNode extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'parent_node_id',
        'title',
        'description',
        'x_position',
        'y_position',
        'tier',
        'required_level',
        'skill_point_cost',
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function parent()
    {
        return $this->belongsTo(SkillNode::class, 'parent_node_id');
    }

    public function children()
    {
        return $this->hasMany(SkillNode::class, 'parent_node_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skill_nodes')
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }

    public function isUnlockedBy(User $user): bool
    {
        return $user->unlockedNodes->contains($this->id);
    }

    public function canBeUnlockedBy(User $user): bool
    {
        // Check level requirement
        if ($user->level < $this->required_level) {
            return false;
        }

        // Check skill points
        if ($user->skill_points < $this->skill_point_cost) {
            return false;
        }

        // Check if parent is unlocked (if exists)
        if ($this->parent_node_id) {
            if (!$this->parent->isUnlockedBy($user)) {
                return false;
            }
        }

        // Check if already unlocked
        if ($this->isUnlockedBy($user)) {
            return false;
        }

        return true;
    }
}

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
        'gacha_currency_reward',
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
            'rare' => '#3b82f6',
            'epic' => '#a855f7',
            'legendary' => '#f59e0b',
            'mythic' => '#ec4899',
            default => '#6b7280',
        };
    }
}

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'target_keywords',
        'job_description',
        'selected_project_ids',
        'selected_skill_ids',
        'pdf_path',
        'match_score',
    ];

    protected $casts = [
        'selected_project_ids' => 'array',
        'selected_skill_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSelectedProjects()
    {
        if (!$this->selected_project_ids) {
            return collect();
        }
        
        return Project::whereIn('id', $this->selected_project_ids)->get();
    }

    public function getSelectedSkills()
    {
        if (!$this->selected_skill_ids) {
            return collect();
        }
        
        return Skill::whereIn('id', $this->selected_skill_ids)->get();
    }
}

class DailyReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'claimed_date',
        'day_number',
        'xp_earned',
        'gacha_currency_earned',
    ];

    protected $casts = [
        'claimed_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
