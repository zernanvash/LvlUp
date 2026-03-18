<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LvlUp - Gamified Knowledge Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Rajdhani', sans-serif; }
        .font-display { font-family: 'Orbitron', monospace; }
        
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="animated-bg min-h-screen text-white">
    
    <!-- Hero Section -->
    <div class="min-h-screen flex items-center justify-center px-4 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-purple-600/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-pink-600/10 rounded-full blur-3xl"></div>
        
        <div class="max-w-6xl mx-auto text-center relative z-10">
            <!-- Logo/Title -->
            <div class="mb-8 float">
                <div class="inline-block p-6 bg-gradient-to-br from-purple-600/20 to-pink-600/20 rounded-3xl border-2 border-purple-500/30 backdrop-blur">
                    <i class="fas fa-rocket text-6xl bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent"></i>
                </div>
            </div>
            
            <h1 class="font-display text-6xl md:text-8xl font-black mb-6 bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 bg-clip-text text-transparent">
                LvlUp
            </h1>
            
            <p class="text-2xl md:text-3xl text-purple-200 mb-4 font-semibold">
                Transform Your Learning Journey
            </p>
            
            <p class="text-lg md:text-xl text-purple-300 mb-12 max-w-2xl mx-auto">
                A gamified knowledge management system that turns skill acquisition into an epic RPG adventure. 
                Track projects, unlock skills, earn achievements, and level up your developer profile.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                <a href="/register" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl font-display font-bold text-lg shadow-2xl transition-all hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i> Start Your Journey
                </a>
                <a href="/login" class="w-full sm:w-auto px-8 py-4 bg-white/10 hover:bg-white/20 rounded-xl font-bold text-lg border-2 border-white/20 backdrop-blur transition-all hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
            </div>
            
            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="p-6 bg-gradient-to-br from-purple-900/40 to-purple-950/40 rounded-2xl border-2 border-purple-500/30 backdrop-blur">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-bold mb-2">XP & Levels</h3>
                    <p class="text-purple-300 text-sm">Earn experience points for every project you create and level up your profile</p>
                </div>
                
                <div class="p-6 bg-gradient-to-br from-blue-900/40 to-blue-950/40 rounded-2xl border-2 border-blue-500/30 backdrop-blur">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-network-wired text-2xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-bold mb-2">Skill Tree</h3>
                    <p class="text-blue-300 text-sm">Unlock powerful skills in an interactive skill tree with dependencies</p>
                </div>
                
                <div class="p-6 bg-gradient-to-br from-pink-900/40 to-pink-950/40 rounded-2xl border-2 border-pink-500/30 backdrop-blur">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-bold mb-2">Achievements</h3>
                    <p class="text-pink-300 text-sm">Earn rare badges for milestones and showcase your legendary accomplishments</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="py-20 px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="font-display text-4xl md:text-5xl font-bold text-center mb-16 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                Why Choose LvlUp?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="p-8 bg-gradient-to-br from-purple-900/20 to-purple-950/20 rounded-2xl border border-purple-500/20 backdrop-blur">
                    <i class="fas fa-rocket text-4xl text-purple-400 mb-4"></i>
                    <h3 class="font-display text-2xl font-bold mb-3">Gamification at Its Core</h3>
                    <p class="text-purple-200">Experience the addictive progression of video games applied to your learning journey. Daily rewards, streak bonuses, and gacha currency keep you motivated.</p>
                </div>
                
                <div class="p-8 bg-gradient-to-br from-blue-900/20 to-blue-950/20 rounded-2xl border border-blue-500/20 backdrop-blur">
                    <i class="fas fa-brain text-4xl text-blue-400 mb-4"></i>
                    <h3 class="font-display text-2xl font-bold mb-3">AI-Powered Resume Builder</h3>
                    <p class="text-blue-200">Paste a job description and let AI match your best projects. Generate professional resumes tailored to each application in seconds.</p>
                </div>
                
                <div class="p-8 bg-gradient-to-br from-pink-900/20 to-pink-950/20 rounded-2xl border border-pink-500/20 backdrop-blur">
                    <i class="fas fa-code text-4xl text-pink-400 mb-4"></i>
                    <h3 class="font-display text-2xl font-bold mb-3">Smart Skill Detection</h3>
                    <p class="text-pink-200">Paste your code and watch as AI automatically detects technologies used. No more manual tagging - we handle it for you.</p>
                </div>
                
                <div class="p-8 bg-gradient-to-br from-amber-900/20 to-amber-950/20 rounded-2xl border border-amber-500/20 backdrop-blur">
                    <i class="fas fa-fire text-4xl text-amber-400 mb-4"></i>
                    <h3 class="font-display text-2xl font-bold mb-3">Beautiful Genshin-Style UI</h3>
                    <p class="text-amber-200">Enjoy a stunning interface inspired by popular gacha games. Animated gradients, rarity glows, and smooth transitions make learning fun.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="py-20 px-4 bg-gradient-to-b from-transparent to-black/30">
        <div class="max-w-6xl mx-auto text-center">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <div class="font-display text-5xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">25+</div>
                    <div class="text-purple-300">Skills Available</div>
                </div>
                <div>
                    <div class="font-display text-5xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent mb-2">10+</div>
                    <div class="text-blue-300">Achievement Badges</div>
                </div>
                <div>
                    <div class="font-display text-5xl font-bold bg-gradient-to-r from-pink-400 to-rose-400 bg-clip-text text-transparent mb-2">100</div>
                    <div class="text-pink-300">Max Level</div>
                </div>
                <div>
                    <div class="font-display text-5xl font-bold bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent mb-2">∞</div>
                    <div class="text-amber-300">Possibilities</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA Section -->
    <div class="py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="font-display text-4xl md:text-5xl font-bold mb-6">Ready to Level Up?</h2>
            <p class="text-xl text-purple-300 mb-8">Join thousands of developers tracking their growth journey</p>
            <a href="/register" class="inline-block px-12 py-5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl font-display font-bold text-xl shadow-2xl transition-all hover:scale-105">
                <i class="fas fa-rocket mr-2"></i> Start Free Today
            </a>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="py-8 px-4 border-t border-white/10">
        <div class="max-w-6xl mx-auto text-center text-purple-400">
            <p>Made with 💜 by Group 2 | Software Engineering 2</p>
            <p class="text-sm mt-2">Jerico F. Abulencia & Zernan Vash Arrive</p>
        </div>
    </footer>
</body>
</html>
