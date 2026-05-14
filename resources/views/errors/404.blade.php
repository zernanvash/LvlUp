<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Level Not Found | LvlUp</title>
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }
        
        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a1d3e, #2d1b4e, #1e0a3c);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-cyan {
            text-shadow: 0 0 20px rgba(6, 182, 212, 0.5);
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        .glitch-text:hover {
            animation: glitch 0.3s linear infinite;
        }

        .neon-border {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.3), inset 0 0 15px rgba(6, 182, 212, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-cyan-500 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-500 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12 floating">
            <div class="inline-block p-8 bg-cyan-500/10 rounded-full border border-cyan-500/30 backdrop-blur neon-border">
                <i class="fas fa-map-marked-alt text-8xl text-cyan-400"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-cyan text-cyan-400 opacity-90">404</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-cyan-400 uppercase tracking-[0.3em] glitch-text">
            Level Not Found
        </h2>
        
        <p class="text-xl text-cyan-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            You've ventured into uncharted territory. This level hasn't been procedurally generated yet, or it's been deleted by a rogue admin.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="/" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all hover:scale-105 active:scale-95 group">
                <i class="fas fa-home mr-2 transition-transform group-hover:-translate-x-1"></i> Return to Spawn
            </a>
            <button onclick="window.history.back()" class="w-full sm:w-auto px-12 py-4 bg-white/5 hover:bg-white/10 rounded-xl font-bold text-lg border border-white/10 backdrop-blur transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-arrow-left mr-2"></i> Previous Save
            </button>
        </div>

        <div class="mt-20 text-cyan-400/30 text-sm font-display uppercase tracking-widest">
            Coordinate Error: AREA_UNREACHABLE_0x404
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: radial-gradient(#06b6d4 1px, transparent 1px); background-size: 40px 40px;">
    </div>
</body>
</html>
