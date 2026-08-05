<x-customer-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white">System Connect — Alfa-COD</h2>
            <p class="text-sm text-gray-400 mt-1">Connect Alfa-COD to push orders and assign sellers to your stores</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Settings -->
        <div class="lg:col-span-2 space-y-6">
            <!-- External API Integration -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Alfa-COD API Connection</h3>
                        <p class="text-sm text-gray-400 mt-1">Configure your Alfa-COD (smanager) API to receive orders and load sellers</p>
                    </div>
                    @if($user->external_api_enabled && $user->external_api_url && $user->external_api_key_encrypted)
                        <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300">Active</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-500/15 px-3 py-1 text-xs font-medium text-amber-200">Not configured</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('app.external-api-settings.save') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="external_api_url" class="block text-sm font-medium text-gray-300 mb-2">Base API URL</label>
                        <input type="url" name="external_api_url" id="external_api_url"
                            value="{{ old('external_api_url', $user->external_api_url) }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:outline-none transition"
                            placeholder="https://smanager.site">
                        <p class="mt-1 text-xs text-gray-400">
                            Enter the base URL of Alfa-COD (WITHOUT /api at the end).<br>
                            ✅ Correct: <code class="text-emerald-400">https://smanager.site</code><br>
                            ❌ Wrong: <code class="text-red-400">https://smanager.site/api</code>
                        </p>
                        @error('external_api_url')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="external_api_key" class="block text-sm font-medium text-gray-300 mb-2">API Key</label>
                        <input type="password" name="external_api_key" id="external_api_key" autocomplete="off"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:outline-none transition"
                            placeholder="{{ $user->external_api_key_encrypted ? 'New key to replace the current one' : 'Your API authentication key (capi_...)' }}">
                        <p class="mt-1 text-xs text-gray-400">Your API key is encrypted and securely stored. It never appears in plain text after saving.</p>
                        @error('external_api_key')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer select-none p-3 bg-[#0a1628] rounded-lg border border-white/10 hover:border-emerald-500/50 transition">
                            <input type="checkbox" name="external_api_enabled" value="1" 
                                {{ $user->external_api_enabled ? 'checked' : '' }}
                                class="rounded border-white/20 bg-[#0a1628] text-emerald-500 focus:ring-emerald-500/30">
                            <div>
                                <div class="font-medium text-white">Enable API Integration</div>
                                <div class="text-xs text-gray-400 mt-0.5">Automatically push orders to Alfa-COD when landing page forms are submitted</div>
                            </div>
                        </label>
                        @if($user->external_api_key_encrypted)
                        <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer select-none">
                            <input type="checkbox" name="clear_external_api_key" value="1" class="rounded border-white/20 bg-[#0a1628] text-cyan-500 focus:ring-cyan-500/30">
                            Remove saved API key
                        </label>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold rounded-lg transition">
                            Save Settings
                        </button>
                    </div>
                </form>

                @if($user->external_api_url && $user->external_api_key_encrypted)
                    <form method="POST" action="{{ route('app.external-api-settings.test') }}" class="mt-4 pt-4 border-t border-white/10">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-[#0a1628] border border-white/15 hover:border-cyan-500/50 text-white text-sm font-medium rounded-lg transition">
                            Test API Connection
                        </button>
                    </form>
                @endif
            </div>

            <!-- Sellers from Alfa-COD -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Alfa-COD Sellers</h3>
                        <p class="text-sm text-gray-400 mt-1">Sellers loaded from Alfa-COD. Assign one to each store when creating or editing a store.</p>
                    </div>
                    @if($sellersLoaded && empty($sellersError))
                        <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300">
                            {{ count($sellers) }} seller{{ count($sellers) === 1 ? '' : 's' }}
                        </span>
                    @endif
                </div>

                @if(!$user->external_api_enabled || !$user->external_api_url || !$user->external_api_key_encrypted)
                    <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
                        Save and enable your Alfa-COD API connection above to load sellers.
                    </div>
                @elseif(!empty($sellersError))
                    <div class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                        {{ $sellersError }}
                    </div>
                @elseif(count($sellers) === 0)
                    <div class="rounded-lg border border-white/10 bg-[#0a1628] px-4 py-3 text-sm text-gray-400">
                        No active sellers found in Alfa-COD.
                    </div>
                @else
                    <div class="overflow-hidden rounded-lg border border-white/10">
                        <table class="min-w-full text-sm">
                            <thead class="bg-[#0a1628] text-gray-400">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium">ID</th>
                                    <th class="px-4 py-2 text-left font-medium">Seller</th>
                                    <th class="px-4 py-2 text-left font-medium">Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($sellers as $seller)
                                    <tr class="text-gray-200">
                                        <td class="px-4 py-2 text-gray-400">{{ $seller['id'] }}</td>
                                        <td class="px-4 py-2">{{ $seller['company_name'] ?: $seller['name'] }}</td>
                                        <td class="px-4 py-2 text-gray-400">{{ $seller['email'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Next step: open
                        <a href="{{ route('stores.create') }}" class="text-cyan-400 hover:underline">Create Store</a>
                        or edit an existing store and choose the seller.
                    </p>
                @endif
            </div>
        </div>

        <!-- Right Column - Info -->
        <div class="space-y-6">
            <!-- How it works -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">How it works</h3>
                <ol class="space-y-3 text-sm text-gray-300">
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-bold">1</span>
                        <span>Connect Alfa-COD with your Base URL and API key</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-bold">2</span>
                        <span>Sellers load automatically from Alfa-COD</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-bold">3</span>
                        <span>Assign a seller when creating or editing a store</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-bold">4</span>
                        <span>New orders are pushed with that seller so they show in their Alfa-COD account</span>
                    </li>
                </ol>
            </div>

            <!-- API Endpoints -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Expected API Endpoints</h3>
                <div class="space-y-3 text-xs">
                    <div class="bg-[#0a1628] border border-white/5 rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded text-[10px] font-bold">POST</span>
                            <code class="text-gray-300">/api/orders</code>
                        </div>
                        <p class="text-gray-400">Create a new order (includes vendor_id / seller)</p>
                    </div>
                    <div class="bg-[#0a1628] border border-white/5 rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 rounded text-[10px] font-bold">GET</span>
                            <code class="text-gray-300">/api/sellers</code>
                        </div>
                        <p class="text-gray-400">List Alfa-COD sellers</p>
                    </div>
                    <div class="bg-[#0a1628] border border-white/5 rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 rounded text-[10px] font-bold">GET</span>
                            <code class="text-gray-300">/api/orders</code>
                        </div>
                        <p class="text-gray-400">Test connection / list orders</p>
                    </div>
                </div>
            </div>

            <!-- Data Structure -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Order Data Structure</h3>
                <div class="bg-[#0a1628] border border-white/5 rounded-lg p-3">
                    <pre class="text-xs text-gray-300 overflow-x-auto"><code>{
  "client_name": "string",
  "client_phone": "string",
  "vendor_id": "number (seller)",
  "source": "whatsapp",
  "website": { "...": "store info" },
  "items": [{
    "product_id": "number",
    "sku": "string",
    "name": "string",
    "quantity": "number",
    "price": "number"
  }],
  "notes": "string",
  "metadata": { "...": "..." }
}</code></pre>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-amber-300 mb-1">Security Notice</h4>
                        <p class="text-xs text-amber-200/80">Your API key is encrypted using Laravel's encryption and stored securely. Never share your API credentials with anyone.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
