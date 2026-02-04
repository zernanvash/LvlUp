<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mastery - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .hex { clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%); }
    </style>
</head>
<body class="bg-[#0d1117] text-[#c9d1d9] font-sans" x-data="{ sidebarOpen: false }">

    <aside 
        x-show="sidebarOpen" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-72 bg-[#010409] border-r border-[#30363d] z-50 flex flex-col shadow-2xl"
    >
        <div class="h-16 flex items-center px-4 justify-between border-b border-[#30363d]">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-graduation-cap text-2xl text-indigo-400"></i>
                <span class="font-bold text-white tracking-tight">MASTERY</span>
            </div>
            <button @click="sidebarOpen = false" class="p-2 hover:bg-[#21262d] rounded-md text-[#8b949e]">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <nav class="flex-1 py-6 px-3 space-y-2">
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-[#21262d] text-[#c9d1d9] @if(Request::is('dashboard')) bg-[#21262d] border-l-2 border-indigo-500 @endif">
                <i class="fa-solid fa-house w-5 text-center opacity-70"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            <a href="/skill-tree" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-[#21262d] text-[#c9d1d9] @if(Request::is('skill-tree')) bg-[#21262d] border-l-2 border-indigo-500 @endif">
                <i class="fa-solid fa-diagram-project w-5 text-center opacity-70"></i>
                <span class="text-sm font-medium">Skill Tree</span>
            </a>
            <a href="/profile" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-[#21262d] text-[#c9d1d9] @if(Request::is('profile')) bg-[#21262d] border-l-2 border-indigo-500 @endif">
                <i class="fa-solid fa-user-astronaut w-5 text-center opacity-70"></i>
                <span class="text-sm font-medium">Profile</span>
            </a>
        </nav>
    </aside>

    <div class="flex flex-col min-h-screen">
        
        <header class="h-16 border-b border-[#30363d] bg-[#0d1117] sticky top-0 z-40 px-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button 
                    x-show="!sidebarOpen" 
                    @click="sidebarOpen = true" 
                    class="p-2 hover:bg-[#21262d] rounded-md border border-[#30363d] transition-all"
                >
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                <div x-show="!sidebarOpen" class="flex items-center gap-2">
                    <span class="text-sm font-bold text-[#f0f6fc] tracking-wide">Mastery Dashboard</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center -space-x-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-500/20 border-2 border-[#0d1117] flex items-center justify-center text-[10px] shadow-lg">🥇</div>
                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 border-2 border-[#0d1117] flex items-center justify-center text-[10px] shadow-lg">🚀</div>
                    <div class="w-8 h-8 bg-[#30363d] border-2 border-[#0d1117] rounded-full flex items-center justify-center text-[8px] font-bold">+2</div>
                </div>
                <a href="/profile" class="w-9 h-9 rounded-full border border-[#30363d] p-0.5 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=User&background=6366f1&color=fff" class="rounded-full">
                </a>
            </div>
        </header>

        <main class="p-6">
            @yield('content')
        </main>
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 backdrop-blur-sm"></div>

</body>
</html>