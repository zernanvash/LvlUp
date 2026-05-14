<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired | LvlUp</title>
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
        
        @keyframes hourglass {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
            100% { transform: rotate(180deg); }
        }

        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a1d3e, #2d1b4e, #1e0a3c);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-purple {
            text-shadow: 0 0 20px rgba(168, 85, 247, 0.5);
        }

        .rotating-hourglass {
            animation: hourglass 4s ease-in-out infinite;
        }

        .neon-border-purple {
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.3), inset 0 0 15px rgba(168, 85, 247, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-500 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-pink-500 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12">
            <div class="inline-block p-8 bg-purple-500/10 rounded-full border border-purple-500/30 backdrop-blur neon-border-purple">
                <i class="fas fa-hourglass-end text-8xl text-purple-400 rotating-hourglass"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-purple text-purple-400 opacity-90">419</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-purple-400 uppercase tracking-[0.3em]">
            Session Expired
        </h2>
        
        <p class="text-xl text-purple-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            Your connection to the game world has timed out. The session token has vanished into the void. Please refresh to continue your adventure.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(168, 85, 247, 0.4)] transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-redo mr-2"></i> Refresh Session
            </button>
            <a href="/" class="w-full sm:w-auto px-12 py-4 bg-white/5 hover:bg-white/10 rounded-xl font-bold text-lg border border-white/10 backdrop-blur transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-home mr-2"></i> World Map
            </a>
        </div>

        <div class="mt-20 text-purple-400/30 text-sm font-display uppercase tracking-widest">
            Security Status: TOKEN_EXPIRED_0x419
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: radial-gradient(#a855f7 1px, transparent 1px); background-size: 40px 40px;">
    </div>
</body>
</html>
