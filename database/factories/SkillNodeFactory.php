<?php

namespace Database\Factories;

use App\Models\SkillNode;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillNodeFactory extends Factory
{
    protected $model = SkillNode::class;

    public function definition(): array
    {
        return [
            'skill_id' => null,
            'parent_node_id' => null,
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'x_position' => fake()->numberBetween(0, 1000),
            'y_position' => fake()->numberBetween(0, 1000),
            'tier' => fake()->numberBetween(1, 5),
            'required_level' => fake()->numberBetween(1, 10),
            'task_requirements' => null,
        ];
    }
}
