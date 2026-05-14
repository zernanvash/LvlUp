<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Slow Down | LvlUp</title>
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
        
        @keyframes drift {
            0% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(2deg); }
            100% { transform: scale(1) rotate(0deg); }
        }

        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a3e3e, #0a3e3e, #0a0e27);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-teal {
            text-shadow: 0 0 20px rgba(20, 184, 166, 0.5);
        }

        .drifting {
            animation: drift 8s ease-in-out infinite;
        }

        .neon-border-teal {
            box-shadow: 0 0 15px rgba(20, 184, 166, 0.3), inset 0 0 15px rgba(20, 184, 166, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-teal-500/20 rounded-full blur-[150px] drifting"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12">
            <div class="inline-block p-8 bg-teal-500/10 rounded-full border border-teal-500/30 backdrop-blur neon-border-teal">
                <i class="fas fa-tachometer-alt text-8xl text-teal-400"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-teal text-teal-400 opacity-90">429</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-teal-400 uppercase tracking-[0.3em]">
            Cool Down
        </h2>
        
        <p class="text-xl text-teal-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            You're moving too fast! The system needs a moment to process your actions. Please wait a few seconds before continuing your journey.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(20,184,166,0.4)] transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-redo mr-2"></i> Retry Action
            </button>
            <a href="/" class="w-full sm:w-auto px-12 py-4 bg-white/5 hover:bg-white/10 rounded-xl font-bold text-lg border border-white/10 backdrop-blur transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-home mr-2"></i> World Map
            </a>
        </div>

        <div class="mt-20 text-teal-400/30 text-sm font-display uppercase tracking-widest">
            Traffic Control: RATE_LIMIT_EXCEEDED
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: radial-gradient(#14b8a6 1px, transparent 1px); background-size: 40px 40px;">
    </div>
</body>
</html>
