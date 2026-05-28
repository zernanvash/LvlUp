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
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // 1. Create or retrieve test user
        $user = User::where('email', 'test@lvlup.dev')->first();
        if (!$user) {
            $user = $this->seedTestUser();
        }
        
        // 2. Call dedicated seeders
        $this->call([
            SkillTreeSeeder::class,
            BadgeSeeder::class,
            LocalSampleUsersSeeder::class,
        ]);

        // 3. Setup test user progress
        $this->setupTestUserProgress($user);

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
    
    private function seedTestUser(): User
    {
        return User::create([
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

    private function setupTestUserProgress(User $user): void
    {
        // Clear existing associations to prevent duplicate key constraints on seed runs
        $user->projects()->delete();
        $user->certificates()->delete();
        $user->badges()->detach();
        $user->unlockedNodes()->detach();
        $user->resumes()->delete(); // Ensure NO resumes are seeded initially

        // 1. Create projects for the test user
        $project1 = \App\Models\Project::create([
            'user_id' => $user->id,
            'name' => 'Personal Portfolio Website',
            'description' => 'A simple static HTML/CSS personal website to showcase my profile and early projects.',
            'url' => 'https://example.com/portfolio',
            'github_url' => 'https://github.com/testuser/portfolio',
            'language' => 'HTML',
            'project_type' => 'web',
            'xp_reward' => 120,
            'is_featured' => true,
        ]);

        $project2 = \App\Models\Project::create([
            'user_id' => $user->id,
            'name' => 'CLI Task Manager',
            'description' => 'A command-line task manager application written in Python to manage daily tasks and log activities.',
            'url' => 'https://example.com/task-manager',
            'github_url' => 'https://github.com/testuser/cli-task-manager',
            'language' => 'Python',
            'project_type' => 'backend',
            'xp_reward' => 120,
            'is_featured' => false,
        ]);

        // Attach skills to projects
        $webSkill = \App\Models\Skill::where('slug', 'web-dev')->first();
        $backendSkill = \App\Models\Skill::where('slug', 'backend')->first();
        if ($webSkill) {
            $project1->skills()->sync([$webSkill->id => ['proficiency' => 3]]);
        }
        if ($backendSkill) {
            $project2->skills()->sync([$backendSkill->id => ['proficiency' => 2]]);
        }

        // 2. Unlock some skill tree nodes (leave others locked/unobtained)
        $nodesToUnlock = \App\Models\SkillNode::whereIn('title', [
            'Hello, World!',
            'HTML & CSS Craftsman',
            'Git Flow',
        ])->get();

        foreach ($nodesToUnlock as $node) {
            $user->unlockedNodes()->attach($node->id, ['unlocked_at' => now()->subDays(2)]);
        }

        // 3. Unlock and equip some badges (achievements)
        $badgesToUnlock = \App\Models\Badge::whereIn('slug', [
            'hello-world',
            'pixel-pusher',
            'server-whisperer',
        ])->get();

        foreach ($badgesToUnlock as $index => $badge) {
            $user->badges()->attach($badge->id, [
                'earned_at' => now()->subDays($index + 1),
                'is_displayed' => true,
            ]);
        }

        // 4. Create some certificates
        \App\Models\Certificate::create([
            'user_id' => $user->id,
            'title' => 'AWS Certified Cloud Practitioner',
            'issuer' => 'Amazon Web Services (AWS)',
            'issued_date' => now()->subMonths(3)->toDateString(),
            'file_path' => 'https://res.cloudinary.com/demo/image/upload/v1234567890/sample_aws_cert.pdf',
            'file_public_id' => 'sample_aws_cert',
            'file_type' => 'pdf',
            'ai_summary' => 'This certificate validates foundational knowledge of AWS cloud platform, infrastructure, and services.',
        ]);

        \App\Models\Certificate::create([
            'user_id' => $user->id,
            'title' => 'Responsive Web Design Developer Certificate',
            'issuer' => 'freeCodeCamp',
            'issued_date' => now()->subMonths(6)->toDateString(),
            'file_path' => 'https://res.cloudinary.com/demo/image/upload/v1234567890/sample_fcc_cert.pdf',
            'file_public_id' => 'sample_fcc_cert',
            'file_type' => 'pdf',
            'ai_summary' => 'This certification verifies proficiency in HTML, CSS, visual design, accessibility, and responsive principles.',
        ]);
    }
}
