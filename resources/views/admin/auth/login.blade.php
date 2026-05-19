<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexCommerce Admin Login</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full text-slate-100 antialiased selection:bg-purple-500 selection:text-white flex min-h-screen items-center justify-center relative overflow-hidden bg-slate-950">

    <!-- Glowing Background Nebulas -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-purple-600/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-indigo-600/10 blur-3xl"></div>

    <div class="w-full max-w-md px-6 py-12 relative z-10">
        <!-- Branding logo header -->
        <div class="flex flex-col items-center mb-8">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-purple-600 via-purple-500 to-indigo-500 shadow-xl shadow-purple-500/20 border border-purple-400/20">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h2 class="mt-4 text-3xl font-black tracking-tight text-white">NEX<span class="text-purple-400">COMMERCE</span></h2>
            <p class="mt-1.5 text-sm text-slate-400 font-medium">Headless Administrative Console</p>
        </div>

        <!-- Login Card -->
        <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/80 p-8 backdrop-blur-2xl shadow-2xl shadow-purple-950/10">
            
            <!-- Session Alert Banner -->
            @if(session('success'))
                <div class="mb-5 rounded-xl bg-emerald-500/10 p-3 text-sm font-semibold text-emerald-400 border border-emerald-500/20">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-xl bg-red-500/10 p-3 text-sm font-semibold text-red-400 border border-red-500/20">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                <!-- Email field -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium"
                            placeholder="admin@nexcommerce.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-400">Password</label>
                        <a href="{{ route('admin.forgot-password') }}" class="text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="password" name="password" id="password" required
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit button -->
                <button type="submit"
                    class="w-full flex h-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all tracking-wide">
                    Unlock Dashboard
                </button>

                <!-- Local development auto-fill bypass -->
                @if(app()->environment('local'))
                    <div class="relative flex items-center justify-center my-6">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-800"></div>
                        </div>
                        <span class="relative bg-slate-900 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Developer Sandbox</span>
                    </div>

                    <button type="button" onclick="autoFillAdmin()"
                        class="w-full flex h-11 items-center justify-center gap-2 rounded-xl border border-dashed border-purple-500/40 bg-purple-500/5 text-sm font-semibold text-purple-400 hover:bg-purple-500/10 transition-colors">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Auto-Fill Super Admin
                    </button>
                @endif

            </form>
        </div>
    </div>

    @if(app()->environment('local'))
        <script>
            function autoFillAdmin() {
                document.getElementById('email').value = 'admin@nexcommerce.com';
                document.getElementById('password').value = 'password';
            }
        </script>
    @endif

</body>
</html>
