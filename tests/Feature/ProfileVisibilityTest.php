<?php

use App\Models\User;

it('displays profile visibility toggle on edit page', function () {
    $user = User::factory()->create(['is_public' => true]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSeeText('Profile Visibility');
    $response->assertSeeText('Public Profile');
});

it('displays public URL when profile is public', function () {
    $user = User::factory()->create([
        'name' => 'testuser',
        'is_public' => true,
    ]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSeeText('Your Public Profile URL');
});

it('does not display public URL section when profile is private', function () {
    $user = User::factory()->create([
        'name' => 'privateuser',
        'is_public' => false,
    ]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertDontSeeText('Your Public Profile URL');
});

it('toggles profile visibility via route', function () {
    $user = User::factory()->create(['is_public' => false]);

    expect($user->is_public)->toBeFalse();

    $response = $this->actingAs($user)->patch(route('profile.toggle-visibility'));

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'visibility-updated');

    expect($user->fresh()->is_public)->toBeTrue();
});

it('displays public indicator badge when profile is public', function () {
    $user = User::factory()->create(['is_public' => true]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSee('fa-globe');
});

it('displays private indicator badge when profile is private', function () {
    $user = User::factory()->create(['is_public' => false]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSee('fa-lock');
});

it('updates profile with title and bio', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'title' => 'Full Stack Developer',
        'bio' => 'I love coding and building awesome projects.',
    ]);

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'profile-updated');

    expect($user->fresh()->title)->toBe('Full Stack Developer');
    expect($user->fresh()->bio)->toBe('I love coding and building awesome projects.');
});
