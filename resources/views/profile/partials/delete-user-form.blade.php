<section class="space-y-6" x-data="{ showModal: false }">
    <header>
        <h2 class="font-display text-xl font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-400"></i> {{ __('Delete Account') }}
        </h2>

        <p class="text-sm text-red-300/80">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        @click="showModal = true"
        class="btn-danger px-6 py-2.5 rounded-lg font-bold"
    >
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ __('Delete Account') }}
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
                <div class="lvl-panel p-8">
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
                            <label for="password" class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="w-full px-4 py-2.5"
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
                                class="btn-secondary flex-1 px-4 py-2.5 rounded-lg font-bold"
                            >
                                {{ __('Cancel') }}
                            </button>

                            <button
                                type="submit"
                                class="btn-danger flex-1 px-4 py-2.5 rounded-lg font-bold"
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
