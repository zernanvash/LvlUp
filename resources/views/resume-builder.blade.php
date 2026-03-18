<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LvlUp | Resume Builder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#0a0a0a] text-zinc-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col">
        <header class="border-b border-zinc-800 bg-zinc-900/50 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="zap" class="text-white w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight">LvlUp</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <button class="text-zinc-400 hover:text-white transition px-3 py-2 text-sm font-medium">Drafts</button>
                    <button class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-full text-sm font-bold transition flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i> Export PDF
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-7 space-y-8">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight">Build Your Resume</h1>
                    <p class="text-zinc-500 mt-2">Fill in your details to level up your job search.</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-3 mb-6">
                        <i data-lucide="user" class="text-indigo-500"></i>
                        <h2 class="text-lg font-bold">Personal Details</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" placeholder="Full Name" class="bg-zinc-800 border-zinc-700 rounded-xl p-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                        <input type="email" placeholder="Email Address" class="bg-zinc-800 border-zinc-700 rounded-xl p-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                        <input type="text" placeholder="Professional Title (e.g. Developer)" class="md:col-span-2 bg-zinc-800 border-zinc-700 rounded-xl p-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <i data-lucide="briefcase" class="text-emerald-500"></i>
                            <h2 class="text-lg font-bold">Work Experience</h2>
                        </div>
                        <button class="text-zinc-400 hover:text-white transition">
                            <i data-lucide="plus-circle" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="p-4 border border-zinc-800 rounded-xl bg-zinc-950/50">
                            <input type="text" placeholder="Company Name" class="w-full bg-transparent border-none text-lg font-semibold placeholder:text-zinc-700 focus:ring-0 mb-1">
                            <input type="text" placeholder="Role / Position" class="w-full bg-transparent border-none text-sm text-indigo-400 placeholder:text-zinc-700 focus:ring-0 mb-3">
                            <textarea placeholder="Describe your achievements..." class="w-full bg-transparent border-none text-zinc-400 placeholder:text-zinc-700 focus:ring-0 resize-none h-20 text-sm"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative">
                <div class="sticky top-24">
                    <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Live Preview
                    </p>
                    <div class="bg-white rounded-lg shadow-2xl w-full aspect-[1/1.414] p-10 text-zinc-900 origin-top scale-95 md:scale-100">
                        <div class="border-b-4 border-zinc-900 pb-4 mb-6">
                            <h2 class="text-3xl font-black uppercase tracking-tighter">Alex Rivera</h2>
                            <p class="text-zinc-500 font-medium italic">Full Stack Developer</p>
                        </div>
                        
                        <div class="space-y-6">
                            <section>
                                <h3 class="text-xs font-black bg-zinc-900 text-white inline-block px-2 py-0.5 mb-3">EXPERIENCE</h3>
                                <div class="text-[11px] leading-relaxed">
                                    <div class="flex justify-between font-bold">
                                        <span>LvlUp Studios</span>
                                        <span>2024 - Present</span>
                                    </div>
                                    <p class="text-zinc-600 italic mb-1">Lead Developer</p>
                                    <ul class="list-disc list-inside text-zinc-500">
                                        <li>Optimized application performance by 40%.</li>
                                        <li>Built modular UI components using Laravel & Tailwind.</li>
                                    </ul>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>