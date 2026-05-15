<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LvlUp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <style>
            :root {
                color-scheme: dark;
                --lvl-bg: #11101f;
                --lvl-surface: #19172b;
                --lvl-surface-raised: #211f35;
                --lvl-surface-soft: #25223a;
                --lvl-border: #3c375d;
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
                --lvl-red: #ef6b6b;
                --lvl-body-bg:
                    radial-gradient(circle at top left, rgba(127, 119, 221, 0.22), transparent 28rem),
                    linear-gradient(180deg, rgba(17, 16, 31, 0.96), rgba(11, 10, 21, 1)),
                    var(--lvl-bg);
                --lvl-panel-bg: rgba(25, 23, 43, 0.94);
                --lvl-shadow: 0 18px 42px rgba(0, 0, 0, 0.28);
            }
            html { min-width: 320px; }
            body {
                min-height: 100dvh;
                overflow-x: hidden;
                color: var(--lvl-text);
                background: var(--lvl-body-bg);
            }
        </style>

        <div class="flex min-h-dvh items-center justify-center px-3 py-5 sm:px-6 sm:py-8">
            <div class="w-full max-w-md">
                <div class="mb-5 flex justify-center sm:mb-8">
                    <a href="/" class="inline-flex items-center justify-center rounded-xl border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface)] px-4 py-2.5 shadow-[var(--lvl-shadow)] sm:px-5 sm:py-3">
                        <x-application-logo class="h-9 w-28 sm:h-10 sm:w-32" />
                    </a>
                </div>

                <div class="rounded-xl border border-[var(--lvl-border-soft)] bg-[var(--lvl-panel-bg)] p-4 shadow-[var(--lvl-shadow)] backdrop-blur sm:p-7">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
