<section>
    <header>
        <h2 class="font-display text-xl font-bold text-white mb-1 flex items-center gap-2">
            <i class="fas fa-lock text-purple-400"></i> {{ __('Update Password') }}
        </h2>

        <p class="text-[var(--lvl-muted)] text-sm mb-4">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">{{ __('Current Password') }}</label>
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                class="w-full px-4 py-2.5"
                autocomplete="current-password" 
            />
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">{{ __('New Password') }}</label>
            <input 
                id="update_password_password" 
                name="password" 
                type="password" 
                class="w-full px-4 py-2.5"
                autocomplete="new-password" 
            />
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-[var(--lvl-muted)] mb-1.5">{{ __('Confirm Password') }}</label>
            <input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="w-full px-4 py-2.5"
                autocomplete="new-password" 
            />
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-glow px-6 py-2.5 rounded-lg font-bold">
                <i class="fas fa-lock mr-2"></i> {{ __('Update Password') }}
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
