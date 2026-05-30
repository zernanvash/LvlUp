<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tab' => 'settings']));

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tab' => 'settings']));

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('resume profile information can be updated via ajax', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->postJson('/resume/profile', [
            'title' => 'Senior Tech Lead',
            'phone_number' => '1234567890',
            'city' => 'New York',
            'country' => 'USA',
            'bio' => 'An experienced software architect.',
            'technical_skills' => 'Laravel, PHP, Vue.js',
            'work_experience' => 'Company A | Architect | 2022-Present',
            'education' => 'University B | BS CS | 2018',
            'linkedin_url' => 'https://linkedin.com/in/testuser',
            'github_url' => 'https://github.com/testuser',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Profile updated successfully!',
        ]);

    $user->refresh();

    $this->assertSame('Senior Tech Lead', $user->title);
    $this->assertSame('Senior Tech Lead', $user->resume_job_title);
    $this->assertSame('An experienced software architect.', $user->bio);
    $this->assertSame('An experienced software architect.', $user->resume_summary);
    $this->assertSame('1234567890', $user->phone_number);
    $this->assertSame('New York', $user->city);
    $this->assertSame('USA', $user->country);
    $this->assertSame('Laravel, PHP, Vue.js', $user->technical_skills);
    $this->assertSame('Company A | Architect | 2022-Present', $user->work_experience);
    $this->assertSame('University B | BS CS | 2018', $user->education);
    $this->assertSame('https://linkedin.com/in/testuser', $user->linkedin_url);
    $this->assertSame('https://github.com/testuser', $user->github_url);
});

test('resume details can be updated via profile form and persists active tab', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'resume_job_title' => 'Staff Engineer',
            'resume_summary' => 'Highly skilled developer with focus on Laravel.',
            'work_experience' => 'Acme Corp | Lead Developer | 2020-2025',
            'education' => 'Tech University | MS CS | 2019',
            'certifications' => 'AWS Certified',
            'languages' => 'English - Native',
            'tab' => 'resume',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tab' => 'resume']));

    $user->refresh();

    $this->assertSame('Staff Engineer', $user->resume_job_title);
    $this->assertSame('Highly skilled developer with focus on Laravel.', $user->resume_summary);
    $this->assertSame('Acme Corp | Lead Developer | 2020-2025', $user->work_experience);
    $this->assertSame('Tech University | MS CS | 2019', $user->education);
    $this->assertSame('AWS Certified', $user->certifications);
    $this->assertSame('English - Native', $user->languages);
});
