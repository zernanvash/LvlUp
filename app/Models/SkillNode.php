<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
