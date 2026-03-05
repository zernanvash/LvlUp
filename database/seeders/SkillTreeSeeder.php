<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillNode;
use Illuminate\Database\Seeder;

class SkillTreeSeeder extends Seeder
{
    public function run(): void
    {
        // Create base skills first
        $skills = [
            ['name' => 'Web Development', 'slug' => 'web-dev', 'icon' => 'fas fa-globe', 'color' => '#3b82f6', 'category' => 'frontend', 'rarity' => 'common'],
            ['name' => 'Backend Magic', 'slug' => 'backend', 'icon' => 'fas fa-server', 'color' => '#8b5cf6', 'category' => 'backend', 'rarity' => 'rare'],
            ['name' => 'Database Wizardry', 'slug' => 'database', 'icon' => 'fas fa-database', 'color' => '#10b981', 'category' => 'backend', 'rarity' => 'epic'],
            ['name' => 'DevOps Ninja', 'slug' => 'devops', 'icon' => 'fas fa-rocket', 'color' => '#f59e0b', 'category' => 'devops', 'rarity' => 'legendary'],
        ];

        foreach ($skills as $skillData) {
            Skill::firstOrCreate(['slug' => $skillData['slug']], $skillData);
        }

        // Create skill tree nodes with catchy names and proper positioning
        $nodes = [
            // Tier 1 - Starting nodes (no parent)
            [
                'title' => 'Hello World',
                'description' => 'Every legend starts somewhere. Create your first project and join the ranks of developers.',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => null,
                'x_position' => 50,
                'y_position' => 10,
                'tier' => 'core',
                'required_level' => 1,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 1, 'description' => 'Upload 1 project to your portfolio']
                ],
            ],

            // Tier 2 - Branch out
            [
                'title' => 'Git Gud',
                'description' => 'Master version control like a pro. Time to commit to greatness!',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 1, // Hello World
                'x_position' => 35,
                'y_position' => 25,
                'tier' => 'basic',
                'required_level' => 2,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 2, 'description' => 'Upload at least 2 projects'],
                    ['type' => 'level_requirement', 'required' => 2, 'description' => 'Reach Level 2 (gain XP by completing projects)']
                ],
            ],
            [
                'title' => 'CSS Sorcerer',
                'description' => 'Bend the web to your will with styling magic. Make it pretty, make it pop!',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 1,
                'x_position' => 65,
                'y_position' => 25,
                'tier' => 'basic',
                'required_level' => 2,
                'task_requirements' => [
                    ['type' => 'skill_projects', 'skill_slug' => 'web-dev', 'required' => 2, 'description' => 'Upload 2 projects tagged with Web Development skills (HTML, CSS, JavaScript, etc.)']
                ],
            ],

            // Tier 3 - Specialization begins
            [
                'title' => 'API Whisperer',
                'description' => 'Talk to servers like they\'re your best friends. REST in peace, SOAP.',
                'skill_id' => Skill::where('slug', 'backend')->first()->id,
                'parent_node_id' => 2, // Git Gud
                'x_position' => 25,
                'y_position' => 40,
                'tier' => 'basic',
                'required_level' => 3,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 3, 'description' => 'Upload a total of 3 projects'],
                    ['type' => 'badge_count', 'required' => 1, 'description' => 'Earn at least 1 achievement badge']
                ],
            ],
            [
                'title' => 'Component Architect',
                'description' => 'Build reusable components like LEGO blocks. Everything is awesome!',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 3, // CSS Sorcerer
                'x_position' => 75,
                'y_position' => 40,
                'tier' => 'basic',
                'required_level' => 3,
                'task_requirements' => [
                    ['type' => 'skill_projects', 'skill_slug' => 'web-dev', 'required' => 3, 'description' => 'Upload 3 projects using Web Development skills (React, Vue, or similar frameworks)']
                ],
            ],

            // Tier 4 - Advanced paths
            [
                'title' => 'Database Bender',
                'description' => 'Manipulate data like the Avatar. Master all four CRUD operations.',
                'skill_id' => Skill::where('slug', 'database')->first()->id,
                'parent_node_id' => 4, // API Whisperer
                'x_position' => 15,
                'y_position' => 55,
                'tier' => 'advanced',
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 4, 'description' => 'Upload a total of 4 projects to your portfolio'],
                    ['type' => 'level_requirement', 'required' => 5, 'description' => 'Reach Level 5 by earning XP from projects and achievements']
                ],
            ],
            [
                'title' => 'Async Assassin',
                'description' => 'Strike with promises and callbacks. Never block the main thread!',
                'skill_id' => Skill::where('slug', 'backend')->first()->id,
                'parent_node_id' => 4,
                'x_position' => 35,
                'y_position' => 55,
                'tier' => 'advanced',
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'skill_projects', 'skill_slug' => 'backend', 'required' => 2, 'description' => 'Upload 2 projects tagged with Backend skills (PHP, Laravel, Node.js, Python, etc.)']
                ],
            ],
            [
                'title' => 'State Manager',
                'description' => 'Keep your app\'s state cleaner than your room. Redux, Vuex, or just vibes.',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 5, // Component Architect
                'x_position' => 65,
                'y_position' => 55,
                'tier' => 'advanced',
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 5, 'description' => 'Upload a total of 5 projects showcasing your skills']
                ],
            ],

            // Tier 5 - Expert level
            [
                'title' => 'Docker Whale Rider',
                'description' => 'Containerize everything! If it runs on your machine, it runs everywhere.',
                'skill_id' => Skill::where('slug', 'devops')->first()->id,
                'parent_node_id' => 6, // Database Bender
                'x_position' => 15,
                'y_position' => 70,
                'tier' => 'master',
                'required_level' => 8,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 6, 'description' => 'Upload a total of 6 projects'],
                    ['type' => 'badge_count', 'required' => 3, 'description' => 'Earn at least 3 achievement badges by completing milestones']
                ],
            ],
            [
                'title' => 'Performance Optimizer',
                'description' => 'Make it fast, make it furious. Shave those milliseconds like a pro barber.',
                'skill_id' => Skill::where('slug', 'backend')->first()->id,
                'parent_node_id' => 7, // Async Assassin
                'x_position' => 35,
                'y_position' => 70,
                'tier' => 'master',
                'required_level' => 8,
                'task_requirements' => [
                    ['type' => 'level_requirement', 'required' => 8, 'description' => 'Reach Level 8 by earning XP'],
                    ['type' => 'skill_projects', 'skill_slug' => 'backend', 'required' => 3, 'description' => 'Upload 3 projects using Backend technologies']
                ],
            ],

            // Tier 6 - Master level
            [
                'title' => 'Full Stack Legend',
                'description' => 'You\'ve mastered both frontend and backend. You are the chosen one.',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 10, // Performance Optimizer
                'x_position' => 50,
                'y_position' => 85,
                'tier' => 'legendary',
                'required_level' => 10,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 10, 'description' => 'Upload a total of 10 projects to your portfolio'],
                    ['type' => 'badge_count', 'required' => 5, 'description' => 'Earn at least 5 achievement badges'],
                    ['type' => 'level_requirement', 'required' => 10, 'description' => 'Reach Level 10 - the legendary milestone']
                ],
            ],
        ];

        foreach ($nodes as $nodeData) {
            SkillNode::create($nodeData);
        }
    }
}
