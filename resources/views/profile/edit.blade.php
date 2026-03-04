@extends('layouts.app')

@section('title', 'Profile')
@section('page_title', auth()->user()->name)
@section('page_subtitle', auth()->user()->title ?? 'Developer')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showSettings: false, showCertificates: false }">
    
    <!-- Action Buttons -->
    <div class="flex items-center gap-4">
        <button 
            @click="showSettings = !showSettings; showCertificates = false" 
            class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all"
            :class="showSettings ? 'ring-2 ring-purple-400' : ''"
        >
            <span class="relative z-10 flex items-center gap-2">
                <i class="fas fa-cog"></i>
                Profile Settings
            </span>
        </button>
        
        <button 
            @click="showCertificates = !showCertificates; showSettings = false" 
            class="btn-glow bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all"
            :class="showCertificates ? 'ring-2 ring-blue-400' : ''"
        >
            <span class="relative z-10 flex items-center gap-2">
                <i class="fas fa-certificate"></i>
                Manage Certificates
            </span>
        </button>

        @if(auth()->user()->is_public)
        <a 
            href="{{ auth()->user()->getPublicUrl() }}" 
            target="_blank"
            class="btn-glow bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all"
        >
            <span class="relative z-10 flex items-center gap-2">
                <i class="fas fa-external-link-alt"></i>
                View Public Profile
            </span>
        </a>
        @endif
    </div>

    <!-- Settings Panel (Hidden by default) -->
    <div x-show="showSettings" x-transition class="space-y-6">
        <!-- Profile Information -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Profile Visibility -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-visibility')
            </div>
        </div>

        <!-- Update Password -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-pink-900/40 to-pink-950/40 backdrop-blur">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-red-900/40 to-red-950/40 backdrop-blur">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <!-- Certificates Panel (Hidden by default) -->
    <div x-show="showCertificates" x-transition>
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-cyan-900/40 to-cyan-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-6">Manage Certificates</h2>
            <p class="text-cyan-300 mb-6">Add your professional certifications to showcase your expertise.</p>
            
            <!-- Coming Soon Message -->
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-certificate text-5xl text-cyan-400"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-white mb-2">Certificate Management Coming Soon</h3>
                <p class="text-cyan-300">This feature will allow you to add and manage your professional certifications.</p>
            </div>
        </div>
    </div>

    <!-- Main Profile View (Shown by default) -->
    <div x-show="!showSettings && !showCertificates" x-transition class="space-y-6">
        
        <!-- Profile Header -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-purple-900/40 backdrop-blur relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 via-pink-600/10 to-purple-600/10 animate-pulse"></div>
            
            <div class="relative z-10 flex items-start gap-8">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <div class="relative">
                        <img 
                            src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=a78bfa&color=fff&size=128' }}" 
                            class="w-32 h-32 rounded-2xl border-4 border-purple-400 shadow-2xl"
                            alt="{{ auth()->user()->name }}"
                        >
                        <div class="absolute -bottom-2 -right-2 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl px-3 py-1 shadow-lg">
                            <span class="font-display text-sm font-bold text-white">Lvl {{ auth()->user()->level }}</span>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex-1">
                    <h1 class="font-display text-4xl font-bold text-white mb-2">{{ auth()->user()->name }}</h1>
                    <p class="text-xl text-purple-300 mb-4">{{ auth()->user()->title ?? 'Developer' }}</p>
                    
                    @if(auth()->user()->bio)
                    <p class="text-purple-200/80 leading-relaxed mb-6 max-w-3xl">{{ auth()->user()->bio }}</p>
                    @else
                    <p class="text-purple-300/60 italic mb-6">No bio added yet. Add one in Profile Settings!</p>
                    @endif

                    <!-- Stats Row -->
                    <div class="flex items-center gap-6 flex-wrap">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trophy text-amber-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-purple-300">Rank</p>
                                <p class="font-display font-bold text-white">{{ auth()->user()->rank }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bolt text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-purple-300">Total XP</p>
                                <p class="font-display font-bold text-white">{{ number_format(auth()->user()->total_xp) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-purple-300">Projects</p>
                                <p class="font-display font-bold text-white">{{ auth()->user()->projects()->count() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-pink-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-network-wired text-pink-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-purple-300">Skills Unlocked</p>
                                <p class="font-display font-bold text-white">{{ auth()->user()->unlockedNodes()->count() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-award text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-purple-300">Achievements</p>
                                <p class="font-display font-bold text-white">{{ auth()->user()->badges()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipped Badges -->
        @php
            $equippedBadges = auth()->user()->badges()->wherePivot('is_displayed', true)->orderBy('user_badges.created_at', 'desc')->limit(6)->get();
        @endphp
        @if($equippedBadges->count() > 0)
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-amber-900/30 to-amber-950/30 backdrop-blur">
            <h2 class="font-display text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-crown text-amber-400"></i>
                Equipped Badges
            </h2>
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
                    $color = $rarityColors[$badge->rarity] ?? 'gray';
                @endphp
                <div class="group relative">
                    <div class="glow-border rounded-xl p-4 bg-gradient-to-br from-{{ $color }}-900/40 to-{{ $color }}-950/40 backdrop-blur text-center card-hover">
                        <div class="text-4xl mb-2"><i class="{{ $badge->icon }}"></i></div>
                        <div class="text-xs font-bold text-white truncate">{{ $badge->title }}</div>
                        <div class="text-xs text-{{ $color }}-300 uppercase mt-1">{{ $badge->rarity }}</div>
                    </div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-black/90 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                        {{ $badge->description }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Social Links & Contact -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LinkedIn -->
            <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 backdrop-blur">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i class="fab fa-linkedin text-2xl text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-white">LinkedIn Profile</h3>
                        <p class="text-xs text-blue-300">Connect professionally</p>
                    </div>
                </div>
                @if(auth()->user()->linkedin_url ?? false)
                <a href="{{ auth()->user()->linkedin_url }}" target="_blank" class="block px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 rounded-lg text-sm text-blue-300 hover:text-blue-200 transition-colors text-center">
                    <i class="fab fa-linkedin mr-2"></i>View Profile
                </a>
                @else
                <p class="text-sm text-blue-300/60 italic">No LinkedIn profile added yet</p>
                @endif
            </div>

            <!-- GitHub -->
            <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-gray-900/40 to-gray-950/40 backdrop-blur">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-gray-500/20 rounded-xl flex items-center justify-center">
                        <i class="fab fa-github text-2xl text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-white">GitHub Profile</h3>
                        <p class="text-xs text-gray-300">View repositories</p>
                    </div>
                </div>
                @if(auth()->user()->github_url ?? false)
                <a href="{{ auth()->user()->github_url }}" target="_blank" class="block px-4 py-2 bg-gray-500/20 hover:bg-gray-500/30 border border-gray-500/30 rounded-lg text-sm text-gray-300 hover:text-gray-200 transition-colors text-center">
                    <i class="fab fa-github mr-2"></i>View Profile
                </a>
                @else
                <p class="text-sm text-gray-300/60 italic">No GitHub profile added yet</p>
                @endif
            </div>
        </div>

        <!-- Certificates Section -->
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-cyan-900/40 to-cyan-950/40 backdrop-blur">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-certificate text-cyan-400"></i>
                    Professional Certificates
                </h2>
                <button 
                    @click="showCertificates = true" 
                    class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 rounded-lg text-sm font-bold text-white transition-colors"
                >
                    <i class="fas fa-plus mr-2"></i>Add Certificate
                </button>
            </div>
            
            <!-- Placeholder for certificates -->
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-cyan-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-certificate text-3xl text-cyan-400"></i>
                </div>
                <p class="text-cyan-300/60 italic">No certificates added yet</p>
            </div>
        </div>

    </div>
</div>
@endsection