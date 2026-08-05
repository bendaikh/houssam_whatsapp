<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(isset($activeStore) && $activeStore)
                        <!-- Store Management Menu -->
                        <x-nav-link :href="route('app.dashboard')" :active="request()->routeIs('app.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('app.products')" :active="request()->routeIs('app.products*')">
                            {{ __('Products') }}
                        </x-nav-link>
                        <x-nav-link :href="route('app.categories')" :active="request()->routeIs('app.categories')">
                            {{ __('Categories') }}
                        </x-nav-link>
                        <x-nav-link :href="route('app.orders')" :active="request()->routeIs('app.orders')">
                            {{ __('Orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('app.whatsapp')" :active="request()->routeIs('app.whatsapp')">
                            {{ __('WhatsApp') }}
                        </x-nav-link>
                        
                        <!-- Settings Dropdown in Nav -->
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ __('More') }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('app.website-customization')">
                                        {{ __('Website Customization') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('workspaces.ai-settings')">
                                        {{ __('AI Settings') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('app.facebook-ads')">
                                        {{ __('Facebook Ads') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('app.tiktok-ads')">
                                        {{ __('TikTok Ads') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        <x-nav-link :href="route('stores.dashboard')" :active="request()->routeIs('stores.*')">
                            {{ __('My Stores') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Active Store Indicator -->
                @if(isset($activeStore) && $activeStore)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('stores.dashboard') }}" class="flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-md hover:bg-emerald-100 transition">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-sm font-medium text-emerald-700">{{ $activeStore->name }}</span>
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                            </svg>
                        </a>
                    </div>
                @endif
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(auth()->user()->role === 'superadmin')
                            <x-dropdown-link :href="route('superadmin.dashboard')">
                                {{ __('Super Admin') }}
                            </x-dropdown-link>
                            <div class="border-t border-gray-100"></div>
                        @endif
                        
                        <x-dropdown-link :href="route('stores.dashboard')">
                            {{ __('My Stores') }}
                        </x-dropdown-link>
                        
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(isset($activeStore) && $activeStore)
                <x-responsive-nav-link :href="route('app.dashboard')" :active="request()->routeIs('app.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('app.products')" :active="request()->routeIs('app.products*')">
                    {{ __('Products') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('app.categories')" :active="request()->routeIs('app.categories')">
                    {{ __('Categories') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('app.orders')" :active="request()->routeIs('app.orders')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('app.whatsapp')" :active="request()->routeIs('app.whatsapp')">
                    {{ __('WhatsApp') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('app.website-customization')" :active="request()->routeIs('app.website-customization')">
                    {{ __('Website Customization') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('workspaces.ai-settings')" :active="request()->routeIs('workspaces.ai-settings')">
                    {{ __('AI Settings') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('stores.dashboard')" :active="request()->routeIs('stores.*')">
                    {{ __('My Stores') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @if(isset($activeStore) && $activeStore)
                <div class="px-4 mb-3">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Current Store</div>
                    <div class="font-medium text-base text-gray-800">{{ $activeStore->name }}</div>
                </div>
            @endif
            
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if(auth()->user()->role === 'superadmin')
                    <x-responsive-nav-link :href="route('superadmin.dashboard')">
                        {{ __('Super Admin') }}
                    </x-responsive-nav-link>
                @endif
                
                <x-responsive-nav-link :href="route('stores.dashboard')">
                    {{ __('My Stores') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
