<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Unauthorized Access | LvlUp</title>
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
        
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a1a0a, #2d220a, #1e1a0a);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-gold {
            text-shadow: 0 0 20px rgba(234, 179, 8, 0.5);
        }

        .floating-lock {
            animation: bounce-subtle 3s ease-in-out infinite;
        }

        .neon-border-gold {
            box-shadow: 0 0 15px rgba(234, 179, 8, 0.3), inset 0 0 15px rgba(234, 179, 8, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-yellow-500 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-500 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12">
            <div class="inline-block p-8 bg-yellow-500/10 rounded-full border border-yellow-500/30 backdrop-blur neon-border-gold floating-lock">
                <i class="fas fa-lock text-8xl text-yellow-500"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-gold text-yellow-500 opacity-90">401</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-yellow-500 uppercase tracking-[0.3em]">
            Unauthorized
        </h2>
        
        <p class="text-xl text-yellow-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            This loot is locked. You need to authenticate your identity before you can claim what's inside.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="/login" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-yellow-600 to-amber-600 hover:from-yellow-500 hover:to-amber-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(234,179,8,0.4)] transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-sign-in-alt mr-2"></i> Identify Yourself
            </a>
            <a href="/" class="w-full sm:w-auto px-12 py-4 bg-white/5 hover:bg-white/10 rounded-xl font-bold text-lg border border-white/10 backdrop-blur transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-home mr-2"></i> Safe Zone
            </a>
        </div>

        <div class="mt-20 text-yellow-500/30 text-sm font-display uppercase tracking-widest">
            Security Protocol: AUTH_REQUIRED_0x401
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: radial-gradient(#eab308 1px, transparent 1px); background-size: 50px 50px;">
    </div>
</body>
</html>
