<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Under Maintenance | LvlUp</title>
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
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a2d3e, #0a1d3e, #0a0e27);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-blue {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        .rotating-cog {
            animation: rotate 10s linear infinite;
        }

        .neon-border-blue {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3), inset 0 0 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-600 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12">
            <div class="relative inline-block p-8 bg-blue-500/10 rounded-3xl border border-blue-500/30 backdrop-blur neon-border-blue">
                <i class="fas fa-cog text-8xl text-blue-400 rotating-cog"></i>
                <i class="fas fa-tools absolute -bottom-2 -right-2 text-4xl text-blue-300"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-blue text-blue-400 opacity-90">503</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-blue-400 uppercase tracking-[0.3em]">
            Maintenance
        </h2>
        
        <p class="text-xl text-blue-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            We're currently upgrading the game engine to bring you new features and improved performance. We'll be back online shortly.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(59,130,246,0.4)] transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-sync-alt mr-2"></i> Check Status
            </button>
        </div>

        <div class="mt-20 text-blue-400/30 text-sm font-display uppercase tracking-widest">
            Queue Status: SERVER_UPGRADING_V2.0
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 60px 60px;">
    </div>
</body>
</html>
