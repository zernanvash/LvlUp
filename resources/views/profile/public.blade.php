@extends('layouts.app')

@section('title', $user->name . "'s Profile")
@section('page_title', $user->name . "'s Profile")
@section('page_subtitle', $user->title ?? 'Developer')

@section('content')
@php
    $vis = $user->visibility_settings ?? [];
    $show = fn(string $key) => $key === 'show_email'
        ? ($vis[$key] ?? false)
        : ($vis[$key] ?? true);
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    {{-- Profile Header --}}
    <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 via-pink-900/30 to-purple-900/40 backdrop-blur relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/5 via-pink-600/5 to-purple-600/5 animate-pulse pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start gap-6">

            {{-- Avatar --}}
            <div class="flex-shrink-0">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                         class="w-28 h-28 rounded-2xl border-4 border-purple-400 shadow-2xl object-cover">
                @else
                    <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-purple-400 shadow-2xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="font-display text-3xl font-bold text-white">{{ $user->name }}</h1>
                    @auth
                        @if(Auth::id() === $user->id)
                            <a href="{{ route('profile.edit') }}"
                               class="px-3 py-1 bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/40 text-purple-300 rounded-lg text-xs font-semibold transition-colors">
                                <i class="fas fa-edit mr-1"></i> Edit Profile
                            </a>
                        @endif
                    @endauth
                </div>

                @if($user->title)
                    <p class="text-lg text-purple-300 mb-2">{{ $user->title }}</p>
                @endif

                @if($user->bio)
                    <p class="text-purple-200/80 leading-relaxed mb-4 max-w-2xl">{{ $user->bio }}</p>
                @endif

                {{-- Contact & Social --}}
                <div class="flex flex-wrap gap-3">
                    @if($show('show_email') && $user->email)
                        <span class="flex items-center gap-1.5 text-sm text-purple-300">
                            <i class="fas fa-envelope text-purple-400"></i> {{ $user->email }}
                        </span>
                    @endif
                    @if($show('show_linkedin') && $user->linkedin_url)
                        <a href="{{ $user->linkedin_url }}" target="_blank"
                           class="flex items-center gap-1.5 px-3 py-1 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-sm text-blue-300 transition-colors">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                    @endif
                    @if($show('show_github') && $user->github_url)
                        <a href="{{ $user->github_url }}" target="_blank"
                           class="flex items-center gap-1.5 px-3 py-1 bg-gray-600/20 hover:bg-gray-600/30 border border-gray-500/30 rounded-lg text-sm text-gray-300 transition-colors">
                            <i class="fab fa-github"></i> GitHub
                        </a>
                    @endif
                    @if($user->website_url)
                        <a href="{{ $user->website_url }}" target="_blank"
                           class="flex items-center gap-1.5 px-3 py-1 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-sm text-purple-300 transition-colors">
                            <i class="fas fa-globe"></i> Website
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        @if($show('show_rank'))
        <div class="relative z-10 mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-gradient-to-br from-indigo-900/50 to-indigo-950/50 p-4 rounded-xl border border-indigo-500/30 text-center">
                <div class="text-xs text-indigo-300 font-medium mb-1">Level</div>
                <div class="text-3xl font-display font-bold text-white">{{ $stats['level'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-purple-900/50 to-purple-950/50 p-4 rounded-xl border border-purple-500/30 text-center">
                <div class="text-xs text-purple-300 font-medium mb-1">Rank</div>
                <div class="text-2xl font-display font-bold text-white">{{ $stats['rank'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-blue-900/50 to-blue-950/50 p-4 rounded-xl border border-blue-500/30 text-center">
                <div class="text-xs text-blue-300 font-medium mb-1">Total XP</div>
                <div class="text-xl font-display font-bold text-white">{{ number_format($stats['total_xp']) }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-900/50 to-green-950/50 p-4 rounded-xl border border-green-500/30 text-center">
                <div class="text-xs text-green-300 font-medium mb-1">Projects</div>
                <div class="text-3xl font-display font-bold text-white">{{ $stats['total_projects'] }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Equipped Badges --}}
    @if($show('show_badges') && $user->equippedBadges->count() > 0)
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/30 to-amber-950/30 backdrop-blur">
        <h3 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-crown text-amber-400"></i> Equipped Badges
        </h3>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach($user->equippedBadges as $badge)
            @php
                $colors = ['common'=>'gray','uncommon'=>'green','rare'=>'blue','epic'=>'purple','legendary'=>'amber','mythic'=>'pink'];
                $c = $colors[$badge->rarity] ?? 'gray';
            @endphp
            <div class="group relative text-center">
                <div class="glow-border rounded-xl p-3 bg-gradient-to-br from-{{ $c }}-900/40 to-{{ $c }}-950/40 card-hover">
                    <div class="text-3xl mb-1"><i class="{{ $badge->icon }}"></i></div>
                    <div class="text-xs font-bold text-white truncate">{{ $badge->title }}</div>
                    <div class="text-xs text-{{ $c }}-300 uppercase">{{ $badge->rarity }}</div>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-black/90 rounded text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                    {{ $badge->description }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Technical Skills --}}
    @if($show('show_technical_skills') && $user->technical_skills)
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-cyan-900/30 to-cyan-950/30 backdrop-blur">
        <h3 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-code text-cyan-400"></i> Technical Skills
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach(array_filter(array_map('trim', explode(',', $user->technical_skills))) as $skill)
            <span class="px-3 py-1.5 bg-cyan-500/20 border border-cyan-500/30 rounded-full text-sm text-cyan-300 font-medium">
                {{ $skill }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Featured Projects --}}
    @if($show('show_projects') && $user->projects->count() > 0)
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <h3 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-folder-open text-purple-400"></i> Featured Projects
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($user->projects as $project)
            <div class="bg-gradient-to-br from-purple-900/30 to-purple-950/30 border border-purple-500/30 rounded-xl p-4 hover:border-purple-400/50 transition card-hover">
                @if($project->thumbnail)
                    <img src="{{ $project->thumbnail }}" alt="{{ $project->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                @endif
                <h4 class="font-display font-semibold text-white mb-1">{{ $project->name }}</h4>
                <p class="text-sm text-gray-300 mb-3 line-clamp-2">{{ $project->description }}</p>
                @if($project->language)
                    <span class="inline-block px-2 py-0.5 text-xs bg-indigo-600/40 text-indigo-200 rounded-full border border-indigo-500/30 mb-2">
                        {{ $project->language }}
                    </span>
                @endif
                <div class="flex gap-3 mt-2">
                    @if($project->url)
                        <a href="{{ $project->url }}" target="_blank" class="text-xs text-purple-400 hover:text-purple-300 transition">
                            <i class="fas fa-external-link-alt mr-1"></i>Live
                        </a>
                    @endif
                    @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-300 transition">
                            <i class="fab fa-github mr-1"></i>GitHub
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Skill Tree & Achievements --}}
    @if($show('show_skills') || $show('show_achievements'))
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @if($show('show_skills'))
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-pink-900/30 to-pink-950/30 backdrop-blur text-center">
            <i class="fas fa-network-wired text-3xl text-pink-400 mb-3"></i>
            <div class="text-sm font-bold text-white mb-2">Skill Tree Progress</div>
            <div class="text-4xl font-display font-bold text-white mb-1">{{ $stats['unlocked_nodes'] }}</div>
            <div class="text-sm text-pink-300">Nodes Unlocked</div>
        </div>
        @endif
        @if($show('show_achievements'))
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-green-900/30 to-green-950/30 backdrop-blur text-center">
            <i class="fas fa-award text-3xl text-green-400 mb-3"></i>
            <div class="text-4xl font-display font-bold text-white mb-1">{{ $stats['total_badges'] }}</div>
            <div class="text-sm text-green-300">Achievements Earned</div>
        </div>
        @endif
    </div>
    @endif

    {{-- Certifications --}}
    @if($show('show_certifications') && $user->certifications)
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-orange-900/30 to-orange-950/30 backdrop-blur">
        <h3 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-certificate text-orange-400"></i> Certifications
        </h3>
        <div class="space-y-2">
            @foreach(array_filter(explode("\n", $user->certifications)) as $cert)
            <div class="flex items-center gap-2 text-orange-200/80">
                <i class="fas fa-check-circle text-orange-400 text-sm flex-shrink-0"></i>
                <span class="text-sm">{{ trim($cert) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
