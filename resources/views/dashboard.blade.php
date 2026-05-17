@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Command Center')
@section('page_subtitle', 'Your portfolio progression at a glance')

@section('content')
@php
    $user = auth()->user();
    $totalNodes = \App\Models\SkillNode::count();
    $unlockedNodes = $user->unlockedNodes->count();
    $skillProgress = $totalNodes > 0 ? ($unlockedNodes / $totalNodes) * 100 : 0;
    $badges = $user->badges;
    $equippedBadges = $user->badges()->wherePivot('is_displayed', true)->orderBy('user_badges.created_at', 'desc')->limit(6)->get();
    $latestBadges = $badges->sortByDesc('pivot.earned_at')->take(4);
    $topSkills = $user->unlockedNodes->map(fn($node) => $node->skill)->filter()->unique('id')->take(4);
    $streak = $user->streak_days ?? 0;
@endphp

<div class="mx-auto max-w-7xl space-y-5" x-data="{ guideOpen: false }">
    @if($showMilestoneBanner)
    <div class="lvl-panel lvl-panel-tight flex flex-col gap-3 border-l-4 sm:flex-row sm:items-center" style="border-left-color: var(--lvl-gold) !important;">
        <div class="h-10 w-10 rounded-lg bg-[#faeeda] border border-[#f3cf91] text-[#754706] flex items-center justify-center">
            <i class="fas fa-bolt"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-[var(--lvl-text)]">You are {{ $xpToNextLevel }} XP away from Level {{ $user->level + 1 }}</p>
            <p class="text-xs text-[var(--lvl-muted)]">Add or improve a project to push your progression forward.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="lvl-action inline-flex items-center justify-center gap-2 rounded-lg bg-[var(--lvl-p600)] px-4 py-2 text-sm font-bold text-white hover:bg-[var(--lvl-p800)] transition">
            <i class="fas fa-plus"></i>
            New Project
        </a>
    </div>
    @endif

    <section class="grid gap-4 lg:grid-cols-[1.35fr_.65fr]">
        <div class="lvl-panel p-5 border-l-4" style="border-left-color: var(--lvl-p400) !important;">
            <div class="flex flex-col gap-5 md:flex-row md:items-center">
                <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-full bg-[var(--lvl-p50)] border-2 border-[var(--lvl-p100)] text-3xl font-black text-[var(--lvl-p800)]">
                    {{ strtoupper(substr($user->getRankTitle(), 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="lvl-label">Current rank</p>
                            <h2 class="text-2xl font-black text-[var(--lvl-text)]">Level {{ $user->level }} - {{ $user->getRankTitle() }}</h2>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="lvl-chip gold"><i class="fas fa-fire"></i>{{ $streak }} day streak</span>
                            @if($streakBonusActive)
                            <span class="lvl-chip green">+{{ number_format(($streakBonusMultiplier - 1) * 100) }}% XP bonus</span>
                            @else
                            <span class="lvl-chip gray">Bonus inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="mb-1 flex justify-between text-xs font-semibold text-[var(--lvl-muted)]">
                            <span>{{ number_format($user->xp) }} / {{ number_format($user->xpNeededForNextLevel()) }} XP</span>
                            <button @click="guideOpen = true" class="text-[var(--lvl-p600)] hover:text-[var(--lvl-p800)]">Progression guide</button>
                        </div>
                        <div class="lvl-xp-bg h-3">
                            <div class="lvl-xp-fill" style="width: {{ $user->xpProgress() }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="lvl-panel p-4 text-center">
                <p class="lvl-label">Projects</p>
                <p class="mt-1 text-3xl font-black text-[var(--lvl-text)]">{{ $projects->count() }}</p>
                <p class="text-xs text-[var(--lvl-muted)]">Portfolio entries</p>
            </div>
            <div class="lvl-panel p-4 text-center">
                <p class="lvl-label">Badges</p>
                <p class="mt-1 text-3xl font-black text-[var(--lvl-text)]">{{ $badges->count() }}</p>
                <p class="text-xs text-[var(--lvl-muted)]">{{ $equippedBadges->count() }} equipped</p>
            </div>
            <div class="lvl-panel p-4 text-center">
                <p class="lvl-label">Skills</p>
                <p class="mt-1 text-3xl font-black text-[var(--lvl-text)]">{{ $unlockedNodes }}</p>
                <p class="text-xs text-[var(--lvl-muted)]">of {{ $totalNodes }} nodes</p>
            </div>
            <div class="lvl-panel p-4 text-center">
                <p class="lvl-label">Total XP</p>
                <p class="mt-1 text-3xl font-black text-[var(--lvl-text)]">{{ number_format($user->total_xp) }}</p>
                <p class="text-xs text-[var(--lvl-muted)]">Lifetime earned</p>
            </div>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2">
        <div class="lvl-panel p-5">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <p class="lvl-label">Skill radar</p>
                    <h3 class="text-base font-bold text-[var(--lvl-text)]">Unlocked capability map</h3>
                </div>
                <span class="lvl-chip">{{ number_format($skillProgress, 1) }}%</span>
            </div>

            @if($topSkills->count())
                <div class="space-y-3">
                    @foreach($topSkills as $index => $skill)
                    @php $width = max(30, 95 - ($index * 13)); @endphp
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-[var(--lvl-p50)] border border-[var(--lvl-p100)] text-[var(--lvl-p600)] flex items-center justify-center">
                            <i class="{{ $skill->icon ?? 'fas fa-code' }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-1 flex justify-between gap-2">
                                <p class="truncate text-sm font-bold text-[var(--lvl-text)]">{{ $skill->name }}</p>
                                <span class="text-xs font-bold text-[var(--lvl-p600)]">{{ $width }}%</span>
                            </div>
                            <div class="lvl-xp-bg h-1.5"><div class="lvl-xp-fill" style="width: {{ $width }}%;"></div></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-[var(--lvl-border)] bg-[var(--lvl-surface-soft)] p-6 text-center">
                    <p class="text-sm font-bold text-[var(--lvl-text)]">No skill nodes unlocked yet</p>
                    <a href="{{ route('skill-tree.index') }}" class="mt-2 inline-flex text-sm font-bold text-[var(--lvl-p600)]">Open the skill tree</a>
                </div>
            @endif
        </div>

        <div class="lvl-panel p-5">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <p class="lvl-label">Activity</p>
                    <h3 class="text-base font-bold text-[var(--lvl-text)]">Recent contribution pulse</h3>
                </div>
                <span class="lvl-chip gray">Last 5 weeks</span>
            </div>

            <div class="grid grid-cols-7 gap-1.5">
                @foreach([1,0,2,3,1,0,4,2,3,4,1,0,2,3,0,1,4,4,3,2,1,3,4,2,1,0,3,4,1,2,4,3,2,1,4] as $dot)
                <span class="h-4 rounded-[4px] border {{ $dot === 0 ? 'bg-[var(--lvl-surface-soft)] border-[var(--lvl-border-soft)]' : '' }}"
                    style="@if($dot === 1) background:#cecbf6; border-color:#cecbf6; @elseif($dot === 2) background:#afa9ec; border-color:#afa9ec; @elseif($dot === 3) background:#7f77dd; border-color:#7f77dd; @elseif($dot === 4) background:#534ab7; border-color:#534ab7; @endif"></span>
                @endforeach
            </div>

            <div class="mt-4 grid grid-cols-7 items-end gap-1.5 h-16">
                @foreach([32, 48, 42, 85, 61, 55, 100] as $bar)
                <span class="rounded-t bg-[var(--lvl-p200)]" style="height: {{ $bar }}%; @if($bar > 80) background: var(--lvl-p600); @endif"></span>
                @endforeach
            </div>
            <div class="mt-2 flex justify-between text-[11px] font-semibold text-[var(--lvl-faint)]">
                <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
            </div>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.2fr_.8fr]">
        <div class="lvl-panel p-5">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="lvl-label">Projects</p>
                    <h3 class="text-base font-bold text-[var(--lvl-text)]">Latest portfolio work</h3>
                </div>
                <a href="{{ route('projects.create') }}" class="lvl-action inline-flex items-center justify-center gap-2 rounded-lg bg-[var(--lvl-p600)] px-4 py-2 text-sm font-bold text-white hover:bg-[var(--lvl-p800)] transition">
                    <i class="fas fa-plus"></i>
                    New project
                </a>
            </div>

            @if($projects->isEmpty())
                <div class="rounded-lg border border-dashed border-[var(--lvl-border)] bg-[var(--lvl-surface-soft)] p-8 text-center">
                    <div class="mx-auto mb-3 h-14 w-14 rounded-full bg-[var(--lvl-p50)] border border-[var(--lvl-p100)] text-[var(--lvl-p600)] flex items-center justify-center">
                        <i class="fas fa-folder-plus text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-[var(--lvl-text)]">Begin your portfolio</p>
                    <p class="mt-1 text-sm text-[var(--lvl-muted)]">Create your first project and start earning XP.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($projects->take(4) as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block rounded-lg border p-4 transition" style="border-color:var(--lvl-border-soft);background:var(--lvl-surface-raised);" onmouseover="this.style.borderColor='var(--lvl-p100)'" onmouseout="this.style.borderColor='var(--lvl-border-soft)'">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-[var(--lvl-text)]">{{ $project->name }}</h4>
                                    @if($project->is_featured)
                                    <span class="lvl-chip gold">Featured</span>
                                    @endif
                                </div>
                                <p class="mt-1 line-clamp-2 text-sm text-[var(--lvl-muted)]">{{ $project->description ?? 'No description yet.' }}</p>
                                @if($project->skills->count())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($project->skills->take(4) as $skill)
                                    <span class="lvl-chip gray">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-2 sm:flex-col sm:items-end">
                                <span class="lvl-chip">+{{ $project->xp_reward }} XP</span>
                                <span class="text-xs font-semibold text-[var(--lvl-faint)]">{{ $project->created_at->diffForHumans(null, true) }} ago</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="lvl-panel p-5">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="lvl-label">Equipped badges</p>
                        <h3 class="text-base font-bold text-[var(--lvl-text)]">Profile loadout</h3>
                    </div>
                    <a href="{{ route('achievements.index') }}" class="text-sm font-bold text-[var(--lvl-p600)] hover:text-[var(--lvl-p800)]">Manage</a>
                </div>
                @if($equippedBadges->count())
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-6 xl:grid-cols-3">
                        @foreach($equippedBadges as $badge)
                        <div class="rounded-lg border p-3 text-center" style="border-color:var(--lvl-border-soft);background:var(--lvl-surface-raised);">
                            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-[var(--lvl-p50)] border border-[var(--lvl-p100)] text-[var(--lvl-p600)] flex items-center justify-center">
                                <i class="{{ $badge->icon }}"></i>
                            </div>
                            <p class="truncate text-xs font-bold text-[var(--lvl-text)]">{{ $badge->title }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="rounded-lg bg-[var(--lvl-surface-soft)] p-4 text-sm text-[var(--lvl-muted)]">Earn and equip badges to personalize your public profile.</p>
                @endif
            </div>

            <div class="lvl-panel p-5">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="lvl-label">Next routes</p>
                        <h3 class="text-base font-bold text-[var(--lvl-text)]">Quick actions</h3>
                    </div>
                </div>
                <div class="grid gap-2">
                    <a href="{{ route('skill-tree.index') }}" class="lvl-nav-link active"><i class="fas fa-network-wired"></i><span>Explore skill tree</span></a>
                    <a href="{{ route('resume.index') }}" class="lvl-nav-link"><i class="fas fa-file-alt"></i><span>Build resume</span></a>
                    <a href="{{ route('projects.index') }}" class="lvl-nav-link"><i class="fas fa-folder-open"></i><span>Manage projects</span></a>
                </div>
            </div>
        </div>
    </section>

    @if($latestBadges->count())
    <section class="lvl-panel p-5">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <p class="lvl-label">Achievements</p>
                <h3 class="text-base font-bold text-[var(--lvl-text)]">Recently unlocked</h3>
            </div>
            <a href="{{ route('achievements.index') }}" class="text-sm font-bold text-[var(--lvl-p600)] hover:text-[var(--lvl-p800)]">View all</a>
        </div>
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach($latestBadges as $badge)
            <div class="rounded-lg border p-4" style="border-color:var(--lvl-border-soft);background:var(--lvl-surface-raised);">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-full bg-[var(--lvl-p50)] border border-[var(--lvl-p100)] text-[var(--lvl-p600)] flex items-center justify-center">
                        <i class="{{ $badge->icon }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-[var(--lvl-text)]">{{ $badge->title }}</p>
                        <p class="text-xs text-[var(--lvl-muted)]">{{ ucfirst($badge->rarity) }} - +{{ $badge->xp_reward }} XP</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <div x-show="guideOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-[#26215c]/40 backdrop-blur-sm" @click="guideOpen = false"></div>
        <div class="lvl-panel relative z-10 w-full max-w-lg p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="lvl-label">Progression guide</p>
                    <h2 class="text-xl font-black text-[var(--lvl-text)]">How XP moves you up</h2>
                </div>
                <button @click="guideOpen = false" class="h-9 w-9 rounded-lg border text-[var(--lvl-muted)]" style="border-color:var(--lvl-border-soft);background:var(--lvl-surface-soft);">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <p class="mb-2 font-bold text-[var(--lvl-text)]">XP sources</p>
                    <div class="divide-y rounded-lg border" style="divide-color:var(--lvl-border-soft);border-color:var(--lvl-border-soft);background:var(--lvl-surface-raised);">
                        <div class="flex justify-between p-3"><span>Create a project</span><strong>100 XP base</strong></div>
                        <div class="flex justify-between p-3"><span>Project code depth</span><strong>up to +400 XP</strong></div>
                        <div class="flex justify-between p-3"><span>Rare badge</span><strong>150 XP</strong></div>
                        <div class="flex justify-between p-3"><span>Legendary badge</span><strong>500 XP</strong></div>
                    </div>
                </div>
                <div>
                    <p class="mb-2 font-bold text-[var(--lvl-text)]">Streak bonus</p>
                    <p class="text-[var(--lvl-muted)]">Add or edit a project, or unlock a skill node, once per day to keep your streak alive. Longer streaks increase XP rewards.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
