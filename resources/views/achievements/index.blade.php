@extends('layouts.app')

@section('title', 'Achievements')
@section('page_title', 'Hall of Glory')
@section('page_subtitle', 'Your legendary accomplishments')

@section('content')
@php
    $equippedBadges = auth()->user()->badges()->wherePivot('is_displayed', true)->orderBy('user_badges.created_at', 'asc')->get();
    $equippedCount  = $equippedBadges->count();

    // Build JS-safe array of equipped badges for Alpine
    $equippedJs = $equippedBadges->map(fn($b) => [
        'id'     => $b->id,
        'title'  => $b->title,
        'icon'   => $b->icon,
        'rarity' => $b->rarity,
        'color'  => match($b->rarity) {
            'uncommon'  => 'green',
            'rare'      => 'blue',
            'epic'      => 'purple',
            'legendary' => 'amber',
            'mythic'    => 'pink',
            default     => 'gray',
        },
    ])->values()->toArray();
@endphp
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Equipped Badges Display (fully reactive via Alpine store) -->
    <div x-data x-show="$store.achievements.equipped.length > 0" class="glow-border rounded-2xl p-6 bg-gradient-to-br from-purple-900/40 to-pink-900/40 backdrop-blur">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-crown text-amber-400"></i>
                Equipped Badges
                <span class="text-sm text-purple-300 font-normal">(<span x-text="$store.achievements.equipped.length"></span>/6)</span>
            </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <template x-for="b in $store.achievements.equipped" :key="b.id">
                <div class="relative group">
                    <div class="glow-border rounded-xl p-4 backdrop-blur text-center transition-transform hover:scale-105"
                         :class="`bg-gradient-to-br from-${b.color}-900/40 to-${b.color}-950/40`">
                        <div class="text-4xl mb-2"><i :class="b.icon"></i></div>
                        <p class="text-xs text-white font-bold truncate" x-text="b.title"></p>
                        <p class="text-xs uppercase" :class="`text-${b.color}-300`" x-text="b.rarity"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/40 to-amber-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-amber-300">Total Badges</p>
                    <p class="font-display text-3xl font-bold text-white">{{ $badgesByCategory->flatten(1)->filter(fn($b) => $b['earned'])->count() }}</p>
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
                            $totalBadges = $badgesByCategory->flatten(1)->count();
                            $earnedBadges = $badgesByCategory->flatten(1)->filter(fn($b) => $b['earned'])->count();
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
                <div x-data>
                    <p class="text-sm text-pink-300">Equipped</p>
                    <p class="font-display text-3xl font-bold text-white" x-text="$store.achievements.equipped.length + '/6'"></p>
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
                            <i class="{{ $badge->icon }}"></i>
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
                @php
                    $badgeJs = [
                        'id'     => $badge->id,
                        'title'  => $badge->title,
                        'icon'   => $badge->icon,
                        'rarity' => $badge->rarity,
                        'color'  => $color,
                    ];
                @endphp
                <div class="bg-gradient-to-t from-black/20 to-transparent p-4"
                     x-data="{ equipped: {{ $isDisplayed ? 'true' : 'false' }}, loading: false, badge: {{ Js::from($badgeJs) }} }">
                    <button
                        @click.prevent="
                            if (loading) return;
                            loading = true;
                            fetch('{{ route('badges.toggle-display', $badge) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    equipped = !equipped;
                                    if (equipped) {
                                        $store.achievements.add(badge);
                                    } else {
                                        $store.achievements.remove(badge.id);
                                    }
                                    window.pushToast({
                                        label: equipped ? 'BADGE EQUIPPED' : 'BADGE UNEQUIPPED',
                                        title: badge.title,
                                        sub: null,
                                        icon: badge.icon,
                                        color: '{{ $badge->rarity_color }}',
                                        duration: 2500,
                                    });
                                } else {
                                    window.pushToast({
                                        label: 'LIMIT REACHED',
                                        title: data.message,
                                        sub: null,
                                        icon: 'fas fa-exclamation-triangle',
                                        color: '#f59e0b',
                                        duration: 3500,
                                    });
                                }
                            })
                            .finally(() => loading = false)
                        "
                        :disabled="loading || (!equipped && $store.achievements.equipped.length >= 6)"
                        class="w-full px-4 py-2 rounded-lg text-xs font-bold uppercase transition-all border"
                        :class="equipped
                            ? 'bg-red-500/20 hover:bg-red-500/40 border-red-500/50 text-red-300'
                            : 'bg-purple-500/20 hover:bg-purple-500/40 border-purple-500/50 text-purple-300 disabled:opacity-40 disabled:cursor-not-allowed'">
                        <template x-if="loading">
                            <span><i class="fas fa-spinner fa-spin mr-1"></i> ...</span>
                        </template>
                        <template x-if="!loading && equipped">
                            <span><i class="fas fa-times-circle mr-1"></i> Unequip</span>
                        </template>
                        <template x-if="!loading && !equipped">
                            <span><i class="fas fa-crown mr-1"></i> Equip</span>
                        </template>
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>


@endsection

@push('scripts')
<script>
// Seed the Alpine store so nested badge components can access it
document.addEventListener('alpine:init', () => {
    Alpine.store('achievements', {
        equipped: @json($equippedJs),
        add(badge) {
            if (!this.equipped.find(b => b.id === badge.id)) {
                this.equipped.push(badge);
            }
        },
        remove(id) {
            this.equipped = this.equipped.filter(b => b.id !== id);
        },
    });
});
</script>
@endpush