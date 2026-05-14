<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - System Failure | LvlUp</title>
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
        
        @keyframes scan {
            0% { top: 0%; }
            100% { top: 100%; }
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        .animated-bg {
            background: linear-gradient(-45deg, #0a0e27, #1a1a0a, #2d1a0a, #1e1e0a);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .glow-amber {
            text-shadow: 0 0 20px rgba(245, 158, 11, 0.5);
        }

        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: rgba(245, 158, 11, 0.2);
            z-index: 20;
            animation: scan 4s linear infinite;
        }

        .warning-blink {
            animation: blink 1s step-end infinite;
        }

        .neon-border-amber {
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.3), inset 0 0 15px rgba(245, 158, 11, 0.3);
        }
    </style>
</head>
<body class="animated-bg min-h-screen text-white flex items-center justify-center p-6 overflow-hidden">
    <div class="scan-line"></div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-20">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-amber-500 rounded-full blur-[150px]"></div>
    </div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="mb-12">
            <div class="inline-block p-8 bg-amber-500/10 rounded-full border border-amber-500/30 backdrop-blur neon-border-amber">
                <i class="fas fa-microchip text-8xl text-amber-500 warning-blink"></i>
            </div>
        </div>

        <h1 class="font-display text-[12rem] leading-none font-black mb-4 glow-amber text-amber-500 opacity-90">500</h1>
        
        <h2 class="font-display text-4xl font-bold mb-6 text-amber-500 uppercase tracking-[0.3em]">
            Critical Error
        </h2>
        
        <p class="text-xl text-amber-100/80 mb-12 max-w-lg mx-auto leading-relaxed">
            The server's core has overheated. Our engineers are currently deploying cooling systems to restore stability.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-500 hover:to-yellow-500 rounded-xl font-display font-bold text-lg shadow-[0_0_20px_rgba(245,158,11,0.4)] transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-sync-alt mr-2"></i> Reboot System
            </button>
            <a href="/" class="w-full sm:w-auto px-12 py-4 bg-white/5 hover:bg-white/10 rounded-xl font-bold text-lg border border-white/10 backdrop-blur transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-home mr-2"></i> Safe Mode
            </a>
        </div>

        <div class="mt-20 text-amber-500/30 text-sm font-display uppercase tracking-widest">
            System Log: CRITICAL_KERNEL_PANIC_0x500
        </div>
    </div>

    <!-- Background Grid -->
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(rgba(245, 158, 11, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(245, 158, 11, 0.1) 1px, transparent 1px); background-size: 50px 50px;">
    </div>
</body>
</html>
