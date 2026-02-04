<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Universidad',
                'description' => 'A narrative-driven horror experience focused on environmental storytelling.',
                'tech' => 'Roblox / Lua',
            ],
            [
                'name' => 'HexCore',
                'description' => 'A modular skill-tree system designed to experiment with progression mechanics.',
                'tech' => 'JavaScript',
            ],
            [
                'name' => 'LvlUp Dashboard',
                'description' => 'A personal dashboard for tracking projects, skills, and long-term growth.',
                'tech' => 'Laravel',
            ],
        ];
    
        foreach ($projects as $project) {
            Project::create([
                'name' => $project['name'],
                'slug' => Str::slug($project['name']),
                'description' => $project['description'],
                'tech' => $project['tech'],
            ]);
        }
    }
}
