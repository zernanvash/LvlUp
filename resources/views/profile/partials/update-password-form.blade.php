<section>
    <header>
        <h2 class="font-display text-2xl font-bold text-white mb-2">
            {{ __('Update Password') }}
        </h2>

        <p class="text-pink-300">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-bold text-white mb-2">{{ __('Current Password') }}</label>
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-pink-500/30 rounded-lg text-white placeholder-pink-300/50 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                autocomplete="current-password" 
            />
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-bold text-white mb-2">{{ __('New Password') }}</label>
            <input 
                id="update_password_password" 
                name="password" 
                type="password" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-pink-500/30 rounded-lg text-white placeholder-pink-300/50 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                autocomplete="new-password" 
            />
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-white mb-2">{{ __('Confirm Password') }}</label>
            <input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="w-full px-4 py-3 bg-black/40 border-2 border-pink-500/30 rounded-lg text-white placeholder-pink-300/50 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                autocomplete="new-password" 
            />
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-glow bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-500 hover:to-purple-500 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all">
                <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-lock"></i>
                    {{ __('Update Password') }}
                </span>
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-300 font-bold flex items-center gap-2"
                >
                    <i class="fas fa-check-circle"></i>
                    {{ __('Password updated successfully!') }}
                </p>
            @endif
        </div>
    </form>
</section>
