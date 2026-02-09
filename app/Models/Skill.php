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
