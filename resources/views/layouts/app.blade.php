<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LvlUp - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        
        * {
            font-family: 'Rajdhani', sans-serif;
        }
        
        .font-display {
            font-family: 'Orbitron', monospace;
        }
        
        /* Animated gradient background */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a1d3e, #2d1b4e, #1e0a3c);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        #skillTreeContainer {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
            touch-action: none;
        }

        #skillTreeCanvas {
            transform-origin: top left;
        }

        #skillTreeViewport {
            user-select: none;
            cursor: grab;
        }

        #skillTreeViewport:active {
            cursor: grabbing;
        }
        
        /* Star field background */
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        
        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            opacity: 0;
            animation: twinkle 3s infinite;
        }
        
        @keyframes twinkle {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }
        
        /* Glowing borders */
        .glow-border {
            position: relative;
            border: 2px solid transparent;
            background: linear-gradient(#1a1d3e, #1a1d3e) padding-box,
                        linear-gradient(135deg, #a78bfa, #ec4899, #f59e0b) border-box;
        }
        
        .glow-border-gold {
            border: 2px solid transparent;
            background: linear-gradient(#1a1d3e, #1a1d3e) padding-box,
                        linear-gradient(135deg, #fbbf24, #f59e0b, #d97706) border-box;
        }
        
        /* Rarity glows */
        .rarity-common { box-shadow: 0 0 20px rgba(156, 163, 175, 0.3); }
        .rarity-rare { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
        .rarity-epic { box-shadow: 0 0 30px rgba(168, 85, 247, 0.6); }
        .rarity-legendary { box-shadow: 0 0 40px rgba(245, 158, 11, 0.7); }
        .rarity-mythic { box-shadow: 0 0 50px rgba(236, 72, 153, 0.8); }
        
        /* XP Bar animation */
        .xp-bar {
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Card hover effect */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
        }
        
        /* Particle effect */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Button glow */
        .btn-glow {
            position: relative;
            overflow: hidden;
        }
        
        .btn-glow::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-glow:hover::before {
            width: 300px;
            height: 300px;
        }
        
        /* Shimmer effect */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }
        
        /* Level badge */
        .level-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            clip-path: polygon(15% 0%, 85% 0%, 100% 50%, 85% 100%, 15% 100%, 0% 50%);
        }

            /* ===== SKILL TREE DESIGN IMPROVEMENTS ===== */

        .skill-node::before {
            content: "";
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 2px dashed rgba(139,92,246,0.3);
            animation: rotate 20s linear infinite;
            pointer-events: none;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .skill-node[data-state="unlocked"] {
            animation: unlockPop 0.5s ease;
        }

        @keyframes unlockPop {
            0% { transform: scale(0.5); }
            100% { transform: scale(1); }
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1a1d3e;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #a78bfa, #ec4899);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #c4b5fd, #f9a8d4);
        }
    </style>
    
    <script>
        // Generate random stars on page load
        document.addEventListener('DOMContentLoaded', () => {
            const starsContainer = document.querySelector('.stars');
            const starCount = 50;
            
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                star.style.animationDelay = `${Math.random() * 3}s`;
                starsContainer.appendChild(star);
            }
        });
    </script>
</head>
<body class="animated-bg text-gray-100 min-h-screen" x-data="{ 
    sidebarOpen: false, 
    showLevelUp: false,
    showBadgeUnlock: false,
    newBadge: null
}">
    <!-- Star field background -->
    <div class="stars"></div>
    
    <!-- Sidebar -->
    <aside 
        x-show="sidebarOpen" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-80 bg-gradient-to-b from-[#1a1d3e]/95 to-[#0a0e27]/95 backdrop-blur-xl border-r-2 border-purple-500/30 z-50 flex flex-col shadow-2xl"
        style="box-shadow: 0 0 60px rgba(168, 85, 247, 0.2);"
    >
        <!-- Sidebar Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b-2 border-purple-500/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-crown text-2xl text-yellow-300"></i>
                </div>
                <div>
                    <h1 class="font-display text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                        LvlUp
                    </h1>
                    <p class="text-xs text-purple-300">Knowledge Mastery System</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="p-2 hover:bg-purple-500/20 rounded-lg transition">
                <i class="fas fa-times text-xl text-purple-300"></i>
            </button>
        </div>

        <!-- User Stats Card -->
        @auth
        <div class="p-6">
            <div class="glow-border rounded-2xl p-6 bg-gradient-to-br from-[#2d1b4e]/50 to-[#1a1d3e]/50 backdrop-blur">
                <div class="flex items-center gap-4 mb-4">
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=a78bfa&color=fff&size=64' }}" 
                             class="w-16 h-16 rounded-2xl border-2 border-purple-400 shadow-lg">
                        <div class="level-badge absolute -bottom-2 -right-2 w-8 h-8 flex items-center justify-center">
                            <span class="font-display text-xs font-bold text-white">{{ auth()->user()->level }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white text-lg">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-purple-300">{{ auth()->user()->title }}</p>
                    </div>
                </div>
                
                <!-- XP Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-purple-300 mb-1">
                        <span>Level {{ auth()->user()->level }}</span>
                        <span>{{ auth()->user()->xp }} / {{ auth()->user()->xpNeededForNextLevel() }} XP</span>
                    </div>
                    <div class="h-3 bg-purple-950/50 rounded-full overflow-hidden border border-purple-500/30">
                        <div class="xp-bar h-full bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 shimmer" 
                             style="width: {{ auth()->user()->xpProgress() }}%;"></div>
                    </div>
                </div>
                
                <!-- Rank Display -->
                <div class="bg-purple-950/30 rounded-lg p-3 border border-purple-500/20">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-trophy text-amber-400"></i>
                        <div>
                            <p class="text-xs text-purple-300">Rank</p>
                            <p class="font-display font-bold text-white">{{ auth()->user()->rank }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endauth

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-2 space-y-2 overflow-y-auto">
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-500/20 transition group {{ Request::is('dashboard') ? 'bg-purple-500/30 border-l-4 border-purple-400' : '' }}">
                <i class="fas fa-home w-5 text-center {{ Request::is('dashboard') ? 'text-purple-300' : 'text-gray-400 group-hover:text-purple-300' }}"></i>
                <span class="font-medium {{ Request::is('dashboard') ? 'text-white' : 'text-gray-300' }}">Dashboard</span>
            </a>
            
            <a href="/skill-tree" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-500/20 transition group {{ Request::is('skill-tree') ? 'bg-purple-500/30 border-l-4 border-purple-400' : '' }}">
                <i class="fas fa-network-wired w-5 text-center {{ Request::is('skill-tree') ? 'text-purple-300' : 'text-gray-400 group-hover:text-purple-300' }}"></i>
                <span class="font-medium {{ Request::is('skill-tree') ? 'text-white' : 'text-gray-300' }}">Skill Tree</span>
            </a>
            
            <a href="/projects" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-500/20 transition group {{ Request::is('projects*') ? 'bg-purple-500/30 border-l-4 border-purple-400' : '' }}">
                <i class="fas fa-folder-open w-5 text-center {{ Request::is('projects*') ? 'text-purple-300' : 'text-gray-400 group-hover:text-purple-300' }}"></i>
                <span class="font-medium {{ Request::is('projects*') ? 'text-white' : 'text-gray-300' }}">Projects</span>
            </a>
            
            <a href="/achievements" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-500/20 transition group {{ Request::is('achievements') ? 'bg-purple-500/30 border-l-4 border-purple-400' : '' }}">
                <i class="fas fa-trophy w-5 text-center {{ Request::is('achievements') ? 'text-purple-300' : 'text-gray-400 group-hover:text-purple-300' }}"></i>
                <span class="font-medium {{ Request::is('achievements') ? 'text-white' : 'text-gray-300' }}">Achievements</span>
            </a>
            
            <a href="/resume" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-500/20 transition group {{ Request::is('resume') ? 'bg-purple-500/30 border-l-4 border-purple-400' : '' }}">
                <i class="fas fa-file-alt w-5 text-center {{ Request::is('resume') ? 'text-purple-300' : 'text-gray-400 group-hover:text-purple-300' }}"></i>
                <span class="font-medium {{ Request::is('resume') ? 'text-white' : 'text-gray-300' }}">Resume Builder</span>
            </a>
            
            <a href="/profile" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-500/20 transition group {{ Request::is('profile') ? 'bg-purple-500/30 border-l-4 border-purple-400' : '' }}">
                <i class="fas fa-user w-5 text-center {{ Request::is('profile') ? 'text-purple-300' : 'text-gray-400 group-hover:text-purple-300' }}"></i>
                <span class="font-medium {{ Request::is('profile') ? 'text-white' : 'text-gray-300' }}">Profile</span>
            </a>
        </nav>
        
        <!-- Logout -->
        <div class="p-4 border-t-2 border-purple-500/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/20 transition group">
                    <i class="fas fa-sign-out-alt w-5 text-center text-red-400"></i>
                    <span class="font-medium text-red-300">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col min-h-screen relative z-10">
        <!-- Top Header -->
        <header class="h-20 border-b-2 border-purple-500/30 backdrop-blur-xl bg-[#1a1d3e]/50 sticky top-0 z-40 px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button 
                    x-show="!sidebarOpen" 
                    @click="sidebarOpen = true" 
                    class="p-3 hover:bg-purple-500/20 rounded-xl border-2 border-purple-500/30 transition btn-glow"
                >
                    <i class="fas fa-bars text-purple-300"></i>
                </button>

                <div x-show="!sidebarOpen" class="hidden md:block">
                    <h2 class="font-display text-xl font-bold text-white">@yield('page_title', 'Dashboard')</h2>
                    <p class="text-xs text-purple-300">@yield('page_subtitle', 'Manage your journey')</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                <!-- Profile Avatar -->
                <a href="/profile" class="w-12 h-12 rounded-2xl border-2 border-purple-400 overflow-hidden hover:border-pink-400 transition shadow-lg">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=a78bfa&color=fff&size=48' }}" 
                         class="w-full h-full object-cover">
                </a>
                @endauth
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 relative z-10">
            @yield('content')
        </main>
    </div>

    <!-- Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-40 backdrop-blur-sm"></div>

    @yield('modals')
</body>
</html>
