<section>
    <header>
        <h2 class="font-display text-2xl font-bold text-white mb-2">
            {{ __('Profile Visibility') }}
        </h2>

        <p class="text-purple-300">
            {{ __('Control who can view your profile and portfolio.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.toggle-visibility') }}" class="mt-6 space-y-6" x-data="{ isPublic: {{ $user->is_public ? 'true' : 'false' }} }">
        @csrf
        @method('patch')

        <div class="flex items-center justify-between p-6 bg-black/30 rounded-xl border-2 border-purple-500/30">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <label for="is_public" class="font-bold text-white text-lg">
                        {{ __('Public Profile') }}
                    </label>
                    <span x-show="isPublic" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-300 border border-green-500/30">
                        <i class="fas fa-globe mr-1"></i> Public
                    </span>
                    <span x-show="!isPublic" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-500/20 text-gray-300 border border-gray-500/30">
                        <i class="fas fa-lock mr-1"></i> Private
                    </span>
                </div>
                <p class="text-sm text-purple-200/70">
                    <span x-show="isPublic">Your profile is visible to everyone. Anyone can view your stats, badges, and featured projects.</span>
                    <span x-show="!isPublic">Your profile is private. Only you can view your full profile.</span>
                </p>
            </div>
            <div class="ml-6">
                <button 
                    type="submit" 
                    class="relative inline-flex h-8 w-16 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 focus:ring-offset-gray-900"
                    :class="isPublic ? 'bg-purple-600' : 'bg-gray-600'"
                    role="switch"
                    :aria-checked="isPublic"
                    @click.prevent="isPublic = !isPublic; $el.closest('form').submit();"
                >
                    <span 
                        class="pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="isPublic ? 'translate-x-8' : 'translate-x-0'"
                    ></span>
                </button>
            </div>
        </div>

        @if ($user->is_public)
            <div class="p-6 bg-purple-500/10 rounded-xl border-2 border-purple-500/30">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-link text-purple-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-white mb-3">
                            {{ __('Your Public Profile URL') }}
                        </h3>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                readonly 
                                value="{{ $user->getPublicUrl() }}" 
                                class="flex-1 text-sm text-white bg-black/40 border-2 border-purple-500/30 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono"
                                id="public-url"
                            >
                            <button 
                                type="button" 
                                onclick="navigator.clipboard.writeText(document.getElementById('public-url').value); this.innerHTML = '<i class=\'fas fa-check\'></i>'; setTimeout(() => this.innerHTML = '<i class=\'fas fa-copy\'></i>', 2000);"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition-colors font-bold"
                                title="Copy URL"
                            >
                                <i class="fas fa-copy"></i>
                            </button>
                            <a 
                                href="{{ $user->getPublicUrl() }}" 
                                target="_blank"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors font-bold"
                                title="View Profile"
                            >
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('status') === 'visibility-updated')
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="p-4 bg-green-500/20 border-2 border-green-500/30 rounded-lg"
            >
                <p class="text-sm text-green-300 font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ __('Profile visibility updated successfully!') }}
                </p>
            </div>
        @endif
    </form>
</section>
