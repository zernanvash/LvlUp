<div class="space-y-6">
    <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-purple-900/40 to-purple-950/40 backdrop-blur">
        <h2 class="font-display text-xl font-bold text-white mb-1 flex items-center gap-2">
            <i class="fas fa-user-edit text-purple-400"></i> Profile Information
        </h2>
        <p class="text-purple-300/70 text-sm mb-6">Your public-facing identity. This is what others see on your profile.</p>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            {{-- Name & Title row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-1.5">
                        Display Name <span class="text-pink-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-white/5 border border-purple-500/30 rounded-xl px-4 py-2.5 text-white placeholder-purple-400/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-1.5">Job Title / Role</label>
                    <input type="text" name="title" value="{{ old('title', $user->title) }}"
                           placeholder="e.g. Full Stack Developer"
                           class="w-full bg-white/5 border border-purple-500/30 rounded-xl px-4 py-2.5 text-white placeholder-purple-400/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-purple-300 mb-1.5">
                    Email <span class="text-pink-400">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-white/5 border border-purple-500/30 rounded-xl px-4 py-2.5 text-white placeholder-purple-400/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="mt-1 text-xs text-amber-400">
                        Email unverified.
                        <button form="send-verification" class="underline hover:text-amber-300">Resend verification</button>
                    </p>
                @endif
            </div>

            {{-- Bio --}}
            <div>
                <label class="block text-sm font-semibold text-purple-300 mb-1.5">Bio / About</label>
                <textarea name="bio" rows="3" placeholder="Tell the world about yourself..."
                          class="w-full bg-white/5 border border-purple-500/30 rounded-xl px-4 py-2.5 text-white placeholder-purple-400/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition resize-none">{{ old('bio', $user->bio) }}</textarea>
                @error('bio') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Social Links --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-blue-300 mb-1.5">
                        <i class="fab fa-linkedin mr-1"></i> LinkedIn URL
                    </label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}"
                           placeholder="https://linkedin.com/in/..."
                           class="w-full bg-white/5 border border-blue-500/30 rounded-xl px-4 py-2.5 text-white placeholder-blue-400/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('linkedin_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1.5">
                        <i class="fab fa-github mr-1"></i> GitHub URL
                    </label>
                    <input type="url" name="github_url" value="{{ old('github_url', $user->github_url) }}"
                           placeholder="https://github.com/..."
                           class="w-full bg-white/5 border border-gray-500/30 rounded-xl px-4 py-2.5 text-white placeholder-gray-400/40 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                    @error('github_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-purple-300 mb-1.5">
                        <i class="fas fa-globe mr-1"></i> Website URL
                    </label>
                    <input type="url" name="website_url" value="{{ old('website_url', $user->website_url) }}"
                           placeholder="https://yoursite.com"
                           class="w-full bg-white/5 border border-purple-500/30 rounded-xl px-4 py-2.5 text-white placeholder-purple-400/40 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    @error('website_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Technical Skills --}}
            <div>
                <label class="block text-sm font-semibold text-cyan-300 mb-1.5">
                    <i class="fas fa-code mr-1"></i> Technical Skills
                    <span class="text-cyan-400/60 font-normal ml-1">(comma-separated)</span>
                </label>
                <input type="text" name="technical_skills" value="{{ old('technical_skills', $user->technical_skills) }}"
                       placeholder="JavaScript, React, Laravel, Docker, Teamwork, Problem Solving..."
                       class="w-full bg-white/5 border border-cyan-500/30 rounded-xl px-4 py-2.5 text-white placeholder-cyan-400/40 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                <p class="mt-1 text-xs text-cyan-400/60">Include both hard skills (languages, frameworks) and soft skills (teamwork, leadership)</p>
                @error('technical_skills') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-8 py-3 rounded-xl font-bold text-white shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i> Save Profile
                </button>
            </div>
        </form>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">@csrf</form>
    </div>
</div>
