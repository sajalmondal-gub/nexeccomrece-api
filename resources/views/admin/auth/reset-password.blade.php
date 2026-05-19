<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexCommerce Admin Password Reset</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="mt-4 text-3xl font-black tracking-tight text-white">RESET<span class="text-purple-400"> PASSWORD</span></h2>
            <p class="mt-1.5 text-sm text-slate-400 font-medium">Create New Security Credentials</p>
        </div>

        <!-- Card -->
        <div class="rounded-3xl border border-indigo-950/40 bg-slate-900/80 p-8 backdrop-blur-2xl shadow-2xl shadow-purple-950/10">
            
            @if(session('error'))
                <div class="mb-5 rounded-xl bg-red-500/10 p-3 text-sm font-semibold text-red-400 border border-red-500/20">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.reset-password.submit') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email (Readonly) -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Account Email</label>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="email" name="email" id="email" readonly value="{{ $email }}"
                            class="block w-full rounded-xl border border-slate-800 bg-slate-950/60 px-4 py-3 text-sm text-slate-400 font-medium focus:outline-none cursor-not-allowed">
                    </div>
                </div>

                <!-- Password field -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">New Password</label>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="password" name="password" id="password" required autofocus
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium"
                            placeholder="Min 8 characters">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Confirm Password</label>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="block w-full rounded-xl border border-indigo-950/60 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-all font-medium"
                            placeholder="Repeat new password">
                    </div>
                </div>

                <!-- Submit button -->
                <button type="submit"
                    class="w-full flex h-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-bold text-white shadow-lg shadow-purple-500/10 hover:from-purple-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all tracking-wide">
                    Set New Password
                </button>

            </form>
        </div>
    </div>

</body>
</html>
