@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Command Center')
@section('page_subtitle', 'Your knowledge empire at a glance')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- XP Progress Section -->
    <div
        class="glow-border rounded-3xl p-5 bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-purple-900/40 backdrop-blur relative overflow-hidden">
        <!-- Animated Background -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-purple-600/10 via-pink-600/10 to-purple-600/10 animate-pulse">
        </div>

        <div class="flex items-center gap-3">
            {{-- Left: Level + XP --}}
            <div class="text-sm whitespace-nowrap">
                <span class="font-semibold">Level {{ auth()->user()->level }} {{ auth()->user()->rank }}</span><br>
                <span class="text-xs text-gray-400">
                    {{ number_format(auth()->user()->xp) }} /
                    {{ number_format(auth()->user()->xpNeededForNextLevel()) }} XP
                    ({{ number_format(auth()->user()->xpProgress(), 1) }}%)
                </span>
            </div>

            {{-- Middle: Progress Bar --}}
            <div class="flex-1">
                <div class="w-full bg-gradient-to-r from-purple-600/20 to-pink-600/20 ">
                    <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 rounded-full h-2 transition-all duration-1000 ease-out" style="width: {{ auth()->user()->xpProgress() }}%"></div>
                </div>
                <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2">
                    <span class="font-display font-bold text-white text-sm drop-shadow-lg">
                        {{ number_format(auth()->user()->xpProgress(), 1) }}%
                    </span>
                </div>
            </div>

            {{-- Right: Next Level --}}
            <div class="text-sm text-right whitespace-nowrap">
                <span class="font-semibold">Next Level</span><br>
                <span class="text-xs text-gray-400">
                    {{ number_format(auth()->user()->xpNeededForNextLevel() - auth()->user()->xp) }} XP
                    to Level {{ auth()->user()->level + 1 }}
                </span>
            </div>
        </div>
    </div>

    <!-- Projects Section -->
    <div>
        <div class="flex items-top justify-between mb-2">
            <div>
                <h2 class="font-display text-2xl font-bold text-white mb-1">Your Projects</h2>
            </div>
            <a href="/projects/create"
                class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-6 py-3 rounded-xl font-bold shadow-lg transition">
                <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    New Project
                </span>
            </a>
        </div>

        @if($projects->isEmpty())
        <!-- Empty State -->
        <div
            class="glow-border rounded-3xl p-12 text-center bg-gradient-to-br from-purple-900/20 to-pink-900/20 backdrop-blur">
            <div
                class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full flex items-center justify-center">
                <i class="fas fa-rocket text-6xl text-purple-400"></i>
            </div>
            <h3 class="font-display text-2xl font-bold text-white mb-2">Begin Your Journey</h3>
            <p class="text-purple-300 mb-8 max-w-md mx-auto">
                Create your first project and start earning XP! Every project brings you closer to mastery.
            </p>
            <a href="/projects/create"
                class="inline-flex items-center gap-2 btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-8 py-4 rounded-xl font-bold shadow-lg transition">
                <span class="relative z-10">Start Your First Project</span>
            </a>
        </div>
        @else
        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
            <div class="group relative">
                <!-- Rarity Glow based on XP reward -->
                @php
                $rarity = match(true) {
                $project->xp_reward >= 500 => 'legendary',
                $project->xp_reward >= 300 => 'epic',
                $project->xp_reward >= 200 => 'rare',
                default => 'common'
                };
                @endphp

                <div
                    class="glow-border rounded-2xl overflow-hidden bg-gradient-to-br from-[#2d1b4e]/80 to-[#1a1d3e]/80 backdrop-blur card-hover rarity-{{ $rarity }} border-2 border-{{ $rarity === 'legendary' ? 'amber' : ($rarity === 'epic' ? 'purple' : ($rarity === 'rare' ? 'blue' : 'gray')) }}-500/30">
                    <!-- Project Header -->
                    <div
                        class="relative h-48 bg-gradient-to-br from-purple-600/20 to-pink-600/20 border-b-2 border-white/10 overflow-hidden">
                        @if($project->thumbnail)
                        <img src="{{ $project->thumbnail }}" class="w-full h-full object-cover opacity-50"
                            alt="{{ $project->name }}">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-code text-6xl text-white/20"></i>
                        </div>
                        @endif

                        <!-- Rarity Stars -->
                        <div class="absolute top-4 right-4 flex gap-1">
                            @for($i = 0; $i < (match($rarity) { 'legendary'=> 5, 'epic' => 4, 'rare' => 3, default => 2
                                }); $i++)
                                <i
                                    class="fas fa-star text-{{ $rarity === 'legendary' ? 'amber' : ($rarity === 'epic' ? 'purple' : ($rarity === 'rare' ? 'blue' : 'gray')) }}-400 text-sm"></i>
                                @endfor
                        </div>

                        <!-- Project ID Badge -->
                        <div class="absolute bottom-4 left-4 bg-black/60 backdrop-blur px-3 py-1 rounded-lg">
                            <span class="font-mono text-xs text-purple-300">ID:
                                {{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>

                    <!-- Project Content -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-display font-bold text-white text-lg flex-1">{{ $project->name }}</h3>
                            @if($project->is_featured)
                            <span
                                class="flex-shrink-0 ml-2 px-2 py-1 bg-amber-500/20 border border-amber-500/30 rounded text-xs text-amber-300">
                                <i class="fas fa-star"></i> Featured
                            </span>
                            @endif
                        </div>

                        <p class="text-sm text-purple-200/70 mb-4 line-clamp-3 leading-relaxed">
                            {{ $project->description ?? 'No description available.' }}
                        </p>

                        <!-- Skills Tags -->
                        @if($project->skills->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($project->skills->take(3) as $skill)
                            <span
                                class="px-3 py-1 bg-{{ $skill->rarity === 'legendary' ? 'amber' : ($skill->rarity === 'epic' ? 'purple' : 'blue') }}-500/20 border border-{{ $skill->rarity === 'legendary' ? 'amber' : ($skill->rarity === 'epic' ? 'purple' : 'blue') }}-500/30 rounded-lg text-xs font-bold text-{{ $skill->rarity === 'legendary' ? 'amber' : ($skill->rarity === 'epic' ? 'purple' : 'blue') }}-300">
                                <i class="{{ $skill->icon }}"></i> {{ $skill->name }}
                            </span>
                            @endforeach
                            @if($project->skills->count() > 3)
                            <span
                                class="px-3 py-1 bg-purple-500/10 border border-purple-500/20 rounded-lg text-xs text-purple-400">
                                +{{ $project->skills->count() - 3 }} more
                            </span>
                            @endif
                        </div>
                        @endif

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-4 border-t-2 border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 text-amber-400">
                                    <i class="fas fa-code"></i>
                                    <span class="text-xs font-bold uppercase">{{ $project->language }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-purple-500/20 px-3 py-1 rounded-lg">
                                <i class="fas fa-bolt text-purple-400"></i>
                                <span class="font-display text-sm font-bold text-purple-300">+{{ $project->xp_reward }}
                                    XP</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Action Overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-8 gap-3">
                        <a href="/projects/{{ $project->id }}"
                            class="btn-glow bg-purple-600 hover:bg-purple-500 px-6 py-3 rounded-xl font-bold shadow-lg transition">
                            <span class="relative z-10">View Details</span>
                        </a>
                        @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank"
                            class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-xl font-bold shadow-lg transition">
                            <i class="fab fa-github"></i> GitHub
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    
    <!-- Equipped Badges Section -->
    @php
    $equippedBadges = auth()->user()->badges()->wherePivot('is_displayed', true)->orderBy('user_badges.created_at',
    'desc')->limit(6)->get();
    @endphp
    @if($equippedBadges->count() > 0)
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/30 to-amber-950/30 backdrop-blur">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-trophy text-amber-400"></i>
                Equipped Badges
            </h3>
            <a href="{{ route('achievements.index') }}" class="text-sm text-amber-300 hover:text-amber-200 transition">
                Manage <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($equippedBadges as $badge)
            <div class="group relative">
                <div
                    class="glow-border rounded-xl p-4 bg-gradient-to-br from-{{ $badge->rarity === 'legendary' ? 'amber' : ($badge->rarity === 'epic' ? 'purple' : ($badge->rarity === 'rare' ? 'blue' : 'gray')) }}-900/40 to-{{ $badge->rarity === 'legendary' ? 'amber' : ($badge->rarity === 'epic' ? 'purple' : ($badge->rarity === 'rare' ? 'blue' : 'gray')) }}-950/40 backdrop-blur text-center card-hover">
                    <div class="text-4xl mb-2">{{ $badge->icon }}</div>
                    <div class="text-xs font-bold text-white truncate">{{ $badge->title }}</div>
                    <div
                        class="text-xs text-{{ $badge->rarity === 'legendary' ? 'amber' : ($badge->rarity === 'epic' ? 'purple' : ($badge->rarity === 'rare' ? 'blue' : 'gray')) }}-300 uppercase mt-1">
                        {{ $badge->rarity }}</div>
                </div>
                <!-- Tooltip -->
                <div
                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-black/90 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                    {{ $badge->description }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Links Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('skill-tree.index') }}"
            class="group glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur card-hover relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-br from-purple-600/0 to-purple-600/20 group-hover:from-purple-600/10 group-hover:to-purple-600/30 transition-all">
            </div>
            <div class="relative z-10">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-network-wired text-3xl text-white"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-white mb-2">Skill Tree</h3>
                <p class="text-sm text-purple-300 mb-4">
                    {{ auth()->user()->unlockedNodes->count() }} nodes unlocked
                </p>
                <div class="flex items-center text-purple-400 text-sm font-bold">
                    Explore <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('achievements.index') }}"
            class="group glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/40 to-amber-950/40 backdrop-blur card-hover relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-br from-amber-600/0 to-amber-600/20 group-hover:from-amber-600/10 group-hover:to-amber-600/30 transition-all">
            </div>
            <div class="relative z-10">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-trophy text-3xl text-white"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-white mb-2">Achievements</h3>
                <p class="text-sm text-amber-300 mb-4">
                    {{ auth()->user()->badges->count() }} badges earned
                </p>
                <div class="flex items-center text-amber-400 text-sm font-bold">
                    View All <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('projects.index') }}"
            class="group glow-border rounded-2xl p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur card-hover relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-br from-blue-600/0 to-blue-600/20 group-hover:from-blue-600/10 group-hover:to-blue-600/30 transition-all">
            </div>
            <div class="relative z-10">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-folder text-3xl text-white"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-white mb-2">Projects</h3>
                <p class="text-sm text-blue-300 mb-4">
                    {{ $projects->count() }} projects created
                </p>
                <div class="flex items-center text-blue-400 text-sm font-bold">
                    Manage <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Skill Tree Progress Summary -->
    @php
    $totalNodes = \App\Models\SkillNode::count();
    $unlockedNodes = auth()->user()->unlockedNodes->count();
    $progressPercentage = $totalNodes > 0 ? ($unlockedNodes / $totalNodes) * 100 : 0;
    @endphp
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-pink-900/30 to-pink-950/30 backdrop-blur">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-display text-xl font-bold text-white flex items-center gap-2 mb-1">
                    <i class="fas fa-network-wired text-pink-400"></i>
                    Skill Tree Progress
                </h3>
                <p class="text-sm text-pink-300">{{ $unlockedNodes }} of {{ $totalNodes }} nodes unlocked</p>
            </div>
            <a href="{{ route('skill-tree.index') }}"
                class="btn-glow bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-500 hover:to-purple-500 px-4 py-2 rounded-xl font-bold text-sm shadow-lg transition">
                <span class="relative z-10">View Tree</span>
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="relative h-6 bg-black/40 rounded-full overflow-hidden border-2 border-pink-500/30 mb-4">
            <div class="absolute inset-0 bg-gradient-to-r from-pink-600/20 to-purple-600/20"></div>
            <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-pink-500 via-purple-500 to-pink-500 rounded-full transition-all duration-1000 ease-out"
                style="width: {{ $progressPercentage }}%">
                <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent animate-pulse"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="font-display font-bold text-white text-xs drop-shadow-lg">
                    {{ number_format($progressPercentage, 1) }}%
                </span>
            </div>
        </div>

        <!-- Node Breakdown by Tier -->
        @php
        $nodesByTier = auth()->user()->unlockedNodes->groupBy('tier');
        @endphp
        <div class="grid grid-cols-5 gap-2">
            @for($tier = 1; $tier <= 5; $tier++) @php $tierCount=$nodesByTier->get($tier, collect())->count();
                $tierTotal = \App\Models\SkillNode::where('tier', $tier)->count();
                @endphp
                <div class="text-center p-3 bg-black/30 rounded-lg border border-pink-500/20">
                    <div class="text-xs text-pink-300 mb-1">Tier {{ $tier }}</div>
                    <div class="font-display font-bold text-white">{{ $tierCount }}/{{ $tierTotal }}</div>
                </div>
                @endfor
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total XP -->
        <div
            class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur card-hover">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bolt text-2xl text-white"></i>
                </div>
                <div class="flex items-center gap-1 text-green-400">
                    <i class="fas fa-arrow-up text-xs"></i>
                    <span class="text-xs font-bold">+15%</span>
                </div>
            </div>
            <h3 class="text-3xl font-display font-bold text-white mb-1">{{ number_format(auth()->user()->total_xp) }}
            </h3>
            <p class="text-sm text-purple-300">Total Experience</p>
        </div>

        <!-- Projects Count -->
        <div
            class="glow-border rounded-2xl p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur card-hover">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-folder text-2xl text-white"></i>
                </div>
                <div class="flex items-center gap-1 text-green-400">
                    <i class="fas fa-arrow-up text-xs"></i>
                    <span class="text-xs font-bold">New</span>
                </div>
            </div>
            <h3 class="text-3xl font-display font-bold text-white mb-1">{{ $projects->count() }}</h3>
            <p class="text-sm text-blue-300">Active Projects</p>
        </div>

        <!-- Skills Unlocked -->
        <div
            class="glow-border rounded-2xl p-6 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur card-hover">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-network-wired text-2xl text-white"></i>
                </div>
                <div class="flex items-center gap-1 text-pink-400">
                    <i class="fas fa-star text-xs"></i>
                    <span class="text-xs font-bold">{{ auth()->user()->skill_points }}</span>
                </div>
            </div>
            <h3 class="text-3xl font-display font-bold text-white mb-1">{{ auth()->user()->unlockedNodes->count() }}
            </h3>
            <p class="text-sm text-pink-300">Skills Mastered</p>
        </div>

        <!-- Achievement Progress -->
        <div
            class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/40 to-amber-950/40 backdrop-blur card-hover">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-trophy text-2xl text-white"></i>
                </div>
                <div class="flex items-center gap-1 text-amber-400">
                    <i class="fas fa-award text-xs"></i>
                    <span class="text-xs font-bold">{{ auth()->user()->rank }}</span>
                </div>
            </div>
            <h3 class="text-3xl font-display font-bold text-white mb-1">{{ auth()->user()->badges->count() }}</h3>
            <p class="text-sm text-amber-300">Achievements</p>
        </div>
    </div>

    <!-- Recent Achievements -->
    @if(auth()->user()->badges->count() > 0)
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-display text-2xl font-bold text-white mb-1">Recent Achievements</h2>
                <p class="text-purple-300">Your latest accomplishments</p>
            </div>
            <a href="{{ route('achievements.index') }}"
                class="text-sm text-purple-300 hover:text-purple-200 transition">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(auth()->user()->badges->sortByDesc('pivot.earned_at')->take(3) as $badge)
            <div
                class="glow-border rounded-2xl p-6 bg-gradient-to-br from-[#2d1b4e]/60 to-[#1a1d3e]/60 backdrop-blur card-hover rarity-{{ $badge->rarity }}">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-{{ $badge->rarity === 'legendary' ? 'amber' : ($badge->rarity === 'epic' ? 'purple' : 'blue') }}-500/20 to-{{ $badge->rarity === 'legendary' ? 'amber' : ($badge->rarity === 'epic' ? 'purple' : 'blue') }}-600/20 rounded-2xl flex items-center justify-center text-3xl shadow-lg">
                        {{ $badge->icon }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white mb-1">{{ $badge->title }}</h3>
                        <p class="text-xs text-purple-300 line-clamp-2">{{ $badge->description }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span
                                class="text-xs font-bold text-{{ $badge->rarity === 'legendary' ? 'amber' : ($badge->rarity === 'epic' ? 'purple' : 'blue') }}-300 uppercase">{{ $badge->rarity }}</span>
                            <span class="text-xs text-purple-400">•
                                {{ \Carbon\Carbon::parse($badge->pivot->earned_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection