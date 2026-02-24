@extends('layouts.app')

@section('title', 'Achievements')
@section('page_title', 'Hall of Glory')
@section('page_subtitle', 'Your legendary accomplishments')

@section('content')
<div class="max-w-7xl mx-auto space-y-8" x-data="badgeManager()">
    
    <!-- Equipped Badges Display -->
    @php
        $equippedBadges = auth()->user()->badges()->wherePivot('is_displayed', true)->orderBy('user_badges.created_at', 'asc')->get();
        $equippedCount = $equippedBadges->count();
    @endphp
    
    @if($equippedCount > 0)
    <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-pink-900/40 backdrop-blur">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-crown text-amber-400"></i>
                Equipped Badges
                <span class="text-sm text-purple-300 font-normal">({{ $equippedCount }}/6)</span>
            </h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($equippedBadges as $badge)
            @php
                $rarityColors = [
                    'common' => 'gray',
                    'uncommon' => 'green',
                    'rare' => 'blue',
                    'epic' => 'purple',
                    'legendary' => 'amber',
                    'mythic' => 'pink'
                ];
                $color = $rarityColors[$badge->rarity];
            @endphp
            <div class="relative group">
                <div class="glow-border rounded-xl p-4 bg-gradient-to-br from-{{ $color }}-900/40 to-{{ $color }}-950/40 backdrop-blur text-center transition-transform hover:scale-105">
                    <div class="text-4xl mb-2">{{ $badge->icon }}</div>
                    <p class="text-xs text-white font-bold truncate">{{ $badge->title }}</p>
                    <p class="text-xs text-{{ $color }}-300 uppercase">{{ $badge->rarity }}</p>
                </div>
                
                <!-- Tooltip -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 whitespace-nowrap shadow-xl border border-{{ $color }}-500/30">
                        {{ $badge->title }}
                        <div class="text-{{ $color }}-300">{{ $badge->description }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Progress Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/40 to-amber-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-amber-300">Total Badges</p>
                    <p class="font-display text-3xl font-bold text-white">{{ $badgesByCategory->flatten()->where('earned', true)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-300">Completion</p>
                    <p class="font-display text-3xl font-bold text-white">
                        @php
                            $totalBadges = $badgesByCategory->flatten()->count();
                            $earnedBadges = $badgesByCategory->flatten()->where('earned', true)->count();
                        @endphp
                        {{ $totalBadges > 0 ? round(($earnedBadges / $totalBadges) * 100) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
        
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gem text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-300">Rarest Badge</p>
                    <p class="font-display text-xl font-bold text-white">
                        @php
                            $rarityOrder = ['common' => 0, 'uncommon' => 1, 'rare' => 2, 'epic' => 3, 'legendary' => 4, 'mythic' => 5];
                            $rarest = auth()->user()->badges->sortByDesc(fn($b) => $rarityOrder[$b->rarity] ?? 0)->first();
                        @endphp
                        {{ $rarest ? ucfirst($rarest->rarity) : 'None' }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-crown text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-pink-300">Equipped</p>
                    <p class="font-display text-3xl font-bold text-white">{{ $equippedCount }}/6</p>
                </div>
            </div>
        </div>
    </div>

    @foreach($badgesByCategory as $category => $categoryBadges)
    <div>
        <h2 class="font-display text-2xl font-bold text-white mb-6 capitalize flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-{{ $category === 'project' ? 'folder' : ($category === 'skill' ? 'code' : ($category === 'level' ? 'chart-line' : 'star')) }}"></i>
            </div>
            {{ ucfirst($category) }} Achievements
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categoryBadges as $badgeData)
            @php
                $badge = $badgeData['badge'];
                $isEarned = $badgeData['earned'];
                $progress = $badgeData['progress'];
                $isDisplayed = $badgeData['is_displayed'];
                
                $rarityColors = [
                    'common' => 'gray',
                    'uncommon' => 'green',
                    'rare' => 'blue',
                    'epic' => 'purple',
                    'legendary' => 'amber',
                    'mythic' => 'pink'
                ];
                $color = $rarityColors[$badge->rarity];
            @endphp
            
            <div class="glow-border rounded-2xl overflow-hidden bg-gradient-to-br from-[#2d1b4e]/80 to-[#1a1d3e]/80 backdrop-blur
                        {{ $isEarned ? 'rarity-' . $badge->rarity : 'opacity-60' }}
                        transition-all duration-300 hover:scale-105">
                <div class="p-6">
                    <!-- Badge Icon -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-20 h-20 bg-gradient-to-br from-{{ $color }}-500/20 to-{{ $color }}-600/20 
                                    border-2 border-{{ $color }}-500/40 rounded-2xl flex items-center justify-center text-4xl
                                    {{ $isEarned ? '' : 'grayscale' }}">
                            {{ $badge->icon }}
                        </div>
                        
                        <!-- Rarity Stars -->
                        <div class="flex gap-1 flex-col items-end">
                            <div class="flex gap-1">
                                @php
                                    $starCount = ['common' => 2, 'uncommon' => 2, 'rare' => 3, 'epic' => 4, 'legendary' => 5, 'mythic' => 6];
                                @endphp
                                @for($i = 0; $i < ($starCount[$badge->rarity] ?? 2); $i++)
                                    <i class="fas fa-star text-{{ $color }}-400 text-sm"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <!-- Badge Details -->
                    <h3 class="font-display font-bold text-white text-lg mb-2">{{ $badge->title }}</h3>
                    <p class="text-sm text-purple-200/70 mb-4">{{ $badge->description }}</p>
                    
                    <!-- Progress Bar (for unearned badges) -->
                    @if(!$isEarned && $progress > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-{{ $color }}-300">Progress</span>
                            <span class="text-white font-bold">{{ round($progress) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700/50 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-{{ $color }}-500 to-{{ $color }}-600 h-full rounded-full transition-all duration-500"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status and Rewards -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-{{ $color }}-300 font-bold uppercase">{{ $badge->rarity }}</span>
                            @if($isEarned)
                                <span class="text-green-400">
                                    <i class="fas fa-check-circle"></i> Earned {{ $badgeData['earned_at'] ? \Carbon\Carbon::parse($badgeData['earned_at'])->diffForHumans() : 'recently' }}
                                </span>
                            @else
                                <span class="text-gray-400">
                                    <i class="fas fa-lock"></i> Locked
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-white/10">
                            <div class="flex items-center gap-2 text-purple-300">
                                <i class="fas fa-bolt"></i>
                                <span class="text-sm font-bold">+{{ $badge->xp_reward }} XP</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons (for earned badges) -->
                @if($isEarned)
                <div class="bg-gradient-to-t from-{{ $color }}-600/20 to-transparent p-4">
                    @if($isDisplayed)
                        <form method="POST" action="{{ route('badges.unequip', $badge) }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-{{ $color }}-500/30 hover:bg-{{ $color }}-500/50 border border-{{ $color }}-500/50 rounded-lg text-xs font-bold text-white uppercase transition-colors">
                                <i class="fas fa-times-circle"></i> Unequip Badge
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('badges.equip', $badge) }}" class="w-full" x-data>
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-{{ $color }}-500/30 hover:bg-{{ $color }}-500/50 border border-{{ $color }}-500/50 rounded-lg text-xs font-bold text-white uppercase transition-colors"
                                    :disabled="$root.querySelector('[x-data]').__x.$data.equippedCount >= 6"
                                    :class="$root.querySelector('[x-data]').__x.$data.equippedCount >= 6 ? 'opacity-50 cursor-not-allowed' : ''">
                                <i class="fas fa-crown"></i> Equip Badge
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<script>
function badgeManager() {
    return {
        equippedCount: {{ $equippedCount }},
        
        init() {
            // Listen for badge equip/unequip events
            this.$watch('equippedCount', value => {
                console.log('Equipped badges:', value);
            });
        }
    }
}
</script>
@endsection
