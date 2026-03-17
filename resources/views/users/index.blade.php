@extends('layouts.app')

@section('title', 'Discover Developers')
@section('page_title', 'Discover')
@section('page_subtitle', 'Find and explore other developers')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-purple-400 pointer-events-none"></i>
            <input
                type="text"
                name="q"
                value="{{ $query }}"
                placeholder="Search by name, title, skills..."
                class="w-full pl-11 pr-4 py-3 bg-[#1a1d3e]/80 border-2 border-purple-500/30 rounded-xl text-white placeholder-purple-400/50 focus:outline-none focus:border-purple-400 transition"
                autofocus
            >
        </div>

        {{-- Rank filter --}}
        <select name="rank"
            class="px-4 py-3 bg-[#1a1d3e]/80 border-2 border-purple-500/30 rounded-xl text-white focus:outline-none focus:border-purple-400 transition">
            <option value="" {{ $rankFilter === '' ? 'selected' : '' }}>All Ranks</option>
            @foreach($ranks as $rank)
                <option value="{{ $rank }}" {{ $rankFilter === $rank ? 'selected' : '' }}>{{ $rank }}</option>
            @endforeach
        </select>

        {{-- Sort --}}
        <select name="sort"
            class="px-4 py-3 bg-[#1a1d3e]/80 border-2 border-purple-500/30 rounded-xl text-white focus:outline-none focus:border-purple-400 transition">
            <option value="level"   {{ $sortBy === 'level'    ? 'selected' : '' }}>Sort: Level</option>
            <option value="projects" {{ $sortBy === 'projects' ? 'selected' : '' }}>Sort: Projects</option>
            <option value="badges"  {{ $sortBy === 'badges'   ? 'selected' : '' }}>Sort: Badges</option>
            <option value="name"    {{ $sortBy === 'name'     ? 'selected' : '' }}>Sort: Name</option>
        </select>

        <button type="submit"
            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl font-bold transition shadow-lg">
            Search
        </button>

        @if($query || $rankFilter)
        <a href="{{ route('users.index') }}"
            class="px-4 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-gray-400 hover:text-white transition text-center">
            <i class="fas fa-times"></i>
        </a>
        @endif
    </form>

    {{-- Results count --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-purple-300">
            @if($query)
                <span class="text-white font-semibold">{{ $users->total() }}</span> result{{ $users->total() !== 1 ? 's' : '' }} for "<span class="text-purple-200">{{ $query }}</span>"
            @else
                <span class="text-white font-semibold">{{ $users->total() }}</span> public developer{{ $users->total() !== 1 ? 's' : '' }}
            @endif
        </p>
    </div>

    {{-- User Cards Grid --}}
    @if($users->isEmpty())
    <div class="glow-border rounded-2xl p-16 text-center bg-gradient-to-br from-purple-900/20 to-pink-900/20 backdrop-blur">
        <i class="fas fa-user-slash text-5xl text-purple-400/40 mb-4"></i>
        <h3 class="font-display text-xl font-bold text-white mb-2">No developers found</h3>
        <p class="text-purple-300 text-sm">Try a different search or remove filters.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($users as $user)
        @php
            $rankColors = [
                'Bronze'   => ['border' => 'border-amber-700/50',  'badge' => 'from-amber-800/60 to-amber-900/60',  'text' => 'text-amber-400'],
                'Silver'   => ['border' => 'border-gray-400/50',   'badge' => 'from-gray-700/60 to-gray-800/60',    'text' => 'text-gray-300'],
                'Gold'     => ['border' => 'border-yellow-400/60', 'badge' => 'from-yellow-700/60 to-yellow-900/60','text' => 'text-yellow-300'],
                'Platinum' => ['border' => 'border-cyan-400/50',   'badge' => 'from-cyan-800/60 to-cyan-900/60',    'text' => 'text-cyan-300'],
                'Diamond'  => ['border' => 'border-blue-400/60',   'badge' => 'from-blue-700/60 to-blue-900/60',    'text' => 'text-blue-300'],
                'Master'   => ['border' => 'border-purple-400/70', 'badge' => 'from-purple-700/60 to-pink-900/60',  'text' => 'text-purple-300'],
            ];
            $rc = $rankColors[$user->rank] ?? $rankColors['Bronze'];
        @endphp
        <a href="{{ route('profile.public', $user->name) }}"
            class="group block glow-border rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e1b4b]/80 to-[#1a1d3e]/80 backdrop-blur border-2 {{ $rc['border'] }} hover:scale-[1.02] transition-transform duration-200 card-hover">

            {{-- Card top accent --}}
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-purple-500/60 to-transparent group-hover:via-pink-500/80 transition-all"></div>

            <div class="p-5">
                {{-- Avatar + Level --}}
                <div class="flex items-start gap-4 mb-4">
                    <div class="relative flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                class="w-14 h-14 rounded-xl border-2 border-purple-400/50 object-cover">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-bold border-2 border-purple-400/50">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        {{-- Level badge --}}
                        <div class="level-badge absolute -bottom-2 -right-2 w-7 h-7 flex items-center justify-center">
                            <span class="font-display text-[10px] font-bold text-white">{{ $user->level }}</span>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="font-display font-bold text-white text-base truncate group-hover:text-purple-200 transition">
                            {{ $user->name }}
                        </h3>
                        @if($user->title)
                            <p class="text-xs text-purple-300 truncate">{{ $user->title }}</p>
                        @endif
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gradient-to-r {{ $rc['badge'] }} {{ $rc['text'] }} border border-current/20">
                            {{ $user->rank }}
                        </span>
                    </div>
                </div>

                {{-- Bio snippet --}}
                @if($user->bio)
                <p class="text-xs text-gray-400 line-clamp-2 mb-4 leading-relaxed">{{ $user->bio }}</p>
                @endif

                {{-- Stats row --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center bg-black/20 rounded-lg py-2">
                        <div class="font-display font-bold text-white text-sm">{{ $user->projects_count }}</div>
                        <div class="text-[9px] text-gray-500 uppercase tracking-wider">Projects</div>
                    </div>
                    <div class="text-center bg-black/20 rounded-lg py-2">
                        <div class="font-display font-bold text-white text-sm">{{ $user->badges_count }}</div>
                        <div class="text-[9px] text-gray-500 uppercase tracking-wider">Badges</div>
                    </div>
                    <div class="text-center bg-black/20 rounded-lg py-2">
                        <div class="font-display font-bold text-white text-sm">{{ number_format($user->total_xp) }}</div>
                        <div class="text-[9px] text-gray-500 uppercase tracking-wider">Total XP</div>
                    </div>
                </div>

                {{-- Equipped badges preview --}}
                @if($user->equippedBadges->count() > 0)
                <div class="flex items-center gap-1.5">
                    @foreach($user->equippedBadges as $badge)
                    <div class="w-7 h-7 rounded-lg bg-black/30 border border-white/10 flex items-center justify-center text-sm"
                        title="{{ $badge->title }}">
                        <i class="{{ $badge->icon }}"></i>
                    </div>
                    @endforeach
                    <span class="text-[10px] text-gray-500 ml-1">equipped</span>
                </div>
                @endif
            </div>

            {{-- View profile footer --}}
            <div class="px-5 pb-4">
                <div class="flex items-center justify-end text-xs text-purple-400 group-hover:text-purple-300 transition font-semibold">
                    View Profile <i class="fas fa-arrow-right ml-1.5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="flex justify-center">
        {{ $users->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
