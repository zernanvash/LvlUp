<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'PHP', 'Laravel', 'JavaScript', 'TypeScript', 'React', 'Vue.js',
            'Angular', 'Node.js', 'Python', 'Django', 'Flask', 'Java',
            'Spring Boot', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis',
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'Git', 'CI/CD',
        ]);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name).'-'.fake()->unique()->randomNumber(5),
            'icon' => 'fa-code',
            'color' => fake()->hexColor(),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement([
                'frontend', 'backend', 'mobile', 'design', 'devops', 'security', 'ai',
            ]),
            'rarity' => fake()->randomElement(['common', 'rare', 'epic', 'legendary']),
            'required_level' => fake()->numberBetween(1, 10),
        ];
    }
}
