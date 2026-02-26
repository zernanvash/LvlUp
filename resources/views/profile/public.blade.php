@extends('layouts.app')

@section('title', $user->name . "'s Profile")
@section('page_title', $user->name . "'s Profile")
@section('page_subtitle', 'Public Portfolio')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- User Stats Card -->
    <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <div class="flex items-center space-x-6">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full border-4 border-purple-400 shadow-lg">
            @else
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-3xl font-bold border-4 border-purple-400 shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-2">
                    <h3 class="text-3xl font-display font-bold text-white">{{ $user->name }}</h3>
                    @auth
                        @if(Auth::id() === $user->id)
                            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-sm">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                        @endif
                    @endauth
                </div>
                @if($user->title)
                    <p class="text-lg text-purple-300 mb-2">{{ $user->title }}</p>
                @endif
                @if($user->bio)
                    <p class="text-gray-300">{{ $user->bio }}</p>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-indigo-900/50 to-indigo-950/50 p-4 rounded-xl border border-indigo-500/30">
                <div class="text-sm text-indigo-300 font-medium">Level</div>
                <div class="text-3xl font-display font-bold text-white">{{ $stats['level'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-purple-900/50 to-purple-950/50 p-4 rounded-xl border border-purple-500/30">
                <div class="text-sm text-purple-300 font-medium">Rank</div>
                <div class="text-3xl font-display font-bold text-white">{{ $stats['rank'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-blue-900/50 to-blue-950/50 p-4 rounded-xl border border-blue-500/30">
                <div class="text-sm text-blue-300 font-medium">Total XP</div>
                <div class="text-2xl font-display font-bold text-white">{{ number_format($stats['total_xp']) }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-900/50 to-green-950/50 p-4 rounded-xl border border-green-500/30">
                <div class="text-sm text-green-300 font-medium">Projects</div>
                <div class="text-3xl font-display font-bold text-white">{{ $stats['total_projects'] }}</div>
            </div>
        </div>
    </div>

    <!-- Equipped Badges -->
    @if($user->equippedBadges->count() > 0)
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <h3 class="text-xl font-display font-bold text-white mb-4">Equipped Badges</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($user->equippedBadges as $badge)
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gradient-to-br {{ $badge->getRarityColorAttribute() }} flex items-center justify-center text-white text-2xl shadow-lg">
                            <i class="{{ $badge->icon }}"></i>
                        </div>
                        <div class="text-xs font-medium text-white">{{ $badge->title }}</div>
                        <div class="text-xs text-purple-300">{{ ucfirst($badge->rarity) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Featured Projects -->
    @if($user->projects->count() > 0)
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <h3 class="text-xl font-display font-bold text-white mb-4">Featured Projects</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($user->projects as $project)
                    <div class="bg-gradient-to-br from-purple-900/30 to-purple-950/30 border border-purple-500/30 rounded-xl p-4 hover:border-purple-400/50 transition card-hover">
                        @if($project->thumbnail)
                            <img src="{{ $project->thumbnail }}" alt="{{ $project->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                        @endif
                        <h4 class="font-display font-semibold text-white mb-2">{{ $project->name }}</h4>
                        <p class="text-sm text-gray-300 mb-3 line-clamp-2">{{ $project->description }}</p>
                        @if($project->language)
                            <span class="inline-block px-3 py-1 text-xs bg-indigo-600/50 text-indigo-200 rounded-full border border-indigo-500/30">
                                {{ $project->language }}
                            </span>
                        @endif
                        <div class="mt-3 flex space-x-3">
                            @if($project->url)
                                <a href="{{ $project->url }}" target="_blank" class="text-xs text-purple-400 hover:text-purple-300 transition">
                                    <i class="fas fa-external-link-alt"></i> Live Demo
                                </a>
                            @endif
                            @if($project->github_url)
                                <a href="{{ $project->github_url }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-300 transition">
                                    <i class="fab fa-github"></i> GitHub
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Skill Tree Progress -->
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <h3 class="text-xl font-display font-bold text-white mb-4">Skill Tree Progress</h3>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-4xl font-display font-bold text-indigo-400">{{ $stats['unlocked_nodes'] }}</div>
                <div class="text-sm text-gray-300">Nodes Unlocked</div>
            </div>
            <div>
                <div class="text-4xl font-display font-bold text-purple-400">{{ $stats['total_badges'] }}</div>
                <div class="text-sm text-gray-300">Badges Earned</div>
            </div>
            <a href="{{ route('skill-tree.index') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition shadow-lg btn-glow">
                <i class="fas fa-network-wired mr-2"></i> View Skill Tree
            </a>
        </div>
    </div>

</div>
@endsection