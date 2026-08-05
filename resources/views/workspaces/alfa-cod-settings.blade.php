<x-stores-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Alfa-COD Connect') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">Connect Alfa-COD to push orders and assign sellers to your stores</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Alfa-COD API Connection</h3>
                                    <p class="text-sm text-gray-500 mt-1">Configure your Alfa-COD (smanager) API to receive orders and load sellers</p>
                                </div>
                                @if($user->external_api_enabled && $user->external_api_url && $user->external_api_key_encrypted)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">Not configured</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('workspaces.alfa-cod-settings.save') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="external_api_url" class="block text-sm font-medium text-gray-700 mb-2">Base API URL</label>
                                    <input type="url" name="external_api_url" id="external_api_url"
                                        value="{{ old('external_api_url', $user->external_api_url) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition"
                                        placeholder="https://smanager.site">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Enter the base URL without <code>/api</code> at the end.
                                        Example: <code class="text-emerald-600">https://smanager.site</code>
                                    </p>
                                    @error('external_api_url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="external_api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                                    <input type="password" name="external_api_key" id="external_api_key" autocomplete="off"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition"
                                        placeholder="{{ $user->external_api_key_encrypted ? 'New key to replace the current one' : 'capi_...' }}">
                                    <p class="mt-1 text-xs text-gray-500">Encrypted and stored securely. Never shown again after saving.</p>
                                    @error('external_api_key')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer select-none p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition">
                                        <input type="checkbox" name="external_api_enabled" value="1"
                                            {{ $user->external_api_enabled ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div>
                                            <div class="font-medium text-gray-900">Enable API Integration</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Push landing-page orders to Alfa-COD automatically</div>
                                        </div>
                                    </label>
                                    @if($user->external_api_key_encrypted)
                                        <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none">
                                            <input type="checkbox" name="clear_external_api_key" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            Remove saved API key
                                        </label>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                                        Save Settings
                                    </button>
                                </div>
                            </form>

                            @if($user->external_api_url && $user->external_api_key_encrypted)
                                <form method="POST" action="{{ route('workspaces.alfa-cod-settings.test') }}" class="mt-4 pt-4 border-t border-gray-200">
                                    @csrf
                                    <button type="submit" class="px-5 py-2.5 bg-white border border-gray-300 hover:border-blue-400 text-gray-800 text-sm font-medium rounded-lg transition">
                                        Test API Connection
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Alfa-COD Sellers</h3>
                                    <p class="text-sm text-gray-500 mt-1">Assign one of these sellers when creating or editing a store.</p>
                                </div>
                                @if($sellersLoaded && empty($sellersError))
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                        {{ count($sellers) }} seller{{ count($sellers) === 1 ? '' : 's' }}
                                    </span>
                                @endif
                            </div>

                            @if(!$user->external_api_enabled || !$user->external_api_url || !$user->external_api_key_encrypted)
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                    Save and enable your Alfa-COD API connection above to load sellers.
                                </div>
                            @elseif(!empty($sellersError) && empty($sellers))
                                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-2">
                                    <p>{{ $sellersError }}</p>
                                    <p class="text-xs text-red-600">
                                        On the Alfa-COD server run:
                                        <code class="bg-red-100 px-1 rounded">git pull origin master</code>
                                        then
                                        <code class="bg-red-100 px-1 rounded">php artisan route:clear</code>
                                    </p>
                                </div>
                            @elseif(!empty($sellersError) && !empty($sellers))
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 mb-4">
                                    {{ $sellersError }}
                                </div>
                                <div class="overflow-hidden rounded-lg border border-gray-200">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-gray-500">
                                            <tr>
                                                <th class="px-4 py-2 text-left font-medium">ID</th>
                                                <th class="px-4 py-2 text-left font-medium">Seller</th>
                                                <th class="px-4 py-2 text-left font-medium">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($sellers as $seller)
                                                <tr class="text-gray-800">
                                                    <td class="px-4 py-2 text-gray-500">{{ $seller['id'] }}</td>
                                                    <td class="px-4 py-2">{{ $seller['company_name'] ?: $seller['name'] }}</td>
                                                    <td class="px-4 py-2 text-gray-500">{{ $seller['email'] ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif(count($sellers) === 0)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                                    No active sellers found in Alfa-COD.
                                </div>
                            @else
                                <div class="overflow-hidden rounded-lg border border-gray-200">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-gray-500">
                                            <tr>
                                                <th class="px-4 py-2 text-left font-medium">ID</th>
                                                <th class="px-4 py-2 text-left font-medium">Seller</th>
                                                <th class="px-4 py-2 text-left font-medium">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($sellers as $seller)
                                                <tr class="text-gray-800">
                                                    <td class="px-4 py-2 text-gray-500">{{ $seller['id'] }}</td>
                                                    <td class="px-4 py-2">{{ $seller['company_name'] ?: $seller['name'] }}</td>
                                                    <td class="px-4 py-2 text-gray-500">{{ $seller['email'] ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="mt-3 text-xs text-gray-500">
                                    Next:
                                    <a href="{{ route('stores.create') }}" class="text-blue-600 hover:underline">Create Store</a>
                                    or edit a store and choose the seller.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">How it works</h3>
                        <ol class="space-y-3 text-sm text-gray-600">
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">1</span>
                                <span>Connect Alfa-COD with your Base URL and API key</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">2</span>
                                <span>Sellers load automatically from Alfa-COD</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">3</span>
                                <span>Assign a seller when creating or editing a store</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">4</span>
                                <span>Orders are pushed with that seller into their Alfa-COD account</span>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-stores-layout>
