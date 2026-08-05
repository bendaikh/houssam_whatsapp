<x-stores-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Store') }}
            </h2>
            <a href="{{ route('stores.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stores
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form action="{{ route('stores.update', $store) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Store Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $store->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subdomain" class="block text-sm font-medium text-gray-700">Subdomain *</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain', $store->subdomain) }}" required
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="mystore">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        .yourdomain.com
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Only letters, numbers, dashes, and underscores allowed</p>
                                @error('subdomain')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="domain" class="block text-sm font-medium text-gray-700">Custom Domain (Optional)</label>
                                <input type="text" name="domain" id="domain" value="{{ old('domain', $store->domain) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="www.mystore.com">
                                <p class="mt-1 text-sm text-gray-500">If you have a custom domain, enter it here</p>
                                @error('domain')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $store->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alfa_cod_seller_id" class="block text-sm font-medium text-gray-700">Alfa-COD Seller</label>
                                @if(count($sellers ?? []) > 0)
                                    <select name="alfa_cod_seller_id" id="alfa_cod_seller_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">— No seller assigned —</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller['id'] }}" @selected((string) old('alfa_cod_seller_id', $store->alfa_cod_seller_id) === (string) $seller['id'])>
                                                {{ $seller['label'] ?? $seller['company_name'] ?? $seller['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">Orders from this store will appear in this seller's Alfa-COD account.</p>
                                @else
                                    <div class="mt-1 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 mb-2">
                                        @if($store->alfa_cod_seller_id)
                                            Currently assigned: <strong>{{ $store->alfa_cod_seller_name ?: ('Seller #' . $store->alfa_cod_seller_id) }}</strong>.
                                        @endif
                                        Seller list not loaded. Enter a seller ID manually or open
                                        <a href="{{ route('workspaces.alfa-cod-settings') }}" class="font-medium underline">Alfa-COD Connect</a>.
                                    </div>
                                    <input type="number" name="alfa_cod_seller_id" id="alfa_cod_seller_id" min="1"
                                        value="{{ old('alfa_cod_seller_id', $store->alfa_cod_seller_id) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Alfa-COD seller / vendor ID">
                                    <input type="text" name="alfa_cod_seller_name" value="{{ old('alfa_cod_seller_name', $store->alfa_cod_seller_name) }}"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Seller name (optional)">
                                @endif
                                @error('alfa_cod_seller_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-4" id="system-connect">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">System Connect</h3>
                                        <p class="text-xs text-gray-500 mt-1">Per-store API credentials used to push landing-page orders. Each store can use a different connection.</p>
                                    </div>
                                    @if($store->hasSystemConnectConfigured())
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">Not configured</span>
                                    @endif
                                </div>

                                <div>
                                    <label for="system_connect_url" class="block text-sm font-medium text-gray-700">Base API URL</label>
                                    <input type="url" name="system_connect_url" id="system_connect_url"
                                        value="{{ old('system_connect_url', $store->system_connect_url) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="https://smanager.site">
                                    <p class="mt-1 text-xs text-gray-500">Base URL without <code>/api</code> at the end.</p>
                                    @error('system_connect_url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="system_connect_key" class="block text-sm font-medium text-gray-700">API Key</label>
                                    <input type="password" name="system_connect_key" id="system_connect_key" autocomplete="off"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="{{ $store->system_connect_key_encrypted ? 'New key to replace the current one' : 'capi_...' }}">
                                    <p class="mt-1 text-xs text-gray-500">Encrypted and stored securely for this store only. Leave blank to keep the current key.</p>
                                    @error('system_connect_key')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer select-none">
                                        <input type="checkbox" name="system_connect_enabled" value="1"
                                            {{ old('system_connect_enabled', $store->system_connect_enabled) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>
                                            <span class="font-medium text-gray-900">Enable System Connect</span>
                                            <span class="block text-xs text-gray-500">Push orders from this store via its own API credentials</span>
                                        </span>
                                    </label>
                                    @if($store->system_connect_key_encrypted)
                                        <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none">
                                            <input type="checkbox" name="clear_system_connect_key" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            Remove saved API key
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $store->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Store is active
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <a href="{{ route('stores.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                    Update Store
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($store->system_connect_url && $store->system_connect_key_encrypted)
                        <form method="POST" action="{{ route('stores.system-connect.test', $store) }}" class="mt-4 pt-4 border-t border-gray-200">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-white border border-gray-300 hover:border-blue-400 text-gray-800 text-sm font-medium rounded transition">
                                Test System Connect
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-stores-layout>
