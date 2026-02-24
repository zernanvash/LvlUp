<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Skill;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('auto-creates skills when attaching tags to projects', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    
    // Attach skills that don't exist yet
    $project->attachSkillsFromTags(['Laravel', 'PHP', 'MySQL']);
    
    // Verify skills were created
    expect(Skill::where('slug', 'laravel')->exists())->toBeTrue();
    expect(Skill::where('slug', 'php')->exists())->toBeTrue();
    expect(Skill::where('slug', 'mysql')->exists())->toBeTrue();
    
    // Verify skills are attached to project
    expect($project->skills()->count())->toBe(3);
    
    // Verify default values are set
    $skill = Skill::where('slug', 'laravel')->first();
    expect($skill->category)->toBe('backend');
    expect($skill->icon)->toBe('fa-code');
    expect($skill->rarity)->toBe('common');
});

it('returns all projects for a skill', function () {
    $skill = Skill::factory()->create(['name' => 'Laravel']);
    
    // Create multiple projects with this skill
    $project1 = Project::factory()->create(['user_id' => $this->user->id]);
    $project2 = Project::factory()->create(['user_id' => $this->user->id]);
    $project3 = Project::factory()->create(['user_id' => $this->user->id]);
    
    $project1->skills()->attach($skill->id, ['proficiency' => 3]);
    $project2->skills()->attach($skill->id, ['proficiency' => 4]);
    // project3 doesn't have this skill
    
    $projects = $skill->getProjectsForSkill();
    
    expect($projects->count())->toBe(2);
    expect($projects->pluck('id')->toArray())->toContain($project1->id, $project2->id);
    expect($projects->pluck('id')->toArray())->not->toContain($project3->id);
});

it('calculates user proficiency with a skill', function () {
    $skill = Skill::factory()->create(['name' => 'React']);
    
    // Create projects with different proficiency levels
    $project1 = Project::factory()->create(['user_id' => $this->user->id]);
    $project2 = Project::factory()->create(['user_id' => $this->user->id]);
    $project3 = Project::factory()->create(['user_id' => $this->user->id]);
    
    $project1->skills()->attach($skill->id, ['proficiency' => 2]);
    $project2->skills()->attach($skill->id, ['proficiency' => 4]);
    $project3->skills()->attach($skill->id, ['proficiency' => 3]);
    
    // Average: (2 + 4 + 3) / 3 = 3.0
    $proficiency = $skill->calculateUserProficiency($this->user);
    
    expect($proficiency)->toBe(3.0);
});

it('returns zero proficiency when user has no projects with skill', function () {
    $skill = Skill::factory()->create(['name' => 'Python']);
    
    $proficiency = $skill->calculateUserProficiency($this->user);
    
    expect($proficiency)->toBe(0.0);
});

it('calculates proficiency only for specific user', function () {
    $skill = Skill::factory()->create(['name' => 'Vue']);
    $otherUser = User::factory()->create();
    
    // This user has projects with skill
    $project1 = Project::factory()->create(['user_id' => $this->user->id]);
    $project1->skills()->attach($skill->id, ['proficiency' => 5]);
    
    // Other user has different proficiency
    $project2 = Project::factory()->create(['user_id' => $otherUser->id]);
    $project2->skills()->attach($skill->id, ['proficiency' => 1]);
    
    expect($skill->calculateUserProficiency($this->user))->toBe(5.0);
    expect($skill->calculateUserProficiency($otherUser))->toBe(1.0);
});
