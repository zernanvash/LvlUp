<?php

use App\Models\User;
use App\Models\Badge;
use App\Models\Project;
use App\Models\SkillNode;

it('displays public profile for public users', function () {
    $user = User::factory()->create([
        'name' => 'testuser',
        'is_public' => true,
        'level' => 5,
        'xp' => 100,
        'rank' => 'Bronze',
        'total_xp' => 500,
    ]);

    $response = $this->get(route('profile.public', ['username' => 'testuser']));

    $response->assertStatus(200);
    $response->assertSeeText('testuser');
    $response->assertSeeText('Level');
    $response->assertSeeText('5');
    $response->assertSeeText('Bronze');
});

it('returns 403 for private profiles when not authenticated', function () {
    $user = User::factory()->create([
        'name' => 'privateuser',
        'is_public' => false,
    ]);

    $response = $this->get(route('profile.public', ['username' => 'privateuser']));

    $response->assertStatus(403);
});

it('allows owner to view their own private profile', function () {
    $user = User::factory()->create([
        'name' => 'privateuser',
        'is_public' => false,
        'level' => 3,
    ]);

    $response = $this->actingAs($user)->get(route('profile.public', ['username' => 'privateuser']));

    $response->assertStatus(200);
    $response->assertSee('privateuser');
});

it('displays equipped badges on public profile', function () {
    $user = User::factory()->create([
        'name' => 'badgeuser',
        'is_public' => true,
    ]);

    $badge = Badge::factory()->create([
        'title' => 'First Steps',
        'icon' => 'fas fa-star',
        'rarity' => 'common',
    ]);

    $user->badges()->attach($badge->id, [
        'earned_at' => now(),
        'is_displayed' => true,
    ]);

    $response = $this->get(route('profile.public', ['username' => 'badgeuser']));

    $response->assertStatus(200);
    $response->assertSeeText('First Steps');
    $response->assertSeeText('Equipped Badges');
});

it('displays featured projects on public profile', function () {
    $user = User::factory()->create([
        'name' => 'projectuser',
        'is_public' => true,
    ]);

    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'My Featured Project',
        'description' => 'A cool project',
        'is_featured' => true,
    ]);

    $response = $this->get(route('profile.public', ['username' => 'projectuser']));

    $response->assertStatus(200);
    $response->assertSeeText('My Featured Project');
    $response->assertSeeText('Featured Projects');
});

it('does not display email on public profile', function () {
    $user = User::factory()->create([
        'name' => 'secureuser',
        'email' => 'secret@example.com',
        'is_public' => true,
    ]);

    $response = $this->get(route('profile.public', ['username' => 'secureuser']));

    $response->assertStatus(200);
    $response->assertDontSee('secret@example.com');
});

it('displays skill tree progress stats', function () {
    $user = User::factory()->create([
        'name' => 'skilluser',
        'is_public' => true,
    ]);

    $node = SkillNode::factory()->create();
    $user->unlockedNodes()->attach($node->id, ['unlocked_at' => now()]);

    $response = $this->get(route('profile.public', ['username' => 'skilluser']));

    $response->assertStatus(200);
    $response->assertSee('Skill Tree Progress');
    $response->assertSee('Nodes Unlocked');
});

it('toggles profile visibility', function () {
    $user = User::factory()->create(['is_public' => true]);

    expect($user->is_public)->toBeTrue();

    $user->toggleVisibility();

    expect($user->is_public)->toBeFalse();

    $user->toggleVisibility();

    expect($user->is_public)->toBeTrue();
});

it('generates correct public URL', function () {
    $user = User::factory()->create(['name' => 'testuser']);

    $url = $user->getPublicUrl();

    expect($url)->toContain('/profile/testuser');
});
