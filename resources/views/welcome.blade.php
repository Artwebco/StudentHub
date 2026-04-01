<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Besedi - Sign In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes move-orb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(50px, -80px) scale(1.2);
            }

            66% {
                transform: translate(-40px, 40px) scale(0.8);
            }
        }

        .bg-gradient-orb-1 {
            /* Зголемен интензитет на сината боја (0.45 наместо 0.25) */
            background: radial-gradient(circle, rgba(37, 99, 235, 0.45) 0%, rgba(37, 99, 235, 0) 70%);
            filter: blur(60px);
            /* Намален blur за поостра боја */
            animation: move-orb 10s ease-in-out infinite;
        }

        .bg-gradient-orb-2 {
            /* Зголемен интензитет на зелената боја (0.35 наместо 0.20) */
            background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, rgba(16, 185, 129, 0) 70%);
            filter: blur(60px);
            animation: move-orb 12s ease-in-out infinite reverse;
        }
    </style>
</head>

<body class="antialiased font-sans bg-white text-slate-900">
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-slate-50 px-4">

        <div class="bg-gradient-orb-1 pointer-events-none absolute -left-40 -top-40 h-[600px] w-[600px] animate-pulse">
        </div>
        <div class="bg-gradient-orb-2 pointer-events-none absolute -bottom-32 -right-32 h-[500px] w-[500px] animate-pulse"
            style="animation-delay: 1s;"></div>

        <svg class="pointer-events-none absolute -left-20 top-0 h-[500px] w-[500px] opacity-[0.08]"
            viewBox="0 0 500 500">
            <circle cx="250" cy="250" r="200" fill="none" stroke="#2563eb" stroke-width="1"></circle>
            <circle cx="250" cy="250" r="240" fill="none" stroke="#059669" stroke-width="0.5"></circle>
        </svg>

        <div class="relative z-10 mb-8 flex items-center gap-3">
            <header class="grid grid-cols-1 items-center gap-2 py-10">
                <div class="flex justify-center">
                    <a href="/" wire:navigate>
                        <x-application-logo class="fill-current text-gray-500" />
                    </a>
                </div>
            </header>
        </div>

        <div
            class="relative z-10 w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">
            <h2 class="mb-6 text-center text-xl font-semibold text-slate-800">Sign In</h2>

            <div class="mb-6 h-px w-full bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-500 ml-1" for="email">Email</label>
                    <input type="email" name="email" id="email" :value="old('email')" required autofocus
                        class="flex h-11 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                        placeholder="your@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-500 ml-1" for="password">Password</label>
                    <input type="password" name="password" id="password" required
                        class="flex h-11 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 transition-all">
                        <span class="text-sm font-medium text-slate-500 group-hover:text-slate-700">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <button type="submit"
                    class="flex h-12 w-full items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-emerald-500 text-sm font-semibold uppercase tracking-widest text-white shadow-lg shadow-blue-500/20 transition-all hover:opacity-90 hover:scale-[1.01] active:scale-[0.99]">
                    Sign In
                </button>
            </form>
        </div>

        <p class="relative z-10 mt-8 text-sm text-slate-400 font-medium">
            Besedi &copy; {{ date('Y') }} | Invoicing System
        </p>
    </div>
</body>

</html>
