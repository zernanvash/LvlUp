<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-red-400/30 bg-red-500/20 px-4 py-2.5 text-sm font-bold text-red-100 shadow-sm transition hover:bg-red-500/30 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg,#11101f)] active:translate-y-px disabled:cursor-not-allowed disabled:opacity-50']) }}>
    {{ $slot }}
</button>
