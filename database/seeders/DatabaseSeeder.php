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
        
        // Call dedicated seeders
        $this->call([
            SkillTreeSeeder::class,
            BadgeSeeder::class,
        ]);
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
}
