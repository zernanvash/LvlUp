# Testing Guide

## Quick Start

### 1. Setup Database
```bash
php artisan migrate:fresh --seed
```

This will:
- Drop all tables and recreate them
- Seed 25 skills (HTML, CSS, JavaScript, React, Laravel, etc.)
- Create 12 skill tree nodes with dependencies
- Add 10 achievement badges
- Create a test user account

### 2. Test User Credentials

**Email**: `test@lvlup.dev`  
**Password**: `password`

**Starting Stats**:
- Level: 5
- XP: 150/650 (towards level 6)
- Skill Points: 10
- Primogems: 500
- Streak: 3 days
- Last Login: Yesterday

### 3. Start Development Server
```bash
php artisan serve
```

Visit: http://localhost:8000

## Manual Testing Checklist

### Authentication
- [ ] Register a new account
- [ ] Login with test user
- [ ] Logout
- [ ] Password reset flow

### Dashboard
- [ ] View dashboard with user stats
- [ ] Check XP progress bar
- [ ] Verify level and rank display
- [ ] Claim daily reward (should work since last login was yesterday)
- [ ] Try claiming daily reward again (should fail)

### Projects
- [ ] Create a new project
- [ ] Verify XP is awarded
- [ ] Add skills to project
- [ ] Check if badges are unlocked
- [ ] Edit project
- [ ] Delete project

### Skill Tree
- [ ] View skill tree visualization
- [ ] Check node positions and connections
- [ ] Unlock a skill node (costs skill points)
- [ ] Try unlocking without enough skill points
- [ ] Try unlocking without parent unlocked
- [ ] Verify skill points are deducted

### Achievements
- [ ] View all badges
- [ ] Check earned vs locked badges
- [ ] Verify badge rarity colors
- [ ] Toggle badge display on profile

### Profile
- [ ] Update profile information
- [ ] Upload avatar
- [ ] Change password
- [ ] View profile stats

### Resume Builder
- [ ] Paste job description
- [ ] Analyze and match projects
- [ ] Generate resume PDF
- [ ] Download resume

## Testing XP System

### Test Level Up
```bash
php artisan tinker
```

```php
$user = User::find(1);
$user->addXP(500); // Should level up from 5 to 6
echo "Level: {$user->level}, XP: {$user->xp}, Skill Points: {$user->skill_points}";
```

Expected: Level 6, remaining XP, +3 skill points

### Test Multiple Level Ups
```php
$user = User::find(1);
$user->addXP(5000); // Should level up multiple times
echo "Level: {$user->level}, XP: {$user->xp}";
```

### Test Rank Progression
```php
$user = User::find(1);
$user->level = 24;
$user->addXP(1); // Should still be Gold
echo "Rank: {$user->rank}";

$user->level = 25;
$user->save();
$user->updateRank();
echo "Rank: {$user->rank}"; // Should be Gold now
```

## Testing Daily Rewards

### Simulate Streak
```php
$user = User::find(1);
$user->last_login = now()->subDays(1);
$user->streak_days = 6;
$user->save();

$user->checkDailyLogin(); // Should increment to 7
echo "Streak: {$user->streak_days}";

$reward = $user->claimDailyReward();
echo "XP: {$reward->xp_earned}, Primogems: {$reward->gacha_currency_earned}";
// Day 7 should give 100 XP and 40 Primogems (2x multiplier)
```

### Test Streak Break
```php
$user = User::find(1);
$user->last_login = now()->subDays(3);
$user->streak_days = 10;
$user->save();

$user->checkDailyLogin(); // Should reset to 1
echo "Streak: {$user->streak_days}";
```

## Testing Badge System

### Test Project Badges
```php
$user = User::find(1);

// Create first project
$project = $user->projects()->create([
    'name' => 'Test Project',
    'description' => 'Testing badges',
    'xp_reward' => 100,
]);

// Should unlock "First Steps" badge
$badges = $user->badges;
echo "Badges earned: " . $badges->count();
```

## Running Automated Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProfileTest.php

# Run with coverage (if configured)
php artisan test --coverage
```

## Common Issues

### 419 Page Expired Error (CSRF Token Mismatch)

This happens when the session expires or isn't configured properly.

**Quick Fix:**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Restart the development server
# Press Ctrl+C to stop, then:
php artisan serve
```

**Then in your browser:**
1. Hard refresh the page (Ctrl+Shift+R or Ctrl+F5)
2. Clear browser cache and cookies for localhost
3. Try logging in again

**If still not working:**
- Check that `APP_KEY` is set in `.env` (run `php artisan key:generate` if empty)
- Verify sessions table exists: `php artisan tinker --execute="echo Schema::hasTable('sessions') ? 'Yes' : 'No';"`
- Check storage permissions: `storage/framework/sessions` should be writable

**Alternative: Use file-based sessions**
In `.env`, change:
```env
SESSION_DRIVER=file
```
Then clear config: `php artisan config:clear`

### Database locked
```bash
php artisan cache:clear
php artisan config:clear
# Delete database/database.sqlite and recreate
php artisan migrate:fresh --seed
```

### XP not updating
Check that the `addXP()` method is being called and saved properly.

### Badges not unlocking
Verify the `Project::booted()` method is triggering the `checkBadges()` method.

### Daily reward already claimed
The reward is tied to the date. Change the system date or wait until tomorrow.
