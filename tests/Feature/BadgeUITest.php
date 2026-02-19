<?php

use App\Models\User;
use App\Models\Badge;

it('displays badge collection page with equipped badges section', function () {
    $user = User::factory()->create();
    
    // Create some badges
    $badge1 = Badge::factory()->create(['rarity' => 'common']);
    $badge2 = Badge::factory()->create(['rarity' => 'rare']);
    
    // Award badges to user
    $user->badges()->attach($badge1->id, ['earned_at' => now(), 'is_displayed' => true]);
    $user->badges()->attach($badge2->id, ['earned_at' => now(), 'is_displayed' => false]);
    
    $response = $this->actingAs($user)->get(route('achievements.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Hall of Glory');
    $response->assertSee('Equipped Badges');
    $response->assertSee('1/6'); // 1 equipped out of 6
});

it('displays progress bars for unearned badges', function () {
    $user = User::factory()->create();
    
    // Create a project badge that requires 5 projects
    $badge = Badge::factory()->create([
        'category' => 'project',
        'threshold' => 5,
        'title' => 'Project Master'
    ]);
    
    $response = $this->actingAs($user)->get(route('achievements.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Project Master');
    $response->assertSee('Locked');
});

it('shows equip button for earned unequipped badges', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();
    
    // Award badge but don't equip it
    $user->badges()->attach($badge->id, ['earned_at' => now(), 'is_displayed' => false]);
    
    $response = $this->actingAs($user)->get(route('achievements.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Equip Badge');
});

it('shows unequip button for equipped badges', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();
    
    // Award and equip badge
    $user->badges()->attach($badge->id, ['earned_at' => now(), 'is_displayed' => true]);
    
    $response = $this->actingAs($user)->get(route('achievements.index'));
    
    $response->assertStatus(200);
    $response->assertSee('Unequip Badge');
});

it('displays rarity colors and glow effects', function () {
    $user = User::factory()->create();
    
    $rarities = ['common', 'rare', 'epic', 'legendary', 'mythic'];
    
    foreach ($rarities as $rarity) {
        $badge = Badge::factory()->create(['rarity' => $rarity]);
        $user->badges()->attach($badge->id, ['earned_at' => now()]);
    }
    
    $response = $this->actingAs($user)->get(route('achievements.index'));
    
    $response->assertStatus(200);
    foreach ($rarities as $rarity) {
        $response->assertSee($rarity);
    }
});

it('enforces 6 badge equip limit in UI', function () {
    $user = User::factory()->create();
    
    // Create and equip 6 badges
    for ($i = 0; $i < 6; $i++) {
        $badge = Badge::factory()->create();
        $user->badges()->attach($badge->id, ['earned_at' => now(), 'is_displayed' => true]);
    }
    
    $response = $this->actingAs($user)->get(route('achievements.index'));
    
    $response->assertStatus(200);
    $response->assertSee('6/6'); // All slots filled
});
