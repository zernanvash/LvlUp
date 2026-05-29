<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
        // XP award only — badge checking is handled explicitly in the controller
        // to avoid double-firing when thumbnail/skills are updated in the same request.
        static::created(function ($project) {
            $project->user->addXP($project->xp_reward);
        });

        static::saved(function ($project) {
            Cache::forget("dashboard.projects.{$project->user_id}");
            Cache::forget("api.stats.{$project->user_id}");
        });

        static::deleted(function ($project) {
            $project->user->addXP(-$project->xp_reward);
            Cache::forget("dashboard.projects.{$project->user_id}");
            Cache::forget("api.stats.{$project->user_id}");
        });
    }

    /**
     * Check all eligible badges for the project's user and return newly earned ones.
     * Does NOT flash to session — caller is responsible for that.
     */
    public function checkBadgesAndReturn(): array
    {
        // Always fetch a fresh user instance so project counts are accurate
        $user = User::with('badges')->find($this->user_id);

        if (!$user) {
            return [];
        }

        $newlyEarned = [];

        // Check all non-skill-specific badges
        $allBadges = Badge::whereNull('required_skill_id')->get();

        foreach ($allBadges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            if ($badge->checkEligibility($user)) {
                $user->badges()->attach($badge->id, [
                    'earned_at'    => now(),
                    'is_displayed' => false,
                ]);
                $user->addXP($badge->xp_reward);
                $user->load('badges');
                $newlyEarned[] = [
                    'title'        => $badge->title,
                    'icon'         => $badge->icon,
                    'rarity'       => $badge->rarity,
                    'rarity_color' => $badge->rarity_color,
                    'xp_reward'    => $badge->xp_reward,
                ];
            }
        }

        // Also check skill-based badges (after skills have been attached)
        $this->load('skills');
        foreach ($this->skills as $skill) {
            $skillBadges = Badge::where('required_skill_id', $skill->id)->get();
            foreach ($skillBadges as $badge) {
                if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                    continue;
                }
                if ($badge->checkEligibility($user)) {
                    $user->badges()->attach($badge->id, [
                        'earned_at'    => now(),
                        'is_displayed' => false,
                    ]);
                    $user->addXP($badge->xp_reward);
                    $user->load('badges');
                    $newlyEarned[] = [
                        'title'        => $badge->title,
                        'icon'         => $badge->icon,
                        'rarity'       => $badge->rarity,
                        'rarity_color' => $badge->rarity_color,
                        'xp_reward'    => $badge->xp_reward,
                    ];
                }
            }
        }

        return $newlyEarned;
    }
}
