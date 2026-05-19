@php
    $siteSettings = app(\App\Services\SettingService::class)->getSettings();
    $adminNotifications = \App\Models\Notification::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
    $unreadCount = $adminNotifications->where('is_read', false)->count();
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <script>
        // Immediately verify theme preference to avoid flashes
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        }
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteSettings['site_name'] . ' Admin')</title>
    @if(!empty($siteSettings['site_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $siteSettings['site_favicon']) }}">
    @endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }

        /* Light-theme overrides - GENIUS CATCH: Escaping the Tailwind forward slash selectors correctly! */
        html.light body {
            background-color: #f4f7fb !important;
            color: #1e293b !important;
        }
        
        /* Backgrounds */
        html.light .bg-slate-950 {
            background-color: #f4f7fb !important;
        }
        html.light .bg-slate-900,
        html.light .bg-slate-900\/50,
        html.light .bg-slate-900\/40,
        html.light .bg-slate-950\/60,
        html.light .bg-slate-950\/40,
        html.light .bg-slate-800 {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
        }
        html.light .bg-slate-900\/90,
        html.light .bg-slate-900\/95 {
            background-color: rgba(255, 255, 255, 0.8) !important;
            border-color: #e2e8f0 !important;
        }
        html.light .bg-slate-900\/60,
        html.light .bg-slate-900\/10 {
            background-color: rgba(255, 255, 255, 0.6) !important;
            border-color: #e2e8f0 !important;
        }
        
        /* Modals & Overlays */
        html.light .bg-slate-950\/80 {
            background-color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Hover states */
        html.light .hover\:bg-slate-800\/60:hover {
            background-color: #f1f5f9 !important;
        }
        html.light .hover\:bg-slate-800:hover {
            background-color: #e2e8f0 !important;
        }
        html.light .hover\:bg-slate-950\/40:hover {
            background-color: #f8fafc !important;
        }

        /* Borders */
        html.light .border-indigo-950\/40,
        html.light .border-indigo-950\/50,
        html.light .border-indigo-950\/60,
        html.light .border-slate-800\/60 {
            border-color: #e2e8f0 !important;
        }

        /* Typography */
        html.light .text-white,
        html.light .text-slate-100,
        html.light .text-slate-200 {
            color: #1e293b !important;
        }
        html.light .text-slate-400,
        html.light .text-slate-300 {
            color: #475569 !important;
        }
        html.light .text-slate-500 {
            color: #64748b !important;
        }
        html.light .hover\:text-white:hover,
        html.light .hover\:text-slate-100:hover {
            color: #0f172a !important;
        }

        /* Fix the gradient text for light mode */
        html.light .bg-gradient-to-r.from-white.via-purple-200.to-indigo-200 {
            background-image: linear-gradient(to right, #6b21a8, #4f46e5) !important;
            color: transparent !important;
        }

        /* Fix sidebar gradient border */
        html.light .from-purple-950\/30.to-indigo-950\/20 {
            background-image: linear-gradient(to right, rgba(233, 213, 255, 0.5), rgba(199, 210, 254, 0.5)) !important;
        }

        /* Inputs */
        html.light input,
        html.light select,
        html.light textarea {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02) !important;
        }
        
        /* Accents */
        html.light .text-purple-400 {
            color: #6b21a8 !important;
        }
        html.light .bg-purple-500\/10 {
            background-color: rgba(124, 58, 237, 0.08) !important;
        }
        html.light .border-purple-500\/20 {
            border-color: rgba(124, 58, 237, 0.15) !important;
        }
    </style>
</head>
<body class="h-full text-slate-100 antialiased selection:bg-purple-500 selection:text-white bg-slate-950">

    <div class="min-h-full">
        <!-- 1. Sidebar for Desktop devices -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col">
            <!-- Sidebar component, glassmorphic dark design with purple gradient edge -->
            <div class="flex min-h-0 flex-1 flex-col border-r border-indigo-950/40 bg-slate-900/90 backdrop-blur-xl">
                <!-- Branding logo -->
                <div class="flex h-20 items-center justify-between px-6 bg-gradient-to-r from-purple-950/30 to-indigo-950/20 border-b border-indigo-950/40">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-purple-600 via-purple-500 to-indigo-500 shadow-lg shadow-purple-500/30 overflow-hidden">
                            @if(!empty($siteSettings['site_logo']) && str_starts_with($siteSettings['site_logo'], 'http'))
                                <img src="{{ $siteSettings['site_logo'] }}" alt="Logo" class="h-6 w-6 object-contain">
                            @elseif(!empty($siteSettings['site_logo']))
                                <img src="{{ asset('storage/' . $siteSettings['site_logo']) }}" alt="Logo" class="h-full w-full object-cover">
                            @else
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            @endif
                        </div>
                        <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-white via-purple-200 to-indigo-200 bg-clip-text text-transparent">
                            {{ $siteSettings['site_name'] }}
                        </span>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-purple-500/10 px-2 py-1 text-xs font-medium text-purple-400 ring-1 ring-inset ring-purple-500/20">
                        v1.0
                    </span>
                </div>

                <!-- Navigation links -->
                <nav class="flex-1 space-y-1.5 px-4 py-6 overflow-y-auto">
                    @php
                        $route = Request::route()->getName();
                    @endphp

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ $route === 'admin.dashboard' ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5 transition-colors {{ $route === 'admin.dashboard' ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Divider -->
                    @if(Auth::user()->hasPermissionTo('manage_products') || Auth::user()->hasRole('Super Admin'))
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Catalog</div>

                    <!-- Categories -->
                    <a href="{{ route('admin.categories.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.categories') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Categories & Brands
                    </a>

                    <!-- Products -->
                    <a href="{{ route('admin.products.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.products') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products & Variants
                    </a>

                    <!-- Combo Offers -->
                    <a href="{{ route('admin.combo-offers.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.combo-offers') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Combo Offers
                    </a>
                    @endif

                    <!-- Divider -->
                    @if(Auth::user()->hasPermissionTo('manage_orders') || Auth::user()->hasRole('Super Admin'))
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Sales</div>

                    <!-- Orders -->
                    <a href="{{ route('admin.orders.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.orders') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Orders & Sales
                    </a>
                    @endif

                    <!-- Coupons & Reviews -->
                    @if(Auth::user()->hasPermissionTo('manage_products') || Auth::user()->hasRole('Super Admin'))
                    <!-- Divider -->
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Marketing & Feedback</div>

                    <!-- Coupons -->
                    <a href="{{ route('admin.coupons.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.coupons') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Coupons
                    </a>

                    <!-- Banners -->
                    <a href="{{ route('admin.banners.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.banners') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        App Banners
                    </a>

                    <!-- Reviews -->
                    <a href="{{ route('admin.reviews.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.reviews') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.49 10.118c-.783-.57-.373-1.81.588-1.81h4.906a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Reviews Approval
                    </a>
                    @endif

                    <!-- Divider -->
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Administration</div>

                    <!-- Roles & Permissions -->
                    @if(Auth::user()->hasPermissionTo('manage_roles') || Auth::user()->hasRole('Super Admin'))
                    <a href="{{ route('admin.roles.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.roles') || str_starts_with($route, 'admin.users') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.957 11.957 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Roles & Permissions
                    </a>
                    @endif

                    <!-- Site Settings -->
                    @if(Auth::user()->hasPermissionTo('manage_settings') || Auth::user()->hasRole('Super Admin'))
                    <a href="{{ route('admin.settings.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.settings') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Site Settings
                    </a>
                    @endif
                </nav>

                <!-- Current Admin Profile Info / Logout -->
                <div class="flex flex-shrink-0 border-t border-indigo-950/40 bg-slate-900/60 p-4">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 shrink-0 rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center font-bold text-white shadow-md overflow-hidden">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile" class="h-full w-full object-cover">
                                @else
                                    {{ substr(Auth::user()->name ?? 'A', 0, 2) }}
                                @endif
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-slate-200">{{ Auth::user()->name ?? 'Admin Mercer' }}</p>
                                <p class="text-xs text-slate-400 font-medium">
                                    {{ Auth::user()->roles->first()?->name ?? 'Administrator' }}
                                </p>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition-colors" title="Logout">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Responsive Mobile Navigation Drawer Sidebar -->
        <div id="mobile-sidebar" class="fixed inset-0 z-50 lg:hidden hidden" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" aria-hidden="true"></div>
            
            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 left-0 flex w-full max-w-xs flex-col border-r border-indigo-950/40 bg-slate-900/95 backdrop-blur-xl p-4 transition-transform duration-300 ease-in-out transform -translate-x-full" id="mobile-sidebar-panel">
                <!-- Header / Close btn -->
                <div class="flex h-16 items-center justify-between px-2 mb-4 border-b border-indigo-950/40">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-purple-600 via-purple-500 to-indigo-500 shadow-lg shadow-purple-500/30 overflow-hidden">
                            @if(!empty($siteSettings['site_logo']) && str_starts_with($siteSettings['site_logo'], 'http'))
                                <img src="{{ $siteSettings['site_logo'] }}" alt="Logo" class="h-5 w-5 object-contain">
                            @elseif(!empty($siteSettings['site_logo']))
                                <img src="{{ asset('storage/' . $siteSettings['site_logo']) }}" alt="Logo" class="h-full w-full object-cover">
                            @else
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            @endif
                        </div>
                        <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-white via-purple-200 to-indigo-200 bg-clip-text text-transparent">
                            {{ $siteSettings['site_name'] }}
                        </span>
                    </div>
                    <button onclick="toggleMobileSidebar()" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation links -->
                <nav class="flex-1 space-y-1.5 overflow-y-auto px-2">
                    @php
                        $route = Request::route()->getName();
                    @endphp

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ $route === 'admin.dashboard' ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Divider -->
                    @if(Auth::user()->hasPermissionTo('manage_products') || Auth::user()->hasRole('Super Admin'))
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Catalog</div>

                    <!-- Categories -->
                    <a href="{{ route('admin.categories.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.categories') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Categories
                    </a>

                    <!-- Products -->
                    <a href="{{ route('admin.products.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.products') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products
                    </a>

                    <!-- Combo Offers -->
                    <a href="{{ route('admin.combo-offers.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.combo-offers') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Combo Offers
                    </a>
                    @endif

                    <!-- Divider -->
                    @if(Auth::user()->hasPermissionTo('manage_orders') || Auth::user()->hasRole('Super Admin'))
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Sales</div>

                    <!-- Orders -->
                    <a href="{{ route('admin.orders.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.orders') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Orders
                    </a>
                    @endif

                    <!-- Coupons, Banners, & Reviews -->
                    @if(Auth::user()->hasPermissionTo('manage_products') || Auth::user()->hasRole('Super Admin'))
                    <!-- Divider -->
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Marketing & Feedback</div>

                    <!-- Coupons -->
                    <a href="{{ route('admin.coupons.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.coupons') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Coupons
                    </a>

                    <!-- Banners -->
                    <a href="{{ route('admin.banners.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.banners') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        App Banners
                    </a>

                    <!-- Reviews -->
                    <a href="{{ route('admin.reviews.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.reviews') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.49 10.118c-.783-.57-.373-1.81.588-1.81h4.906a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Reviews Approval
                    </a>
                    @endif

                    <!-- Divider -->
                    <div class="h-px bg-slate-800/60 my-4 mx-2"></div>
                    <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Administration</div>

                    <!-- Roles & Permissions -->
                    @if(Auth::user()->hasPermissionTo('manage_roles') || Auth::user()->hasRole('Super Admin'))
                    <a href="{{ route('admin.roles.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ str_starts_with($route, 'admin.roles') || str_starts_with($route, 'admin.users') ? 'bg-gradient-to-r from-purple-600/95 to-indigo-600/90 text-white shadow-lg shadow-purple-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.957 11.957 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Roles & Users
                    </a>
                    @endif
                </nav>

                <!-- Mobile Profile Logout Footer -->
                <div class="flex flex-shrink-0 border-t border-indigo-950/40 bg-slate-900/60 p-4 mt-auto">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 shrink-0 rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center font-bold text-white shadow-md overflow-hidden">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile" class="h-full w-full object-cover">
                                @else
                                    {{ substr(Auth::user()->name ?? 'A', 0, 2) }}
                                @endif
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-semibold text-slate-200">{{ Auth::user()->name ?? 'Admin Mercer' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">
                                    {{ Auth::user()->roles->first()?->name ?? 'Administrator' }}
                                </p>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Main Dashboard Wrapper -->
        <div class="flex flex-col lg:pl-72 h-full min-h-screen bg-slate-950">
            <!-- Topbar (Sticky header) -->
            <div class="sticky top-0 z-40 flex h-20 shrink-0 items-center justify-between border-b border-indigo-950/40 bg-slate-900/40 backdrop-blur-md px-6">
                <!-- Hamburger Trigger for mobile screens -->
                <div class="flex items-center gap-3.5">
                    <button onclick="toggleMobileSidebar()" class="lg:hidden p-2.5 rounded-xl bg-slate-900/60 border border-indigo-950/40 text-slate-400 hover:text-white transition-all focus:outline-none" title="Navigation Menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <h1 class="text-xl font-bold tracking-tight text-white">@yield('page_title', 'Dashboard')</h1>
                </div>

                <!-- Notifications / Profile Actions -->
                <div class="flex items-center gap-4">
                    <!-- Sun/Moon Theme Toggler -->
                    <button id="theme-toggle" onclick="toggleTheme()" class="p-2.5 rounded-xl bg-slate-900/60 border border-indigo-950/40 text-slate-400 hover:text-white hover:border-purple-500/50 transition-all focus:outline-none" title="Toggle Light/Dark Theme">
                        <!-- Moon Icon -->
                        <svg id="theme-toggle-dark-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <!-- Sun Icon -->
                        <svg id="theme-toggle-light-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                    </button>

                    <!-- Interactive Notification Dropdown -->
                    <div class="relative">
                        <button id="notification-bell-btn" onclick="toggleNotificationDropdown()" class="p-2.5 rounded-xl bg-slate-900/60 border border-indigo-950/40 text-slate-400 hover:text-white hover:border-purple-500/50 transition-all relative focus:outline-none" title="Notifications">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Badge -->
                            @if($unreadCount > 0)
                            <span id="notification-badge" class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-lg ring-2 ring-slate-950">
                                {{ $unreadCount }}
                            </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div id="notification-dropdown" class="absolute right-0 mt-3 w-80 rounded-2xl border border-indigo-950/60 bg-slate-900 shadow-2xl z-50 hidden transition-all duration-200 origin-top-right transform scale-95 opacity-0">
                            <div class="flex items-center justify-between border-b border-indigo-950/50 px-4 py-3.5">
                                <span class="text-sm font-bold text-white">Console Notifications</span>
                                <button onclick="clearAllNotifications()" class="text-[10px] font-bold text-purple-400 hover:text-purple-300 uppercase tracking-wider bg-transparent border-0 cursor-pointer">Mark all read</button>
                            </div>

                            <div class="max-h-72 overflow-y-auto divide-y divide-indigo-950/40" id="notification-list-container">
                                @forelse($adminNotifications as $notif)
                                <div onclick="readNotification(this, {{ $notif->id }})" class="p-3.5 hover:bg-slate-950/40 cursor-pointer transition-all flex gap-3 relative group" data-unread="{{ $notif->is_read ? 'false' : 'true' }}" data-message="{{ htmlspecialchars($notif->message) }}" data-date="{{ $notif->created_at->format('M d, Y h:i A') }}">
                                    <div class="h-8.5 w-8.5 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20 shrink-0">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="notif-item-title text-xs font-semibold {{ $notif->is_read ? 'text-slate-400' : 'text-slate-200' }} truncate">{{ $notif->title }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $notif->message }}</p>
                                        <span class="text-[9px] text-slate-500 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if(!$notif->is_read)
                                    <span class="h-2 w-2 rounded-full bg-purple-500 absolute top-4 right-4 ring-2 ring-slate-900 group-hover:ring-transparent notification-dot"></span>
                                    @endif
                                </div>
                                @empty
                                <div class="p-6 text-center">
                                    <p class="text-xs text-slate-500">No new notifications.</p>
                                </div>
                                @endforelse
                            </div>

                            <div class="p-2 border-t border-indigo-950/50 text-center">
                                <span class="text-[10px] text-slate-500">Secure Administrative Console</span>
                            </div>
                        </div>
                    </div>

                    <!-- Server Badge -->
                    <span class="hidden md:inline-flex items-center rounded-lg bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Live Server
                    </span>

                    <!-- Premium Header Profile Dropdown Action -->
                    <div class="relative">
                        <button id="profile-dropdown-btn" onclick="toggleProfileDropdown()" class="flex h-10 w-10 shrink-0 rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-500 items-center justify-center font-bold text-white shadow-md hover:scale-105 active:scale-95 transition-all focus:outline-none overflow-hidden" title="Admin Profile Details">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="Profile" class="h-full w-full object-cover">
                            @else
                                {{ substr(Auth::user()->name ?? 'A', 0, 2) }}
                            @endif
                        </button>

                        <!-- Profile Dropdown box -->
                        <div id="profile-dropdown" class="absolute right-0 mt-3 w-60 rounded-2xl border border-indigo-950/60 bg-slate-900 shadow-2xl z-50 hidden transition-all duration-200 origin-top-right transform scale-95 opacity-0">
                            <!-- Dropdown Header -->
                            <div class="border-b border-indigo-950/50 px-4 py-3.5">
                                <p class="text-xs font-bold text-white">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ Auth::user()->email ?? 'admin@nexcommerce.com' }}</p>
                                <span class="inline-flex items-center rounded mt-1.5 bg-purple-500/10 px-1.5 py-0.5 text-[8px] font-medium text-purple-400 ring-1 ring-inset ring-purple-500/20">
                                    {{ Auth::user()->roles->first()?->name ?? 'Administrator' }}
                                </span>
                            </div>

                            <!-- Dropdown Options -->
                            <div class="p-1.5 space-y-1">
                                <button onclick="openProfileSettingsModal()" class="w-full text-left group flex items-center gap-2 rounded-xl px-3 py-2 text-[11px] font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all">
                                    <svg class="h-4 w-4 text-slate-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                    Profile Settings
                                </button>
                                
                                @if(Auth::user()->hasPermissionTo('manage_users') || Auth::user()->hasRole('Super Admin'))
                                <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-2 rounded-xl px-3 py-2 text-[11px] font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all">
                                    <svg class="h-4 w-4 text-slate-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Users Directory
                                </a>
                                @endif
                            </div>

                            <!-- Logout Action -->
                            <div class="p-1.5 border-t border-indigo-950/50">
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full group flex items-center gap-2 rounded-xl px-3 py-2 text-[11px] font-semibold text-red-400 hover:bg-red-500/10 transition-all">
                                        <svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Main Content Area -->
            <main class="flex-1 py-8 px-6 lg:px-8">
                <!-- Flash Notification Banner -->
                @if(session('success'))
                    <div class="mb-6 flex items-center justify-between rounded-xl bg-emerald-500/10 p-4 text-emerald-400 border border-emerald-500/20 shadow-lg shadow-emerald-950/10" id="alert-banner">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="document.getElementById('alert-banner').style.display='none'" class="text-emerald-400/70 hover:text-emerald-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 flex items-center justify-between rounded-xl bg-red-500/10 p-4 text-red-400 border border-red-500/20 shadow-lg shadow-red-950/10" id="error-banner">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="document.getElementById('error-banner').style.display='none'" class="text-red-400/70 hover:text-red-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Sticky footer -->
            <footer class="mt-auto border-t border-indigo-950/40 py-6 px-6 lg:px-8 text-center text-xs text-slate-500 font-medium bg-slate-900/10">
                &copy; {{ date('Y') }} NexCommerce Headless E-commerce Ecosystem. Crafted with premium purple gradients.
            </footer>
        </div>
    </div>

    <!-- 4. Beautiful Admin Profile Settings Modal Popup -->
    <div id="profile-settings-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div onclick="closeProfileSettingsModal()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl border border-indigo-950/60 bg-slate-900 p-6 shadow-2xl transition-all">
                <!-- Close Button -->
                <button onclick="closeProfileSettingsModal()" class="absolute top-4 right-4 p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Header -->
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-white">Administrator Profile Settings</h3>
                    <p class="text-xs text-slate-400 mt-1">Configure your personal name, email, and password credentials</p>
                </div>

                <!-- Update Form -->
                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4 m-0" enctype="multipart/form-data">
                    @csrf
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Profile Picture</label>
                        <input type="file" name="profile_image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-purple-500/10 file:text-purple-400 hover:file:bg-purple-500/20 transition-all cursor-pointer border border-indigo-950/50 bg-slate-950 rounded-xl p-1">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Profile Name</label>
                        <input type="text" name="name" required value="{{ Auth::user()->name ?? '' }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input type="email" name="email" required value="{{ Auth::user()->email ?? '' }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div class="h-px bg-indigo-950/40 my-2"></div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">New Password (Optional)</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm new password" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-indigo-950/40">
                        <button type="button" onclick="closeProfileSettingsModal()" class="px-4 py-2.5 rounded-xl border border-indigo-950/50 text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-xs font-semibold text-white shadow-lg shadow-purple-500/20 hover:scale-102 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 5. Notification Details Modal -->
    <div id="notification-details-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <div onclick="closeNotificationDetailsModal()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl border border-indigo-950/60 bg-slate-900 p-6 shadow-2xl transition-all">
                <button onclick="closeNotificationDetailsModal()" class="absolute top-4 right-4 p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-white">Notification Details</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <p id="notif-detail-title" class="text-sm font-bold text-slate-200"></p>
                        <p id="notif-detail-date" class="text-[10px] text-slate-400 mt-1"></p>
                    </div>
                    <div class="rounded-xl border border-indigo-950/50 bg-slate-950 p-4">
                        <p id="notif-detail-message" class="text-xs text-slate-300 leading-relaxed whitespace-pre-wrap"></p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-indigo-950/40 text-center">
                        <button onclick="closeNotificationDetailsModal()" class="px-6 py-2.5 rounded-xl bg-slate-800 text-xs font-semibold text-white hover:bg-slate-700 transition-all">Acknowledge Alert</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme & Navigation Controllers Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            
            if (html.classList.contains('light')) {
                html.classList.remove('light');
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            } else {
                html.classList.remove('dark');
                html.classList.add('light');
                localStorage.setItem('theme', 'light');
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            }
        }

        // Initialize Theme Toggle Icons on page load
        document.addEventListener('DOMContentLoaded', () => {
            const theme = localStorage.getItem('theme') || 'dark';
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            if (theme === 'light') {
                if (darkIcon) darkIcon.classList.add('hidden');
                if (lightIcon) lightIcon.classList.remove('hidden');
            } else {
                if (darkIcon) darkIcon.classList.remove('hidden');
                if (lightIcon) lightIcon.classList.add('hidden');
            }
        });

        // Responsive mobile sidebar toggle controllers
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const panel = document.getElementById('mobile-sidebar-panel');
            
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    panel.classList.remove('-translate-x-full');
                }, 10);
            } else {
                panel.classList.add('-translate-x-full');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
            }
        }

        // Profile Dropdown Toggle
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('scale-95', 'opacity-0');
                    dropdown.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                dropdown.classList.remove('scale-100', 'opacity-100');
                dropdown.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 150);
            }
        }

        // Close dropdowns & modals when clicking outside
        window.addEventListener('click', function(e) {
            // Profile dropdown click outside check
            const profileBtn = document.getElementById('profile-dropdown-btn');
            const profileDropdown = document.getElementById('profile-dropdown');
            if (profileDropdown && profileBtn && !profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('scale-100', 'opacity-100');
                profileDropdown.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    profileDropdown.classList.add('hidden');
                }, 150);
            }

            // Notification dropdown click outside check
            const notifBtn = document.getElementById('notification-bell-btn');
            const notifDropdown = document.getElementById('notification-dropdown');
            if (notifDropdown && notifBtn && !notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('scale-100', 'opacity-100');
                notifDropdown.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    notifDropdown.classList.add('hidden');
                }, 150);
            }
        });

        // Profile settings modal actions
        function openProfileSettingsModal() {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown) {
                dropdown.classList.add('hidden');
                dropdown.classList.remove('scale-100', 'opacity-100');
            }
            const modal = document.getElementById('profile-settings-modal');
            modal.classList.remove('hidden');
        }

        function closeProfileSettingsModal() {
            const modal = document.getElementById('profile-settings-modal');
            modal.classList.add('hidden');
        }

        // Notifications Dropdown toggle
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('scale-95', 'opacity-0');
                    dropdown.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                dropdown.classList.remove('scale-100', 'opacity-100');
                dropdown.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 150);
            }
        }

        // Read specific notification
        function readNotification(el, id) {
            // Setup Modal Content
            const titleEl = el.querySelector('.notif-item-title');
            const title = titleEl ? titleEl.innerText : 'Notification';
            const message = el.getAttribute('data-message') || '';
            const date = el.getAttribute('data-date') || '';
            
            document.getElementById('notif-detail-title').innerText = title;
            document.getElementById('notif-detail-message').innerText = message;
            document.getElementById('notif-detail-date').innerText = date;

            // Show Modal
            const dropdown = document.getElementById('notification-dropdown');
            if (dropdown) {
                dropdown.classList.remove('scale-100', 'opacity-100');
                dropdown.classList.add('scale-95', 'opacity-0');
                setTimeout(() => dropdown.classList.add('hidden'), 150);
            }
            document.getElementById('notification-details-modal').classList.remove('hidden');

            if (el.getAttribute('data-unread') === 'true') {
                el.setAttribute('data-unread', 'false');
                el.classList.add('opacity-50');
                if (titleEl) {
                    titleEl.classList.remove('text-slate-200');
                    titleEl.classList.add('text-slate-400');
                }
                const dot = el.querySelector('.notification-dot');
                if (dot) dot.remove();

                // Decrement Badge
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    let count = parseInt(badge.innerText) - 1;
                    if (count > 0) {
                        badge.innerText = count;
                    } else {
                        badge.remove();
                    }
                }

                // Call server to mark as read
                if (id) {
                    fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).catch(err => console.log(err));
                }
            }
        }

        function closeNotificationDetailsModal() {
            document.getElementById('notification-details-modal').classList.add('hidden');
        }

        // Clear all notifications
        function clearAllNotifications() {
            const container = document.getElementById('notification-list-container');
            const alerts = container.children;
            for (let i = 0; i < alerts.length; i++) {
                alerts[i].setAttribute('data-unread', 'false');
                alerts[i].classList.add('opacity-50');
                const dot = alerts[i].querySelector('.notification-dot');
                if (dot) dot.remove();
            }
            const badge = document.getElementById('notification-badge');
            if (badge) badge.remove();
        }
    </script>
</body>
</html>
