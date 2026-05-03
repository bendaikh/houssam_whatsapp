<x-stores-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('AI API Integration') }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">Configure global AI settings - applies to all workspaces and stores</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('openai_success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-sm text-green-700">{{ session('openai_success') }}</p>
                </div>
            @endif
            @if (session('openai_error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-sm text-red-700">{{ session('openai_error') }}</p>
                </div>
            @endif
            @if (session('anthropic_success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <p class="text-sm text-green-700">{{ session('anthropic_success') }}</p>
                </div>
            @endif
            @if (session('anthropic_error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-sm text-red-700">{{ session('anthropic_error') }}</p>
                </div>
            @endif

            <!-- OpenAI Connection -->
            <div id="openai-connect" class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">OpenAI Connection</h3>
                            <p class="text-sm text-gray-500 mt-1">Enter your OpenAI API key (sk-...). It will be encrypted and stored securely.</p>
                        </div>
                        @if($aiSetting && $aiSetting->openai_api_key_encrypted)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">Key Saved</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">Not Configured</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('workspaces.ai-settings.openai.save') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="openai_api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                            <input type="password" name="openai_api_key" id="openai_api_key" autocomplete="off"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition"
                                placeholder="{{ $aiSetting && $aiSetting->openai_api_key_encrypted ? 'Enter new key to replace current one' : 'sk-...' }}">
                            @error('openai_api_key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="openai_model" class="block text-sm font-medium text-gray-700 mb-2">Default Model</label>
                            <input type="text" name="openai_model" id="openai_model" required
                                value="{{ old('openai_model', optional($aiSetting)->openai_model ?? 'gpt-4o') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition"
                                placeholder="gpt-4o">
                            @error('openai_model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer select-none p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition">
                                <input type="checkbox" name="auto_reply_enabled" value="1" 
                                    {{ optional($aiSetting)->auto_reply_enabled ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="font-medium text-gray-900">Enable AI Auto-Reply on WhatsApp</div>
                                    <div class="text-xs text-gray-500 mt-0.5">AI will automatically respond to incoming WhatsApp messages</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none">
                                <input type="checkbox" name="clear_openai_key" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                                Remove saved key
                            </label>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                                Save Settings
                            </button>
                        </div>
                    </form>

                    @if($aiSetting && $aiSetting->openai_api_key_encrypted)
                        <form method="POST" action="{{ route('workspaces.ai-settings.openai.test') }}" class="mt-4 pt-4 border-t border-gray-200">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-white border border-gray-300 hover:border-blue-400 text-gray-700 text-sm font-medium rounded-lg transition">
                                Test OpenAI Connection
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Anthropic Connection -->
            <div id="anthropic-connect" class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Anthropic (Claude) Connection</h3>
                            <p class="text-sm text-gray-500 mt-1">Alternative to OpenAI. Enter your Anthropic API key (sk-ant-...). It will be encrypted.</p>
                        </div>
                        @if($aiSetting && $aiSetting->anthropic_api_key_encrypted)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">Key Saved</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">Not Configured</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('workspaces.ai-settings.anthropic.save') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="anthropic_api_key" class="block text-sm font-medium text-gray-700 mb-2">Anthropic API Key</label>
                            <input type="password" name="anthropic_api_key" id="anthropic_api_key" autocomplete="off"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition"
                                placeholder="{{ $aiSetting && $aiSetting->anthropic_api_key_encrypted ? 'Enter new key to replace current one' : 'sk-ant-...' }}">
                            @error('anthropic_api_key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="anthropic_model" class="block text-sm font-medium text-gray-700 mb-2">Default Model</label>
                            <select name="anthropic_model" id="anthropic_model" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition">
                                <option value="claude-3-5-sonnet-20241022" {{ old('anthropic_model', optional($aiSetting)->anthropic_model ?? 'claude-3-5-sonnet-20241022') == 'claude-3-5-sonnet-20241022' ? 'selected' : '' }}>Claude 3.5 Sonnet (Recommended)</option>
                                <option value="claude-3-5-haiku-20241022" {{ old('anthropic_model', optional($aiSetting)->anthropic_model) == 'claude-3-5-haiku-20241022' ? 'selected' : '' }}>Claude 3.5 Haiku (Faster, Cheaper)</option>
                                <option value="claude-3-opus-20240229" {{ old('anthropic_model', optional($aiSetting)->anthropic_model) == 'claude-3-opus-20240229' ? 'selected' : '' }}>Claude 3 Opus (Most Capable)</option>
                            </select>
                            @error('anthropic_model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none">
                            <input type="checkbox" name="clear_anthropic_key" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                            Remove saved key
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition">
                                Save Settings
                            </button>
                        </div>
                    </form>

                    @if($aiSetting && $aiSetting->anthropic_api_key_encrypted)
                        <form method="POST" action="{{ route('workspaces.ai-settings.anthropic.test') }}" class="mt-4 pt-4 border-t border-gray-200">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-white border border-gray-300 hover:border-purple-400 text-gray-700 text-sm font-medium rounded-lg transition">
                                Test Anthropic Connection
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900">Global Account Settings</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            These AI API settings apply to <strong>all workspaces and stores</strong> in your account. You only need to configure this once, and all your workspaces and stores will be able to use AI features like auto-reply, landing page generation, and ad copy generation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-stores-layout>
