<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | LvlUp</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: radial-gradient(circle at 20% 20%, #1e293b, #020617);
        }

        .glass {
            backdrop-filter: blur(20px);
            background: rgba(255,255,255,0.05);
        }

        .glow {
            box-shadow: 0 0 40px rgba(99,102,241,0.25);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6 text-white">

    <div class="w-full max-w-5xl rounded-3xl overflow-hidden shadow-2xl grid lg:grid-cols-2">

        <!-- LEFT PANEL -->
        <div class="relative hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-indigo-600 via-purple-700 to-fuchsia-700">

            <div>
                <h1 class="text-3xl font-bold">LvlUp</h1>
                <p class="text-indigo-200 mt-2 text-sm">
                    Secure access to your learning system
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold mb-3">
                    Welcome back
                </h2>
                <p class="text-indigo-100/80">
                    Continue your progress and manage your skills,
                    projects, and achievements in one place.
                </p>
            </div>

            <div class="text-xs text-indigo-200/70">
                © {{ date('Y') }} LvlUp System
            </div>

        </div>


        <!-- RIGHT PANEL -->
        <div class="glass glow p-10 bg-slate-900">

            <h2 class="text-3xl font-bold mb-1">Sign in</h2>
            <p class="text-gray-400 mb-8 text-sm">
                Enter your account credentials
            </p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label class="text-sm text-gray-300">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full mt-1 px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 focus:border-indigo-500 outline-none"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-400" />
                </div>

                <!-- PASSWORD -->
                <div>
                    <div class="flex justify-between text-sm text-gray-300">
                        <label>Password</label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-400 hover:underline">
                            Forgot?
                        </a>
                        @endif
                    </div>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full mt-1 px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 focus:border-indigo-500 outline-none"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-400" />
                </div>

                <!-- REMEMBER -->
                <label class="flex items-center gap-2 text-sm text-gray-400">
                    <input type="checkbox" name="remember" class="accent-indigo-600">
                    Keep me signed in
                </label>

                <!-- BUTTON -->
                <button
                    type="submit"
                    class="w-full py-3 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-90 font-semibold"
                >
                    Sign in
                </button>
            </form>

            <!-- REGISTER -->
            <p class="text-center text-sm text-gray-400 mt-6">
                New here?
                <a href="{{ route('register') }}" class="text-indigo-400 hover:underline font-medium">
                    Create account
                </a>
            </p>

        </div>

    </div>

</body>
</html>