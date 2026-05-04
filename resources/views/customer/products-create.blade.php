@extends('layouts.customer')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Product
                </h2>
                <p class="text-sm text-gray-400 mt-1">Add a new product to your catalog</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('app.products.select-theme') }}" class="px-4 py-2 bg-gray-700/50 hover:bg-gray-700 text-white font-medium rounded-lg transition flex items-center gap-2 text-sm border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Change Theme
                </a>
                <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>
    </div>

    <!-- Selected Theme Badge -->
    <div class="mb-6 bg-[#0f1c2e] border border-white/10 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $theme === 'theme2' ? 'bg-cyan-500/20' : 'bg-emerald-500/20' }}">
                <svg class="w-6 h-6 {{ $theme === 'theme2' ? 'text-cyan-400' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white font-semibold flex items-center gap-2">
                    @if($theme === 'theme2')
                        Theme 2 - E-Commerce Style
                        <span class="text-xs bg-cyan-500/20 text-cyan-400 px-2 py-0.5 rounded">Selected</span>
                    @else
                        Theme 1 - Classic Layout
                        <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded">Selected</span>
                    @endif
                </h3>
                <p class="text-sm text-gray-400">
                    @if($theme === 'theme2')
                        High-converting sales page with trust badges and social proof
                    @else
                        Clean, professional product page with AI-generated sections
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('app.products.select-theme') }}" class="text-sm text-gray-400 hover:text-white transition">
            Change &rarr;
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('warning'))
    <div class="mb-6 bg-yellow-500/20 border border-yellow-500/50 text-yellow-400 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>{{ session('warning') }}</span>
    </div>
    @endif

    @php
        $aiSetting = \App\Models\AiApiSetting::where('user_id', auth()->id())->first();
        $hasAiConfigured = $aiSetting && (!empty($aiSetting->openai_api_key_encrypted) || !empty($aiSetting->anthropic_api_key_encrypted));
    @endphp

    @if(!$hasAiConfigured)
    <div class="mb-6 bg-purple-500/20 border border-purple-500/50 text-purple-300 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <span class="font-semibold">AI Landing Page Feature Requires Configuration</span>
            <span class="block text-sm mt-1">Please <a href="{{ route('workspaces.ai-settings') }}" class="underline hover:text-purple-200">configure your AI API settings</a> to use the AI landing page generation feature.</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg">
        <p class="font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Please fix the following errors:
        </p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-4xl">
        <form action="{{ route('app.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="theme" value="{{ $theme }}">
            
            <!-- Product Information Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Product Information</h3>
                    <button 
                        type="button" 
                        id="aiGenerateBtn"
                        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span id="aiGenerateBtnText">AI Generate Landing Page</span>
                    </button>
                </div>
                
                <div id="aiNotice" class="mb-4 bg-blue-500/20 border border-blue-500/50 text-blue-400 px-4 py-3 rounded-lg flex items-start gap-2 hidden">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">AI Landing Page Generation</p>
                        <p class="text-sm mt-1">Fill in the product details below, then click the button above to generate a professional landing page using AI.</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter product name"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <!-- Quill Rich Text Editor -->
                        <div id="description-editor" class="bg-white rounded-lg" style="min-height: 200px;"></div>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="hidden"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Compare Price -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price (DHS) *</label>
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                step="0.01"
                                min="0"
                                required
                                value="{{ old('price') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0.00"
                            />
                            @error('price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="compare_at_price" class="block text-sm font-medium text-gray-300 mb-2">Compare at Price (DHS)</label>
                            <input 
                                type="number" 
                                id="compare_at_price" 
                                name="compare_at_price" 
                                step="0.01"
                                min="0"
                                value="{{ old('compare_at_price') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0.00"
                            />
                            @error('compare_at_price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                        <select 
                            id="category_id" 
                            name="category_id"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        >
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock and SKU -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-300 mb-2">Stock</label>
                            <input 
                                type="number" 
                                id="stock" 
                                name="stock" 
                                min="0"
                                value="{{ old('stock', 0) }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0"
                            />
                            @error('stock')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-300 mb-2">SKU</label>
                            <input 
                                type="text" 
                                id="sku" 
                                name="sku" 
                                value="{{ old('sku') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="Enter SKU"
                            />
                            @error('sku')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Variations Toggle -->
                    <div class="border-t border-white/10 pt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="has_variations" class="block text-sm font-medium text-gray-300">Product has variations</label>
                                <p class="text-xs text-gray-500 mt-1">Enable if this product comes in different sizes, colors, or other options</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <!-- Hidden input to ensure a value is always sent -->
                                <input type="hidden" name="has_variations" value="0">
                                <input 
                                    type="checkbox" 
                                    id="has_variations" 
                                    name="has_variations" 
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('has_variations') ? 'checked' : '' }}
                                    onchange="toggleVariations(this.checked)"
                                />
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Variations Card -->
            <div id="variationsCard" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hidden">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Product Variations</h3>
                        <p class="text-xs text-gray-500 mt-1">Add different options for this product (e.g., sizes, colors)</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="addVariation()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Variation
                    </button>
                </div>

                <div id="variationsContainer" class="space-y-4">
                    <!-- Variations will be added here dynamically -->
                </div>

                <div id="noVariationsMessage" class="text-center py-8 text-gray-500 text-sm">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Click "Add Variation" to create your first product variation
                </div>
                </div>
            </div>

            <!-- Quantity-Based Promotions Card -->
            <div id="promotionsCard" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Quantity-Based Pricing
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Set special prices when customers buy multiple items</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="has_promotions" value="0">
                            <input 
                                type="checkbox" 
                                id="has_promotions" 
                                name="has_promotions" 
                                value="1"
                                class="sr-only peer"
                                onchange="togglePromotions(this.checked)"
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                        </label>
                    </div>
                </div>

                <div id="promotionsContent" class="hidden">
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-300">
                                <p class="font-semibold mb-1">How it works:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Set different prices based on quantity purchased</li>
                                    <li>Example: Buy 1 for 100 DHS, Buy 2 for 90 DHS each, Buy 3+ for 80 DHS each</li>
                                    <li>Promotions apply automatically at checkout</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-300">Pricing Tiers</h4>
                        <button 
                            type="button" 
                            onclick="addPromotion()"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Tier
                        </button>
                    </div>

                    <div id="promotionsContainer" class="space-y-3">
                        <!-- Promotions will be added here dynamically -->
                    </div>
                    
                    <!-- Hidden field to ensure promotions data is always captured -->
                    <input type="hidden" id="promotions_json" name="promotions_json" value="[]">

                    <div id="noPromotionsMessage" class="text-center py-8 border-2 border-dashed border-yellow-500/30 rounded-lg bg-yellow-500/5">
                        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-yellow-300 font-semibold mb-1">⚠️ No pricing tiers added yet!</p>
                        <p class="text-gray-400 text-sm">Click <strong class="text-yellow-400">"Add Tier"</strong> above to create quantity-based pricing</p>
                        <p class="text-xs mt-2 text-gray-500">Example: Buy 2+ items → Pay 90 DHS each instead of 100 DHS</p>
                    </div>
                </div>
            </div>

            <!-- Product Images Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Product Images</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload Images (Multiple)</label>
                    <p class="text-xs text-gray-500 mb-3">First image will be set as main image by default. You can change it later.</p>
                    <div class="border-2 border-dashed border-white/10 rounded-lg p-8 text-center hover:border-emerald-500/50 transition">
                        <input 
                            type="file" 
                            id="images" 
                            name="images[]" 
                            multiple
                            accept="image/*"
                            class="hidden"
                            onchange="previewImages(event)"
                        />
                        <label for="images" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-400 mb-1">Click to upload images</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                        </label>
                    </div>
                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4"></div>
                    <input type="hidden" id="mainImageIndex" name="main_image_index" value="0">
                    @error('images')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Landing Page Sections (Image with Description) -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Landing Page Sections</h3>
                        <p class="text-xs text-gray-500 mt-1">Auto-created from uploaded images. AI will generate titles & descriptions when you enable AI Landing Page Generation.</p>
                    </div>
                </div>
                
                <div id="landingSectionsContainer" class="space-y-4">
                    <div class="text-center py-8 text-gray-500 text-sm" id="noSectionsMessage">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Upload images above to auto-create sections
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Settings</h3>
                
                <div class="space-y-4">
                    <!-- AI Landing Page Generation Toggle -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <label for="generate_landing_page" class="block text-sm font-medium text-gray-300">AI Landing Page</label>
                            <p class="text-xs text-gray-500 mt-1">Automatically generate a professional landing page using AI</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="generate_landing_page" 
                                name="generate_landing_page" 
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-600 peer-checked:to-blue-600"></div>
                        </label>
                    </div>

                    <!-- AI Product Images Generation Toggle -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <label for="generate_product_images" class="block text-sm font-medium text-gray-300">AI Product Images</label>
                            <p class="text-xs text-gray-500 mt-1">Generate 5 realistic product images using AI (requires at least 1 uploaded image)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="generate_product_images" 
                                name="generate_product_images" 
                                class="sr-only peer"
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-orange-600 peer-checked:to-yellow-600"></div>
                        </label>
                    </div>
                    
                    <!-- Active Status -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-300">Active</label>
                            <p class="text-xs text-gray-500 mt-1">Make this product visible on your website</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                class="sr-only peer"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Featured Status -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="is_featured" class="block text-sm font-medium text-gray-300">Featured</label>
                            <p class="text-xs text-gray-500 mt-1">Show this product in featured section</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="is_featured" 
                                name="is_featured" 
                                class="sr-only peer"
                                {{ old('is_featured') ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Landing Page Form Fields -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6" x-data="formFieldsManager">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">حقول نموذج صفحة الهبوط</h3>
                        <p class="text-sm text-gray-400 mt-1">تخصيص حقول نموذج الاتصال على صفحة الهبوط الخاصة بك</p>
                    </div>
                    <button 
                        type="button"
                        @click="addField()"
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة حقل
                    </button>
                </div>

                <div class="space-y-4 mb-4">
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 text-blue-300 text-sm">
                        <strong>ملاحظة:</strong> يمكنك تعديل أو إزالة أي حقل من الحقول أدناه. الحقول المحددة كـ "مطلوب" سيتعين على العملاء ملؤها.
                    </div>
                </div>

                <input type="hidden" name="form_fields" x-model="formFieldsJson">

                <template x-if="fields.length === 0">
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>لم يتم إضافة حقول نموذج مخصصة بعد</p>
                        <p class="text-sm mt-1">انقر على "إضافة حقل" لإنشاء حقول مخصصة لنموذج صفحة الهبوط الخاصة بك</p>
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(field, index) in fields" :key="field.id">
                        <div class="bg-[#0a1628] border border-white/10 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                    <h4 class="font-semibold text-white" x-text="'الحقل ' + (index + 1)"></h4>
                                </div>
                                <button 
                                    type="button"
                                    @click="removeField(index)"
                                    class="text-red-400 hover:text-red-300 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Field Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">نوع الحقل</label>
                                    <select 
                                        x-model="field.type"
                                        class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                    >
                                        <option value="text">نص</option>
                                        <option value="email">بريد إلكتروني</option>
                                        <option value="tel">هاتف</option>
                                        <option value="number">رقم</option>
                                        <option value="textarea">نص متعدد الأسطر</option>
                                        <option value="select">قائمة منسدلة</option>
                                    </select>
                                </div>

                                <!-- Required -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">مطلوب</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            x-model="field.required"
                                            class="sr-only peer"
                                        />
                                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                        <span class="ml-3 text-sm text-gray-300">حقل مطلوب</span>
                                    </label>
                                </div>

                                <!-- Label AR -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">التسمية (عربي)</label>
                                    <input 
                                        type="text" 
                                        x-model="field.label_ar"
                                        placeholder="مثال: العنوان"
                                        class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                    />
                                </div>

                                <!-- Placeholder AR -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">النص التوضيحي (عربي)</label>
                                    <input 
                                        type="text" 
                                        x-model="field.placeholder_ar"
                                        placeholder="مثال: أدخل عنوانك"
                                        class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                    />
                                </div>

                                <!-- Options for select -->
                                <template x-if="field.type === 'select'">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-300 mb-2">الخيارات (مفصولة بفاصلة)</label>
                                        <input 
                                            type="text" 
                                            x-model="field.options_text"
                                            @input="field.options = field.options_text.split(',').map(o => o.trim()).filter(o => o)"
                                            placeholder="مثال: الدار البيضاء, الرباط, مراكش"
                                            class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                        />
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Product
                </button>
            </div>
        </form>
    </div>

    <script>
        let sectionCounter = 0;
        let uploadedFiles = [];
        let variationCounter = 0;
        let promotionCounter = 0;

        function togglePromotions(enabled) {
            const promotionsContent = document.getElementById('promotionsContent');
            if (enabled) {
                promotionsContent.classList.remove('hidden');
            } else {
                promotionsContent.classList.add('hidden');
            }
        }

        function updatePromotionsJson() {
            const container = document.getElementById('promotionsContainer');
            const promotions = [];
            const promotionDivs = container.querySelectorAll('[id^="promotion-"]');
            
            promotionDivs.forEach((div) => {
                const minQty = div.querySelector('input[name*="[min_quantity]"]');
                const maxQty = div.querySelector('input[name*="[max_quantity]"]');
                const price = div.querySelector('input[name*="[price]"]');
                
                if (minQty && price) {
                    promotions.push({
                        min_quantity: minQty.value || '',
                        max_quantity: maxQty ? (maxQty.value || null) : null,
                        price: price.value || ''
                    });
                }
            });
            
            document.getElementById('promotions_json').value = JSON.stringify(promotions);
            console.log('Updated promotions JSON:', promotions);
        }

        function addPromotion() {
            const container = document.getElementById('promotionsContainer');
            const noPromotionsMsg = document.getElementById('noPromotionsMessage');
            
            if (noPromotionsMsg) {
                noPromotionsMsg.style.display = 'none';
            }
            
            const promotionId = promotionCounter++;
            
            const promotionDiv = document.createElement('div');
            promotionDiv.className = 'border border-yellow-500/30 rounded-lg p-4 bg-[#0a1628] relative';
            promotionDiv.id = `promotion-${promotionId}`;
            promotionDiv.innerHTML = `
                <button 
                    type="button" 
                    onclick="removePromotion(${promotionId})"
                    class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-300 mb-1">Promotion Label</label>
                    <input 
                        type="text" 
                        name="promotions[${promotionId}][label]" 
                        placeholder="e.g., Shop Now, Buy Now, Order Now (leave empty for default)"
                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        oninput="updatePromotionsJson()"
                    />
                    <p class="text-xs text-gray-400 mt-1">This will replace "Buy" or "اشتري" in your landing page</p>
                </div>
                
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Min Quantity *</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][min_quantity]" 
                            min="1"
                            required
                            placeholder="e.g., 2"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            oninput="updatePromotionsJson()"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Max Quantity</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][max_quantity]" 
                            min="1"
                            placeholder="Leave empty for unlimited"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            oninput="updatePromotionsJson()"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Price per Unit (DHS) *</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][price]" 
                            step="0.01"
                            min="0"
                            required
                            placeholder="e.g., 90.00"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            oninput="updatePromotionsJson()"
                        />
                    </div>
                </div>
                
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded p-2 text-xs text-yellow-300">
                    <strong>Example:</strong> Min: 2, Max: 4, Price: 90.00 → Customers buying 2-4 items pay 90 DHS per item
                </div>
            `;
            
            container.appendChild(promotionDiv);
            updatePromotionsJson();
        }

        function removePromotion(promotionId) {
            const promotionDiv = document.getElementById(`promotion-${promotionId}`);
            if (promotionDiv) {
                promotionDiv.remove();
            }
            
            const container = document.getElementById('promotionsContainer');
            if (container.children.length === 0) {
                document.getElementById('noPromotionsMessage').style.display = 'block';
            }
            updatePromotionsJson();
        }

        function toggleVariations(enabled) {
            const variationsCard = document.getElementById('variationsCard');
            const priceField = document.getElementById('price');
            const comparePriceField = document.getElementById('compare_at_price');
            const stockField = document.getElementById('stock');
            const skuField = document.getElementById('sku');
            const basicPriceFields = [priceField, comparePriceField, stockField, skuField];
            
            if (enabled) {
                variationsCard.classList.remove('hidden');
                // Disable and clear basic price/stock fields when variations are enabled
                basicPriceFields.forEach(field => {
                    if (field) {
                        field.disabled = true;
                        field.classList.add('opacity-50', 'cursor-not-allowed');
                        // Remove required attribute and set value to empty or 0
                        field.removeAttribute('required');
                        if (field.type === 'number') {
                            field.value = '0';
                        }
                    }
                });
            } else {
                variationsCard.classList.add('hidden');
                // Re-enable basic price/stock fields
                basicPriceFields.forEach(field => {
                    if (field) {
                        field.disabled = false;
                        field.classList.remove('opacity-50', 'cursor-not-allowed');
                        // Re-add required attribute for price field
                        if (field.id === 'price') {
                            field.setAttribute('required', 'required');
                        }
                    }
                });
            }
        }

        function addVariation() {
            const container = document.getElementById('variationsContainer');
            const noVariationsMsg = document.getElementById('noVariationsMessage');
            
            if (noVariationsMsg) {
                noVariationsMsg.style.display = 'none';
            }
            
            const variationId = variationCounter++;
            
            const variationDiv = document.createElement('div');
            variationDiv.className = 'border border-blue-500/30 rounded-lg p-4 bg-[#0a1628] relative';
            variationDiv.id = `variation-${variationId}`;
            variationDiv.innerHTML = `
                <button 
                    type="button" 
                    onclick="removeVariation(${variationId})"
                    class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h4 class="text-sm font-semibold text-blue-400 mb-4">Variation ${variationId + 1}</h4>
                
                <div class="space-y-3">
                    <!-- Attributes Section -->
                    <div class="border border-white/10 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-medium text-gray-300">Attributes</label>
                            <button 
                                type="button" 
                                onclick="addAttribute(${variationId})"
                                class="text-xs px-2 py-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded transition"
                            >
                                + Add Attribute
                            </button>
                        </div>
                        <div id="attributes-container-${variationId}" class="space-y-2">
                            <!-- Attributes will be added here -->
                        </div>
                        <p class="text-xs text-gray-500 mt-2">e.g., Color: Red, Size: Large</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">Price (DHS) *</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][price]" 
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">Compare at Price</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][compare_at_price]" 
                                step="0.01"
                                min="0"
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00"
                            />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">Stock *</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][stock]" 
                                min="0"
                                value="0"
                                required
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">SKU</label>
                            <input 
                                type="text" 
                                name="variations[${variationId}][sku]" 
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="SKU-${variationId + 1}"
                            />
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                name="variations[${variationId}][is_default]" 
                                value="1"
                                class="rounded bg-[#0f1c2e] border-white/10 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-xs text-gray-300">Default variation</span>
                        </label>
                        
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                name="variations[${variationId}][is_active]" 
                                value="1"
                                checked
                                class="rounded bg-[#0f1c2e] border-white/10 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-xs text-gray-300">Active</span>
                        </label>
                    </div>
                </div>
            `;
            
            container.appendChild(variationDiv);
            
            // Add first attribute by default
            addAttribute(variationId);
        }

        function addAttribute(variationId) {
            const container = document.getElementById(`attributes-container-${variationId}`);
            const attributeId = container.children.length;
            
            const attributeDiv = document.createElement('div');
            attributeDiv.className = 'flex gap-2';
            attributeDiv.innerHTML = `
                <input 
                    type="text" 
                    name="variations[${variationId}][attributes][${attributeId}][name]" 
                    placeholder="Attribute (e.g., Color)"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                />
                <input 
                    type="text" 
                    name="variations[${variationId}][attributes][${attributeId}][value]" 
                    placeholder="Value (e.g., Red)"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                />
                <button 
                    type="button" 
                    onclick="this.parentElement.remove()"
                    class="text-red-400 hover:text-red-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            
            container.appendChild(attributeDiv);
        }

        function removeVariation(variationId) {
            const variationDiv = document.getElementById(`variation-${variationId}`);
            if (variationDiv) {
                variationDiv.remove();
            }
            
            // Show no variations message if no variations left
            const container = document.getElementById('variationsContainer');
            if (container.children.length === 0) {
                document.getElementById('noVariationsMessage').style.display = 'block';
            }
        }

        function previewImages(event) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('landingSectionsContainer');
            preview.innerHTML = '';
            container.innerHTML = '';
            
            const files = event.target.files;
            uploadedFiles = Array.from(files);
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs">Image ${i + 1}${i === 0 ? ' (Hero)' : ''}</span>
                        </div>
                    `;
                    preview.appendChild(div);
                    
                    // Auto-create landing section for each image (except first one which is hero)
                    if (i > 0) {
                        createAutoLandingSection(i, e.target.result);
                    }
                }
                
                reader.readAsDataURL(file);
            }
            
            sectionCounter = files.length - 1; // Exclude hero image from counter
        }

        function createAutoLandingSection(imageIndex, imageDataUrl) {
            const container = document.getElementById('landingSectionsContainer');
            const noSectionsMsg = document.getElementById('noSectionsMessage');
            if (noSectionsMsg) {
                noSectionsMsg.style.display = 'none';
            }
            
            const sectionId = imageIndex - 1; // 0-based for sections (since we skip hero)
            
            const sectionDiv = document.createElement('div');
            sectionDiv.className = 'border border-cyan-500/30 rounded-lg p-4 bg-[#0a1628]';
            sectionDiv.id = `section-${sectionId}`;
            sectionDiv.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-cyan-400">Section ${sectionId + 1} - Image ${imageIndex + 1}</h4>
                    <span class="text-xs text-gray-500 px-2 py-1 bg-purple-900/30 rounded">AI will generate descriptions</span>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="mb-2">
                            <img src="${imageDataUrl}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                        </div>
                        <input type="hidden" name="landing_sections[${sectionId}][auto_generated]" value="1">
                        <input type="hidden" name="landing_sections[${sectionId}][image_index]" value="${imageIndex}">
                    </div>
                    <div class="bg-purple-500/10 border border-purple-500/30 rounded p-3 text-center">
                        <svg class="w-8 h-8 text-purple-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <p class="text-xs text-purple-300">AI will generate titles and descriptions in French, English, and Arabic for this image when you enable AI Landing Page Generation</p>
                    </div>
                </div>
            `;
            
            container.appendChild(sectionDiv);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const generateToggle = document.getElementById('generate_landing_page');
            const aiNotice = document.getElementById('aiNotice');
            
            generateToggle.addEventListener('change', function() {
                if (this.checked) {
                    aiNotice.classList.remove('hidden');
                } else {
                    aiNotice.classList.add('hidden');
                }
            });

            // Add form validation for promotions
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const hasPromotionsCheckbox = document.getElementById('has_promotions');
                const promotionsContainer = document.getElementById('promotionsContainer');
                
                // Collect promotions data and store in hidden JSON field as backup
                const promotions = [];
                const promotionDivs = promotionsContainer.querySelectorAll('[id^="promotion-"]');
                
                console.log('Found promotion divs:', promotionDivs.length);
                
                promotionDivs.forEach((div, index) => {
                    const minQty = div.querySelector('input[name*="[min_quantity]"]');
                    const maxQty = div.querySelector('input[name*="[max_quantity]"]');
                    const price = div.querySelector('input[name*="[price]"]');
                    
                    if (minQty && price && minQty.value && price.value) {
                        promotions.push({
                            min_quantity: minQty.value,
                            max_quantity: maxQty ? maxQty.value : null,
                            price: price.value
                        });
                    }
                });
                
                console.log('Collected promotions:', promotions);
                document.getElementById('promotions_json').value = JSON.stringify(promotions);
                
                if (hasPromotionsCheckbox.checked && promotions.length === 0) {
                    e.preventDefault();
                    
                    // Show error notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3';
                    notification.innerHTML = `
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Promotions enabled but no tiers added!</p>
                            <p class="text-sm">Please click "Add Tier" to add at least one pricing tier, or uncheck "Enable Quantity-Based Pricing".</p>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Scroll to promotions section
                    document.getElementById('promotionsCard').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Remove notification after 5 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 5000);
                    
                    return false;
                }
            });

            const aiGenerateBtn = document.getElementById('aiGenerateBtn');
            const aiGenerateBtnText = document.getElementById('aiGenerateBtnText');
            
            aiGenerateBtn.addEventListener('click', function() {
                const nameField = document.getElementById('name');
                const descriptionField = document.getElementById('description');
                const categoryField = document.getElementById('category_id');
                const priceField = document.getElementById('price');
                
                if (!nameField.value || !priceField.value) {
                    alert('Please fill in at least the product name and price before generating the landing page.');
                    return;
                }
                
                const generateToggle = document.getElementById('generate_landing_page');
                generateToggle.checked = true;
                aiNotice.classList.remove('hidden');
                
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3';
                notification.innerHTML = `
                    <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <div>
                        <p class="font-semibold">AI Landing Page Enabled</p>
                        <p class="text-sm">Your landing page will be generated when you create the product</p>
                    </div>
                `;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 4000);
            });
        });
    </script>

    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <style>
        #description-editor {
            min-height: 200px;
            background: white;
            border-radius: 0 0 0.5rem 0.5rem;
        }
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            background: white;
            border-color: rgba(255,255,255,0.1) !important;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: rgba(255,255,255,0.1) !important;
            min-height: 180px;
            font-size: 16px;
        }
        .ql-editor {
            min-height: 180px;
            color: #1f2937;
        }
        .ql-editor.ql-blank::before {
            color: #9ca3af;
            font-style: normal;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const descriptionTextarea = document.getElementById('description');
            const editorElement = document.getElementById('description-editor');
            
            if (editorElement && descriptionTextarea) {
                const uploadUrl = '{{ route("app.quill.upload-image") }}';
                const csrfToken = '{{ csrf_token() }}';
                
                function imageHandler() {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();
                    
                    const quill = this.quill;
                    
                    input.onchange = function() {
                        const file = input.files[0];
                        if (!file) return;
                        
                        const formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', csrfToken);
                        
                        const range = quill.getSelection(true);
                        quill.insertText(range.index, 'Uploading...', { italic: true });
                        
                        fetch(uploadUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            body: formData
                        })
                        .then(r => r.json())
                        .then(data => {
                            quill.deleteText(range.index, 'Uploading...'.length);
                            if (data.success && data.url) {
                                quill.insertEmbed(range.index, 'image', data.url);
                                quill.setSelection(range.index + 1);
                            } else {
                                alert('Image upload failed');
                            }
                        })
                        .catch(err => {
                            quill.deleteText(range.index, 'Uploading...'.length);
                            console.error(err);
                            alert('Image upload failed');
                        });
                    };
                }
                
                const quill = new Quill('#description-editor', {
                    theme: 'snow',
                    placeholder: 'Enter product description...',
                    modules: {
                        toolbar: {
                            container: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'align': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link', 'image', 'video'],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                ['blockquote', 'code-block'],
                                ['clean']
                            ],
                            handlers: { 'image': imageHandler }
                        }
                    }
                });

                // Set initial value
                if (descriptionTextarea.value) {
                    quill.root.innerHTML = descriptionTextarea.value;
                }

                // Sync editor content to textarea on form submit
                const form = descriptionTextarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        descriptionTextarea.value = quill.root.innerHTML;
                    });
                }

                // Also sync on change
                quill.on('text-change', function() {
                    descriptionTextarea.value = quill.root.innerHTML;
                });
            }
        });
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formFieldsManager', () => ({
                fields: [
                    {
                        id: 'name',
                        type: 'text',
                        label_ar: 'الاسم',
                        placeholder_ar: 'الاسم',
                        required: true,
                        is_default: true,
                        options: [],
                        options_text: ''
                    },
                    {
                        id: 'phone',
                        type: 'tel',
                        label_ar: 'الهاتف',
                        placeholder_ar: 'الهاتف',
                        required: true,
                        is_default: true,
                        options: [],
                        options_text: ''
                    },
                    {
                        id: 'note',
                        type: 'textarea',
                        label_ar: 'ملاحظات',
                        placeholder_ar: 'ملاحظات',
                        required: false,
                        is_default: true,
                        options: [],
                        options_text: ''
                    }
                ],
                
                init() {
                    // Ensure each field has an options_text property for select fields
                    this.fields = this.fields.map(field => {
                        if (field.type === 'select' && field.options) {
                            field.options_text = field.options.join(', ');
                        }
                        return field;
                    });
                },
                
                get formFieldsJson() {
                    return JSON.stringify(this.fields);
                },
                
                addField() {
                    this.fields.push({
                        id: 'field_' + Date.now(),
                        type: 'text',
                        label_ar: '',
                        placeholder_ar: '',
                        required: false,
                        options: [],
                        options_text: ''
                    });
                },
                
                removeField(index) {
                    if (confirm('هل أنت متأكد من أنك تريد إزالة هذا الحقل؟')) {
                        this.fields.splice(index, 1);
                    }
                }
            }));
        });
    </script>
@endsection
