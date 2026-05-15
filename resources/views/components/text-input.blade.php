@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'min-h-11 rounded-lg border border-[var(--lvl-border-soft,#302c4a)] bg-[var(--lvl-surface-raised,#211f35)] px-3 py-2.5 text-base text-[var(--lvl-text,#f7f4ff)] shadow-sm placeholder:text-[var(--lvl-faint,#928aa8)] focus:border-[var(--lvl-p400,#7f77dd)] focus:ring-[var(--lvl-p400,#7f77dd)] sm:text-sm disabled:cursor-not-allowed disabled:opacity-60']) }}>
