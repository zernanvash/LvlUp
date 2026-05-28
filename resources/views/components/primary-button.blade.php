<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-p100,#3c3489)] bg-[var(--lvl-p600,#534ab7)] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[var(--lvl-p400,#7f77dd)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600,#afa9ec)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg,#11101f)] active:translate-y-px disabled:cursor-not-allowed disabled:opacity-50']) }}>
    {{ $slot }}
</button>
