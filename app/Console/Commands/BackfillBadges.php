<?php

namespace App\Console\Commands;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillBadges extends Command
{
    protected $signature = 'badges:backfill';
    protected $description = 'Clean orphaned user_badges and retroactively award earned badges';

    public function handle(): void
    {
        // Remove rows pointing to deleted badge IDs
        $validIds = Badge::pluck('id')->toArray();
        $deleted = DB::table('user_badges')->whereNotIn('badge_id', $validIds)->delete();
        $this->info("Removed {$deleted} orphaned user_badge rows.");

        // Retroactively award badges to every user
        $users = User::with('projects.skills', 'badges', 'unlockedNodes')->get();

        foreach ($users as $user) {
            $awarded = 0;
            foreach (Badge::all() as $badge) {
                if ($user->badges->contains($badge->id)) {
                    continue;
                }
                if ($badge->checkEligibility($user)) {
                    $user->badges()->attach($badge->id, [
                        'earned_at'    => now(),
                        'is_displayed' => false,
                    ]);
                    $awarded++;
                    $user->load('badges');
                }
            }
            $this->line("  {$user->name}: +{$awarded} badges");
        }

        $this->info('Done.');
    }
}
