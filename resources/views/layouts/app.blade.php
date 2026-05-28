<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LvlUp - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            overflow-x: hidden;
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
        .btn-glow {
            position: relative; overflow: hidden;
            background: linear-gradient(90deg, var(--lvl-p200), var(--lvl-p400)) !important;
            color: var(--lvl-p800) !important;
            border: 1px solid var(--lvl-p100) !important;
            box-shadow: 0 2px 12px rgba(83,74,183,0.25) !important;
            transition: opacity .15s ease, transform .15s ease !important;
        }
        .btn-glow:hover { opacity: .9 !important; transform: translateY(-1px) !important; }
        .btn-secondary {
            background: var(--lvl-surface-soft) !important;
            border: 1px solid var(--lvl-border) !important;
            color: var(--lvl-muted) !important;
            border-radius: .5rem !important;
            transition: background .15s, border-color .15s, color .15s !important;
        }
        .btn-secondary:hover {
            background: var(--lvl-surface-raised) !important;
            border-color: var(--lvl-p100) !important;
            color: var(--lvl-p800) !important;
        }
        .btn-danger {
            background: rgba(239,107,107,0.08) !important;
            border: 1px solid rgba(239,107,107,0.2) !important;
            color: var(--lvl-red) !important;
            border-radius: .5rem !important;
            transition: background .15s, border-color .15s !important;
        }
        .btn-danger:hover {
            background: rgba(239,107,107,0.18) !important;
            border-color: rgba(239,107,107,0.4) !important;
        }
        .page-progress {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 100%;
            height: 3px;
            pointer-events: none;
            opacity: 0;
            transform-origin: left;
            transform: scaleX(.08);
            background: linear-gradient(90deg, var(--lvl-p600), var(--lvl-gold));
            transition: opacity .16s ease, transform .7s ease;
        }
        body.is-navigating .page-progress,
        body.is-submitting .page-progress {
            opacity: 1;
            transform: scaleX(.72);
        }
        body.is-loaded .page-progress {
            transform: scaleX(1);
        }
        .animated-bg, .stars, .star { display: none !important; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }
        .animate-skeleton-pulse {
            animation: pulse 1.8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .page-skeleton { display: none; }
        body.is-navigating main { display: none !important; }
        body.is-navigating .page-skeleton { display: block !important; }

        /* Page-specific loading states */
        .page-skeleton > div { display: none; }
        body.loading-dashboard .skeleton-dashboard { display: block; }
        body.loading-skill-tree .skeleton-skill-tree { display: block; }
        body.loading-projects .skeleton-projects { display: block; }
        body.loading-resume .skeleton-resume { display: block; }
        body.loading-achievements .skeleton-achievements { display: block; }
        body.loading-discover .skeleton-discover { display: block; }
        body.loading-profile .skeleton-profile { display: block; }

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

        @media (max-width: 640px) {
            body { min-width: 320px; }
            .lvl-topbar {
                min-height: 4.5rem;
                height: auto;
                padding-top: .75rem;
                padding-bottom: .75rem;
            }
            .lvl-nav-link {
                min-height: 2.75rem;
                padding: .72rem .75rem;
                font-size: .95rem;
            }
            .lvl-panel,
            .glow-border {
                border-radius: .65rem !important;
            }
            .card-hover:hover,
            .btn-glow:hover {
                transform: none !important;
            }
        }

        /* ── Global form elements (inputs only, not buttons) ── */
        main input[type=text],
        main input[type=url],
        main input[type=email],
        main input[type=password],
        main input[type=number],
        main textarea,
        main select {
            background: var(--lvl-surface-soft) !important;
            border: 1px solid var(--lvl-border) !important;
            border-radius: .5rem !important;
            color: var(--lvl-text) !important;
            font-size: .8125rem !important;
            font-family: inherit !important;
            transition: border-color .15s ease, box-shadow .15s ease !important;
        }
        main input[type=text]:focus,
        main input[type=url]:focus,
        main input[type=email]:focus,
        main input[type=password]:focus,
        main input[type=number]:focus,
        main textarea:focus,
        main select:focus {
            border-color: var(--lvl-p400) !important;
            box-shadow: 0 0 0 3px rgba(127,119,221,0.15) !important;
            outline: none !important;
        }
        main input::placeholder,
        main textarea::placeholder { color: var(--lvl-faint) !important; opacity: 1 !important; }
        main select option {
            background: var(--lvl-surface) !important;
            color: var(--lvl-text) !important;
        }
        main input[type=checkbox],
        main input[type=radio] { accent-color: var(--lvl-p400) !important; }
        main .text-red-400,
        main .text-red-500 { color: var(--lvl-red) !important; }
    </style>

    <script>
        window._toastQueue = [];
        window.pushToast = (opts) => window._toastQueue.push(opts);
    </script>
</head>
<body x-data="appShell()" class="min-h-screen">
<div class="page-progress" aria-hidden="true"></div>
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

    $tutorialCatalog = [
        'dashboard' => [
            'key' => 'page-dashboard',
            'label' => 'First look',
            'title' => 'Command Center',
            'body' => 'This page summarizes your level, XP, projects, badges, and suggested next actions. Use it as your home base when deciding what to improve next.',
            'steps' => ['Watch your XP bar for level progress.', 'Create or improve projects to earn XP.', 'Open the progression guide when you want the full rules.'],
        ],
        'skill-tree.index' => [
            'key' => 'page-skill-tree',
            'label' => 'Map basics',
            'title' => 'Skill Tree',
            'body' => 'Drag around the map, zoom with the controls, and open nodes to see what unlocks them. Green nodes are ready to claim.',
            'steps' => ['Tap any node to inspect requirements.', 'Add projects when a node needs more evidence.', 'Unlocked nodes can trigger XP, badges, and new paths.'],
        ],
        'projects.index' => [
            'key' => 'page-projects',
            'label' => 'Portfolio basics',
            'title' => 'Projects',
            'body' => 'Projects are the main proof you use to level up. Add real work, attach skills, and keep descriptions clear so the skill tree and resume builder have better data.',
            'steps' => ['Use New Project to add portfolio work.', 'Filter by type when your list grows.', 'Featured projects appear more prominently on your public profile.'],
        ],
        'projects.create' => [
            'key' => 'page-project-create',
            'label' => 'Project setup',
            'title' => 'Add a Project',
            'body' => 'A stronger project entry earns better XP and helps unlock skill nodes. Include what you built, what tools you used, and any live or repository links.',
            'steps' => ['Pick the closest project type.', 'Add skill tags that honestly match the work.', 'Use links or evidence when available.'],
        ],
        'resume.index' => [
            'key' => 'page-resume',
            'label' => 'Resume flow',
            'title' => 'Resume Builder',
            'body' => 'The resume builder pulls from your profile, selected projects, skills, and certificates. Complete your profile first for better AI output.',
            'steps' => ['Choose which projects to include.', 'Enter a target job title before generating.', 'Download the PDF after reviewing the generated content.'],
        ],
        'achievements.index' => [
            'key' => 'page-achievements',
            'label' => 'Badge basics',
            'title' => 'Achievements',
            'body' => 'Badges mark milestones and some can be equipped on your public profile. You can equip up to six earned badges.',
            'steps' => ['Earn badges through projects, skills, levels, and activity.', 'Use equip to control what appears on your profile.', 'Locked badges show progress when available.'],
        ],
        'users.index' => [
            'key' => 'page-discover',
            'label' => 'Discover basics',
            'title' => 'Discover',
            'body' => 'Discover helps you find public developer profiles. Search by name, title, or skills, then open profiles for portfolio inspiration.',
            'steps' => ['Use rank and sort filters to narrow results.', 'Open a profile to view projects and badges.', 'Your own visibility is managed from Profile.'],
        ],
        'profile.edit' => [
            'key' => 'page-profile',
            'label' => 'Profile basics',
            'title' => 'Profile',
            'body' => 'Your profile feeds the public profile and resume builder. Fill in title, bio, skills, links, and visibility settings when you are ready.',
            'steps' => ['Complete resume details for better generated PDFs.', 'Toggle public visibility intentionally.', 'Keep links and project evidence up to date.'],
        ],
    ];

    $currentTutorial = $tutorialCatalog[Route::currentRouteName()] ?? null;
@endphp

@if($currentTutorial)
<script>
window._lvlupPageTutorial = @json($currentTutorial);
</script>
@endif

<!-- Level-Up Overlay -->
<div
    x-show="showLevelUp"
    x-transition
    @click="showLevelUp = false"
    class="fixed inset-0 z-[9999] flex items-center justify-center cursor-pointer p-4"
    x-cloak
>
    <div class="absolute inset-0 backdrop-blur-sm" style="background: var(--lvl-overlay);"></div>
    <div class="lvl-panel relative z-10 w-full max-w-md p-5 text-center border-l-4 sm:p-8" style="border-left-color: var(--lvl-p600) !important;">
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
    <aside class="lvl-sidebar absolute inset-y-0 left-0 flex w-[min(18rem,calc(100vw-2rem))] flex-col">
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
    <header class="lvl-topbar sticky top-0 z-30 h-20 px-3 sm:px-6 lg:px-8 flex items-center justify-between gap-3">
        <div class="flex min-w-0 flex-1 items-center gap-3 sm:gap-4">
            <button @click="sidebarOpen = true" class="md:hidden h-11 w-11 flex-shrink-0 rounded-lg border border-[var(--lvl-border-soft)] bg-white text-[var(--lvl-p600)]">
                <i class="fas fa-bars"></i>
            </button>
            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl font-bold text-[var(--lvl-text)] truncate">@yield('page_title', 'Dashboard')</h1>
                <p class="text-xs sm:text-sm text-[var(--lvl-muted)] truncate">@yield('page_subtitle', 'Manage your progression')</p>
            </div>
        </div>

        @auth
        <div class="flex flex-shrink-0 items-center gap-2">
            <button
                type="button"
                @click="openPageTutorial(true)"
                x-show="pageTutorial"
                x-cloak
                class="hidden h-11 w-11 items-center justify-center rounded-lg border border-[var(--lvl-border-soft)] bg-white text-[var(--lvl-p600)] transition hover:border-[var(--lvl-p100)] sm:flex"
                aria-label="Show page tips"
                title="Show page tips"
            >
                <i class="fas fa-question"></i>
            </button>
            <a href="{{ route('profile.edit') }}" class="relative inline-flex hover:opacity-80 transition" style="padding:2px;">
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 border-2"
                     style="background:var(--lvl-p200);color:var(--lvl-p800);border-color:var(--lvl-p100);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="absolute -bottom-0.5 -right-1 text-[9px] font-bold leading-tight px-1 rounded-full border"
                      style="background:var(--lvl-p100);color:var(--lvl-p600);border-color:var(--lvl-surface);border-width:1.5px;">
                    {{ auth()->user()->level }}
                </span>
            </a>
        </div>
        @endauth
    </header>

    <!-- Global Skeleton Loader (visible during page transitions) -->
    <div class="page-skeleton px-3 py-5 sm:px-6 sm:py-6 lg:px-8 space-y-6 animate-skeleton-pulse" aria-hidden="true">
        <!-- 1. Dashboard Skeleton -->
        <div class="skeleton-dashboard space-y-6">
            <div class="lvl-panel p-4 flex items-center gap-3 border-l-4" style="border-left-color: var(--lvl-border-soft) !important;">
                <div class="h-8 w-8 bg-white/5 rounded-lg"></div>
                <div class="h-4 bg-white/5 rounded w-1/3"></div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-[1.35fr_.65fr] gap-6">
                <div class="lvl-panel p-6 border-l-4 space-y-4" style="border-left-color: var(--lvl-border-soft) !important;">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 bg-white/5 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 bg-white/5 rounded w-1/4"></div>
                            <div class="h-5 bg-white/5 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="h-3 bg-white/5 rounded w-full mt-4"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="lvl-panel p-4 flex flex-col items-center justify-center space-y-2">
                        <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        <div class="h-6 bg-white/5 rounded w-1/3"></div>
                    </div>
                    <div class="lvl-panel p-4 flex flex-col items-center justify-center space-y-2">
                        <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        <div class="h-6 bg-white/5 rounded w-1/3"></div>
                    </div>
                    <div class="lvl-panel p-4 flex flex-col items-center justify-center space-y-2">
                        <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        <div class="h-6 bg-white/5 rounded w-1/3"></div>
                    </div>
                    <div class="lvl-panel p-4 flex flex-col items-center justify-center space-y-2">
                        <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        <div class="h-6 bg-white/5 rounded w-1/3"></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="lvl-panel p-6 space-y-4">
                    <div class="h-4 bg-white/5 rounded w-1/4"></div>
                    <div class="h-40 bg-white/5 rounded-xl w-full"></div>
                </div>
                <div class="lvl-panel p-6 space-y-4">
                    <div class="h-4 bg-white/5 rounded w-1/4"></div>
                    <div class="h-40 bg-white/5 rounded-xl w-full"></div>
                </div>
            </div>
        </div>

        <!-- 2. Skill Tree Skeleton -->
        <div class="skeleton-skill-tree relative -mx-4 -my-6 h-[calc(100vh-5rem)] overflow-hidden sm:-mx-6 lg:-mx-8">
            <div class="absolute top-4 left-1/2 -translate-x-1/2 w-[calc(100%-1.5rem)] max-w-5xl z-10">
                <div class="lvl-panel flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-white/5 rounded-lg"></div>
                        <div class="space-y-1.5">
                            <div class="h-2.5 bg-white/5 rounded w-12"></div>
                            <div class="h-4 bg-white/5 rounded w-24"></div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="h-8 w-12 bg-white/5 rounded"></div>
                        <div class="h-8 w-12 bg-white/5 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="absolute inset-0 bg-[#16152a]/20 flex items-center justify-center">
                <div class="w-24 h-24 rounded-full border border-white/5 flex items-center justify-center animate-ping">
                    <i class="fas fa-sitemap text-3xl text-purple-500/20"></i>
                </div>
            </div>
            <div class="absolute bottom-6 right-6 flex flex-col gap-2 z-10">
                <div class="h-9 w-9 bg-white/5 rounded-lg border border-white/10"></div>
                <div class="h-9 w-9 bg-white/5 rounded-lg border border-white/10"></div>
            </div>
        </div>

        <!-- 3. Projects Skeleton -->
        <div class="skeleton-projects space-y-6">
            <div class="flex items-center justify-between">
                <div class="h-4 bg-white/5 rounded w-20"></div>
                <div class="h-9 w-32 bg-white/5 rounded-lg"></div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="h-7 w-12 bg-white/5 rounded-lg"></div>
                <div class="h-7 w-16 bg-white/5 rounded-lg"></div>
                <div class="h-7 w-20 bg-white/5 rounded-lg"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="lvl-panel p-4 space-y-4">
                    <div class="h-32 bg-white/5 rounded-lg w-full"></div>
                    <div class="h-4 bg-white/5 rounded w-3/4"></div>
                    <div class="h-3 bg-white/5 rounded w-full"></div>
                </div>
                <div class="lvl-panel p-4 space-y-4">
                    <div class="h-32 bg-white/5 rounded-lg w-full"></div>
                    <div class="h-4 bg-white/5 rounded w-3/4"></div>
                    <div class="h-3 bg-white/5 rounded w-full"></div>
                </div>
                <div class="lvl-panel p-4 space-y-4">
                    <div class="h-32 bg-white/5 rounded-lg w-full"></div>
                    <div class="h-4 bg-white/5 rounded w-3/4"></div>
                    <div class="h-3 bg-white/5 rounded w-full"></div>
                </div>
            </div>
        </div>

        <!-- 4. Resume Skeleton -->
        <div class="skeleton-resume grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="space-y-5">
                <div class="lvl-panel p-6 space-y-4">
                    <div class="h-4 bg-white/5 rounded w-1/3"></div>
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div class="h-12 bg-white/5 rounded-lg w-full"></div>
                        <div class="h-12 bg-white/5 rounded-lg w-full"></div>
                    </div>
                </div>
                <div class="lvl-panel p-5 space-y-3">
                    <div class="h-4 bg-white/5 rounded w-1/4"></div>
                    <div class="flex flex-wrap gap-2">
                        <div class="h-6 w-16 bg-white/5 rounded-full"></div>
                        <div class="h-6 w-20 bg-white/5 rounded-full"></div>
                    </div>
                </div>
            </div>
            <div class="lvl-panel p-6 space-y-6">
                <div class="h-5 bg-white/5 rounded w-1/3"></div>
                <div class="space-y-4">
                    <div class="h-12 bg-white/5 rounded-xl w-full"></div>
                    <div class="h-24 bg-white/5 rounded-xl w-full"></div>
                </div>
            </div>
        </div>

        <!-- 5. Achievements Skeleton -->
        <div class="skeleton-achievements space-y-6">
            <div class="lvl-panel p-6 border-l-4 space-y-4" style="border-left-color: var(--lvl-border-soft) !important;">
                <div class="h-5 bg-white/5 rounded w-1/4"></div>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    <div class="h-20 bg-white/5 rounded-xl"></div>
                    <div class="h-20 bg-white/5 rounded-xl"></div>
                    <div class="h-20 bg-white/5 rounded-xl"></div>
                    <div class="h-20 bg-white/5 rounded-xl"></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="lvl-panel p-5 flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/5 rounded-lg"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-2.5 bg-white/5 rounded w-12"></div>
                        <div class="h-4 bg-white/5 rounded w-8"></div>
                    </div>
                </div>
                <div class="lvl-panel p-5 flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/5 rounded-lg"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-2.5 bg-white/5 rounded w-12"></div>
                        <div class="h-4 bg-white/5 rounded w-8"></div>
                    </div>
                </div>
                <div class="lvl-panel p-5 flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/5 rounded-lg"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-2.5 bg-white/5 rounded w-12"></div>
                        <div class="h-4 bg-white/5 rounded w-8"></div>
                    </div>
                </div>
                <div class="lvl-panel p-5 flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/5 rounded-lg"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-2.5 bg-white/5 rounded w-12"></div>
                        <div class="h-4 bg-white/5 rounded w-8"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Discover Skeleton -->
        <div class="skeleton-discover space-y-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="h-10 bg-white/5 rounded-lg flex-1"></div>
                <div class="h-10 w-28 bg-white/5 rounded-lg"></div>
                <div class="h-10 w-28 bg-white/5 rounded-lg"></div>
                <div class="h-10 w-24 bg-white/5 rounded-lg"></div>
            </div>
            <div class="h-4 bg-white/5 rounded w-1/4"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="lvl-panel p-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 bg-white/5 rounded-full"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-4 bg-white/5 rounded w-2/3"></div>
                            <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="h-3 bg-white/5 rounded w-full"></div>
                </div>
                <div class="lvl-panel p-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 bg-white/5 rounded-full"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-4 bg-white/5 rounded w-2/3"></div>
                            <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="h-3 bg-white/5 rounded w-full"></div>
                </div>
                <div class="lvl-panel p-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 bg-white/5 rounded-full"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-4 bg-white/5 rounded w-2/3"></div>
                            <div class="h-3 bg-white/5 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="h-3 bg-white/5 rounded w-full"></div>
                </div>
            </div>
        </div>

        <!-- 7. Profile Skeleton -->
        <div class="skeleton-profile space-y-6">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <div class="h-9 w-20 bg-white/5 rounded-lg"></div>
                <div class="h-9 w-24 bg-white/5 rounded-lg"></div>
                <div class="h-9 w-20 bg-white/5 rounded-lg"></div>
                <div class="h-9 w-28 bg-white/5 rounded-lg"></div>
            </div>
            <div class="lvl-panel p-8 border-l-4 space-y-6" style="border-left-color: var(--lvl-border-soft) !important;">
                <div class="space-y-2">
                    <div class="h-6 bg-white/5 rounded w-1/5"></div>
                    <div class="h-3.5 bg-white/5 rounded w-1/3"></div>
                </div>
                <div class="space-y-4 pt-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <div class="h-3 bg-white/5 rounded w-1/4"></div>
                            <div class="h-10 bg-white/5 rounded-lg w-full"></div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="h-3 bg-white/5 rounded w-1/4"></div>
                            <div class="h-10 bg-white/5 rounded-lg w-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="px-3 py-5 sm:px-6 sm:py-6 lg:px-8">
        @yield('content')
    </main>
</div>

@yield('modals')
@stack('scripts')

<div class="fixed bottom-3 right-3 z-[9998] flex w-[calc(100vw-1.5rem)] max-w-sm flex-col gap-3 pointer-events-none sm:bottom-6 sm:right-6 sm:w-[calc(100vw-2rem)]" id="toastStack">
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

<div
    x-show="tutorialOpen && currentTutorial"
    x-cloak
    x-transition
    class="fixed inset-x-3 bottom-3 z-[9997] sm:bottom-6 sm:left-auto sm:right-6 sm:w-full sm:max-w-md"
>
    <div class="lvl-panel overflow-hidden">
        <div class="border-l-4 p-4 sm:p-5" style="border-left-color: var(--lvl-p600);">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border border-[var(--lvl-p100)] bg-[var(--lvl-p50)] text-[var(--lvl-p600)]">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="lvl-label" x-text="(currentTutorial && currentTutorial.label) || 'Quick tip'"></p>
                    <h2 class="mt-1 text-base font-black text-[var(--lvl-text)]" x-text="(currentTutorial && currentTutorial.title) || ''"></h2>
                    <p class="mt-2 text-sm leading-5 text-[var(--lvl-muted)]" x-text="(currentTutorial && currentTutorial.body) || ''"></p>
                </div>
                <button
                    type="button"
                    @click="dismissTutorial(false)"
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] text-[var(--lvl-muted)] hover:text-[var(--lvl-text)]"
                    aria-label="Close tutorial"
                >
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <ul class="mt-4 space-y-2 text-sm text-[var(--lvl-muted)]" x-show="currentTutorial && currentTutorial.steps && currentTutorial.steps.length">
                <template x-for="step in ((currentTutorial && currentTutorial.steps) || [])" :key="step">
                    <li class="flex gap-2">
                        <i class="fas fa-check mt-1 text-xs text-[var(--lvl-p600)]"></i>
                        <span x-text="step"></span>
                    </li>
                </template>
            </ul>

            <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-3">
                <button
                    type="button"
                    @click="completeTutorial()"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[var(--lvl-p100)] bg-[var(--lvl-p600)] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[var(--lvl-p400)]"
                >
                    Got it
                </button>
                <button
                    type="button"
                    @click="skipTutorial()"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[var(--lvl-border-soft)] bg-[var(--lvl-surface-raised)] px-4 py-2.5 text-sm font-bold text-[var(--lvl-muted)] transition hover:text-[var(--lvl-text)]"
                >
                    Skip
                </button>
                <button
                    type="button"
                    @click="muteTutorials()"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2.5 text-sm font-bold text-[var(--lvl-faint)] transition hover:text-[var(--lvl-muted)]"
                >
                    Hide all
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function appShell() {
    return {
        sidebarOpen: false,
        showLevelUp: false,
        levelUpData: {},
        toasts: [],
        _toastId: 0,
        pageTutorial: window._lvlupPageTutorial || null,
        currentTutorial: null,
        tutorialOpen: false,
        tutorialStoragePrefix: 'lvlup:tutorial:',
        tutorialsMutedKey: 'lvlup:tutorials-muted',

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

            window.addEventListener('lvlup-feature-hint', (event) => {
                this.openFeatureTutorial(event.detail || {});
            });

            this.$nextTick(() => {
                setTimeout(() => this.openPageTutorial(false), 450);
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

        hasSeenTutorial(key) {
            return !key || localStorage.getItem(this.tutorialStoragePrefix + key) === '1';
        },

        tutorialsMuted() {
            return localStorage.getItem(this.tutorialsMutedKey) === '1';
        },

        openPageTutorial(force = false) {
            if (!this.pageTutorial) return;
            if (!force && (this.tutorialsMuted() || this.hasSeenTutorial(this.pageTutorial.key))) return;
            this.currentTutorial = this.pageTutorial;
            this.tutorialOpen = true;
        },

        openFeatureTutorial(tutorial) {
            if (!tutorial.key || this.tutorialsMuted() || this.hasSeenTutorial(tutorial.key)) return;
            this.currentTutorial = tutorial;
            this.tutorialOpen = true;
        },

        dismissTutorial(markSeen = false) {
            if (markSeen && this.currentTutorial?.key) {
                localStorage.setItem(this.tutorialStoragePrefix + this.currentTutorial.key, '1');
            }
            this.tutorialOpen = false;
        },

        completeTutorial() {
            this.dismissTutorial(true);
        },

        skipTutorial() {
            this.dismissTutorial(true);
        },

        muteTutorials() {
            localStorage.setItem(this.tutorialsMutedKey, '1');
            this.dismissTutorial(true);
        },
    };
}
</script>
<script>
(() => {
    const prefetched = new Set();

    const sameOrigin = (url) => {
        try {
            const parsed = new URL(url, window.location.href);
            return parsed.origin === window.location.origin;
        } catch {
            return false;
        }
    };

    const shouldPrefetch = (anchor) => {
        if (!anchor || !anchor.href || anchor.target || anchor.hasAttribute('download')) return false;
        if (!sameOrigin(anchor.href)) return false;
        if (anchor.href.includes('#') || anchor.href === window.location.href) return false;
        return true;
    };

    const prefetch = (anchor) => {
        if (!shouldPrefetch(anchor) || prefetched.has(anchor.href)) return;
        prefetched.add(anchor.href);

        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = anchor.href;
        link.as = 'document';
        document.head.appendChild(link);
    };

    document.addEventListener('mouseover', (event) => {
        const anchor = event.target.closest('a');
        if (anchor) prefetch(anchor);
    }, { passive: true });

    document.addEventListener('touchstart', (event) => {
        const anchor = event.target.closest('a');
        if (anchor) prefetch(anchor);
    }, { passive: true });

    document.addEventListener('click', (event) => {
        const anchor = event.target.closest('a');
        if (shouldPrefetch(anchor)) {
            // Remove prior loading classes
            document.body.classList.forEach(cls => {
                if (cls.startsWith('loading-')) document.body.classList.remove(cls);
            });

            // Determine page category based on path
            const path = new URL(anchor.href, window.location.href).pathname;
            let pageType = 'dashboard';

            if (path === '/' || path.includes('/dashboard')) {
                pageType = 'dashboard';
            } else if (path.includes('/skill-tree')) {
                pageType = 'skill-tree';
            } else if (path.includes('/projects')) {
                pageType = 'projects';
            } else if (path.includes('/resume')) {
                pageType = 'resume';
            } else if (path.includes('/achievements')) {
                pageType = 'achievements';
            } else if (path.includes('/users') || path.includes('/discover')) {
                pageType = 'discover';
            } else if (path.includes('/profile')) {
                pageType = 'profile';
            }

            document.body.classList.add('is-navigating', `loading-${pageType}`);
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noBusy === 'true') return;

        document.body.classList.add('is-submitting');
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
            button.dataset.originalHTML = button.innerHTML;
            button.dataset.originalValue = button.value || '';
            button.disabled = true;
            if (button.tagName === 'BUTTON') {
                button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${button.dataset.loadingText || 'Saving...'}`;
            } else if (button.tagName === 'INPUT') {
                button.value = button.dataset.loadingText || 'Saving...';
            }
        });
    });

    window.addEventListener('pageshow', () => {
        document.body.classList.remove('is-navigating', 'is-submitting');
        document.body.classList.forEach(cls => {
            if (cls.startsWith('loading-')) document.body.classList.remove(cls);
        });
        document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
            button.disabled = false;
            if (button.tagName === 'BUTTON' && button.dataset.originalHTML) {
                button.innerHTML = button.dataset.originalHTML;
            } else if (button.tagName === 'INPUT' && button.dataset.originalValue) {
                button.value = button.dataset.originalValue;
            }
        });
        requestAnimationFrame(() => {
            document.body.classList.add('is-loaded');
            setTimeout(() => document.body.classList.remove('is-loaded'), 260);
        });
    });
})();
</script>
</body>
</html>
