<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wider text-[var(--lvl-faint)]">Verification</p>
        <h1 class="mt-1 text-2xl font-black text-[var(--lvl-text)]">Check your email</h1>
        <p class="mt-2 text-sm text-[var(--lvl-muted)]">
            {{ __('Before getting started, verify your email address using the link we sent. If it did not arrive, we can send another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/15 px-3 py-2 text-sm font-medium text-emerald-200">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="w-full sm:w-auto">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg px-4 py-2.5 text-sm font-bold text-[var(--lvl-muted)] transition hover:text-[var(--lvl-p800)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)]">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
