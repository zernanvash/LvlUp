<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'url' => fake()->optional()->url(),
            'github_url' => fake()->optional()->url(),
            'language' => fake()->randomElement(['PHP', 'JavaScript', 'Python', 'Java', 'TypeScript', 'Go', 'Ruby']),
            'thumbnail' => fake()->optional()->imageUrl(640, 480, 'tech'),
            'xp_reward' => fake()->numberBetween(50, 200),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
            'metadata' => null,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function withHighXP(): static
    {
        return $this->state(fn (array $attributes) => [
            'xp_reward' => fake()->numberBetween(150, 300),
        ]);
    }
}
