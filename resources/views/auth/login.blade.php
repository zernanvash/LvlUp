<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | LvlUp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --lvl-bg: #11101f;
            --lvl-surface: #19172b;
            --lvl-surface-soft: #25223a;
            --lvl-border: #3c375d;
            --lvl-border-soft: #302c4a;
            --lvl-text: #f7f4ff;
            --lvl-muted: #c5bed8;
            --lvl-faint: #928aa8;
            --lvl-p50:  #2a2551;
            --lvl-p100: #3c3489;
            --lvl-p200: #534ab7;
            --lvl-p400: #7f77dd;
            --lvl-p600: #afa9ec;
            --lvl-p800: #eeedfe;
            --lvl-gold: #ef9f27;
            --lvl-green: #9bcf5a;
            --lvl-panel-bg: rgba(25,23,43,0.97);
            --lvl-shadow: 0 18px 42px rgba(0,0,0,0.45);
        }

        * { font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif; box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            color: var(--lvl-text);
            background:
                radial-gradient(circle at top left, rgba(127,119,221,0.22), transparent 28rem),
                linear-gradient(180deg, rgba(17,16,31,0.96), rgba(11,10,21,1)),
                var(--lvl-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 720px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--lvl-panel-bg);
            border: 1px solid var(--lvl-border-soft);
            border-radius: .75rem;
            box-shadow: var(--lvl-shadow);
            overflow: hidden;
        }

        .left-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            padding: 2rem 1.75rem;
            background: linear-gradient(160deg, var(--lvl-p50) 0%, var(--lvl-surface) 100%);
            border-right: 1px solid var(--lvl-border-soft);
        }

        /* Logo */
        .logo-img { height: 52px; width: auto; display: block; margin-left: 0; }

        /* Tagline */
        .tagline     { font-size: 14px; font-weight: 600; color: var(--lvl-text); line-height: 1.4; }
        .tagline-sub { font-size: 11px; color: var(--lvl-muted); line-height: 1.65; margin-top: 6px; }

        /* Features */
        .features { display: flex; flex-direction: column; }
        .feat {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 0;
            border-top: 1px solid var(--lvl-border-soft);
            font-size: 11px; color: var(--lvl-muted);
        }
        .feat-dot { width: 4px; height: 4px; border-radius: 50%; background: var(--lvl-p400); flex-shrink: 0; }
        .feat strong { color: var(--lvl-text); font-weight: 600; }

        .copyright { font-size: 9px; color: var(--lvl-faint); }

        /* ── Right panel ── */
        .right-panel {
            padding: 2rem 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--lvl-panel-bg);
        }

        .form-title { font-size: 19px; font-weight: 700; color: var(--lvl-text); }
        .form-sub   { font-size: 11px; color: var(--lvl-faint); margin-top: 3px; margin-bottom: 1.4rem; }

        .lvl-label {
            display: flex; justify-content: space-between; align-items: center;
            color: var(--lvl-faint); font-size: 9px; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase;
            margin-bottom: 5px;
        }

        .lvl-input {
            width: 100%;
            background: var(--lvl-surface-soft);
            border: 1px solid var(--lvl-border);
            border-radius: .5rem;
            padding: .55rem .85rem .55rem 2.1rem;
            font-size: .8125rem;
            color: var(--lvl-text);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }
        .lvl-input:focus {
            border-color: var(--lvl-p400);
            box-shadow: 0 0 0 3px rgba(127,119,221,0.18);
        }
        .lvl-input::placeholder { color: var(--lvl-faint); }

        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: .65rem; top: 50%; transform: translateY(-50%);
            font-size: .7rem; color: var(--lvl-faint); pointer-events: none;
        }

        .forgot { font-size: 10px; font-weight: 700; color: var(--lvl-p400); text-transform: none; letter-spacing: 0; text-decoration: none; }
        .forgot:hover { color: var(--lvl-p600); }

        .divider { border: none; border-top: 1px solid var(--lvl-border-soft); margin: .9rem 0; }

        .btn-primary {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: .45rem;
            padding: .6rem 1rem;
            border-radius: .5rem;
            background: linear-gradient(90deg, var(--lvl-p200), var(--lvl-p400));
            color: var(--lvl-p800);
            font-size: .8125rem; font-weight: 700;
            border: none; cursor: pointer; font-family: inherit;
            transition: opacity .15s, transform .15s;
        }
        .btn-primary:hover  { opacity: .9; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        .error-msg { color: #ef6b6b; font-size: .7rem; margin-top: .3rem; }

        @media (max-width: 640px) {
            .login-card { grid-template-columns: 1fr; }
            .left-panel { display: none; }
        }
    </style>
</head>
<body>

<div class="login-card">

    {{-- ── LEFT PANEL ── --}}
    <div class="left-panel">

        <x-application-logo class="logo-img" />

        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <div class="tagline">Track your growth as a developer.</div>
                <div class="tagline-sub">Log projects, earn XP, unlock skills, and build a portfolio that shows how far you've come.</div>
            </div>

            <div class="features">
                <div class="feat"><span class="feat-dot"></span><span><strong>Projects</strong> — log your work &amp; earn XP</span></div>
                <div class="feat"><span class="feat-dot"></span><span><strong>Skill Tree</strong> — unlock nodes as you build</span></div>
                <div class="feat"><span class="feat-dot"></span><span><strong>Achievements</strong> — collect badges &amp; level up</span></div>
                <div class="feat"><span class="feat-dot"></span><span><strong>Resume</strong> — auto-generate from your skills</span></div>
            </div>
        </div>

        <div class="copyright">© {{ date('Y') }} LvlUp</div>
    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="right-panel">

        <div class="form-title">Sign in</div>
        <div class="form-sub">Enter your credentials to continue</div>

        <x-auth-session-status class="mb-4" style="font-size:.8rem;color:var(--lvl-green);" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:.85rem;">
            @csrf

            {{-- Email --}}
            <div>
                <div class="lvl-label">
                    Email <span style="color:#ef6b6b;">*</span>
                </div>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required autofocus autocomplete="email"
                        placeholder="you@example.com"
                        class="lvl-input"
                    >
                </div>
                <x-input-error :messages="$errors->get('email')" class="error-msg" />
            </div>

            {{-- Password --}}
            <div>
                <div class="lvl-label">
                    Password <span style="color:#ef6b6b;">*</span>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
                    @endif
                </div>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input
                        type="password"
                        name="password"
                        required autocomplete="current-password"
                        placeholder="••••••••"
                        class="lvl-input"
                    >
                </div>
                <x-input-error :messages="$errors->get('password')" class="error-msg" />
            </div>

            {{-- Remember me --}}
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="checkbox" name="remember" style="accent-color:var(--lvl-p400);">
                <span style="font-size:.8rem;color:var(--lvl-muted);">Keep me signed in</span>
            </label>

            <hr class="divider">

            <button type="submit" class="btn-primary">
                <i class="fas fa-rocket" style="font-size:.7rem;"></i>
                Sign in &amp; continue
            </button>
        </form>

        <p style="text-align:center;font-size:.7rem;color:var(--lvl-faint);margin-top:.9rem;">
            New here?
            <a href="{{ route('register') }}"
               style="color:var(--lvl-p400);font-weight:700;text-decoration:none;"
               onmouseover="this.style.color='var(--lvl-p600)'"
               onmouseout="this.style.color='var(--lvl-p400)'">
                Create an account
            </a>
        </p>

    </div>
</div>

</body>
</html>
