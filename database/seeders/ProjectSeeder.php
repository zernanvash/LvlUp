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
        \App\Models\Project::create([
            'name' => 'Portfolio System',
            'slug' => 'portfolio-system',
            'description' => 'A Laravel based skill tracking system with a hex-grid tree.',
            'language' => 'PHP',
        ]);
    
        \App\Models\Project::create([
            'name' => 'Skill Tree Visualizer',
            'slug' => 'skill-tree-visualizer',
            'description' => 'A JavaScript component for rendering complex skill trees.',
            'language' => 'JavaScript',
        ]);
        
    }
}
