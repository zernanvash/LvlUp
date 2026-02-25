<section>
    <header>
        <h2 class="font-display text-2xl font-bold text-white mb-2">
            {{ __('Profile Information') }}
        </h2>

        <p class="text-purple-300">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-bold text-white mb-2">{{ __('Name') }}</label>
            <input 
                id="name" 
                name="name" 
                type="text" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-purple-500/30 rounded-lg text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
                value="{{ old('name', $user->name) }}" 
                required 
                autofocus 
                autocomplete="name" 
            />
            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-bold text-white mb-2">{{ __('Email') }}</label>
            <input 
                id="email" 
                name="email" 
                type="email" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-purple-500/30 rounded-lg text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
                value="{{ old('email', $user->email) }}" 
                required 
                autocomplete="username" 
            />
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-amber-500/10 border-2 border-amber-500/30 rounded-lg">
                    <p class="text-sm text-amber-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-amber-200 hover:text-amber-100 font-bold transition-colors">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-300 font-bold">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="title" class="block text-sm font-bold text-white mb-2">{{ __('Title') }}</label>
            <input 
                id="title" 
                name="title" 
                type="text" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-purple-500/30 rounded-lg text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
                value="{{ old('title', $user->title) }}" 
                placeholder="e.g., Full Stack Developer" 
            />
            @error('title')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="bio" class="block text-sm font-bold text-white mb-2">{{ __('Bio') }}</label>
            <textarea 
                id="bio" 
                name="bio" 
                rows="4" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-purple-500/30 rounded-lg text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none" 
                placeholder="Tell us about yourself..."
            >{{ old('bio', $user->bio) }}</textarea>
            @error('bio')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="linkedin_url" class="block text-sm font-bold text-white mb-2">
                    <i class="fab fa-linkedin text-blue-400 mr-2"></i>{{ __('LinkedIn Profile URL') }}
                </label>
                <input 
                    id="linkedin_url" 
                    name="linkedin_url" 
                    type="url" 
                    class="w-full px-4 py-3 bg-black/40 border-2 border-purple-500/30 rounded-lg text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
                    value="{{ old('linkedin_url', $user->linkedin_url) }}" 
                    placeholder="https://linkedin.com/in/yourprofile" 
                />
                @error('linkedin_url')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="github_url" class="block text-sm font-bold text-white mb-2">
                    <i class="fab fa-github text-gray-400 mr-2"></i>{{ __('GitHub Profile URL') }}
                </label>
                <input 
                    id="github_url" 
                    name="github_url" 
                    type="url" 
                    class="w-full px-4 py-3 bg-black/40 border-2 border-purple-500/30 rounded-lg text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
                    value="{{ old('github_url', $user->github_url) }}" 
                    placeholder="https://github.com/yourusername" 
                />
                @error('github_url')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-glow bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all">
                <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    {{ __('Save Changes') }}
                </span>
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-300 font-bold flex items-center gap-2"
                >
                    <i class="fas fa-check-circle"></i>
                    {{ __('Saved successfully!') }}
                </p>
            @endif
        </div>
    </form>
</section>
