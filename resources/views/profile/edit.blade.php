@extends('layouts.app')

@section('title', 'Profile')
@section('page_title', auth()->user()->name)
@section('page_subtitle', auth()->user()->title ?? 'Developer')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showSettings: false, showCertificates: false }">
    
    <!-- Action Buttons -->
    <div class="flex items-center gap-4 flex-wrap">
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

    <!-- Flash Messages -->
    @if(session('status') === 'photo-updated')
    <div class="bg-green-500/20 border border-green-500/40 rounded-xl px-6 py-4 text-green-300 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-400"></i>
        Profile photo updated successfully!
    </div>
    @endif
    @if(session('status') === 'certificate-uploaded')
    <div class="bg-green-500/20 border border-green-500/40 rounded-xl px-6 py-4 text-green-300 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-400"></i>
        Certificate uploaded successfully!
    </div>
    @endif
    @if(session('status') === 'certificate-deleted')
    <div class="bg-red-500/20 border border-red-500/40 rounded-xl px-6 py-4 text-red-300 flex items-center gap-3">
        <i class="fas fa-trash text-red-400"></i>
        Certificate deleted.
    </div>
    @endif

    <!-- Settings Panel -->
    <div x-show="showSettings" x-transition x-cloak class="space-y-6">

        <!-- Profile Photo Upload -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-pink-900/40 backdrop-blur">
            <h2 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-camera text-purple-400"></i>
                Profile Photo
            </h2>
            <div class="flex items-center gap-8">
                <img 
                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=a78bfa&color=fff&size=128' }}" 
                    class="w-24 h-24 rounded-2xl border-4 border-purple-400 shadow-2xl object-cover flex-shrink-0"
                    alt="{{ auth()->user()->name }}"
                >
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="flex-1">
                    @csrf
                    <div x-data="{ fileName: '' }" class="space-y-4">
                        <label class="block">
                            <span class="text-purple-200 text-sm font-medium mb-2 block">Choose a new photo (JPG, PNG, WebP — max 4MB)</span>
                            <input 
                                type="file" 
                                name="photo" 
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                id="photo-input"
                                @change="fileName = $event.target.files[0]?.name ?? ''"
                            >
                            <label for="photo-input" class="flex items-center gap-3 px-4 py-3 bg-purple-900/40 border-2 border-dashed border-purple-500/50 hover:border-purple-400 rounded-xl cursor-pointer transition-colors">
                                <i class="fas fa-upload text-purple-400"></i>
                                <span class="text-purple-300 text-sm" x-text="fileName || 'Click to select photo'"></span>
                            </label>
                        </label>
                        @error('photo')
                        <p class="text-red-400 text-sm">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl font-bold text-white transition-all">
                            <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Photo
                        </button>
                    </div>
                </form>
            </div>
        </div>

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

    <!-- Certificates Panel -->
    <div x-show="showCertificates" x-transition>
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-cyan-900/40 to-cyan-950/40 backdrop-blur">
            <h2 class="font-display text-2xl font-bold text-white mb-2">Manage Certificates</h2>
            <p class="text-cyan-300 mb-8">Upload your professional certifications. Images or PDFs accepted (max 8MB).</p>

            <!-- Upload Form -->
            <form action="{{ route('certificates.store') }}" method="POST" enctype="multipart/form-data" class="mb-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-cyan-200 text-sm font-medium mb-2">Certificate Name <span class="text-red-400">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            placeholder="e.g. AWS Solutions Architect"
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-cyan-900/30 border border-cyan-500/30 rounded-xl text-white placeholder-cyan-400/50 focus:outline-none focus:border-cyan-400 transition"
                        >
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-cyan-200 text-sm font-medium mb-2">Issuing Organization</label>
                        <input 
                            type="text" 
                            name="issuer" 
                            placeholder="e.g. Amazon Web Services"
                            value="{{ old('issuer') }}"
                            class="w-full px-4 py-3 bg-cyan-900/30 border border-cyan-500/30 rounded-xl text-white placeholder-cyan-400/50 focus:outline-none focus:border-cyan-400 transition"
                        >
                    </div>
                    <div>
                        <label class="block text-cyan-200 text-sm font-medium mb-2">Issue Date</label>
                        <input 
                            type="date" 
                            name="issued_at"
                            value="{{ old('issued_at') }}"
                            class="w-full px-4 py-3 bg-cyan-900/30 border border-cyan-500/30 rounded-xl text-white focus:outline-none focus:border-cyan-400 transition"
                        >
                    </div>
                    <div>
                        <label class="block text-cyan-200 text-sm font-medium mb-2">Certificate File <span class="text-red-400">*</span></label>
                        <div x-data="{ fileName: '' }">
                            <input 
                                type="file" 
                                name="file"
                                id="cert-file"
                                accept="image/jpeg,image/png,image/webp,application/pdf"
                                class="hidden"
                                @change="fileName = $event.target.files[0]?.name ?? ''"
                            >
                            <label for="cert-file" class="flex items-center gap-3 px-4 py-3 bg-cyan-900/30 border-2 border-dashed border-cyan-500/50 hover:border-cyan-400 rounded-xl cursor-pointer transition-colors">
                                <i class="fas fa-file-upload text-cyan-400"></i>
                                <span class="text-cyan-300 text-sm" x-text="fileName || 'JPG, PNG, WebP or PDF'"></span>
                            </label>
                        </div>
                        @error('file')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 rounded-xl font-bold text-white transition-all">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Certificate
                </button>
            </form>

            <!-- Certificates List -->
            @php $certificates = auth()->user()->certificates ?? collect(); @endphp

            @if($certificates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($certificates as $cert)
                <div class="bg-cyan-900/30 border border-cyan-500/30 rounded-xl p-4">
                    <div class="w-full h-32 bg-cyan-950/50 rounded-lg mb-3 overflow-hidden flex items-center justify-center">
                        @if(str_ends_with(strtolower($cert->file_url), '.pdf') || str_contains($cert->file_url, '/raw/'))
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-red-400 mb-2"></i>
                                <p class="text-xs text-cyan-300">PDF Document</p>
                            </div>
                        @else
                            <img src="{{ $cert->file_url }}" alt="{{ $cert->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <h4 class="font-bold text-white text-sm mb-1 truncate">{{ $cert->name }}</h4>
                    @if($cert->issuer)<p class="text-cyan-300 text-xs mb-1">{{ $cert->issuer }}</p>@endif
                    @if($cert->issued_at)<p class="text-cyan-400/60 text-xs mb-3">{{ \Carbon\Carbon::parse($cert->issued_at)->format('M Y') }}</p>@endif
                    <div class="flex gap-2">
                        <a href="{{ $cert->file_url }}" target="_blank" class="flex-1 text-center px-3 py-2 bg-cyan-600/30 hover:bg-cyan-600/50 border border-cyan-500/30 rounded-lg text-xs font-bold text-cyan-300 transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <form action="{{ route('certificates.destroy', $cert) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this certificate?')" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/40 border border-red-500/30 rounded-lg text-xs font-bold text-red-400 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-certificate text-5xl text-cyan-400"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-white mb-2">No Certificates Yet</h3>
                <p class="text-cyan-300">Upload your first certificate above to showcase your credentials.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Profile View -->
    <div x-show="!showSettings && !showCertificates" x-transition class="space-y-6">
        
        <!-- Profile Header -->
        <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 via-pink-900/40 to-purple-900/40 backdrop-blur relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 via-pink-600/10 to-purple-600/10 animate-pulse"></div>
            
            <div class="relative z-10 flex items-start gap-8">
                <!-- Avatar with hover edit overlay -->
                <div class="flex-shrink-0">
                    <div class="relative group cursor-pointer" @click="showSettings = true">
                        <img 
                            src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=a78bfa&color=fff&size=128' }}" 
                            class="w-32 h-32 rounded-2xl border-4 border-purple-400 shadow-2xl object-cover"
                            alt="{{ auth()->user()->name }}"
                        >
                        <div class="absolute inset-0 bg-black/60 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-camera text-white text-2xl mb-1"></i>
                                <p class="text-white text-xs font-bold">Change Photo</p>
                            </div>
                        </div>
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
                    $rarityColors = ['common'=>'gray','uncommon'=>'green','rare'=>'blue','epic'=>'purple','legendary'=>'amber','mythic'=>'pink'];
                    $color = $rarityColors[$badge->rarity] ?? 'gray';
                @endphp
                <div class="group relative">
                    <div class="glow-border rounded-xl p-4 bg-gradient-to-br from-{{ $color }}-900/40 to-{{ $color }}-950/40 backdrop-blur text-center card-hover">
                        <div class="text-4xl mb-2">{{ $badge->icon }}</div>
                        <div class="text-xs font-bold text-white truncate">{{ $badge->title }}</div>
                        <div class="text-xs text-{{ $color }}-300 uppercase mt-1">{{ $badge->rarity }}</div>
                    </div>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-black/90 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                        {{ $badge->description }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Social Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

        <!-- Certificates Preview -->
        <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-cyan-900/40 to-cyan-950/40 backdrop-blur">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-certificate text-cyan-400"></i>
                    Professional Certificates
                </h2>
                <button 
                    @click="showCertificates = true; showSettings = false" 
                    class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 rounded-lg text-sm font-bold text-white transition-colors"
                >
                    <i class="fas fa-plus mr-2"></i>Add Certificate
                </button>
            </div>

            @php $certificates = auth()->user()->certificates ?? collect(); @endphp

            @if($certificates->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($certificates->take(4) as $cert)
                <a href="{{ $cert->file_url }}" target="_blank" class="block bg-cyan-900/30 border border-cyan-500/30 hover:border-cyan-400/50 rounded-xl p-3 transition group">
                    <div class="w-full h-20 bg-cyan-950/50 rounded-lg mb-2 overflow-hidden flex items-center justify-center">
                        @if(str_contains($cert->file_url, '/raw/'))
                            <i class="fas fa-file-pdf text-3xl text-red-400"></i>
                        @else
                            <img src="{{ $cert->file_url }}" alt="{{ $cert->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @endif
                    </div>
                    <p class="text-white text-xs font-bold truncate">{{ $cert->name }}</p>
                    @if($cert->issuer)<p class="text-cyan-400/60 text-xs truncate">{{ $cert->issuer }}</p>@endif
                </a>
                @endforeach
            </div>
            @if($certificates->count() > 4)
            <p class="text-cyan-400/60 text-sm mt-3 text-center">+{{ $certificates->count() - 4 }} more — <button @click="showCertificates = true" class="text-cyan-400 hover:text-cyan-300 underline">view all</button></p>
            @endif
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-cyan-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-certificate text-3xl text-cyan-400"></i>
                </div>
                <p class="text-cyan-300/60 italic">No certificates added yet</p>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
