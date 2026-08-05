<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="flex h-screen overflow-hidden">
                <!-- Sidebar -->
                <aside class="w-64 bg-white border-r border-gray-200 flex flex-col" x-data="{ open: true }">
                    <!-- Logo -->
                    <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
                        <a href="{{ route('stores.dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    </div>

                    <!-- Store Selector -->
                    @if(isset($activeStore) && $activeStore)
                        <div class="px-4 py-3 border-b border-gray-200 bg-emerald-50">
                            <a href="{{ route('stores.dashboard') }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-emerald-100 transition group">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-emerald-600 font-medium">Current Store</p>
                                        <p class="text-sm font-semibold text-emerald-900 truncate">{{ $activeStore->name }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-emerald-600 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                </svg>
                            </a>
                        </div>
                    @endif

                    <!-- Navigation -->
                    <nav class="flex-1 overflow-y-auto py-4 px-3">
                        <div class="space-y-1">
                            <!-- Dashboard -->
                            <a href="{{ route('app.dashboard') }}" class="{{ request()->routeIs('app.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Dashboard
                            </a>

                            <!-- Products -->
                            <a href="{{ route('app.products') }}" class="{{ request()->routeIs('app.products*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Products
                            </a>

                            <!-- Categories -->
                            <a href="{{ route('app.categories') }}" class="{{ request()->routeIs('app.categories') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Categories
                            </a>

                            <!-- Orders -->
                            <a href="{{ route('app.orders') }}" class="{{ request()->routeIs('app.orders') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Orders
                            </a>

                            <!-- Leads -->
                            <a href="{{ route('app.leads') }}" class="{{ request()->routeIs('app.leads') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Leads
                            </a>

                            <!-- Divider -->
                            <div class="pt-4 pb-2">
                                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Communication</p>
                            </div>

                            <!-- WhatsApp -->
                            <a href="{{ route('app.whatsapp') }}" class="{{ request()->routeIs('app.whatsapp*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                WhatsApp
                            </a>

                            <!-- Conversations -->
                            <a href="{{ route('app.conversations') }}" class="{{ request()->routeIs('app.conversations*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                                Conversations
                            </a>

                            <!-- Divider -->
                            <div class="pt-4 pb-2">
                                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Store Settings</p>
                            </div>

                            <!-- Website Customization -->
                            <a href="{{ route('app.website-customization') }}" class="{{ request()->routeIs('app.website-customization') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                                Website Customization
                            </a>

                            <!-- AI Settings -->
                            <a href="{{ route('workspaces.ai-settings') }}" class="{{ request()->routeIs('workspaces.ai-settings') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                AI Settings
                            </a>

                            <!-- Divider -->
                            <div class="pt-4 pb-2">
                                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Marketing</p>
                            </div>

                            <!-- Campaigns -->
                            <a href="{{ route('app.campaigns') }}" class="{{ request()->routeIs('app.campaigns') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                                Campaigns
                            </a>

                            <!-- Facebook Ads -->
                            <a href="{{ route('app.facebook-ads') }}" class="{{ request()->routeIs('app.facebook-ads') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Facebook Ads
                            </a>

                            <!-- TikTok Ads -->
                            <a href="{{ route('app.tiktok-ads') }}" class="{{ request()->routeIs('app.tiktok-ads') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                TikTok Ads
                            </a>
                        </div>
                    </nav>

                    <!-- User Section -->
                    <div class="border-t border-gray-200 p-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 text-left min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg py-1 border border-gray-200">
                                @if(auth()->user()->role === 'superadmin')
                                    <a href="{{ route('superadmin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        Super Admin
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                @endif
                                <a href="{{ route('stores.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    My Stores
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    Profile Settings
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Top Bar -->
                    @include('layouts.navigation')

                    <!-- Page Content -->
                    <main class="flex-1 overflow-y-auto bg-gray-50">
                        <!-- Page Heading -->
                        @isset($header)
                            <header class="bg-white shadow-sm">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <div class="py-12">
                            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                {{ $slot }}
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </body>
</html>
