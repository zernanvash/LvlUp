<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillNode;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSkills();
        $this->seedSkillTree();
        $this->seedBadges();
    }
    
    private function seedSkills()
    {
        $skills = [
            // Frontend
            ['name' => 'HTML', 'slug' => 'html', 'icon' => 'fab fa-html5', 'color' => '#e34c26', 'category' => 'frontend', 'rarity' => 'common'],
            ['name' => 'CSS', 'slug' => 'css', 'icon' => 'fab fa-css3-alt', 'color' => '#264de4', 'category' => 'frontend', 'rarity' => 'common'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'icon' => 'fab fa-js', 'color' => '#f7df1e', 'category' => 'frontend', 'rarity' => 'rare'],
            ['name' => 'React', 'slug' => 'react', 'icon' => 'fab fa-react', 'color' => '#61dafb', 'category' => 'frontend', 'rarity' => 'epic'],
            ['name' => 'Vue', 'slug' => 'vue', 'icon' => 'fab fa-vuejs', 'color' => '#42b883', 'category' => 'frontend', 'rarity' => 'epic'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind', 'icon' => 'fas fa-wind', 'color' => '#06b6d4', 'category' => 'frontend', 'rarity' => 'rare'],
            
            // Backend
            ['name' => 'PHP', 'slug' => 'php', 'icon' => 'fab fa-php', 'color' => '#777bb4', 'category' => 'backend', 'rarity' => 'common'],
            ['name' => 'Laravel', 'slug' => 'laravel', 'icon' => 'fab fa-laravel', 'color' => '#ff2d20', 'category' => 'backend', 'rarity' => 'epic'],
            ['name' => 'Node.js', 'slug' => 'nodejs', 'icon' => 'fab fa-node-js', 'color' => '#339933', 'category' => 'backend', 'rarity' => 'rare'],
            ['name' => 'Python', 'slug' => 'python', 'icon' => 'fab fa-python', 'color' => '#3776ab', 'category' => 'backend', 'rarity' => 'rare'],
            ['name' => 'Django', 'slug' => 'django', 'icon' => 'fas fa-server', 'color' => '#092e20', 'category' => 'backend', 'rarity' => 'epic'],
            
            // Database
            ['name' => 'MySQL', 'slug' => 'mysql', 'icon' => 'fas fa-database', 'color' => '#4479a1', 'category' => 'backend', 'rarity' => 'common'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'icon' => 'fas fa-database', 'color' => '#336791', 'category' => 'backend', 'rarity' => 'rare'],
            ['name' => 'MongoDB', 'slug' => 'mongodb', 'icon' => 'fas fa-leaf', 'color' => '#47a248', 'category' => 'backend', 'rarity' => 'rare'],
            
            // Mobile
            ['name' => 'React Native', 'slug' => 'react-native', 'icon' => 'fab fa-react', 'color' => '#61dafb', 'category' => 'mobile', 'rarity' => 'epic'],
            ['name' => 'Flutter', 'slug' => 'flutter', 'icon' => 'fas fa-mobile-alt', 'color' => '#02569b', 'category' => 'mobile', 'rarity' => 'epic'],
            
            // DevOps
            ['name' => 'Git', 'slug' => 'git', 'icon' => 'fab fa-git-alt', 'color' => '#f05032', 'category' => 'devops', 'rarity' => 'common'],
            ['name' => 'Docker', 'slug' => 'docker', 'icon' => 'fab fa-docker', 'color' => '#2496ed', 'category' => 'devops', 'rarity' => 'rare'],
            ['name' => 'Linux', 'slug' => 'linux', 'icon' => 'fab fa-linux', 'color' => '#fcc624', 'category' => 'devops', 'rarity' => 'rare'],
            ['name' => 'AWS', 'slug' => 'aws', 'icon' => 'fab fa-aws', 'color' => '#ff9900', 'category' => 'devops', 'rarity' => 'epic'],
            
            // Design
            ['name' => 'UI/UX Design', 'slug' => 'ui-ux', 'icon' => 'fas fa-paint-brush', 'color' => '#ff61f6', 'category' => 'design', 'rarity' => 'rare'],
            ['name' => 'Figma', 'slug' => 'figma', 'icon' => 'fab fa-figma', 'color' => '#f24e1e', 'category' => 'design', 'rarity' => 'rare'],
            
            // Security
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity', 'icon' => 'fas fa-shield-alt', 'color' => '#00d9ff', 'category' => 'security', 'rarity' => 'legendary'],
            
            // AI/ML
            ['name' => 'Machine Learning', 'slug' => 'ml', 'icon' => 'fas fa-brain', 'color' => '#ff6f00', 'category' => 'ai', 'rarity' => 'legendary'],
            ['name' => 'TensorFlow', 'slug' => 'tensorflow', 'icon' => 'fas fa-robot', 'color' => '#ff6f00', 'category' => 'ai', 'rarity' => 'legendary'],
        ];
        
        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
    
    private function seedSkillTree()
    {
        // Root node
        $root = SkillNode::create([
            'skill_id' => Skill::where('slug', 'html')->first()->id,
            'title' => 'Foundations',
            'description' => 'The absolute core of all tech disciplines. Learn logical thinking, problem solving, and how computers work.',
            'x_position' => 50,
            'y_position' => 80,
            'tier' => 'core',
            'required_level' => 1,
            'skill_point_cost' => 0, // Free
        ]);
        
        // Web Development Branch
        $html = SkillNode::create([
            'skill_id' => Skill::where('slug', 'html')->first()->id,
            'parent_node_id' => $root->id,
            'title' => 'HTML Mastery',
            'description' => 'Learn semantic HTML, accessibility, and SEO-friendly markup.',
            'x_position' => 25,
            'y_position' => 220,
            'tier' => 'basic',
            'required_level' => 1,
            'skill_point_cost' => 1,
        ]);
        
        $css = SkillNode::create([
            'skill_id' => Skill::where('slug', 'css')->first()->id,
            'parent_node_id' => $html->id,
            'title' => 'CSS Styling',
            'description' => 'Master Flexbox, Grid, animations, and responsive design.',
            'x_position' => 15,
            'y_position' => 360,
            'tier' => 'basic',
            'required_level' => 2,
            'skill_point_cost' => 1,
        ]);
        
        $js = SkillNode::create([
            'skill_id' => Skill::where('slug', 'javascript')->first()->id,
            'parent_node_id' => $html->id,
            'title' => 'JavaScript Power',
            'description' => 'DOM manipulation, events, async programming, and modern ES6+.',
            'x_position' => 35,
            'y_position' => 360,
            'tier' => 'advanced',
            'required_level' => 3,
            'skill_point_cost' => 2,
        ]);
        
        $react = SkillNode::create([
            'skill_id' => Skill::where('slug', 'react')->first()->id,
            'parent_node_id' => $js->id,
            'title' => 'React Framework',
            'description' => 'Build modern SPAs with hooks, context, and component architecture.',
            'x_position' => 25,
            'y_position' => 500,
            'tier' => 'master',
            'required_level' => 5,
            'skill_point_cost' => 3,
        ]);
        
        // Backend Branch
        $php = SkillNode::create([
            'skill_id' => Skill::where('slug', 'php')->first()->id,
            'parent_node_id' => $root->id,
            'title' => 'PHP Development',
            'description' => 'Server-side programming, OOP, and web applications.',
            'x_position' => 55,
            'y_position' => 220,
            'tier' => 'basic',
            'required_level' => 1,
            'skill_point_cost' => 1,
        ]);
        
        $laravel = SkillNode::create([
            'skill_id' => Skill::where('slug', 'laravel')->first()->id,
            'parent_node_id' => $php->id,
            'title' => 'Laravel Framework',
            'description' => 'MVC architecture, Eloquent ORM, authentication, and APIs.',
            'x_position' => 55,
            'y_position' => 360,
            'tier' => 'master',
            'required_level' => 4,
            'skill_point_cost' => 3,
        ]);
        
        // Database
        $mysql = SkillNode::create([
            'skill_id' => Skill::where('slug', 'mysql')->first()->id,
            'parent_node_id' => $php->id,
            'title' => 'Database Design',
            'description' => 'SQL queries, normalization, indexing, and relationships.',
            'x_position' => 65,
            'y_position' => 360,
            'tier' => 'advanced',
            'required_level' => 3,
            'skill_point_cost' => 2,
        ]);
        
        // DevOps Branch
        $git = SkillNode::create([
            'skill_id' => Skill::where('slug', 'git')->first()->id,
            'parent_node_id' => $root->id,
            'title' => 'Version Control',
            'description' => 'Git workflows, branching, merging, and collaboration.',
            'x_position' => 75,
            'y_position' => 220,
            'tier' => 'basic',
            'required_level' => 1,
            'skill_point_cost' => 1,
        ]);
        
        $docker = SkillNode::create([
            'skill_id' => Skill::where('slug', 'docker')->first()->id,
            'parent_node_id' => $git->id,
            'title' => 'Containerization',
            'description' => 'Docker containers, images, and orchestration.',
            'x_position' => 75,
            'y_position' => 360,
            'tier' => 'advanced',
            'required_level' => 6,
            'skill_point_cost' => 2,
        ]);
        
        // AI/ML Branch
        $python = SkillNode::create([
            'skill_id' => Skill::where('slug', 'python')->first()->id,
            'parent_node_id' => $root->id,
            'title' => 'Python Programming',
            'description' => 'Versatile language for web, data science, and automation.',
            'x_position' => 85,
            'y_position' => 220,
            'tier' => 'basic',
            'required_level' => 2,
            'skill_point_cost' => 1,
        ]);
        
        $ml = SkillNode::create([
            'skill_id' => Skill::where('slug', 'ml')->first()->id,
            'parent_node_id' => $python->id,
            'title' => 'Machine Learning',
            'description' => 'AI algorithms, neural networks, and data modeling.',
            'x_position' => 85,
            'y_position' => 360,
            'tier' => 'legendary',
            'required_level' => 10,
            'skill_point_cost' => 5,
        ]);
    }
    
    private function seedBadges()
    {
        $badges = [
            // Project milestones
            [
                'title' => 'First Steps',
                'slug' => 'first-project',
                'description' => 'Create your first project',
                'icon' => '🚀',
                'rarity' => 'common',
                'category' => 'project',
                'threshold' => 1,
                'xp_reward' => 50,
                'gacha_currency_reward' => 10,
            ],
            [
                'title' => 'Portfolio Builder',
                'slug' => 'five-projects',
                'description' => 'Create 5 projects',
                'icon' => '📁',
                'rarity' => 'rare',
                'category' => 'project',
                'threshold' => 5,
                'xp_reward' => 200,
                'gacha_currency_reward' => 50,
            ],
            [
                'title' => 'Project Master',
                'slug' => 'ten-projects',
                'description' => 'Create 10 projects',
                'icon' => '💼',
                'rarity' => 'epic',
                'category' => 'project',
                'threshold' => 10,
                'xp_reward' => 500,
                'gacha_currency_reward' => 100,
            ],
            
            // Skill badges
            [
                'title' => 'JavaScript Ninja',
                'slug' => 'js-master',
                'description' => 'Complete 3 JavaScript projects',
                'icon' => '🥷',
                'rarity' => 'epic',
                'category' => 'skill',
                'required_skill_id' => Skill::where('slug', 'javascript')->first()->id,
                'threshold' => 3,
                'xp_reward' => 300,
                'gacha_currency_reward' => 75,
            ],
            [
                'title' => 'Laravel Architect',
                'slug' => 'laravel-expert',
                'description' => 'Complete 5 Laravel projects',
                'icon' => '🏗️',
                'rarity' => 'legendary',
                'category' => 'skill',
                'required_skill_id' => Skill::where('slug', 'laravel')->first()->id,
                'threshold' => 5,
                'xp_reward' => 1000,
                'gacha_currency_reward' => 200,
            ],
            
            // Streak badges
            [
                'title' => 'Committed',
                'slug' => 'week-streak',
                'description' => 'Login for 7 consecutive days',
                'icon' => '🔥',
                'rarity' => 'rare',
                'category' => 'streak',
                'threshold' => 7,
                'xp_reward' => 100,
                'gacha_currency_reward' => 30,
            ],
            [
                'title' => 'Unstoppable',
                'slug' => 'month-streak',
                'description' => 'Login for 30 consecutive days',
                'icon' => '⚡',
                'rarity' => 'legendary',
                'category' => 'streak',
                'threshold' => 30,
                'xp_reward' => 1000,
                'gacha_currency_reward' => 300,
            ],
            
            // Level badges
            [
                'title' => 'Beginner',
                'slug' => 'level-5',
                'description' => 'Reach level 5',
                'icon' => '🌱',
                'rarity' => 'common',
                'category' => 'level',
                'threshold' => 5,
                'xp_reward' => 100,
                'gacha_currency_reward' => 20,
            ],
            [
                'title' => 'Expert',
                'slug' => 'level-25',
                'description' => 'Reach level 25',
                'icon' => '⭐',
                'rarity' => 'epic',
                'category' => 'level',
                'threshold' => 25,
                'xp_reward' => 500,
                'gacha_currency_reward' => 100,
            ],
            [
                'title' => 'Legend',
                'slug' => 'level-50',
                'description' => 'Reach level 50',
                'icon' => '👑',
                'rarity' => 'mythic',
                'category' => 'level',
                'threshold' => 50,
                'xp_reward' => 2000,
                'gacha_currency_reward' => 500,
            ],
        ];
        
        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
