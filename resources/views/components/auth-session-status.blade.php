@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-emerald-400/30 bg-emerald-500/15 px-3 py-2 text-sm font-medium leading-5 text-emerald-200']) }}>
        {{ $status }}
    </div>
@endif
