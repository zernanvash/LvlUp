<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SkillNode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalSampleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'name' => 'Maya Frontend',
                'email' => 'maya@lvlup.local',
                'title' => 'Frontend Developer',
                'bio' => 'Builds polished interfaces, design systems, and accessibility-minded product flows.',
                'level' => 8,
                'xp' => 260,
                'total_xp' => 1680,
                'rank' => 'Bronze',
                'technical_skills' => 'React, Tailwind CSS, Alpine.js, Accessibility, UI Design',
                'projects' => [
                    ['name' => 'Portfolio Command Center', 'language' => 'TypeScript', 'project_type' => 'web', 'is_featured' => true],
                    ['name' => 'Component Library Refresh', 'language' => 'JavaScript', 'project_type' => 'web', 'is_featured' => true],
                    ['name' => 'Mobile Onboarding Flow', 'language' => 'Vue', 'project_type' => 'mobile', 'is_featured' => false],
                ],
            ],
            [
                'name' => 'Noah Backend',
                'email' => 'noah@lvlup.local',
                'title' => 'Backend Developer',
                'bio' => 'Enjoys APIs, queues, cache layers, database tuning, and making slow pages feel lighter.',
                'level' => 14,
                'xp' => 420,
                'total_xp' => 3840,
                'rank' => 'Silver',
                'technical_skills' => 'Laravel, MySQL, Redis, Queues, API Design',
                'projects' => [
                    ['name' => 'Aiven Valkey Cache Layer', 'language' => 'PHP', 'project_type' => 'backend', 'is_featured' => true],
                    ['name' => 'Resume Analysis API', 'language' => 'PHP', 'project_type' => 'backend', 'is_featured' => true],
                    ['name' => 'Activity Stream Worker', 'language' => 'Go', 'project_type' => 'devops', 'is_featured' => false],
                    ['name' => 'Search Ranking Service', 'language' => 'Python', 'project_type' => 'backend', 'is_featured' => false],
                ],
            ],
            [
                'name' => 'Iris Fullstack',
                'email' => 'iris@lvlup.local',
                'title' => 'Full Stack Developer',
                'bio' => 'Ships complete features from schema and controllers to clean, usable screens.',
                'level' => 27,
                'xp' => 760,
                'total_xp' => 8900,
                'rank' => 'Gold',
                'technical_skills' => 'Laravel, React, SQLite, Product Design, Testing',
                'projects' => [
                    ['name' => 'Skill Tree Progression', 'language' => 'PHP', 'project_type' => 'fullstack', 'is_featured' => true],
                    ['name' => 'Achievement Showcase', 'language' => 'JavaScript', 'project_type' => 'fullstack', 'is_featured' => true],
                    ['name' => 'Public Profile Builder', 'language' => 'PHP', 'project_type' => 'fullstack', 'is_featured' => true],
                    ['name' => 'Local Dev Seed Pack', 'language' => 'PHP', 'project_type' => 'devops', 'is_featured' => false],
                    ['name' => 'Resume PDF Cache', 'language' => 'PHP', 'project_type' => 'backend', 'is_featured' => false],
                ],
            ],
            [
                'name' => 'Kai Private',
                'email' => 'kai@lvlup.local',
                'title' => 'Student Developer',
                'bio' => 'A private profile account for checking visibility and permission behavior.',
                'level' => 3,
                'xp' => 80,
                'total_xp' => 280,
                'rank' => 'Bronze',
                'is_public' => false,
                'technical_skills' => 'HTML, CSS, JavaScript',
                'projects' => [
                    ['name' => 'First Laravel CRUD', 'language' => 'PHP', 'project_type' => 'web', 'is_featured' => false],
                    ['name' => 'Responsive Login Practice', 'language' => 'HTML', 'project_type' => 'web', 'is_featured' => false],
                ],
            ],
        ];

        $skills = Skill::query()->limit(8)->get();
        $badges = Badge::query()->limit(6)->get();
        $nodes = SkillNode::query()->limit(6)->get();

        foreach ($samples as $index => $sample) {
            $user = User::updateOrCreate(
                ['email' => $sample['email']],
                [
                    'name' => $sample['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'title' => $sample['title'],
                    'bio' => $sample['bio'],
                    'level' => $sample['level'],
                    'xp' => $sample['xp'],
                    'total_xp' => $sample['total_xp'],
                    'rank' => $sample['rank'],
                    'is_public' => $sample['is_public'] ?? true,
                    'last_login' => now()->subDays($index),
                    'last_activity_date' => now()->subDays($index)->toDateString(),
                    'streak_days' => max(1, 7 - $index),
                    'technical_skills' => $sample['technical_skills'],
                    'linkedin_url' => 'https://linkedin.com/in/' . str($sample['name'])->slug(),
                    'github_url' => 'https://github.com/' . str($sample['name'])->slug(),
                    'website_url' => 'https://' . str($sample['name'])->slug() . '.local',
                    'visibility_settings' => [
                        'show_email' => false,
                        'show_badges' => true,
                        'show_linkedin' => true,
                        'show_github' => true,
                        'show_skills' => true,
                        'show_achievements' => true,
                        'show_projects' => true,
                        'show_rank' => true,
                        'show_technical_skills' => true,
                        'show_certifications' => true,
                    ],
                ],
            );

            Project::withoutEvents(function () use ($user, $sample, $skills) {
                foreach ($sample['projects'] as $projectData) {
                    $project = Project::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'name' => $projectData['name'],
                        ],
                        [
                            'description' => $projectData['name'] . ' is a sample local project for testing LvlUp screens and profile data.',
                            'url' => 'https://example.com/' . str($projectData['name'])->slug(),
                            'github_url' => 'https://github.com/lvlup-demo/' . str($projectData['name'])->slug(),
                            'language' => $projectData['language'],
                            'project_type' => $projectData['project_type'],
                            'thumbnail' => null,
                            'xp_reward' => 120,
                            'is_featured' => $projectData['is_featured'],
                            'metadata' => ['sample' => true],
                        ],
                    );

                    if ($skills->isNotEmpty()) {
                        $sync = $skills->random(min(3, $skills->count()))
                            ->mapWithKeys(fn (Skill $skill) => [$skill->id => ['proficiency' => rand(1, 5)]])
                            ->all();
                        $project->skills()->sync($sync);
                    }
                }
            });

            if ($badges->isNotEmpty()) {
                $badgeSync = $badges->take(min(3 + $index, $badges->count()))
                    ->mapWithKeys(fn (Badge $badge, int $badgeIndex) => [
                        $badge->id => [
                            'earned_at' => now()->subDays($badgeIndex + $index),
                            'is_displayed' => $badgeIndex < 3,
                        ],
                    ])
                    ->all();
                $user->badges()->syncWithoutDetaching($badgeSync);
            }

            if ($nodes->isNotEmpty()) {
                $nodeSync = $nodes->take(min(2 + $index, $nodes->count()))
                    ->mapWithKeys(fn (SkillNode $node, int $nodeIndex) => [
                        $node->id => ['unlocked_at' => now()->subDays($nodeIndex + 1)],
                    ])
                    ->all();
                $user->unlockedNodes()->syncWithoutDetaching($nodeSync);
            }
        }
    }
}
