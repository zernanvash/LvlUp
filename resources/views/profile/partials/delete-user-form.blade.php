<section class="space-y-6" x-data="{ showModal: false }">
    <header>
        <h2 class="font-display text-2xl font-bold text-white mb-2">
            {{ __('Delete Account') }}
        </h2>

        <p class="text-red-300">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        @click="showModal = true"
        class="btn-glow bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all"
    >
        <span class="relative z-10 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            {{ __('Delete Account') }}
        </span>
    </button>

    <!-- Delete Confirmation Modal -->
    <div 
        x-show="showModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="showModal = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                x-show="showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md"
            >
                <div class="glow-border rounded-2xl p-8 bg-gradient-to-br from-red-900/90 to-red-950/90 backdrop-blur-xl">
                    <form method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')

                        <div class="text-center mb-6">
                            <div class="w-16 h-16 mx-auto mb-4 bg-red-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                            </div>
                            <h2 class="font-display text-2xl font-bold text-white mb-2">
                                {{ __('Are you sure?') }}
                            </h2>
                            <p class="text-sm text-red-200">
                                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-sm font-bold text-white mb-2">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="w-full px-4 py-3 bg-black/40 border-2 border-red-500/30 rounded-lg text-white placeholder-red-300/50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                placeholder="{{ __('Enter your password') }}"
                            />
                            @error('password', 'userDeletion')
                                <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="showModal = false"
                                class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg font-bold text-white transition-colors"
                            >
                                {{ __('Cancel') }}
                            </button>

                            <button
                                type="submit"
                                class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-500 rounded-lg font-bold text-white transition-colors"
                            >
                                {{ __('Delete Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
