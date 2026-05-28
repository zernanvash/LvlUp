@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-[var(--lvl-muted,#c5bed8)]']) }}>
    {{ $value ?? $slot }}
</label>
