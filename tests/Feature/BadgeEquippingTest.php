<?php

use App\Models\User;
use App\Models\Badge;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->badges = Badge::factory()->count(8)->create();
    
    // Attach badges to user (earned but not equipped)
    foreach ($this->badges as $badge) {
        $this->user->badges()->attach($badge->id, [
            'earned_at' => now(),
            'is_displayed' => false
        ]);
    }
});

it('allows user to equip a badge they own', function () {
    $result = $this->user->equipBadge($this->badges[0]->id);
    
    expect($result)->toBeTrue();
    
    $badge = $this->user->badges()->where('badge_id', $this->badges[0]->id)->first();
    expect($badge->pivot->is_displayed)->toBe(1);
});

it('prevents user from equipping a badge they do not own', function () {
    $unownedBadge = Badge::factory()->create();
    
    $result = $this->user->equipBadge($unownedBadge->id);
    
    expect($result)->toBeFalse();
});

it('enforces 6 badge equip limit', function () {
    // Equip 6 badges
    for ($i = 0; $i < 6; $i++) {
        $result = $this->user->equipBadge($this->badges[$i]->id);
        expect($result)->toBeTrue();
    }
    
    // Try to equip 7th badge
    $result = $this->user->equipBadge($this->badges[6]->id);
    expect($result)->toBeFalse();
    
    // Verify only 6 are equipped
    $equippedCount = $this->user->badges()->wherePivot('is_displayed', true)->count();
    expect($equippedCount)->toBe(6);
});

it('allows equipping after unequipping when at limit', function () {
    // Equip 6 badges
    for ($i = 0; $i < 6; $i++) {
        $this->user->equipBadge($this->badges[$i]->id);
    }
    
    // Unequip one
    $this->user->unequipBadge($this->badges[0]->id);
    
    // Now 7th badge should work
    $result = $this->user->equipBadge($this->badges[6]->id);
    expect($result)->toBeTrue();
    
    $equippedCount = $this->user->badges()->wherePivot('is_displayed', true)->count();
    expect($equippedCount)->toBe(6);
});

it('allows user to unequip a badge', function () {
    $this->user->equipBadge($this->badges[0]->id);
    
    $result = $this->user->unequipBadge($this->badges[0]->id);
    
    expect($result)->toBeTrue();
    
    $badge = $this->user->badges()->where('badge_id', $this->badges[0]->id)->first();
    expect($badge->pivot->is_displayed)->toBe(0);
});

it('prevents unequipping a badge user does not own', function () {
    $unownedBadge = Badge::factory()->create();
    
    $result = $this->user->unequipBadge($unownedBadge->id);
    
    expect($result)->toBeFalse();
});

it('returns true when equipping already equipped badge', function () {
    $this->user->equipBadge($this->badges[0]->id);
    
    // Try to equip again
    $result = $this->user->equipBadge($this->badges[0]->id);
    
    expect($result)->toBeTrue();
});

it('tracks equip order via updated_at timestamp', function () {
    // Equip badges with delays to ensure different timestamps
    $this->user->equipBadge($this->badges[0]->id);
    sleep(1);
    $this->user->equipBadge($this->badges[1]->id);
    sleep(1);
    $this->user->equipBadge($this->badges[2]->id);
    
    // Get equipped badges in order
    $equippedBadges = $this->user->equippedBadges()->get();
    
    expect($equippedBadges)->toHaveCount(3);
    expect($equippedBadges[0]->id)->toBe($this->badges[0]->id);
    expect($equippedBadges[1]->id)->toBe($this->badges[1]->id);
    expect($equippedBadges[2]->id)->toBe($this->badges[2]->id);
});
