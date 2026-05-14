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
        'task_requirements',
    ];

    protected $casts = [
        'task_requirements' => 'array',
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

        // Check task requirements
        $taskProgress = $this->checkTaskRequirements($user);
        foreach ($taskProgress as $task) {
            if (!$task['completed']) {
                return false;
            }
        }

        return true;
    }

    public function checkTaskRequirements(User $user): array
    {
        if (!$this->task_requirements) {
            return [];
        }

        $results = [];

        foreach ($this->task_requirements as $task) {
            $type = $task['type'];
            $required = $task['required'];
            $current = 0;

            switch ($type) {
                case 'project_count':
                    $current = $user->projects()->count();
                    break;

                // Match projects by their project_type field (e.g. 'web', 'backend', 'fullstack')
                case 'project_type':
                    $projectType = $task['project_type'];
                    $current = $user->projects()
                        ->where('project_type', $projectType)
                        ->count();
                    break;

                // Match projects by skill slug OR by skill category — whichever finds more
                case 'skill_projects':
                    $skillSlug = $task['skill_slug'];
                    $skill = Skill::where('slug', $skillSlug)->first();

                    if ($skill) {
                        // Direct skill attachment match
                        $bySkill = $user->projects()
                            ->whereHas('skills', fn($q) => $q->where('skills.id', $skill->id))
                            ->count();

                        // Also match by skill category (catches user-created tags in same category)
                        $byCategory = $user->projects()
                            ->whereHas('skills', fn($q) => $q->where('skills.category', $skill->category))
                            ->count();

                        // Also match by project_type mapped from skill category
                        $typeMap = [
                            'frontend' => 'web',
                            'backend'  => 'backend',
                            'devops'   => 'devops',
                            'mobile'   => 'mobile',
                            'ai'       => 'ai',
                        ];
                        $mappedType = $typeMap[$skill->category] ?? null;
                        $byType = $mappedType
                            ? $user->projects()->where('project_type', $mappedType)->count()
                            : 0;

                        $current = max($bySkill, $byCategory, $byType);
                    }
                    break;

                case 'badge_count':
                    $current = $user->badges()->count();
                    break;

                case 'level_requirement':
                    $current = $user->level;
                    break;

                case 'node_unlock':
                    $nodeId = $task['node_id'];
                    $current = $user->unlockedNodes()->where('skill_node_id', $nodeId)->exists() ? 1 : 0;
                    $required = 1;
                    break;
            }

            $results[] = [
                'type' => $type,
                'description' => $task['description'],
                'current' => $current,
                'required' => $required,
                'completed' => $current >= $required,
            ];
        }

        return $results;
    }

    public function calculateTaskProgress(User $user): array
    {
        return $this->checkTaskRequirements($user);
    }
}
