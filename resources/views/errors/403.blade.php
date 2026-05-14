<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | LvlUp</title>
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
        
        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.2); }
            50% { box-shadow: 0 0 40px rgba(239, 68, 68, 0.5); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a0a0a, #2d0a0a, #1e0a1e);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-red {
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }

        .alert-pulse {
            animation: pulse-red 2s infinite;
        }

        .shake-on-hover:hover {
            animation: shake 0.2s ease-in-out infinite;
        }

        .neon-border-red {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.3), inset 0 0 15px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-red-500 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-orange-500 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12">
            <div class="inline-block p-8 bg-red-500/10 rounded-2xl border border-red-500/30 backdrop-blur neon-border-red alert-pulse">
                <i class="fas fa-user-shield text-8xl text-red-500"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-red text-red-500 opacity-90">403</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-red-500 uppercase tracking-[0.3em]">
            Access Denied
        </h2>
        
        <p class="text-xl text-red-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            Your current level or permissions are insufficient to enter this zone. High-level clearance required.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="/" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(239,68,68,0.4)] transition-all hover:scale-105 active:scale-95 group shake-on-hover">
                <i class="fas fa-home mr-2"></i> Retreat to Safety
            </a>
            <a href="/login" class="w-full sm:w-auto px-12 py-4 bg-white/5 hover:bg-white/10 rounded-xl font-bold text-lg border border-white/10 backdrop-blur transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-key mr-2"></i> Authenticate
            </a>
        </div>

        <div class="mt-20 text-red-500/30 text-sm font-display uppercase tracking-widest">
            Security Protocol: INSUFFICIENT_LEVEL_ERR
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: radial-gradient(#ef4444 1px, transparent 1px); background-size: 40px 40px;">
    </div>
</body>
</html>
