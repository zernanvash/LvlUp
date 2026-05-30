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
        return match ($this->rarity) {
            'common' => '#9ca3af',
            'rare' => '#3b82f6',
            'epic' => '#a855f7',
            'legendary' => '#f59e0b',
            default => '#6b7280',
        };
    }

    /**
     * Get all projects for this skill
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectsForSkill()
    {
        return $this->projects()->get();
    }

    /**
     * Calculate user proficiency with this skill
     * Based on number of projects and average proficiency level
     *
     * @return float Average proficiency (0 if no projects)
     */
    public function calculateUserProficiency(User $user): float
    {
        $coreSlug = $this->slug;
        $coreSlugs = ['web-dev', 'backend', 'database', 'devops', 'mobile', 'ai', 'fullstack'];

        if (! in_array($coreSlug, $coreSlugs)) {
            // For custom/non-core skills (like Vue, React), use the original simple calculation
            $userProjects = $this->projects()
                ->where('user_id', $user->id)
                ->get();

            if ($userProjects->isEmpty()) {
                return 0;
            }

            $totalProficiency = $userProjects->sum('pivot.proficiency');

            return $totalProficiency / $userProjects->count();
        }

        // For core skills, use the smart category & keyword matching
        $typeMap = [
            'web-dev' => 'web',
            'backend' => 'backend',
            'database' => 'backend',
            'devops' => 'devops',
            'mobile' => 'mobile',
            'ai' => 'ai',
            'fullstack' => 'fullstack',
        ];

        $subSkillKeywords = [
            'web-dev' => ['html', 'css', 'javascript', 'js', 'typescript', 'ts', 'react', 'vue', 'frontend', 'tailwind', 'sass', 'bootstrap'],
            'backend' => ['php', 'laravel', 'python', 'django', 'flask', 'nodejs', 'express', 'java', 'spring', 'go', 'golang', 'ruby', 'rails', 'api', 'c#', 'dotnet'],
            'database' => ['sql', 'mysql', 'postgresql', 'postgres', 'sqlite', 'mongodb', 'redis', 'nosql', 'caching', 'db', 'database', 'eloquent'],
            'devops' => ['docker', 'kubernetes', 'aws', 'gcp', 'azure', 'linux', 'bash', 'shell', 'ci/cd', 'nginx', 'git', 'github'],
            'mobile' => ['mobile', 'ios', 'android', 'swift', 'kotlin', 'flutter', 'react-native'],
            'ai' => ['ai', 'ml', 'machine-learning', 'tensorflow', 'pytorch', 'sklearn', 'pandas', 'numpy', 'nlp', 'llm'],
            'fullstack' => ['fullstack', 'nextjs', 'nuxt', 'inertia', 'livewire', 'svelte'],
        ];

        $targetType = $typeMap[$coreSlug] ?? null;
        $keywords = $subSkillKeywords[$coreSlug] ?? [];

        $query = Project::where('user_id', $user->id);

        $query->where(function ($q) use ($coreSlug, $targetType, $keywords) {
            // Direct skill attachment
            $q->whereHas('skills', function ($sq) use ($coreSlug) {
                $sq->where('slug', $coreSlug);
            });

            // Or by project type (except database which is backend, we handle database by keywords)
            if ($targetType && $coreSlug !== 'database') {
                $q->orWhere('project_type', $targetType);
            }

            // Or by sub-skill tag names / slug keywords
            if (! empty($keywords)) {
                $q->orWhereHas('skills', function ($sq) use ($keywords) {
                    $sq->whereIn('slug', $keywords)
                        ->orWhere(function ($subQ) use ($keywords) {
                            foreach ($keywords as $kw) {
                                $subQ->orWhere('slug', 'like', "%{$kw}%");
                            }
                        });
                });
            }
        });

        $userProjects = $query->with('skills')->get();

        if ($userProjects->isEmpty()) {
            return 0;
        }

        $totalProficiency = 0;
        $count = 0;

        foreach ($userProjects as $project) {
            $maxProficiency = 1;

            foreach ($project->skills as $ps) {
                if ($ps->slug === $coreSlug || in_array($ps->slug, $keywords)) {
                    $maxProficiency = max($maxProficiency, $ps->pivot->proficiency ?? 1);
                }
            }

            $totalProficiency += $maxProficiency;
            $count++;
        }

        return $count > 0 ? $totalProficiency / $count : 0;
    }
}
