<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'url',
        'github_url',
        'language',
        'project_type',
        'thumbnail',
        'xp_reward',
        'is_featured',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skill')
                    ->withPivot('proficiency')
                    ->withTimestamps();
    }

    // Methods
    public function attachSkillsFromTags(array $tags): void
    {
        foreach ($tags as $tagName) {
            $skill = Skill::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tagName)],
                [
                    'name' => $tagName,
                    'category' => 'backend', // Default category for auto-created skills
                    'icon' => 'fa-code',
                    'color' => '#6366f1',
                    'rarity' => 'common',
                ]
            );
            
            if (!$this->skills->contains($skill->id)) {
                $this->skills()->attach($skill->id, ['proficiency' => 1]);
            }
        }
    }

    public function analyzeCodeAndSuggestSkills(string $code): array
    {
        $suggestions = [];
        
        $patterns = [
            'React' => ['/import.*react/i', '/<.*Component/i'],
            'Vue' => ['/import.*vue/i', '/new Vue/i'],
            'Laravel' => ['/Eloquent/', '/Route::/'],
            'Database' => ['/SELECT.*FROM/i', '/INSERT INTO/i', '/eloquent/i'],
            'Frontend' => ['/addEventListener/i', '/querySelector/i'],
            'CSS' => ['/tailwind/i', '/flexbox/i', '/grid/i'],
            'API' => ['/fetch\(/i', '/axios/i', '/api\//i'],
            'Python' => ['/import.*numpy/i', '/import.*pandas/i', '/def.*:/'],
            'Machine Learning' => ['/tensorflow/i', '/pytorch/i', '/sklearn/i'],
        ];

        foreach ($patterns as $skill => $regexes) {
            foreach ($regexes as $regex) {
                if (preg_match($regex, $code)) {
                    $suggestions[] = $skill;
                    break;
                }
            }
        }

        return array_unique($suggestions);
    }

    protected static function booted()
    {
        static::created(function ($project) {
            // Award XP to user when project is created
            $project->user->addXP($project->xp_reward);
            
            // Check for badge achievements
            $project->checkBadges();
        });
    }

    private function checkBadges(): void
    {
        $user = $this->user;
        
        // Check skill-based badges
        foreach ($this->skills as $skill) {
            $projectsWithSkill = $user->projects()
                ->whereHas('skills', fn($q) => $q->where('skills.id', $skill->id))
                ->count();
                
            $badges = Badge::where('required_skill_id', $skill->id)
                ->where('threshold', '<=', $projectsWithSkill)
                ->get();
                
            foreach ($badges as $badge) {
                if (!$user->badges->contains($badge->id)) {
                    $user->badges()->attach($badge->id, [
                        'earned_at' => now(),
                        'is_displayed' => false
                    ]);
                    
                    // Award badge XP reward
                    $user->addXP($badge->xp_reward);
                }
            }
        }
        
        // Check total project count badges
        $totalProjects = $user->projects()->count();
        $projectBadges = Badge::whereNull('required_skill_id')
            ->where('category', 'project')
            ->where('threshold', '<=', $totalProjects)
            ->get();
            
        foreach ($projectBadges as $badge) {
            if (!$user->badges->contains($badge->id)) {
                $user->badges()->attach($badge->id, [
                    'earned_at' => now(),
                    'is_displayed' => false
                ]);
                
                // Award badge XP reward
                $user->addXP($badge->xp_reward);
            }
        }
    }
}
