<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Visibility') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Control who can view your profile and portfolio.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.toggle-visibility') }}" class="mt-6 space-y-6" x-data="{ isPublic: {{ $user->is_public ? 'true' : 'false' }} }">
        @csrf
        @method('patch')

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <label for="is_public" class="font-medium text-gray-900">
                        {{ __('Public Profile') }}
                    </label>
                    <span x-show="isPublic" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-globe mr-1"></i> Public
                    </span>
                    <span x-show="!isPublic" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-lock mr-1"></i> Private
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-600">
                    <span x-show="isPublic">Your profile is visible to everyone. Anyone can view your stats, badges, and featured projects.</span>
                    <span x-show="!isPublic">Your profile is private. Only you can view your full profile.</span>
                </p>
            </div>
            <div class="ml-4">
                <button 
                    type="submit" 
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                    :class="isPublic ? 'bg-indigo-600' : 'bg-gray-200'"
                    role="switch"
                    :aria-checked="isPublic"
                    @click.prevent="isPublic = !isPublic; $el.closest('form').submit();"
                >
                    <span 
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="isPublic ? 'translate-x-5' : 'translate-x-0'"
                    ></span>
                </button>
            </div>
        </div>

        @if ($user->is_public)
            <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-link text-indigo-600"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-indigo-900">
                            {{ __('Your Public Profile URL') }}
                        </h3>
                        <div class="mt-2 flex items-center gap-2">
                            <input 
                                type="text" 
                                readonly 
                                value="{{ $user->getPublicUrl() }}" 
                                class="flex-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                id="public-url"
                            >
                            <button 
                                type="button" 
                                onclick="navigator.clipboard.writeText(document.getElementById('public-url').value); this.innerHTML = '<i class=\'fas fa-check\'></i> Copied'; setTimeout(() => this.innerHTML = '<i class=\'fas fa-copy\'></i> Copy', 2000);"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            <a 
                                href="{{ $user->getPublicUrl() }}" 
                                target="_blank"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <i class="fas fa-external-link-alt"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('status') === 'visibility-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-green-600 font-medium"
            >{{ __('Profile visibility updated.') }}</p>
        @endif
    </form>
</section>
