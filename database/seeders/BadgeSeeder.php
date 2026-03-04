<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Common badges - Early achievements
            [
                'title' => 'First Steps',
                'slug' => 'first-steps',
                'description' => 'Created your first project. Welcome to the journey!',
                'icon' => 'fas fa-baby',
                'rarity' => 'common',
                'category' => 'project',
                'threshold' => 1,
                'xp_reward' => 50,
            ],
            [
                'title' => 'Getting Started',
                'slug' => 'getting-started',
                'description' => 'Reached level 2. You\'re on your way!',
                'icon' => 'fas fa-seedling',
                'rarity' => 'common',
                'category' => 'level',
                'threshold' => 2,
                'xp_reward' => 100,
            ],

            // Rare badges
            [
                'title' => 'Triple Threat',
                'slug' => 'triple-threat',
                'description' => 'Completed 3 projects. The grind is real!',
                'icon' => 'fas fa-fire',
                'rarity' => 'rare',
                'category' => 'project',
                'threshold' => 3,
                'xp_reward' => 150,
            ],
            [
                'title' => 'Rising Star',
                'slug' => 'rising-star',
                'description' => 'Reached level 5. You\'re shining bright!',
                'icon' => 'fas fa-star',
                'rarity' => 'rare',
                'category' => 'level',
                'threshold' => 5,
                'xp_reward' => 200,
            ],

            // Rare badges
            [
                'title' => 'Code Warrior',
                'slug' => 'code-warrior',
                'description' => 'Completed 5 projects. Your keyboard is your weapon!',
                'icon' => 'fas fa-sword',
                'rarity' => 'rare',
                'category' => 'project',
                'threshold' => 5,
                'xp_reward' => 300,
            ],
            [
                'title' => 'Web Slinger',
                'slug' => 'web-slinger',
                'description' => 'Completed 3 web development projects. With great power...',
                'icon' => 'fas fa-spider',
                'rarity' => 'rare',
                'category' => 'skill',
                'required_skill_id' => Skill::where('slug', 'web-dev')->first()?->id,
                'threshold' => 3,
                'xp_reward' => 350,
            ],

            // Epic badges
            [
                'title' => 'Backend Boss',
                'slug' => 'backend-boss',
                'description' => 'Mastered 3 backend projects. The server bows to you!',
                'icon' => 'fas fa-crown',
                'rarity' => 'epic',
                'category' => 'skill',
                'required_skill_id' => Skill::where('slug', 'backend')->first()?->id,
                'threshold' => 3,
                'xp_reward' => 500,
            ],
            [
                'title' => 'Level 10 Legend',
                'slug' => 'level-10-legend',
                'description' => 'Reached level 10. You\'re in the big leagues now!',
                'icon' => 'fas fa-trophy',
                'rarity' => 'epic',
                'category' => 'level',
                'threshold' => 10,
                'xp_reward' => 750,
            ],

            // Legendary badges
            [
                'title' => 'Project Overlord',
                'slug' => 'project-overlord',
                'description' => 'Completed 10 projects. Your portfolio is legendary!',
                'icon' => 'fas fa-dragon',
                'rarity' => 'legendary',
                'category' => 'project',
                'threshold' => 10,
                'xp_reward' => 1000,
            ],

            // Mythic badge - Ultimate achievement
            [
                'title' => 'Coding Deity',
                'slug' => 'coding-deity',
                'description' => 'Reached level 20. Mortals tremble before your code!',
                'icon' => 'fas fa-bolt',
                'rarity' => 'mythic',
                'category' => 'level',
                'threshold' => 20,
                'xp_reward' => 2000,
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::firstOrCreate(['slug' => $badgeData['slug']], $badgeData);
        }
    }
}
