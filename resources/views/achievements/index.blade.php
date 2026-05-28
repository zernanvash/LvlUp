@extends('layouts.app')

@section('title', 'Achievements')
@section('page_title', 'Hall of Glory')
@section('page_subtitle', 'Your legendary accomplishments')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Equipped Badges Display (fully reactive via Alpine store) -->
    <div x-data x-show="$store.achievements.equipped.length > 0" class="lvl-panel p-6 border-l-4" style="border-left-color: var(--lvl-p400) !important;">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-crown text-amber-400"></i>
                Equipped Badges
                <span class="text-sm text-purple-300 font-normal" aria-label="{{ $equippedCount }}/6">(<span x-text="$store.achievements.equipped.length">{{ $equippedCount }}</span>/6)</span>
                <span class="sr-only">{{ $equippedCount }}/6</span>
            </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <template x-for="b in $store.achievements.equipped" :key="b.id">
                <div class="relative group">
                    <div class="lvl-panel p-4 text-center transition-transform hover:scale-105" style="border-color: var(--lvl-border-soft); background: var(--lvl-surface-raised);">
                        <div class="text-4xl mb-2"><i :class="b.icon"></i></div>
                        <p class="text-xs text-white font-bold truncate" x-text="b.title"></p>
                        <p class="text-xs uppercase" :style="`color: ${b.color === 'gray' ? 'var(--lvl-muted)' : b.color === 'amber' ? 'var(--lvl-gold)' : b.color === 'green' ? 'var(--lvl-green)' : b.color === 'blue' ? '#3b82f6' : b.color === 'purple' ? 'var(--lvl-p600)' : '#ec4899'}`" x-text="b.rarity"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="tilt-card lvl-panel p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-2xl text-white"></i>
                </div>
                <div>
                    <p class="lvl-label text-amber-300">Total Badges</p>
                    <p class="font-display text-3xl font-bold text-white">{{ $badgesByCategory->flatten(1)->filter(fn($b) => $b['earned'])->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="tilt-card lvl-panel p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-2xl text-white"></i>
                </div>
                <div>
                    <p class="lvl-label text-purple-300">Completion</p>
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
        
        <div class="tilt-card lvl-panel p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gem text-2xl text-white"></i>
                </div>
                <div>
                    <p class="lvl-label text-blue-300">Rarest Badge</p>
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
        
        <div class="tilt-card lvl-panel p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-crown text-2xl text-white"></i>
                </div>
                <div x-data>
                    <p class="lvl-label text-pink-300">Equipped</p>
                    <p class="font-display text-3xl font-bold text-white" x-text="$store.achievements.equipped.length + '/6'">{{ $equippedCount }}/6</p>
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
            
            <div class="tilt-card lvl-panel overflow-hidden transition-all duration-300 {{ $isEarned ? '' : 'opacity-50' }}">
                <div class="p-5">
                    <!-- Badge Icon -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-3xl border-2 {{ $isEarned ? '' : 'grayscale' }}"
                             style="background: {{ $badge->rarity_color }}22; border-color: {{ $badge->rarity_color }}44; color: {{ $badge->rarity_color }};">
                            <i class="{{ $badge->icon }}"></i>
                        </div>
                        
                        <!-- Rarity Stars -->
                        <div class="flex gap-1 flex-col items-end">
                            <div class="flex gap-1">
                                @php
                                    $starCount = ['common' => 2, 'uncommon' => 2, 'rare' => 3, 'epic' => 4, 'legendary' => 5, 'mythic' => 6];
                                @endphp
                                @for($i = 0; $i < ($starCount[$badge->rarity] ?? 2); $i++)
                                    <i class="fas fa-star text-sm" style="color: {{ $badge->rarity_color }};"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <!-- Badge Details -->
                    <h3 class="font-bold text-white text-base mb-1.5 truncate" title="{{ $badge->title }}">{{ $badge->title }}</h3>
                    <p class="text-xs text-[var(--lvl-muted)] mb-3 line-clamp-2 h-8 leading-normal">{{ $badge->description }}</p>
                    
                    <!-- Progress Bar (for unearned badges) -->
                    @if(!$isEarned && $progress > 0)
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-[10px] mb-1">
                            <span style="color: {{ $badge->rarity_color }};">Progress</span>
                            <span class="text-white font-bold">{{ round($progress) }}%</span>
                        </div>
                        <div class="w-full bg-[var(--lvl-surface-soft)] rounded-full h-1.5 overflow-hidden border border-[var(--lvl-border-soft)]">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ $progress }}%; background: {{ $badge->rarity_color }};"></div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status and Rewards -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold uppercase" style="color: {{ $badge->rarity_color }};">{{ $badge->rarity }}</span>
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
                        
                        <div class="flex items-center justify-between pt-3 border-t border-white/5">
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
                        :aria-label="equipped ? 'Unequip Badge' : 'Equip Badge'"
                        @click.prevent="
                            if (loading) return;
                            window.dispatchEvent(new CustomEvent('lvlup-feature-hint', {
                                detail: {
                                    key: 'feature-badge-equip',
                                    label: 'Feature hint',
                                    title: 'Equipping badges',
                                    body: 'Equipped badges are the ones shown on your public profile. You can equip up to six, and you can swap them whenever you want.',
                                    steps: [
                                        'Equip badges that best represent your work.',
                                        'Unequip one if you hit the six-badge limit.',
                                        'Locked badges become available after you meet their requirements.',
                                    ],
                                },
                            }));
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
                        class="w-full px-4 py-2 rounded-lg text-xs font-bold uppercase transition-all"
                        :class="equipped ? 'btn-danger' : 'btn-secondary'">
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
