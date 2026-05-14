<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LvlUp - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
            --lvl-p200: #534ab7;
            --lvl-p400: #7f77dd;
            --lvl-p600: #afa9ec;
            --lvl-p800: #eeedfe;
            --lvl-gold: #ef9f27;
            --lvl-green: #9bcf5a;
            --lvl-red: #ef6b6b;
            --lvl-body-bg:
                radial-gradient(circle at top left, rgba(127, 119, 221, 0.22), transparent 28rem),
                linear-gradient(180deg, rgba(17, 16, 31, 0.96), rgba(11, 10, 21, 1)),
                var(--lvl-bg);
            --lvl-sidebar-bg: rgba(18, 16, 32, 0.94);
            --lvl-topbar-bg: rgba(18, 16, 32, 0.84);
            --lvl-panel-bg: rgba(25, 23, 43, 0.94);
            --lvl-shadow: 0 18px 42px rgba(0, 0, 0, 0.28);
            --lvl-hover-shadow: 0 16px 36px rgba(0, 0, 0, 0.24);
            --lvl-overlay: rgba(7, 6, 16, 0.72);
        }

        [x-cloak] { display: none !important; }
        * { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body {
            min-height: 100vh;
            color: var(--lvl-text);
            background: var(--lvl-body-bg);
        }
        .font-display { font-family: Inter, ui-sans-serif, system-ui, sans-serif; letter-spacing: 0; }

        .lvl-sidebar {
            background: var(--lvl-sidebar-bg);
            border-right: 1px solid var(--lvl-border-soft);
            box-shadow: var(--lvl-shadow);
        }
        .lvl-topbar {
            background: var(--lvl-topbar-bg);
            border-bottom: 1px solid var(--lvl-border-soft);
            backdrop-filter: blur(18px);
        }
        .lvl-nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            border-radius: .5rem;
            padding: .62rem .75rem;
            color: var(--lvl-muted);
            font-size: .9rem;
            font-weight: 600;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
            border: 1px solid transparent;
        }
        .lvl-nav-link:hover {
            background: var(--lvl-surface-soft);
            color: var(--lvl-p800);
            border-color: var(--lvl-border-soft);
        }
        .lvl-nav-link.active {
            background: var(--lvl-p50);
            border-color: var(--lvl-p100);
            color: var(--lvl-p800);
        }
        .lvl-nav-link i { width: 1rem; text-align: center; color: currentColor; }

        .lvl-panel,
        .glow-border {
            background: var(--lvl-panel-bg) !important;
            border: 1px solid var(--lvl-border-soft) !important;
            border-radius: .75rem !important;
            box-shadow: var(--lvl-shadow) !important;
            backdrop-filter: blur(12px);
        }
        .lvl-panel-tight { padding: 1rem 1.25rem; }
        .lvl-label {
            color: var(--lvl-faint);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .lvl-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            border: 1px solid var(--lvl-p100);
            background: var(--lvl-p50);
            color: var(--lvl-p800);
            padding: .2rem .55rem;
            font-size: .72rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .lvl-chip.gold { background: #faeeda; border-color: #f3cf91; color: #754706; }
        .lvl-chip.green { background: #eaf3de; border-color: #c7dda5; color: #31560c; }
        .lvl-chip.gray { background: var(--lvl-surface-soft); border-color: var(--lvl-border); color: var(--lvl-muted); }
        .lvl-action { color: #ffffff !important; }
        .lvl-xp-bg {
            height: .5rem;
            overflow: hidden;
            border-radius: 999px;
            background: var(--lvl-surface-soft);
            border: 1px solid var(--lvl-border-soft);
        }
        .lvl-xp-fill,
        .xp-bar,
        .xp-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--lvl-p600), var(--lvl-p400)) !important;
            transition: width .8s ease;
        }

        .card-hover { transition: transform .16s ease, box-shadow .16s ease; }
        .card-hover:hover { transform: translateY(-2px) !important; box-shadow: var(--lvl-hover-shadow) !important; }
        .btn-glow { position: relative; overflow: hidden; }
        .btn-glow:hover { transform: translateY(-1px); }
        .animated-bg, .stars, .star { display: none !important; }

        main .glow-border .text-white,
        main .lvl-panel .text-white { color: var(--lvl-text) !important; }
        main .glow-border .lvl-action,
        main .lvl-panel .lvl-action { color: #ffffff !important; }
        main .text-gray-100, main .text-gray-200, main .text-gray-300 { color: var(--lvl-muted) !important; }
        main .text-gray-400, main .text-gray-500, main .text-purple-300, main .text-purple-400 { color: var(--lvl-muted) !important; }
        main .border-white\/10, main .border-white\/20, main .border-purple-500\/30 { border-color: var(--lvl-border-soft) !important; }
        main .bg-white,
        .lvl-topbar .bg-white,
        .lvl-sidebar .bg-white,
        main .bg-white\/5,
        main .bg-white\/10,
        main .bg-black\/30,
        main .bg-black\/40 {
            background: var(--lvl-surface-raised) !important;
        }

        .level-badge {
            background: var(--lvl-p600);
            color: white;
            border-radius: 999px;
        }
    </style>

    <script>
        window._toastQueue = [];
        window.pushToast = (opts) => window._toastQueue.push(opts);
    </script>
</head>
<body x-data="appShell()" class="min-h-screen">
@if(session('level_up'))
<script>window._levelUpData = @json(session('level_up'));</script>
@endif
@if(session('new_badges'))
<script>window._newBadges = @json(session('new_badges'));</script>
@endif
@if(session('nodes_ready'))
<script>window._nodesReady = @json(session('nodes_ready'));</script>
@endif

@php
    $navItems = [
        ['href' => route('dashboard'), 'match' => 'dashboard', 'icon' => 'fas fa-gauge-high', 'label' => 'Dashboard'],
        ['href' => route('skill-tree.index'), 'match' => 'skill-tree*', 'icon' => 'fas fa-network-wired', 'label' => 'Skill Tree'],
        ['href' => route('projects.index'), 'match' => 'projects*', 'icon' => 'fas fa-folder-open', 'label' => 'Projects'],
        ['href' => route('resume.index'), 'match' => 'resume*', 'icon' => 'fas fa-file-alt', 'label' => 'Resume'],
        ['href' => route('achievements.index'), 'match' => 'achievements', 'icon' => 'fas fa-trophy', 'label' => 'Achievements'],
        ['href' => route('users.index'), 'match' => 'users*', 'icon' => 'fas fa-users', 'label' => 'Discover'],
    ];
@endphp

<!-- Level-Up Overlay -->
<div
    x-show="showLevelUp"
    x-transition
    @click="showLevelUp = false"
    class="fixed inset-0 z-[9999] flex items-center justify-center cursor-pointer p-4"
    x-cloak
>
    <div class="absolute inset-0 backdrop-blur-sm" style="background: var(--lvl-overlay);"></div>
    <div class="lvl-panel relative z-10 w-full max-w-md p-8 text-center border-l-4" style="border-left-color: var(--lvl-p600) !important;">
        <div class="mx-auto mb-4 h-16 w-16 rounded-full bg-[var(--lvl-p50)] border border-[var(--lvl-p100)] flex items-center justify-center text-[var(--lvl-p600)]">
            <i class="fas fa-crown text-2xl"></i>
        </div>
        <p class="lvl-label mb-2">Achievement Unlocked</p>
        <h1 class="text-3xl font-black text-[var(--lvl-text)]">LEVEL UP</h1>
        <div class="text-6xl font-black text-[var(--lvl-p600)] mt-2" x-text="levelUpData.new_level"></div>
        <p class="text-sm text-[var(--lvl-muted)] mt-2" x-text="levelUpData.rank_title"></p>
        <p class="text-xs text-[var(--lvl-faint)] mt-5">Click anywhere to continue</p>
    </div>
</div>

<!-- Desktop Sidebar -->
<aside class="lvl-sidebar fixed inset-y-0 left-0 z-40 hidden w-72 flex-col md:flex">
    <div class="h-20 px-6 flex items-center border-b border-[var(--lvl-border-soft)]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-application-logo class="h-10 w-32" />
        </a>
    </div>

    @auth
    <div class="p-5">
        <div class="lvl-panel lvl-panel-tight border-l-4" style="border-left-color: var(--lvl-p400) !important;">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=534ab7&color=fff&size=64' }}"
                     class="h-12 w-12 rounded-full border border-[var(--lvl-p100)] object-cover"
                     alt="{{ auth()->user()->name }}">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-[var(--lvl-text)]">{{ auth()->user()->name }}</p>
                    <span class="lvl-chip">{{ auth()->user()->getRankTitle() }}</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="mb-1 flex justify-between text-[11px] font-semibold text-[var(--lvl-muted)]">
                    <span>Level {{ auth()->user()->level }}</span>
                    <span>{{ number_format(auth()->user()->xpProgress(), 1) }}%</span>
                </div>
                <div class="lvl-xp-bg">
                    <div class="lvl-xp-fill" style="width: {{ auth()->user()->xpProgress() }}%;"></div>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <nav class="flex-1 space-y-1 px-4">
        @foreach($navItems as $item)
            <a href="{{ $item['href'] }}" class="lvl-nav-link {{ Request::is($item['match']) ? 'active' : '' }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    @auth
    <div class="p-4 border-t border-[var(--lvl-border-soft)]">
        <a href="{{ route('profile.edit') }}" class="lvl-nav-link {{ Request::is('profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" class="lvl-nav-link w-full">
                <i class="fas fa-right-from-bracket text-[var(--lvl-red)]"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
    @endauth
</aside>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 md:hidden">
    <div class="absolute inset-0 backdrop-blur-sm" style="background: var(--lvl-overlay);" @click="sidebarOpen = false"></div>
    <aside class="lvl-sidebar absolute inset-y-0 left-0 flex w-72 flex-col">
        <div class="h-20 px-5 flex items-center justify-between border-b border-[var(--lvl-border-soft)]">
            <x-application-logo class="h-10 w-32" />
            <button @click="sidebarOpen = false" class="h-9 w-9 rounded-lg border border-[var(--lvl-border-soft)] bg-white text-[var(--lvl-muted)]">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="flex-1 space-y-1 p-4">
            @foreach($navItems as $item)
                <a href="{{ $item['href'] }}" class="lvl-nav-link {{ Request::is($item['match']) ? 'active' : '' }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </aside>
</div>

<div class="min-h-screen md:pl-72">
    <header class="lvl-topbar sticky top-0 z-30 h-20 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center gap-4 min-w-0">
            <button @click="sidebarOpen = true" class="md:hidden h-10 w-10 rounded-lg border border-[var(--lvl-border-soft)] bg-white text-[var(--lvl-p600)]">
                <i class="fas fa-bars"></i>
            </button>
            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl font-bold text-[var(--lvl-text)] truncate">@yield('page_title', 'Dashboard')</h1>
                <p class="text-xs sm:text-sm text-[var(--lvl-muted)] truncate">@yield('page_subtitle', 'Manage your progression')</p>
            </div>
        </div>

        @auth
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-full border border-[var(--lvl-border-soft)] bg-white px-2 py-1.5 hover:border-[var(--lvl-p100)] transition">
            <span class="hidden sm:block text-right">
                <span class="block text-xs font-bold text-[var(--lvl-text)]">{{ auth()->user()->name }}</span>
                <span class="block text-[11px] text-[var(--lvl-muted)]">Lv. {{ auth()->user()->level }}</span>
            </span>
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=534ab7&color=fff&size=48' }}"
                 class="h-9 w-9 rounded-full object-cover"
                 alt="{{ auth()->user()->name }}">
        </a>
        @endauth
    </header>

    <main class="px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</div>

@yield('modals')
@stack('scripts')

<div class="fixed bottom-6 right-6 z-[9998] flex w-[calc(100vw-2rem)] max-w-sm flex-col gap-3 pointer-events-none" id="toastStack">
    <template x-for="toast in toasts" :key="toast.id">
        <div class="pointer-events-auto lvl-panel relative overflow-hidden p-4 flex items-start gap-3"
            x-show="toast.visible"
            x-transition
            @click="dismissToast(toast.id)">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center flex-shrink-0"
                :style="`background: ${toast.color || '#534ab7'}22; color: ${toast.color || '#534ab7'}; border: 1px solid ${toast.color || '#534ab7'}44`">
                <i :class="toast.icon || 'fas fa-bell'"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="lvl-label" x-text="toast.label"></p>
                <p class="text-sm font-bold text-[var(--lvl-text)]" x-text="toast.title"></p>
                <p x-show="toast.sub" class="text-xs text-[var(--lvl-muted)] mt-0.5" x-text="toast.sub"></p>
            </div>
            <div class="absolute bottom-0 left-0 h-1"
                :style="`width: ${toast.progress}%; background: ${toast.color || '#534ab7'}; transition: width ${toast.duration}ms linear;`"></div>
        </div>
    </template>
</div>

<script>
function appShell() {
    return {
        sidebarOpen: false,
        showLevelUp: false,
        levelUpData: {},
        toasts: [],
        _toastId: 0,

        init() {
            window.pushToast = (opts) => this.pushToast(opts);
            if (window._toastQueue && window._toastQueue.length) {
                window._toastQueue.forEach(opts => this.pushToast(opts));
                window._toastQueue = [];
            }

            if (window._levelUpData) {
                this.levelUpData = window._levelUpData;
                this.showLevelUp = true;
                setTimeout(() => this.showLevelUp = false, 5000);
            }

            if (window._newBadges && window._newBadges.length) {
                window._newBadges.forEach((badge, idx) => {
                    setTimeout(() => this.pushToast({
                        label: badge.rarity.toUpperCase() + ' BADGE UNLOCKED',
                        title: badge.title,
                        sub: '+' + badge.xp_reward + ' XP',
                        icon: badge.icon,
                        color: badge.rarity_color,
                        duration: 5000,
                    }), idx * 600);
                });
            }

            if (window._nodesReady && window._nodesReady.length) {
                const badgeDelay = window._newBadges ? window._newBadges.length * 600 : 0;
                window._nodesReady.forEach((node, idx) => {
                    setTimeout(() => this.pushToast({
                        label: 'SKILL READY TO UNLOCK',
                        title: node.title,
                        sub: 'Head to the Skill Tree to claim it.',
                        icon: node.icon,
                        color: node.color,
                        duration: 6000,
                    }), badgeDelay + idx * 700);
                });
            }

            document.body.addEventListener('trigger-level-up', (e) => {
                this.levelUpData = e.detail;
                this.showLevelUp = true;
                setTimeout(() => this.showLevelUp = false, 5000);
            });
        },

        pushToast({ label, title, sub, icon, color, duration = 4000 }) {
            const id = ++this._toastId;
            const toast = { id, label, title, sub, icon, color, duration, visible: true, progress: 100 };
            this.toasts.push(toast);
            this.$nextTick(() => {
                setTimeout(() => {
                    const t = this.toasts.find(x => x.id === id);
                    if (t) t.progress = 0;
                }, 50);
            });
            setTimeout(() => this.dismissToast(id), duration + 300);
        },

        dismissToast(id) {
            const t = this.toasts.find(x => x.id === id);
            if (t) t.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 300);
        },
    };
}
</script>
</body>
</html>
