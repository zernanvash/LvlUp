@props([
    'code',
    'title',
    'heading',
    'message',
    'icon' => 'fas fa-triangle-exclamation',
    'primaryLabel' => 'Go home',
    'primaryHref' => '/',
    'primaryAction' => null,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'secondaryAction' => null,
    'status' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} - {{ $title }} | LvlUp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            color-scheme: dark;
            --lvl-bg: #11101f;
            --lvl-surface: #19172b;
            --lvl-surface-raised: #211f35;
            --lvl-border-soft: #302c4a;
            --lvl-text: #f7f4ff;
            --lvl-muted: #c5bed8;
            --lvl-faint: #928aa8;
            --lvl-p50: #2a2551;
            --lvl-p100: #3c3489;
            --lvl-p400: #7f77dd;
            --lvl-p600: #afa9ec;
            --lvl-p800: #eeedfe;
            --lvl-gold: #ef9f27;
            --lvl-body-bg:
                radial-gradient(circle at top left, rgba(127, 119, 221, 0.22), transparent 28rem),
                linear-gradient(180deg, rgba(17, 16, 31, 0.96), rgba(11, 10, 21, 1)),
                var(--lvl-bg);
            --lvl-panel-bg: rgba(25, 23, 43, 0.94);
            --lvl-shadow: 0 18px 42px rgba(0, 0, 0, 0.28);
        }

        * { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        html { min-width: 320px; }
        body {
            min-height: 100dvh;
            overflow-x: hidden;
            color: var(--lvl-text);
            background: var(--lvl-body-bg);
        }
    </style>
</head>
<body class="px-3 py-5 sm:px-6 sm:py-8">
    <main class="mx-auto flex min-h-[calc(100dvh-2.5rem)] w-full max-w-3xl items-center justify-center sm:min-h-[calc(100dvh-4rem)]">
        <section class="w-full rounded-xl border border-[var(--lvl-border-soft)] bg-[var(--lvl-panel-bg)] p-4 text-center shadow-[var(--lvl-shadow)] backdrop-blur sm:p-8">
            <a href="/" class="mx-auto mb-6 inline-flex items-center justify-center sm:mb-8">
                <x-application-logo class="h-9 w-28 sm:h-10 sm:w-32" />
            </a>

            <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-xl border border-[var(--lvl-p100)] bg-[var(--lvl-p50)] text-[var(--lvl-p600)] sm:mb-6 sm:h-16 sm:w-16">
                <i class="{{ $icon }} text-xl sm:text-2xl"></i>
            </div>

            <p class="text-xs font-bold uppercase tracking-wider text-[var(--lvl-faint)] sm:text-sm">{{ $status ?? 'Error code' }}</p>
            <h1 class="mt-2 text-5xl font-black leading-none text-[var(--lvl-p600)] sm:text-7xl">{{ $code }}</h1>
            <h2 class="mt-4 text-xl font-black text-[var(--lvl-text)] sm:text-3xl">{{ $heading }}</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-[var(--lvl-muted)] sm:text-base">{{ $message }}</p>

            <div class="mt-7 flex flex-col items-stretch justify-center gap-3 sm:mt-8 sm:flex-row sm:items-center">
                @if ($primaryAction === 'reload')
                    <button onclick="window.location.reload()" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-p100)] bg-[var(--lvl-p600)] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[var(--lvl-p400)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)] active:translate-y-px">
                        <i class="fas fa-rotate-right"></i>
                        {{ $primaryLabel }}
                    </button>
                @elseif ($primaryAction === 'back')
                    <button onclick="window.history.back()" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-p100)] bg-[var(--lvl-p600)] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[var(--lvl-p400)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)] active:translate-y-px">
                        <i class="fas fa-arrow-left"></i>
                        {{ $primaryLabel }}
                    </button>
                @else
                    <a href="{{ $primaryHref }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-p100)] bg-[var(--lvl-p600)] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[var(--lvl-p400)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)] active:translate-y-px">
                        <i class="fas fa-home"></i>
                        {{ $primaryLabel }}
                    </a>
                @endif

                @if ($secondaryLabel)
                    @if ($secondaryAction === 'reload')
                        <button onclick="window.location.reload()" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] px-5 py-2.5 text-sm font-bold text-[var(--lvl-muted)] shadow-sm transition hover:border-[var(--lvl-p100)] hover:text-[var(--lvl-text)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)] active:translate-y-px">
                            <i class="fas fa-rotate-right"></i>
                            {{ $secondaryLabel }}
                        </button>
                    @elseif ($secondaryAction === 'back')
                        <button onclick="window.history.back()" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] px-5 py-2.5 text-sm font-bold text-[var(--lvl-muted)] shadow-sm transition hover:border-[var(--lvl-p100)] hover:text-[var(--lvl-text)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)] active:translate-y-px">
                            <i class="fas fa-arrow-left"></i>
                            {{ $secondaryLabel }}
                        </button>
                    @else
                        <a href="{{ $secondaryHref }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] px-5 py-2.5 text-sm font-bold text-[var(--lvl-muted)] shadow-sm transition hover:border-[var(--lvl-p100)] hover:text-[var(--lvl-text)] focus:outline-none focus:ring-2 focus:ring-[var(--lvl-p600)] focus:ring-offset-2 focus:ring-offset-[var(--lvl-bg)] active:translate-y-px">
                            <i class="fas fa-arrow-left"></i>
                            {{ $secondaryLabel }}
                        </a>
                    @endif
                @endif
            </div>
        </section>
    </main>
</body>
</html>
