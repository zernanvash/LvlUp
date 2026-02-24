<?php

use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use App\Services\ResumeAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->analyzer = new ResumeAnalyzer();
});

it('extracts keywords from job description', function () {
    $jobDescription = 'We are looking for a Senior PHP Developer with Laravel experience. 
                       Must have strong knowledge of MySQL, Redis, and Docker. 
                       Experience with React and Vue.js is a plus.';
    
    $keywords = $this->analyzer->extractKeywords($jobDescription);
    
    expect($keywords)->toBeArray()
        ->and($keywords)->toContain('php')
        ->and($keywords)->toContain('laravel')
        ->and($keywords)->toContain('mysql')
        ->and($keywords)->toContain('redis')
        ->and($keywords)->toContain('docker')
        ->and($keywords)->toContain('react')
        ->and($keywords)->toContain('vue');
});

it('scores project against keywords with weighted matching', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'E-commerce Platform',
        'description' => 'Built with Laravel and Vue.js, using MySQL database',
        'language' => 'PHP',
    ]);
    
    // Attach skills
    $laravel = Skill::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $vue = Skill::factory()->create(['name' => 'Vue.js', 'slug' => 'vue-js']);
    $project->skills()->attach($laravel->id, ['proficiency' => 4]);
    $project->skills()->attach($vue->id, ['proficiency' => 3]);
    
    $keywords = ['laravel', 'vue', 'mysql', 'php'];
    $score = $this->analyzer->scoreProject($project->fresh(), $keywords);
    
    expect($score)->toBeGreaterThan(0)
        ->and($score)->toBeLessThanOrEqual(100);
});

it('ranks projects by relevance to keywords', function () {
    $user = User::factory()->create();
    
    // Create projects with different relevance
    $project1 = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Laravel API',
        'description' => 'RESTful API built with Laravel and MySQL',
    ]);
    
    $project2 = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'React Dashboard',
        'description' => 'Admin dashboard built with React',
    ]);
    
    $project3 = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Full Stack App',
        'description' => 'Built with Laravel backend and React frontend',
    ]);
    
    // Attach skills
    $laravel = Skill::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $react = Skill::factory()->create(['name' => 'React', 'slug' => 'react']);
    
    $project1->skills()->attach($laravel->id);
    $project3->skills()->attach([$laravel->id, $react->id]);
    
    $keywords = ['laravel', 'php', 'backend'];
    $projects = collect([$project1, $project2, $project3]);
    
    $ranked = $this->analyzer->rankProjects($projects, $keywords);
    
    expect($ranked)->toHaveCount(3)
        ->and($ranked->first()->relevance_score)->toBeGreaterThanOrEqual($ranked->last()->relevance_score);
});

it('calculates match score for user profile', function () {
    $user = User::factory()->create();
    
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Laravel Project',
        'description' => 'Built with Laravel, MySQL, and Redis',
    ]);
    
    $laravel = Skill::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $mysql = Skill::factory()->create(['name' => 'MySQL', 'slug' => 'mysql']);
    $project->skills()->attach([$laravel->id, $mysql->id]);
    
    $keywords = ['laravel', 'mysql', 'redis', 'docker'];
    $matchScore = $this->analyzer->calculateMatchScore($user->fresh(), $keywords);
    
    expect($matchScore)->toBeGreaterThan(0)
        ->and($matchScore)->toBeLessThanOrEqual(100);
});

it('handles empty keywords gracefully', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    $score = $this->analyzer->scoreProject($project, []);
    $matchScore = $this->analyzer->calculateMatchScore($user, []);
    
    expect($score)->toBe(0.0)
        ->and($matchScore)->toBe(0.0);
});

it('removes stop words from keyword extraction', function () {
    $jobDescription = 'We are looking for a developer with experience in the following technologies';
    
    $keywords = $this->analyzer->extractKeywords($jobDescription);
    
    // Stop words should not be in keywords
    expect($keywords)->not->toContain('we')
        ->and($keywords)->not->toContain('are')
        ->and($keywords)->not->toContain('for')
        ->and($keywords)->not->toContain('the')
        ->and($keywords)->not->toContain('in');
});

it('uses weighted scoring for skill tags, description, and name', function () {
    $user = User::factory()->create();
    
    // Project with skill tag match (highest weight: 3x)
    $project1 = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Project',
        'description' => 'A test project',
    ]);
    $laravel = Skill::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $project1->skills()->attach($laravel->id);
    
    // Project with only description match (medium weight: 2x)
    $project2 = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Project',
        'description' => 'Built with Laravel framework',
    ]);
    
    $keywords = ['laravel'];
    
    $score1 = $this->analyzer->scoreProject($project1->fresh(), $keywords);
    $score2 = $this->analyzer->scoreProject($project2, $keywords);
    
    // Skill tag match should score higher than description match
    expect($score1)->toBeGreaterThan($score2);
});
