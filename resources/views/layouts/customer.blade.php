<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ChatEasy') }} - Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Ensure form fields inside the main page area always have readable
           dark text on a white background. The body uses `text-white` which
           was being inherited by inputs and made user typing invisible. */
        main input:not([type="checkbox"]):not([type="radio"]):not([type="color"]):not([type="file"]):not([type="range"]):not([type="submit"]):not([type="button"]):not([type="reset"]),
        main textarea,
        main select {
            color: #111827; /* gray-900 */
            background-color: #ffffff;
        }
        main input::placeholder,
        main textarea::placeholder {
            color: #9ca3af; /* gray-400 */
            opacity: 1;
        }
        main input[type="file"] {
            color: #374151; /* gray-700 */
        }
        /* Color picker swatch should keep its native look but stay readable */
        main input[type="color"] {
            background-color: #ffffff;
        }
    </style>
</head>
<body class="bg-[#0a1628] text-white antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0f1c2e] border-r border-white/10 flex-shrink-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="px-6 py-4 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">C</span>
                        </div>
                        <span class="text-xl font-bold">ChatEasy</span>
                    </div>
                </div>

                <!-- Context Selector (Workspace/Store) -->
                @php
                    $activeWorkspaceId = session('active_workspace_id');
                    $activeWorkspace = $activeWorkspaceId ? \App\Models\Workspace::find($activeWorkspaceId) : null;
                    $isStoreContext = isset($activeStore) && $activeStore;
                    $isWorkspaceContext = $activeWorkspace && !$isStoreContext;
                @endphp

                @if($isStoreContext)
                    <!-- Store Context -->
                    <div class="px-3 py-3 border-b border-white/10">
                        <a href="{{ route('stores.dashboard') }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-white/5 transition group">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-emerald-400 font-medium">Current Store</p>
                                    <p class="text-sm font-semibold text-white truncate">{{ $activeStore->name }}</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-emerald-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                            </svg>
                        </a>
                    </div>
                @elseif($isWorkspaceContext)
                    <!-- Workspace Context -->
                    <div class="px-3 py-3 border-b border-white/10">
                        <a href="{{ route('workspaces.dashboard') }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-white/5 transition group">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-blue-400 font-medium">Current Workspace</p>
                                    <p class="text-sm font-semibold text-white truncate">{{ $activeWorkspace->name }}</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-blue-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                            </svg>
                        </a>
                    </div>
                @endif

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" x-data="{ socialMediaOpen: {{ request()->routeIs('app.facebook-ads') || request()->routeIs('app.tiktok-ads') || request()->routeIs('app.ad-campaigns*') || request()->routeIs('app.campaign-creator*') ? 'true' : 'false' }}, aiApiOpen: {{ request()->routeIs('workspaces.ai-settings') ? 'true' : 'false' }}, productsOpen: {{ request()->routeIs('app.products*') || request()->routeIs('app.categories*') ? 'true' : 'false' }} }">
                
                @if(request()->routeIs('workspaces.*'))
                    <!-- Workspace Management Navigation -->
                    <a href="{{ route('workspaces.dashboard') }}" class="{{ request()->routeIs('workspaces.dashboard') ? 'bg-blue-500/20 text-blue-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium">My Workspaces</span>
                    </a>

                    <a href="{{ route('workspaces.create') }}" class="{{ request()->routeIs('workspaces.create') ? 'bg-blue-500/20 text-blue-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="text-sm font-medium">Create Workspace</span>
                    </a>

                    <!-- AI API Integration Section (Workspace Level) -->
                    <div class="border-t border-white/10 my-2"></div>
                    
                    <div>
                        <button @click="aiApiOpen = !aiApiOpen" class="w-full {{ request()->routeIs('workspaces.ai-settings') ? 'text-blue-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center justify-between px-3 py-2.5 rounded-lg transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <span class="text-sm font-medium">AI API Integration</span>
                            </div>
                            <svg :class="{'rotate-180': aiApiOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="aiApiOpen" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('workspaces.ai-settings') }}#openai-connect" class="{{ request()->routeIs('workspaces.ai-settings') ? 'bg-blue-500/20 text-blue-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.282 9.821a5.985 5.985 0 00-.516-4.91 6.046 6.046 0 00-6.51-2.9A6.065 6.065 0 004.981 4.18a5.985 5.985 0 00-3.998 2.9 6.046 6.046 0 00.743 7.097 5.98 5.98 0 00.51 4.911 6.051 6.051 0 006.515 2.9A5.985 5.985 0 0013.26 24a6.056 6.056 0 005.772-4.206 5.99 5.99 0 003.997-2.9 6.056 6.056 0 00-.747-7.073zM13.26 22.43a4.476 4.476 0 01-2.876-1.04l.141-.081 4.779-2.758a.795.795 0 00.392-.681v-6.737l2.02 1.168a.071.071 0 01.038.052v5.583a4.504 4.504 0 01-4.494 4.494zM3.6 18.304a4.47 4.47 0 01-.535-3.014l.142.085 4.783 2.759a.771.771 0 00.78 0l5.843-3.369v2.332a.08.08 0 01-.033.062L9.74 19.95a4.5 4.5 0 01-6.14-1.646zM2.34 7.896a4.485 4.485 0 012.366-1.973V11.6a.766.766 0 00.388.676l5.815 3.355-2.02 1.168a.076.076 0 01-.071 0l-4.83-2.786A4.504 4.504 0 012.34 7.872zm16.597 3.855l-5.833-3.387L15.119 7.2a.076.076 0 01.071 0l4.83 2.791a4.494 4.494 0 01-.676 8.105v-5.678a.79.79 0 00-.407-.667zm2.01-3.023l-.141-.085-4.774-2.782a.776.776 0 00-.785 0L9.409 9.23V6.897a.066.066 0 01.028-.061l4.83-2.787a4.5 4.5 0 016.68 4.66zm-12.64 4.135l-2.02-1.164a.08.08 0 01-.038-.057V6.075a4.5 4.5 0 017.375-3.453l-.142.08L8.704 5.46a.795.795 0 00-.393.681zm1.097-2.365l2.602-1.5 2.607 1.5v2.999l-2.597 1.5-2.607-1.5z"/>
                                </svg>
                                <span>OpenAI Connect</span>
                            </a>
                            <a href="#" class="text-gray-400 hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.4 2.4l-.8.5c-.4.2-.6.6-.6 1v2.6c0 .4.2.8.6 1l.8.5c.5.3 1 .1 1.3-.3l1.5-2.2c.3-.5.2-1-.2-1.4l-1.3-1.4c-.4-.4-.9-.5-1.3-.3zm-4.8 0c-.4-.2-.9-.1-1.3.3L7 4.1c-.4.4-.5.9-.2 1.4l1.5 2.2c.3.4.8.6 1.3.3l.8-.5c.4-.2.6-.6.6-1V3.9c0-.4-.2-.8-.6-1l-.8-.5zM12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm7.6 3.6l-.8-.5c-.4-.2-.8 0-1 .4l-1.5 2.2c-.3.5-.2 1 .2 1.4l1.3 1.4c.4.4.9.5 1.3.3l.8-.5c.4-.2.6-.6.6-1v-2.6c0-.4-.2-.8-.6-1l-.3-.1zm-15.2 0c-.4.2-.6.6-.6 1v2.6c0 .4.2.8.6 1l.8.5c.4.2.9.1 1.3-.3l1.3-1.4c.4-.4.5-.9.2-1.4l-1.5-2.2c-.2-.4-.6-.6-1-.4l-.8.5-.3.1z"/>
                                </svg>
                                <span>Claude Connect</span>
                            </a>
                        </div>
                    </div>

                @elseif(request()->routeIs('stores.dashboard') || request()->routeIs('stores.create') || request()->routeIs('stores.edit'))
                    <!-- Store Management Navigation -->
                    <a href="{{ route('workspaces.dashboard') }}" class="text-gray-400 hover:bg-white/5 flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="text-sm font-medium">Back to Workspaces</span>
                    </a>

                    <div class="border-t border-white/10 my-2"></div>

                    <a href="{{ route('stores.dashboard') }}" class="{{ request()->routeIs('stores.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-sm font-medium">My Stores</span>
                    </a>

                    <a href="{{ route('stores.create') }}" class="{{ request()->routeIs('stores.create') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="text-sm font-medium">Create Store</span>
                    </a>

                @else
                    <!-- Store App Navigation (Products, WhatsApp, etc.) -->
                    <a href="{{ route('stores.dashboard') }}" class="text-gray-400 hover:bg-white/5 flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="text-sm font-medium">Back to Stores</span>
                    </a>

                    <div class="border-t border-white/10 my-2"></div>

                    <a href="{{ route('app.dashboard') }}" class="{{ request()->routeIs('app.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-sm font-medium">Tableau de bord</span>
                    </a>
                    
                    <a href="{{ route('app.whatsapp') }}" class="{{ request()->routeIs('app.whatsapp') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span class="text-sm font-medium">WhatsApp Accounts</span>
                    </a>
                    
                    <a href="{{ route('app.leads') }}" class="{{ request()->routeIs('app.leads') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="text-sm font-medium">Orders</span>
                    </a>
                    
                    <!-- Products Section -->
                    <div>
                        <button @click="productsOpen = !productsOpen" class="w-full {{ request()->routeIs('app.products*') ? 'text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center justify-between px-3 py-2.5 rounded-lg transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="text-sm font-medium">Products</span>
                            </div>
                            <svg :class="{'rotate-180': productsOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="productsOpen" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('app.products') }}" class="{{ request()->routeIs('app.products') && !request()->routeIs('app.products.create') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                <span>All Products</span>
                            </a>
                            <a href="{{ route('app.products.create') }}" class="{{ request()->routeIs('app.products.create') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Create Product</span>
                            </a>
                            <a href="{{ route('app.categories') }}" class="{{ request()->routeIs('app.categories*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span>Categories</span>
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('app.website-customization') }}" class="{{ request()->routeIs('app.website-customization*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        <span class="text-sm font-medium">Website Customization</span>
                    </a>
                    
                    <a href="{{ route('app.pixel-connect') }}" class="{{ request()->routeIs('app.pixel-connect*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-sm font-medium">Pixel Connect</span>
                    </a>
                    
                    <!-- Social Media API Integration Section -->
                    <div>
                        <button @click="socialMediaOpen = !socialMediaOpen" class="w-full {{ request()->routeIs('app.facebook-ads') || request()->routeIs('app.tiktok-ads') || request()->routeIs('app.ad-campaigns*') || request()->routeIs('app.campaign-creator*') ? 'text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center justify-between px-3 py-2.5 rounded-lg transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                <span class="text-sm font-medium">Social Media API</span>
                            </div>
                            <svg :class="{'rotate-180': socialMediaOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="socialMediaOpen" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('app.ad-campaigns') }}" class="{{ request()->routeIs('app.ad-campaigns') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <span class="material-icons text-sm">campaign</span>
                                <span>Campaigns Dashboard</span>
                            </a>
                            <a href="{{ route('app.campaign-creator') }}" class="{{ request()->routeIs('app.campaign-creator*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <span class="material-icons text-sm">auto_awesome</span>
                                <span>Create Campaign (AI)</span>
                            </a>
                            <a href="{{ route('app.facebook-ads') }}" class="{{ request()->routeIs('app.facebook-ads') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook Ads Connect</span>
                            </a>
                            <a href="{{ route('app.tiktok-ads') }}" class="{{ request()->routeIs('app.tiktok-ads') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400' }} hover:bg-white/5 flex items-center gap-3 px-3 py-2 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.53.02C1.84-.117.02 1.79.02 11.82V23.8h11.96V.02h.55zm5.66 0c-.28 0-.53.02-.79.07v12.03H23.98v-.28c0-9.65-1.54-11.65-5.79-11.82zM12.53 23.98V24h11.45v-3.08H12.53v3.06z"/>
                                </svg>
                                <span>TikTok Ads Connect</span>
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('app.external-api-settings') }}" class="{{ request()->routeIs('app.external-api-settings') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-400 hover:bg-white/5' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm font-medium">System Connect</span>
                    </a>
                @endif
                </nav>

                <!-- User Profile -->
                <div class="px-3 py-4 border-t border-white/10">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 px-3 py-2 w-full rounded-lg hover:bg-white/5 transition">
                            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0 text-left">
                                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->subscription_plan ?? 'Free' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="absolute bottom-full left-0 right-0 mb-2 bg-[#1a2332] rounded-lg shadow-lg py-1 border border-white/10">
                            @if(auth()->user()->role === 'superadmin')
                                <a href="{{ route('superadmin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition">
                                    Super Admin
                                </a>
                                <div class="border-t border-white/10 my-1"></div>
                            @endif
                            <a href="{{ route('workspaces.dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition">
                                My Workspaces
                            </a>
                            @if(session('active_workspace_id'))
                                <a href="{{ route('stores.dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition">
                                    My Stores
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition">
                                Profile Settings
                            </a>
                            <div class="border-t border-white/10 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5 transition">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                    <style>
                        [x-cloak] { display: none !important; }
                    </style>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-[#0f1c2e] border-b border-white/10 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        @if (isset($header))
                            {{ $header }}
                        @else
                            <h2 class="text-xl font-semibold text-white">
                                @if(request()->routeIs('workspaces.*'))
                                    Workspace Management
                                @elseif(request()->routeIs('stores.*'))
                                    Store Management
                                @elseif(request()->routeIs('app.*'))
                                    @if(isset($activeStore))
                                        {{ $activeStore->name }}
                                    @else
                                        Dashboard
                                    @endif
                                @else
                                    Dashboard
                                @endif
                            </h2>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        {{-- Header elements hidden as requested --}}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>
</body>
</html>
