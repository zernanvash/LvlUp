<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Badge;
use App\Models\SkillNode;
use App\Models\Project;
use App\Models\Certificate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTestAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a test user account that has all badges, skills, and max level/xp.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating or updating the test account...');

        // 1. Create or update user
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Max Level Dev',
                'password' => Hash::make('password'),
                'title' => 'Chief Technology Officer',
                'bio' => "I am a passionate technology leader with 10+ years of experience in architecting scalable solutions. I've unlocked every achievement, mastered every skill branch, and built an extensive array of projects.",
                'level' => 100,
                'xp' => 1000000,
                'total_xp' => 1000000,
                'rank' => 'Master',
                'is_public' => true,
                'streak_days' => 365,
                'technical_skills' => 'PHP, Laravel, Vue.js, React, Node.js, Python, AWS, Docker, Kubernetes, MySQL, PostgreSQL',
                'work_experience' => "CTO @ LvlUp (2020-Present)\n- Led a team of 50 engineers to build the ultimate developer platform.\n- Reduced server costs by 50% through Kubernetes orchestration.\n\nSenior Software Engineer @ TechCorp (2015-2020)\n- Scaled microservices to handle 1M+ daily active users.",
                'education' => "M.S. Computer Science\nStanford University (2015)\n\nB.S. Computer Engineering\nMIT (2013)",
                'resume_job_title' => 'CTO / VP of Engineering',
            ]
        );

        $this->info("User '{$user->email}' ready with password 'password'.");

        // 2. Unlock all badges
        $badges = Badge::all();
        if ($badges->isEmpty()) {
            $this->warn('No badges found in the database. Run seeders first.');
        } else {
            // Attach all with some equipped
            $syncData = [];
            foreach ($badges as $index => $badge) {
                // Equip the first 6 badges so the profile looks cool
                $syncData[$badge->id] = [
                    'is_displayed' => $index < 6,
                    'earned_at' => now()->subDays(rand(1, 100))
                ];
            }
            $user->badges()->sync($syncData);
            $this->info("Unlocked {$badges->count()} badges.");
        }

        // 3. Unlock all skill nodes
        $skillNodes = SkillNode::all();
        if ($skillNodes->isEmpty()) {
            $this->warn('No skill nodes found in the database. Run seeders first.');
        } else {
            $syncData = [];
            foreach ($skillNodes as $node) {
                $syncData[$node->id] = ['unlocked_at' => now()->subDays(rand(1, 100))];
            }
            $user->unlockedNodes()->sync($syncData);
            $this->info("Unlocked {$skillNodes->count()} skill nodes.");
        }

        // 4. Give them a few cool dummy projects if they don't have enough
        if ($user->projects()->count() < 3) {
            $project1 = $user->projects()->create([
                'name' => 'LvlUp Platform Core',
                'description' => 'The main monolith for the gamified developer experience. Built with Laravel 11 and Alpine.js.',
                'repository_url' => 'https://github.com/example/lvlup',
                'project_url' => 'https://lvlup.example.com',
                'is_featured' => true,
                'xp_reward' => 50,
            ]);
            
            $project2 = $user->projects()->create([
                'name' => 'Microservice Auth Gateway',
                'description' => 'High-performance API gateway handling rate limiting, JWT validation, and routing.',
                'repository_url' => 'https://github.com/example/auth-gateway',
                'is_featured' => true,
                'xp_reward' => 50,
            ]);

            $project3 = $user->projects()->create([
                'name' => 'Distributed Cache Engine',
                'description' => 'Custom Redis-like in-memory store built in Golang with Raft consensus.',
                'is_featured' => true,
                'xp_reward' => 50,
            ]);

            $this->info("Created dummy projects.");
        }

        // 5. Create a dummy certificate if they don't have any
        if ($user->certificates()->count() === 0) {
            $user->certificates()->create([
                'title' => 'AWS Certified Solutions Architect - Professional',
                'issuer' => 'Amazon Web Services',
                'issued_date' => now()->subMonths(6),
                'file_path' => 'https://res.cloudinary.com/demo/image/upload/sample.jpg', // dummy
                'file_public_id' => 'sample',
                'file_type' => 'image',
                'ai_summary' => 'Certified AWS Solutions Architect, demonstrating advanced knowledge in designing distributed systems on AWS.',
            ]);
            $this->info("Created dummy certification.");
        }

        $this->info('Test account setup complete!');
        $this->info("Login Details:\nEmail: test@example.com\nPassword: password");
    }
}
