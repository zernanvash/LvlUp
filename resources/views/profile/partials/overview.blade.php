<div class="space-y-6">
    {{-- Profile Header --}}
    <div class="lvl-panel p-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/5 via-pink-600/5 to-purple-600/5 animate-pulse pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start gap-6">
            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <div class="relative">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=a78bfa&color=fff&size=128' }}"
                         class="w-28 h-28 rounded-2xl border-4 border-purple-400 shadow-2xl object-cover" alt="{{ $user->name }}">
                    <div class="absolute -bottom-2 -right-2 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl px-2.5 py-1 shadow-lg">
                        <span class="font-display text-xs font-bold text-white">Lvl {{ $user->level }}</span>
                    </div>
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h1 class="font-display text-3xl font-bold text-white mb-1">{{ $user->name }}</h1>
                <p class="text-lg text-purple-300 mb-3">{{ $user->title ?? 'Developer' }}</p>
                @if($user->bio)
                    <p class="text-purple-200/80 leading-relaxed mb-4 max-w-2xl">{{ $user->bio }}</p>
                @else
                    <p class="text-purple-300/50 italic mb-4">No bio yet — add one in Profile Settings.</p>
                @endif

                {{-- Social Links --}}
                <div class="flex flex-wrap gap-3">
                    @if($user->linkedin_url)
                    <a href="{{ $user->linkedin_url }}" target="_blank"
                       class="flex items-center gap-2 px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-sm text-blue-300 transition-colors">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                    @endif
                    @if($user->github_url)
                    <a href="{{ $user->github_url }}" target="_blank"
                       class="flex items-center gap-2 px-3 py-1.5 bg-gray-600/20 hover:bg-gray-600/30 border border-gray-500/30 rounded-lg text-sm text-gray-300 transition-colors">
                        <i class="fab fa-github"></i> GitHub
                    </a>
                    @endif
                    @if($user->website_url)
                    <a href="{{ $user->website_url }}" target="_blank"
                       class="flex items-center gap-2 px-3 py-1.5 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-sm text-purple-300 transition-colors">
                        <i class="fas fa-globe"></i> Website
                    </a>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex sm:flex-col gap-3 flex-wrap">
                @foreach([
                    ['icon' => 'fa-trophy', 'color' => 'amber', 'label' => 'Rank', 'value' => $user->rank],
                    ['icon' => 'fa-bolt', 'color' => 'purple', 'label' => 'Total XP', 'value' => number_format($user->total_xp)],
                    ['icon' => 'fa-folder', 'color' => 'blue', 'label' => 'Projects', 'value' => $user->projects()->count()],
                    ['icon' => 'fa-award', 'color' => 'green', 'label' => 'Badges', 'value' => $user->badges()->count()],
                ] as $stat)
                <div class="flex items-center gap-2 bg-white/5 rounded-xl px-3 py-2">
                    <div class="w-8 h-8 bg-{{ $stat['color'] }}-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-purple-400">{{ $stat['label'] }}</p>
                        <p class="font-display font-bold text-white text-sm">{{ $stat['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Equipped Badges --}}
    @php $equippedBadges = $user->badges()->wherePivot('is_displayed', true)->get(); @endphp
    @if($equippedBadges->count() > 0)
    <div class="lvl-panel p-6 border-l-4" style="border-left-color: var(--lvl-gold) !important;">
        <h2 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-crown text-amber-400"></i> Equipped Badges
        </h2>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach($equippedBadges as $badge)
            @php
                $colors = ['common'=>'gray','uncommon'=>'green','rare'=>'blue','epic'=>'purple','legendary'=>'amber','mythic'=>'pink'];
                $c = $colors[$badge->rarity] ?? 'gray';
            @endphp
            <div class="group relative text-center">
                <div class="lvl-panel p-3 text-center card-hover" style="border-color: var(--lvl-border-soft); background: var(--lvl-surface-raised);">
                    <div class="text-3xl mb-1" style="color: {{ $badge->rarity_color }};"><i class="{{ $badge->icon }}"></i></div>
                    <div class="text-xs font-bold text-white truncate">{{ $badge->title }}</div>
                    <div class="text-xs uppercase" style="color: {{ $badge->rarity_color }}; font-size: 10px;">{{ $badge->rarity }}</div>
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
    @if($user->technical_skills)
    <div class="lvl-panel p-6 border-l-4" style="border-left-color: #22d3ee !important;">
        <h2 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-code text-cyan-400"></i> Technical Skills
        </h2>
        <div class="flex flex-wrap gap-2">
            @foreach(array_filter(array_map('trim', explode(',', $user->technical_skills))) as $skill)
            <span class="lvl-chip gray font-medium text-sm px-3 py-1.5">
                {{ $skill }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Certifications --}}
    @if($user->certifications)
    <div class="lvl-panel p-6 border-l-4" style="border-left-color: #6366f1 !important;">
        <h2 class="font-display text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-certificate text-indigo-400"></i> Certifications
        </h2>
        <div class="prose prose-invert prose-sm max-w-none">
            <p class="text-indigo-200/80 whitespace-pre-line">{{ $user->certifications }}</p>
        </div>
    </div>
    @endif

</div>
