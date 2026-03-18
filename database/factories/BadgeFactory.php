<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        $rarities = ['common', 'rare', 'epic', 'legendary', 'mythic'];
        $categories = ['project', 'skill', 'level', 'special'];
        $icons = ['🏆', '⭐', '🎯', '🔥', '💎', '👑', '🎖️', '🏅'];

        return [
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement($icons),
            'rarity' => fake()->randomElement($rarities),
            'category' => fake()->randomElement($categories),
            'required_skill_id' => null,
            'threshold' => fake()->numberBetween(1, 20),
            'xp_reward' => fake()->numberBetween(50, 500),
        ];
    }
}
