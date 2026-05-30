<?php

use App\Mcp\Prompts\OptimizeResumePrompt;
use App\Mcp\Resources\UserProfileResource;
use App\Mcp\Servers\PortfolioServer;
use App\Mcp\Tools\AddUserProject;
use App\Mcp\Tools\GetUserProjects;
use App\Mcp\Tools\GetUserSkills;
use App\Mcp\Tools\GetUserStats;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;

it('GetUserStats tool returns correct structured statistics', function () {
    $user = User::factory()->create([
        'name' => 'Maya Frontend',
        'level' => 8,
        'xp' => 260,
        'rank' => 'Bronze',
    ]);

    $response = PortfolioServer::actingAs($user)->tool(GetUserStats::class);

    $response->assertOk();
    $response->assertSee('"name":"Maya Frontend"');
    $response->assertSee('"level":8');
});

it('GetUserProjects tool returns all user projects', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Portfolio Command Center',
        'language' => 'TypeScript',
        'project_type' => 'web',
    ]);

    $response = PortfolioServer::actingAs($user)->tool(GetUserProjects::class);

    $response->assertOk();
    $response->assertSee('Portfolio Command Center');
    $response->assertSee('TypeScript');
});

it('AddUserProject tool successfully creates a project and awards XP', function () {
    $user = User::factory()->create([
        'level' => 1,
        'xp' => 0,
    ]);

    $response = PortfolioServer::actingAs($user)->tool(AddUserProject::class, [
        'name' => 'New MCP Integration',
        'description' => 'Test project creation via tool',
        'language' => 'PHP',
        'project_type' => 'backend',
        'code_content' => "line 1\nline 2\nline 3\nline 4\nline 5", // 5 lines -> +10 XP reward (100 base + 10 = 110)
        'tags' => 'Laravel, MCP',
    ]);

    $response->assertOk();
    $response->assertSee('"success":true');
    $response->assertSee('"xp_earned":110');

    // Assert database entry exists
    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'New MCP Integration',
        'language' => 'PHP',
        'xp_reward' => 110,
    ]);

    // Assert user XP was awarded (110 XP makes user level up from 1 to 2, leaving 10 XP)
    $user->refresh();
    expect($user->level)->toBe(2);
    expect($user->xp)->toBe(10);
    expect($user->total_xp)->toBe(110);
});

it('GetUserSkills tool lists skills and proficiencies', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'category' => 'backend',
    ]);

    // Attach skill to project to have proficiency > 0
    $project = Project::factory()->create(['user_id' => $user->id]);
    $project->skills()->attach($skill->id, ['proficiency' => 4]);

    $response = PortfolioServer::actingAs($user)->tool(GetUserSkills::class);

    $response->assertOk();
    $response->assertSee('Laravel');
    $response->assertSee('"project_proficiency":4');
});

it('UserProfileResource returns markdown profile details', function () {
    $user = User::factory()->create([
        'name' => 'Kai Private',
        'bio' => 'Private profile account for testing',
        'title' => 'Student Developer',
    ]);

    $response = PortfolioServer::actingAs($user)->resource(UserProfileResource::class, [
        'userId' => $user->id,
    ]);

    $response->assertOk();
    $response->assertSee('# Developer Profile: Kai Private');
    $response->assertSee('Student Developer');
    $response->assertSee('Private profile account for testing');
});

it('OptimizeResumePrompt returns recruiter guidelines', function () {
    $user = User::factory()->create([
        'name' => 'Iris Fullstack',
        'title' => 'Full Stack Developer',
    ]);

    $response = PortfolioServer::actingAs($user)->prompt(OptimizeResumePrompt::class, [
        'job_description' => 'Looking for a Laravel React Fullstack developer',
    ]);

    $response->assertOk();
    $response->assertSee('You are a professional technical recruiter and resume writer.');
    $response->assertSee('Looking for a Laravel React Fullstack developer');
});
