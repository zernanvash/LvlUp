<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wider text-[var(--lvl-faint)]">Secure access</p>
        <h1 class="mt-1 text-2xl font-black text-[var(--lvl-text)]">Welcome back</h1>
        <p class="mt-2 text-sm text-[var(--lvl-muted)]">Continue your progress across skills, projects, resumes, and achievements.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-4">
                <x-input-label for="password" :value="__('Password')" />

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[var(--lvl-p600)] hover:text-[var(--lvl-p800)]">
                        Forgot?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label class="flex items-center gap-2 text-sm text-[var(--lvl-muted)]">
            <input type="checkbox" name="remember" class="rounded border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] text-[var(--lvl-p600)] focus:ring-[var(--lvl-p400)]">
            <span>Keep me signed in</span>
        </label>

        <x-primary-button class="w-full">
            {{ __('Sign in') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-[var(--lvl-muted)]">
        New here?
        <a href="{{ route('register') }}" class="font-semibold text-[var(--lvl-p600)] hover:text-[var(--lvl-p800)]">
            Create account
        </a>
    </p>
</x-guest-layout>
