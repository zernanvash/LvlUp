<?php

use App\Models\User;
use App\Models\Badge;
use App\Models\SkillNode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    $user = User::updateOrCreate(
        ['email' => 'god@lvlup.com'],
        [
            'name' => 'G.O.D. (aka GOAT OL DEVELOPER)',
            'password' => Hash::make('password'),
            'title' => 'Supreme Architect / Master of Operations',
            'bio' => "I am the Greatest Of All Time. I have transcended normal programming.",
            'level' => 100,
            'xp' => 9999999,
            'total_xp' => 9999999,
            'rank' => 'Grandmaster',
            'is_public' => true,
            'streak_days' => 999,
            'technical_skills' => 'Every single language, framework, and system known to mankind. From Assembly to Laravel.',
            'work_experience' => "CREATOR @ The Universe (Start of Time - Present)\n- Architected reality.",
            'education' => "Omniscience",
            'resume_job_title' => 'The GOAT',
        ]
    );

    $badges = Badge::all();
    if (!$badges->isEmpty()) {
        $syncData = [];
        foreach ($badges as $index => $badge) {
            $syncData[$badge->id] = [
                'is_displayed' => $index < 6,
                'earned_at' => now()->subDays(rand(1, 10))
            ];
        }
        $user->badges()->sync($syncData);
    }

    $skillNodes = SkillNode::all();
    if (!$skillNodes->isEmpty()) {
        $syncData = [];
        foreach ($skillNodes as $node) {
            $syncData[$node->id] = ['unlocked_at' => now()->subDays(rand(1, 10))];
        }
        $user->unlockedNodes()->sync($syncData);
    }

    if ($user->projects()->count() < 3) {
        $user->projects()->create([
            'name' => 'Reality Engine',
            'description' => 'The underlying system that powers all existence.',
            'is_featured' => true,
            'xp_reward' => 1000,
        ]);
        $user->projects()->create([
            'name' => 'Time Machine',
            'description' => 'A basic temporal displacement application built in PHP.',
            'is_featured' => true,
            'xp_reward' => 1000,
        ]);
        $user->projects()->create([
            'name' => 'Infinity Scaling',
            'description' => 'Auto-scaling cluster capable of handling infinite requests per second.',
            'is_featured' => true,
            'xp_reward' => 1000,
        ]);
    }

    if ($user->certificates()->count() === 0) {
        $user->certificates()->create([
            'title' => 'Grandmaster of the Universe',
            'issuer' => 'The Cosmos',
            'issued_date' => now()->subYears(100),
            'file_path' => 'https://res.cloudinary.com/demo/image/upload/sample.jpg',
            'file_public_id' => 'sample',
            'file_type' => 'image',
            'ai_summary' => 'Certified Omniscient Being.',
        ]);
    }

    DB::commit();
    echo "\nG.O.D. created successfully! Email: god@lvlup.com | Password: password\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
