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
                'tier' => 1,
                'required_level' => 1,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 1, 'description' => 'Create your first project']
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
                'tier' => 2,
                'required_level' => 2,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 2, 'description' => 'Complete 2 projects'],
                    ['type' => 'level_requirement', 'required' => 2, 'description' => 'Reach level 2']
                ],
            ],
            [
                'title' => 'CSS Sorcerer',
                'description' => 'Bend the web to your will with styling magic. Make it pretty, make it pop!',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 1,
                'x_position' => 65,
                'y_position' => 25,
                'tier' => 2,
                'required_level' => 2,
                'task_requirements' => [
                    ['type' => 'skill_projects', 'skill_slug' => 'web-dev', 'required' => 2, 'description' => 'Complete 2 web development projects']
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
                'tier' => 3,
                'required_level' => 3,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 3, 'description' => 'Complete 3 projects'],
                    ['type' => 'badge_count', 'required' => 1, 'description' => 'Earn your first badge']
                ],
            ],
            [
                'title' => 'Component Architect',
                'description' => 'Build reusable components like LEGO blocks. Everything is awesome!',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 3, // CSS Sorcerer
                'x_position' => 75,
                'y_position' => 40,
                'tier' => 3,
                'required_level' => 3,
                'task_requirements' => [
                    ['type' => 'skill_projects', 'skill_slug' => 'web-dev', 'required' => 3, 'description' => 'Complete 3 web projects']
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
                'tier' => 4,
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 4, 'description' => 'Complete 4 projects'],
                    ['type' => 'level_requirement', 'required' => 5, 'description' => 'Reach level 5']
                ],
            ],
            [
                'title' => 'Async Assassin',
                'description' => 'Strike with promises and callbacks. Never block the main thread!',
                'skill_id' => Skill::where('slug', 'backend')->first()->id,
                'parent_node_id' => 4,
                'x_position' => 35,
                'y_position' => 55,
                'tier' => 4,
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'skill_projects', 'skill_slug' => 'backend', 'required' => 2, 'description' => 'Complete 2 backend projects']
                ],
            ],
            [
                'title' => 'State Manager',
                'description' => 'Keep your app\'s state cleaner than your room. Redux, Vuex, or just vibes.',
                'skill_id' => Skill::where('slug', 'web-dev')->first()->id,
                'parent_node_id' => 5, // Component Architect
                'x_position' => 65,
                'y_position' => 55,
                'tier' => 4,
                'required_level' => 5,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 5, 'description' => 'Complete 5 projects']
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
                'tier' => 5,
                'required_level' => 8,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 6, 'description' => 'Complete 6 projects'],
                    ['type' => 'badge_count', 'required' => 3, 'description' => 'Earn 3 badges']
                ],
            ],
            [
                'title' => 'Performance Optimizer',
                'description' => 'Make it fast, make it furious. Shave those milliseconds like a pro barber.',
                'skill_id' => Skill::where('slug', 'backend')->first()->id,
                'parent_node_id' => 7, // Async Assassin
                'x_position' => 35,
                'y_position' => 70,
                'tier' => 5,
                'required_level' => 8,
                'task_requirements' => [
                    ['type' => 'level_requirement', 'required' => 8, 'description' => 'Reach level 8'],
                    ['type' => 'skill_projects', 'skill_slug' => 'backend', 'required' => 3, 'description' => 'Complete 3 backend projects']
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
                'tier' => 6,
                'required_level' => 10,
                'task_requirements' => [
                    ['type' => 'project_count', 'required' => 10, 'description' => 'Complete 10 projects'],
                    ['type' => 'badge_count', 'required' => 5, 'description' => 'Earn 5 badges'],
                    ['type' => 'level_requirement', 'required' => 10, 'description' => 'Reach level 10']
                ],
            ],
        ];

        foreach ($nodes as $nodeData) {
            SkillNode::create($nodeData);
        }
    }
}
