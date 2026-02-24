<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Skill;
use App\Models\SkillNode;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTestUser();
        $this->seedSkills();
        $this->seedSkillTree();
        $this->seedBadges();
    }
    
    private function seedTestUser()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@lvlup.dev',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'title' => 'Test Developer',
            'level' => 5,
            'xp' => 150,
            'total_xp' => 650,
            'rank' => 'Bronze',
            'is_public' => true,
            'last_login' => now()->subDay(),
            'bio' => 'A test user account for exploring the LvlUp gamification system.',
        ]);
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
        // Root node - unlocked by uploading first project
        $root = SkillNode::create([
            'skill_id' => null,
            'title' => 'Taking the First Steps',
            'description' => 'Begin your journey by uploading your first project.',
            'x_position' => 50,
            'y_position' => 50,
            'tier' => 1,
            'required_level' => 1,
            'task_requirements' => [
                ['type' => 'project_count', 'description' => 'Upload your first project', 'required' => 1]
            ],
        ]);
        
        // Web Development Path
        $webDev = SkillNode::create([
            'skill_id' => Skill::where('slug', 'html')->first()->id,
            'parent_node_id' => $root->id,
            'title' => 'Web Developer Path',
            'description' => 'Master web development fundamentals.',
            'x_position' => 30,
            'y_position' => 150,
            'tier' => 1,
            'required_level' => 1,
            'task_requirements' => [
                ['type' => 'project_count', 'description' => 'Upload 2 projects', 'required' => 2]
            ],
        ]);
        
        // Backend Path
        $backendDev = SkillNode::create([
            'skill_id' => Skill::where('slug', 'php')->first()->id,
            'parent_node_id' => $root->id,
            'title' => 'Backend Developer Path',
            'description' => 'Build server-side applications.',
            'x_position' => 70,
            'y_position' => 150,
            'tier' => 1,
            'required_level' => 1,
            'task_requirements' => [
                ['type' => 'project_count', 'description' => 'Upload 2 projects', 'required' => 2]
            ],
        ]);
    }
    
    private function seedBadges()
    {
        $badges = [
            [
                'title' => 'First Steps',
                'slug' => 'first-project',
                'description' => 'Create your first project',
                'icon' => '🚀',
                'rarity' => 'common',
                'category' => 'project',
                'threshold' => 1,
                'xp_reward' => 50,
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
            ],
        ];
        
        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
