@extends('layouts.app')

@section('title', 'Achievements')
@section('page_title', 'Hall of Glory')
@section('page_subtitle', 'Your legendary accomplishments')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Progress Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/40 to-amber-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-amber-300">Total Badges</p>
                    <p class="font-display text-3xl font-bold text-white">{{ count($earnedBadges) }}</p>
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
                    <p class="font-display text-3xl font-bold text-white">{{ count($earnedBadges) > 0 ? round((count($earnedBadges) / $badges->flatten()->count()) * 100) : 0 }}%</p>
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
                            $rarities = ['common' => 0, 'rare' => 1, 'epic' => 2, 'legendary' => 3, 'mythic' => 4];
                            $rarest = auth()->user()->badges->sortByDesc(fn($b) => $rarities[$b->rarity])->first();
                        @endphp
                        {{ $rarest ? ucfirst($rarest->rarity) : 'None' }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-fire text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-pink-300">Current Streak</p>
                    <p class="font-display text-3xl font-bold text-white">{{ auth()->user()->streak_days }}</p>
                </div>
            </div>
        </div>
    </div>

    @foreach($badges as $category => $categoryBadges)
    <div>
        <h2 class="font-display text-2xl font-bold text-white mb-6 capitalize flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-{{ $category === 'project' ? 'folder' : ($category === 'skill' ? 'code' : ($category === 'streak' ? 'fire' : 'chart-line')) }}"></i>
            </div>
            {{ ucfirst($category) }} Achievements
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categoryBadges as $badge)
            @php
                $isEarned = in_array($badge->id, $earnedBadges);
                $rarityColors = [
                    'common' => 'gray',
                    'rare' => 'blue',
                    'epic' => 'purple',
                    'legendary' => 'amber',
                    'mythic' => 'pink'
                ];
                $color = $rarityColors[$badge->rarity];
            @endphp
            
            <div class="glow-border rounded-2xl overflow-hidden bg-gradient-to-br from-[#2d1b4e]/80 to-[#1a1d3e]/80 backdrop-blur
                        {{ $isEarned ? 'rarity-' . $badge->rarity : 'opacity-60' }}">
                <div class="p-6">
                    <!-- Badge Icon -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-20 h-20 bg-gradient-to-br from-{{ $color }}-500/20 to-{{ $color }}-600/20 
                                    border-2 border-{{ $color }}-500/40 rounded-2xl flex items-center justify-center text-4xl
                                    {{ $isEarned ? '' : 'grayscale' }}">
                            {{ $badge->icon }}
                        </div>
                        
                        <!-- Rarity Stars -->
                        <div class="flex gap-1">
                            @php
                                $starCount = ['common' => 2, 'rare' => 3, 'epic' => 4, 'legendary' => 5, 'mythic' => 6];
                            @endphp
                            @for($i = 0; $i < $starCount[$badge->rarity]; $i++)
                                <i class="fas fa-star text-{{ $color }}-400 text-sm"></i>
                            @endfor
                        </div>
                    </div>
                    
                    <!-- Badge Details -->
                    <h3 class="font-display font-bold text-white text-lg mb-2">{{ $badge->title }}</h3>
                    <p class="text-sm text-purple-200/70 mb-4">{{ $badge->description }}</p>
                    
                    <!-- Progress/Rewards -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-{{ $color }}-300 font-bold uppercase">{{ $badge->rarity }}</span>
                            @if($isEarned)
                                @php
                                    $earned = auth()->user()->badges->find($badge->id);
                                @endphp
                                <span class="text-green-400">
                                    <i class="fas fa-check-circle"></i> Earned {{ $earned->pivot->earned_at->diffForHumans() }}
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
                            <div class="flex items-center gap-2 text-amber-300">
                                <i class="fas fa-gem"></i>
                                <span class="text-sm font-bold">+{{ $badge->gacha_currency_reward }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Earned Overlay -->
                @if($isEarned)
                <div class="bg-gradient-to-t from-{{ $color }}-600/20 to-transparent p-4 flex items-center justify-center">
                    <span class="px-4 py-2 bg-{{ $color }}-500/30 border border-{{ $color }}-500/50 rounded-lg text-xs font-bold text-{{ $color }}-200 uppercase">
                        <i class="fas fa-crown"></i> Unlocked
                    </span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
