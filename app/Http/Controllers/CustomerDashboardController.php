<?php

namespace App\Http\Controllers;

use App\Models\AiApiSetting;
use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiLandingPageService;
use App\Jobs\GenerateProductLandingPageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class CustomerDashboardController extends Controller
{
    protected function getActiveStoreId()
    {
        return session('active_store_id');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $stats = [
            'conversations' => Conversation::whereHas('whatsappProfile', function($q) use ($user, $storeId) {
                $q->where('user_id', $user->id)
                  ->when($storeId, function($q) use ($storeId) {
                      $q->where('store_id', $storeId);
                  });
            })->count(),
            'messages' => Message::whereHas('whatsappProfile', function($q) use ($user, $storeId) {
                $q->where('user_id', $user->id)
                  ->when($storeId, function($q) use ($storeId) {
                      $q->where('store_id', $storeId);
                  });
            })->count(),
            'orders' => \App\Models\ProductLead::where('user_id', $user->id)
                ->when($storeId, function($q) use ($storeId) {
                    $q->whereHas('product', function($q) use ($storeId) {
                        $q->where('store_id', $storeId);
                    });
                })
                ->count(),
            'products' => \App\Models\Product::where('user_id', $user->id)
                ->when($storeId, function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->count(),
            'categories' => \App\Models\Category::when($storeId, function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->count(),
            'active_profiles' => WhatsappProfile::where('user_id', $user->id)
                ->when($storeId, function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->where('status', 'connected')
                ->count(),
            'ai_tokens' => 0,
            'sales_percentage' => 0,
        ];
        
        $recent_conversations = Conversation::whereHas('whatsappProfile', function($q) use ($user, $storeId) {
            $q->where('user_id', $user->id)
              ->when($storeId, function($q) use ($storeId) {
                  $q->where('store_id', $storeId);
              });
        })
        ->with(['whatsappProfile'])
        ->latest('last_message_at')
        ->take(10)
        ->get();
        
        $whatsapp_profiles = WhatsappProfile::where('user_id', $user->id)
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->get();
        
        return view('customer.dashboard', compact('stats', 'recent_conversations', 'whatsapp_profiles'));
    }
    
    public function whatsapp()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $profiles = WhatsappProfile::where('user_id', $user->id)
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->get();
        
        return view('customer.whatsapp', compact('profiles'));
    }
    
    public function conversations()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $conversations = Conversation::whereHas('whatsappProfile', function($q) use ($user, $storeId) {
            $q->where('user_id', $user->id)
              ->when($storeId, function($q) use ($storeId) {
                  $q->where('store_id', $storeId);
              });
        })
        ->with(['whatsappProfile'])
        ->latest('last_message_at')
        ->paginate(20);
        
        return view('customer.conversations', compact('conversations'));
    }
    
    public function conversationDetail($id)
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $conversation = Conversation::whereHas('whatsappProfile', function($q) use ($user, $storeId) {
            $q->where('user_id', $user->id)
              ->when($storeId, function($q) use ($storeId) {
                  $q->where('store_id', $storeId);
              });
        })
        ->with(['messages', 'whatsappProfile'])
        ->findOrFail($id);
        
        return view('customer.conversation-detail', compact('conversation'));
    }
    
    public function aiSettings()
    {
        $user = auth()->user();
        $aiSetting = AiApiSetting::where('user_id', $user->id)->first();

        return view('workspaces.ai-settings', compact('aiSetting'));
    }

    public function saveOpenAiSettings(Request $request)
    {
        $validated = $request->validate([
            'openai_api_key' => 'nullable|string|max:2048',
            'openai_model' => 'required|string|max:128',
            'clear_openai_key' => 'nullable|boolean',
            'auto_reply_enabled' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        
        $setting = AiApiSetting::firstOrNew(['user_id' => $user->id]);
        $setting->openai_model = $validated['openai_model'];
        $setting->auto_reply_enabled = $request->boolean('auto_reply_enabled');

        if ($request->boolean('clear_openai_key')) {
            $setting->openai_api_key_encrypted = null;
        } elseif (! empty($validated['openai_api_key'])) {
            $setting->openai_api_key_encrypted = Crypt::encryptString(trim($validated['openai_api_key']));
        }

        $setting->save();

        return redirect()
            ->route('workspaces.ai-settings')
            ->withFragment('openai-connect')
            ->with('success', 'Paramètres OpenAI enregistrés.');
    }

    public function testOpenAiConnection(Request $request)
    {
        $user = auth()->user();
        $setting = AiApiSetting::where('user_id', $user->id)->first();

        if (! $setting || empty($setting->openai_api_key_encrypted)) {
            return redirect()
                ->route('workspaces.ai-settings')
                ->withFragment('openai-connect')
                ->with('openai_error', 'Enregistrez d\'abord une clé API OpenAI.');
        }

        try {
            $apiKey = Crypt::decryptString($setting->openai_api_key_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('workspaces.ai-settings')
                ->withFragment('openai-connect')
                ->with('openai_error', 'Impossible de lire la clé enregistrée. Saisissez-la à nouveau et enregistrez.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(20)
            ->acceptJson()
            ->get('https://api.openai.com/v1/models');

        if (! $response->successful()) {
            $message = data_get($response->json(), 'error.message') ?: $response->body();

            return redirect()
                ->route('workspaces.ai-settings')
                ->withFragment('openai-connect')
                ->with('openai_error', is_string($message) ? $message : 'La requête vers OpenAI a échoué.');
        }

        return redirect()
            ->route('workspaces.ai-settings')
            ->withFragment('openai-connect')
            ->with('openai_success', 'Connexion OpenAI réussie.');
    }

    public function saveAnthropicSettings(Request $request)
    {
        $validated = $request->validate([
            'anthropic_api_key' => 'nullable|string|max:2048',
            'anthropic_model' => 'required|string|max:128',
            'clear_anthropic_key' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        
        $setting = AiApiSetting::firstOrNew(['user_id' => $user->id]);
        $setting->anthropic_model = $validated['anthropic_model'];

        if ($request->boolean('clear_anthropic_key')) {
            $setting->anthropic_api_key_encrypted = null;
        } elseif (! empty($validated['anthropic_api_key'])) {
            $setting->anthropic_api_key_encrypted = Crypt::encryptString(trim($validated['anthropic_api_key']));
        }

        $setting->save();

        return redirect()
            ->route('workspaces.ai-settings')
            ->withFragment('anthropic-connect')
            ->with('success', 'Paramètres Anthropic enregistrés.');
    }

    public function testAnthropicConnection(Request $request)
    {
        $user = auth()->user();
        $setting = AiApiSetting::where('user_id', $user->id)->first();

        if (! $setting || empty($setting->anthropic_api_key_encrypted)) {
            return redirect()
                ->route('workspaces.ai-settings')
                ->withFragment('anthropic-connect')
                ->with('anthropic_error', 'Enregistrez d\'abord une clé API Anthropic.');
        }

        try {
            $apiKey = Crypt::decryptString($setting->anthropic_api_key_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('workspaces.ai-settings')
                ->withFragment('anthropic-connect')
                ->with('anthropic_error', 'Impossible de lire la clé enregistrée. Saisissez-la à nouveau et enregistrez.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(20)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $setting->anthropic_model ?: 'claude-3-5-sonnet-20241022',
                'max_tokens' => 10,
                'messages' => [
                    ['role' => 'user', 'content' => 'Hello']
                ]
            ]);

        if (! $response->successful()) {
            $message = data_get($response->json(), 'error.message') ?: $response->body();

            return redirect()
                ->route('workspaces.ai-settings')
                ->withFragment('anthropic-connect')
                ->with('anthropic_error', is_string($message) ? $message : 'La requête vers Anthropic a échoué.');
        }

        return redirect()
            ->route('workspaces.ai-settings')
            ->withFragment('anthropic-connect')
            ->with('anthropic_success', 'Connexion Anthropic réussie.');
    }
    
    public function orders()
    {
        return view('customer.orders');
    }
    
    public function products()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $query = \App\Models\Product::where('user_id', $user->id);
        
        $store = null;
        if ($storeId) {
            $query->where('store_id', $storeId);
            $store = \App\Models\Store::find($storeId);
        }
        
        $products = $query->latest()->paginate(10);
        
        return view('customer.products', compact('products', 'store'));
    }
    
    public function productsSelectTheme()
    {
        return view('customer.products-select-theme');
    }

    public function productsCreate(Request $request)
    {
        $theme = $request->query('theme', 'theme1');
        
        if (!in_array($theme, ['theme1', 'theme2'])) {
            return redirect()->route('app.products.select-theme');
        }
        
        $storeId = $this->getActiveStoreId();
        
        $query = \App\Models\Category::where('is_active', true);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $categories = $query->orderBy('order')->orderBy('name')->get();
        
        $viewName = $theme === 'theme2' ? 'customer.products-create-theme2' : 'customer.products-create';
        
        return view($viewName, compact('categories', 'theme'));
    }
    
    public function productsStore(Request $request)
    {
        // Check if has_variations is enabled
        $hasVariations = $request->boolean('has_variations');
        $hasPromotions = $request->boolean('has_promotions');
        
        // Debug logging
        \Log::info('Product creation - has_promotions: ' . ($hasPromotions ? 'true' : 'false'));
        \Log::info('Product creation - promotions data: ' . json_encode($request->input('promotions')));
        \Log::info('Product creation - promotions_json: ' . $request->input('promotions_json'));
        \Log::info('Product creation - all request keys: ' . json_encode(array_keys($request->all())));
        
        // Check for promotions - first try the regular array, then fallback to JSON field
        $promotionsData = $request->input('promotions');
        
        // If regular promotions array is empty but we have JSON data, use that instead
        if (empty($promotionsData) && $request->has('promotions_json')) {
            $jsonData = json_decode($request->input('promotions_json'), true);
            if (!empty($jsonData) && is_array($jsonData)) {
                $promotionsData = [];
                foreach ($jsonData as $index => $promo) {
                    $promotionsData[$index] = $promo;
                }
                // Merge back into request so validation works
                $request->merge(['promotions' => $promotionsData]);
                \Log::info('Product creation - using promotions from JSON fallback: ' . json_encode($promotionsData));
            }
        }
        
        // Only require promotions array if has_promotions is true AND promotions were submitted
        $promotionsSubmitted = $hasPromotions && !empty($promotionsData);
        
        \Log::info('Product creation - promotionsSubmitted: ' . ($promotionsSubmitted ? 'true' : 'false'));
        \Log::info('Product creation - promotions count: ' . ($promotionsSubmitted ? count($promotionsData) : 0));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme' => 'nullable|string|in:theme1,theme2',
            'theme_data' => 'nullable|array',
            'price' => $hasVariations ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'has_variations' => 'nullable|boolean',
            'has_promotions' => 'nullable|boolean',
            'variations' => $hasVariations ? 'required|array|min:1' : 'nullable|array',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.compare_at_price' => 'nullable|numeric|min:0',
            'variations.*.stock' => 'required_with:variations|integer|min:0',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'promotions' => 'nullable|array',
            'promotions.*.min_quantity' => 'required_with:promotions|integer|min:1',
            'promotions.*.max_quantity' => 'nullable|integer|min:1',
            'promotions.*.price' => 'required_with:promotions|numeric|min:0',
            'landing_sections' => 'nullable|array',
            'landing_sections.*.image' => 'nullable|image|max:2048',
            'landing_sections.*.title_fr' => 'nullable|string|max:255',
            'landing_sections.*.description_fr' => 'nullable|string',
            'landing_sections.*.title_en' => 'nullable|string|max:255',
            'landing_sections.*.description_en' => 'nullable|string',
            'landing_sections.*.title_ar' => 'nullable|string|max:255',
            'landing_sections.*.description_ar' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['store_id'] = $this->getActiveStoreId();
        $validated['theme'] = $validated['theme'] ?? 'theme1';
        $validated['theme_data'] = $request->input('theme_data');
        $validated['slug'] = \Str::slug($validated['name']) . '-' . time();
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['has_variations'] = $hasVariations;
        $validated['has_promotions'] = $hasPromotions;

        // If has variations, stock/sku/price will be managed by variations
        if ($hasVariations) {
            $validated['stock'] = 0;
            $validated['sku'] = null;
            $validated['price'] = 0; // Set to 0 as it won't be used
        } else {
            $validated['stock'] = $validated['stock'] ?? 0;
        }
        
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }
        
        if ($request->has('landing_sections')) {
            $landingSections = [];
            foreach ($request->input('landing_sections', []) as $index => $section) {
                if (!empty($section['auto_generated'])) {
                    $imageIndex = $section['image_index'] ?? 0;
                    $sectionData = [
                        'image_index' => $imageIndex,
                        'pending_ai' => true,
                    ];
                    $landingSections[] = $sectionData;
                } else {
                    $sectionData = [
                        'title_fr' => $section['title_fr'] ?? '',
                        'description_fr' => $section['description_fr'] ?? '',
                        'title_en' => $section['title_en'] ?? '',
                        'description_en' => $section['description_en'] ?? '',
                        'title_ar' => $section['title_ar'] ?? '',
                        'description_ar' => $section['description_ar'] ?? '',
                    ];
                    
                    if ($request->hasFile("landing_sections.{$index}.image")) {
                        $sectionData['image'] = $request->file("landing_sections.{$index}.image")->store('products/landing-sections', 'public');
                    }
                    
                    if (!empty($sectionData['title_fr']) || !empty($sectionData['description_fr'])) {
                        $landingSections[] = $sectionData;
                    }
                }
            }
            
            if (!empty($landingSections)) {
                $validated['landing_page_sections'] = $landingSections;
            }
        }
        
        if ($request->boolean('generate_landing_page')) {
            $validated['landing_page_status'] = 'pending';
        } else {
            $validated['landing_page_status'] = 'none';
        }

        if ($request->boolean('generate_product_images')) {
            $validated['ai_images_status'] = 'pending';
        } else {
            $validated['ai_images_status'] = 'none';
        }
        
        $product = \App\Models\Product::create($validated);
        
        // Handle product variations
        if ($hasVariations && $request->has('variations')) {
            $hasDefault = false;
            
            foreach ($request->input('variations', []) as $variationData) {
                $attributes = [];
                if (!empty($variationData['attributes'])) {
                    foreach ($variationData['attributes'] as $attr) {
                        if (!empty($attr['name']) && !empty($attr['value'])) {
                            $attributes[$attr['name']] = $attr['value'];
                        }
                    }
                }
                
                $isDefault = isset($variationData['is_default']) && $variationData['is_default'];
                
                // Only one variation can be default
                if ($isDefault && $hasDefault) {
                    $isDefault = false;
                } elseif ($isDefault) {
                    $hasDefault = true;
                }
                
                \App\Models\ProductVariation::create([
                    'product_id' => $product->id,
                    'sku' => $variationData['sku'] ?? null,
                    'price' => $variationData['price'],
                    'compare_at_price' => $variationData['compare_at_price'] ?? null,
                    'stock' => $variationData['stock'] ?? 0,
                    'attributes' => $attributes,
                    'is_active' => isset($variationData['is_active']) && $variationData['is_active'],
                    'is_default' => $isDefault,
                ]);
            }
            
            // If no default was set, make the first variation default
            if (!$hasDefault) {
                $firstVariation = $product->variations()->first();
                if ($firstVariation) {
                    $firstVariation->update(['is_default' => true]);
                }
            }
        }

        // Handle product promotions
        if ($promotionsSubmitted) {
            foreach ($request->input('promotions', []) as $promotionData) {
                \App\Models\ProductPromotion::create([
                    'product_id' => $product->id,
                    'min_quantity' => $promotionData['min_quantity'],
                    'max_quantity' => $promotionData['max_quantity'] ?? null,
                    'price' => $promotionData['price'],
                    'is_active' => true,
                ]);
            }
        }

        $jobsDispatched = [];
        
        if ($request->boolean('generate_landing_page')) {
            GenerateProductLandingPageJob::dispatch($product, auth()->id());
            $jobsDispatched[] = 'AI landing page';
        }

        if ($request->boolean('generate_product_images')) {
            \App\Jobs\GenerateProductImagesJob::dispatch($product, auth()->id(), 5);
            $jobsDispatched[] = 'AI product images';
        }
        
        if (!empty($jobsDispatched)) {
            $message = 'Product created successfully! ' . implode(' and ', $jobsDispatched) . ' generation started in the background.';
            return redirect()->route('app.products')->with('success', $message);
        }
        
        return redirect()->route('app.products')->with('success', 'Product created successfully!');
    }
    
    public function productsEdit($id)
    {
        $storeId = $this->getActiveStoreId();

        $product = \App\Models\Product::with(['variations', 'promotions'])
            ->where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);

        $categories = \App\Models\Category::where('is_active', true)
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('customer.products-edit', compact('product', 'categories'));
    }
    
    public function productsUpdate(Request $request, $id)
    {
        $storeId = $this->getActiveStoreId();

        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);

        // Check if has_variations is enabled
        $hasVariations = $request->boolean('has_variations');
        $hasPromotions = $request->boolean('has_promotions');
        
        // Debug logging
        \Log::info('Product update - ID: ' . $id . ' - has_promotions: ' . ($hasPromotions ? 'true' : 'false'));
        \Log::info('Product update - ID: ' . $id . ' - promotions data: ' . json_encode($request->input('promotions')));
        \Log::info('Product update - ID: ' . $id . ' - promotions_json: ' . $request->input('promotions_json'));
        
        // Check for promotions - first try the regular array, then fallback to JSON field
        $promotionsData = $request->input('promotions');
        
        // If regular promotions array is empty but we have JSON data, use that instead
        if (empty($promotionsData) && $request->has('promotions_json')) {
            $jsonData = json_decode($request->input('promotions_json'), true);
            if (!empty($jsonData) && is_array($jsonData)) {
                $promotionsData = [];
                foreach ($jsonData as $index => $promo) {
                    $promotionsData[$index] = $promo;
                }
                // Merge back into request so validation works
                $request->merge(['promotions' => $promotionsData]);
                \Log::info('Product update - ID: ' . $id . ' - using promotions from JSON fallback: ' . json_encode($promotionsData));
            }
        }
        
        // Only require promotions array if has_promotions is true AND promotions were submitted
        $promotionsSubmitted = $hasPromotions && !empty($promotionsData);
        
        \Log::info('Product update - ID: ' . $id . ' - promotionsSubmitted: ' . ($promotionsSubmitted ? 'true' : 'false'));
        \Log::info('Product update - ID: ' . $id . ' - promotions count: ' . ($promotionsSubmitted ? count($promotionsData) : 0));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => $hasVariations ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'has_variations' => 'nullable|boolean',
            'has_promotions' => 'nullable|boolean',
            'variations' => $hasVariations ? 'required|array|min:1' : 'nullable|array',
            'variations.*.id' => 'nullable|exists:product_variations,id',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.compare_at_price' => 'nullable|numeric|min:0',
            'variations.*.stock' => 'required_with:variations|integer|min:0',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'promotions' => 'nullable|array',
            'promotions.*.id' => 'nullable|exists:product_promotions,id',
            'promotions.*.min_quantity' => 'required_with:promotions|integer|min:1',
            'promotions.*.max_quantity' => 'nullable|integer|min:1',
            'promotions.*.price' => 'required_with:promotions|numeric|min:0',
            'landing_sections' => 'nullable|array',
            'landing_sections.*.image' => 'nullable|image|max:2048',
            'landing_sections.*.existing_image' => 'nullable|string',
            'landing_sections.*.title_fr' => 'nullable|string|max:255',
            'landing_sections.*.description_fr' => 'nullable|string',
            'landing_sections.*.title_en' => 'nullable|string|max:255',
            'landing_sections.*.description_en' => 'nullable|string',
            'landing_sections.*.title_ar' => 'nullable|string|max:255',
            'landing_sections.*.description_ar' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['has_variations'] = $hasVariations;
        $validated['has_promotions'] = $hasPromotions;

        // If has variations, stock/sku/price will be managed by variations
        if ($hasVariations) {
            $validated['stock'] = 0;
            $validated['sku'] = null;
            $validated['price'] = 0; // Set to 0 as it won't be used
        } else {
            $validated['stock'] = $validated['stock'] ?? 0;
        }
        
        if ($request->hasFile('images')) {
            $imagePaths = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }
        
        if ($request->has('landing_sections')) {
            $landingSections = [];
            foreach ($request->input('landing_sections', []) as $index => $section) {
                $sectionData = [
                    'title_fr' => $section['title_fr'] ?? '',
                    'description_fr' => $section['description_fr'] ?? '',
                    'title_en' => $section['title_en'] ?? '',
                    'description_en' => $section['description_en'] ?? '',
                    'title_ar' => $section['title_ar'] ?? '',
                    'description_ar' => $section['description_ar'] ?? '',
                ];
                
                if ($request->hasFile("landing_sections.{$index}.image")) {
                    $sectionData['image'] = $request->file("landing_sections.{$index}.image")->store('products/landing-sections', 'public');
                } elseif (!empty($section['existing_image'])) {
                    $sectionData['image'] = $section['existing_image'];
                }
                
                if (!empty($sectionData['title_fr']) || !empty($sectionData['description_fr'])) {
                    $landingSections[] = $sectionData;
                }
            }
            
            $validated['landing_page_sections'] = $landingSections;
        }
        
        if ($request->has('delete_images')) {
            $currentImages = $product->images ?? [];
            $deleteImages = $request->input('delete_images', []);
            $validated['images'] = array_values(array_diff($currentImages, $deleteImages));
            
            foreach ($deleteImages as $image) {
                \Storage::disk('public')->delete($image);
            }
        }
        
        $product->update($validated);
        
        // Handle product variations
        if ($hasVariations && $request->has('variations')) {
            $submittedIds = [];
            $hasDefault = false;
            
            foreach ($request->input('variations', []) as $variationData) {
                $attributes = [];
                if (!empty($variationData['attributes'])) {
                    foreach ($variationData['attributes'] as $attr) {
                        if (!empty($attr['name']) && !empty($attr['value'])) {
                            $attributes[$attr['name']] = $attr['value'];
                        }
                    }
                }
                
                $isDefault = isset($variationData['is_default']) && $variationData['is_default'];
                
                if ($isDefault && $hasDefault) {
                    $isDefault = false;
                } elseif ($isDefault) {
                    $hasDefault = true;
                }
                
                $variationParams = [
                    'product_id' => $product->id,
                    'sku' => $variationData['sku'] ?? null,
                    'price' => $variationData['price'],
                    'compare_at_price' => $variationData['compare_at_price'] ?? null,
                    'stock' => $variationData['stock'] ?? 0,
                    'attributes' => $attributes,
                    'is_active' => isset($variationData['is_active']) && $variationData['is_active'],
                    'is_default' => $isDefault,
                ];
                
                if (!empty($variationData['id'])) {
                    $variation = \App\Models\ProductVariation::where('id', $variationData['id'])
                        ->where('product_id', $product->id)
                        ->first();
                    
                    if ($variation) {
                        $variation->update($variationParams);
                        $submittedIds[] = $variation->id;
                    }
                } else {
                    $variation = \App\Models\ProductVariation::create($variationParams);
                    $submittedIds[] = $variation->id;
                }
            }
            
            // Delete variations that were not submitted
            $product->variations()->whereNotIn('id', $submittedIds)->delete();
            
            // If no default was set, make the first variation default
            if (!$hasDefault) {
                $firstVariation = $product->variations()->first();
                if ($firstVariation) {
                    $firstVariation->update(['is_default' => true]);
                }
            }
        } else {
            // If variations are disabled, delete all existing variations
            $product->variations()->delete();
        }

        // Handle product promotions
        if ($hasPromotions) {
            if ($promotionsSubmitted) {
                $submittedPromotionIds = [];

                foreach ($request->input('promotions', []) as $promotionData) {
                    $promotionParams = [
                        'product_id' => $product->id,
                        'min_quantity' => $promotionData['min_quantity'],
                        'max_quantity' => $promotionData['max_quantity'] ?? null,
                        'price' => $promotionData['price'],
                        'is_active' => true,
                    ];

                    if (!empty($promotionData['id'])) {
                        $promotion = \App\Models\ProductPromotion::where('id', $promotionData['id'])
                            ->where('product_id', $product->id)
                            ->first();

                        if ($promotion) {
                            $promotion->update($promotionParams);
                            $submittedPromotionIds[] = $promotion->id;
                        }
                    } else {
                        $promotion = \App\Models\ProductPromotion::create($promotionParams);
                        $submittedPromotionIds[] = $promotion->id;
                    }
                }

                // Delete promotions that were not submitted
                $product->promotions()->whereNotIn('id', $submittedPromotionIds)->delete();
            }
            // If has_promotions is true but no promotions submitted, keep existing promotions
        } else {
            // Only delete promotions if has_promotions checkbox is explicitly unchecked
            $product->promotions()->delete();
        }

        return redirect()->route('app.products')->with('success', 'Product updated successfully!');
    }
    
    public function productsDestroy($id)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        // Delete product images from storage
        if ($product->images) {
            foreach ($product->images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }
        
        // Delete AI generated images
        if ($product->ai_generated_images) {
            foreach ($product->ai_generated_images as $image) {
                $path = str_replace('/storage/', '', $image);
                \Storage::disk('public')->delete($path);
            }
        }
        
        $product->delete();
        
        return redirect()->route('app.products')->with('success', 'Product deleted successfully!');
    }

    public function generateLandingPage($productId)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($productId);
        
        $product->update(['landing_page_status' => 'pending']);
        
        GenerateProductLandingPageJob::dispatch($product, auth()->id());
        
        return response()->json([
            'success' => true,
            'message' => 'Landing page generation started! It will be ready in a few moments.'
        ]);
    }

    public function generateProductImages($productId)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($productId);
        
        $product->update([
            'ai_images_status' => 'pending',
            'ai_images_progress' => 0,
            'ai_images_generated' => 0,
        ]);
        
        \App\Jobs\GenerateProductImagesJob::dispatch($product, auth()->id(), 5);
        
        return response()->json([
            'success' => true,
            'message' => 'AI image generation started! This may take a few minutes.'
        ]);
    }

    public function checkImageGenerationProgress($productId)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($productId);
        
        return response()->json([
            'status' => $product->ai_images_status,
            'progress' => $product->ai_images_progress,
            'generated' => $product->ai_images_generated,
            'total' => $product->ai_images_total,
            'images' => $product->ai_generated_images ?? [],
        ]);
    }

    public function landingPageBuilder($id)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        $store = null;
        if ($storeId) {
            $store = \App\Models\Store::find($storeId);
        }
        
        return view('customer.products-landing-builder', compact('product', 'store'));
    }

    public function saveLandingPageBuilder(Request $request, $id)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        $validated = $request->validate([
            'sections' => 'nullable|array',
            'sections.*.title_fr' => 'nullable|string|max:255',
            'sections.*.description_fr' => 'nullable|string',
            'sections.*.title_en' => 'nullable|string|max:255',
            'sections.*.description_en' => 'nullable|string',
            'sections.*.title_ar' => 'nullable|string|max:255',
            'sections.*.description_ar' => 'nullable|string',
            'sections.*.image' => 'nullable|string',
            'page_data' => 'nullable|array',
            'page_data.fr' => 'nullable|array',
            'page_data.en' => 'nullable|array',
            'page_data.ar' => 'nullable|array',
            'show_product_sections' => 'nullable|boolean',
        ]);
        
        // Add show_product_sections to each language's page data
        $pageDataFr = $validated['page_data']['fr'] ?? [];
        $pageDataEn = $validated['page_data']['en'] ?? [];
        $pageDataAr = $validated['page_data']['ar'] ?? [];
        
        $showSections = $validated['show_product_sections'] ?? true;
        $pageDataFr['show_product_sections'] = $showSections;
        $pageDataEn['show_product_sections'] = $showSections;
        $pageDataAr['show_product_sections'] = $showSections;
        
        $product->update([
            'landing_page_sections' => $validated['sections'] ?? [],
            'landing_page_fr' => $pageDataFr,
            'landing_page_en' => $pageDataEn,
            'landing_page_ar' => $pageDataAr,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Landing page updated successfully!'
        ]);
    }

    public function uploadQuillImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120'
        ]);
        
        $path = $request->file('image')->store('products/descriptions', 'public');
        $url = \Storage::url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path
        ]);
    }

    public function uploadProductImage(Request $request, $id)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);
        
        $path = $request->file('image')->store('products/landing-sections', 'public');
        $url = \Storage::url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path
        ]);
    }

    public function setMainImage(Request $request, $id)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        $validated = $request->validate([
            'image_path' => 'required|string'
        ]);
        
        $images = $product->images ?? [];
        $imagePath = $validated['image_path'];
        
        // Remove from current position and add to front
        $images = array_values(array_diff($images, [$imagePath]));
        array_unshift($images, $imagePath);
        
        $product->update(['images' => $images]);
        
        return response()->json([
            'success' => true,
            'message' => 'Main image updated successfully!'
        ]);
    }

    public function updateImageDescription(Request $request, $id)
    {
        $storeId = $this->getActiveStoreId();
        
        $product = \App\Models\Product::where('user_id', auth()->id())
            ->when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        $validated = $request->validate([
            'image_path' => 'required|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);
        
        $imageDescriptions = $product->image_descriptions ?? [];
        $imageDescriptions[$validated['image_path']] = [
            'fr' => $validated['description_fr'] ?? '',
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? '',
        ];
        
        $product->update(['image_descriptions' => $imageDescriptions]);
        
        return response()->json([
            'success' => true,
            'message' => 'Image description updated successfully!'
        ]);
    }
    
    public function campaigns()
    {
        return view('customer.campaigns');
    }

    public function leads()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $leads = \App\Models\ProductLead::with('product')
            ->where('user_id', $user->id)
            ->when($storeId, function($q) use ($storeId) {
                $q->whereHas('product', function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                });
            })
            ->latest()
            ->paginate(20);
        
        return view('customer.leads', compact('leads'));
    }
    
    public function categories()
    {
        $storeId = $this->getActiveStoreId();
        
        $query = \App\Models\Category::orderBy('order')->orderBy('name');
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $categories = $query->get();
        return view('customer.categories', compact('categories'));
    }
    
    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['store_id'] = $this->getActiveStoreId();
        
        \App\Models\Category::create($validated);
        
        return redirect()->route('app.categories')->with('success', 'Category created successfully!');
    }
    
    public function categoriesUpdate(Request $request, $id)
    {
        $storeId = $this->getActiveStoreId();
        
        $category = \App\Models\Category::when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        $category->update($validated);
        
        return redirect()->route('app.categories')->with('success', 'Category updated successfully!');
    }
    
    public function categoriesDestroy($id)
    {
        $storeId = $this->getActiveStoreId();
        
        $category = \App\Models\Category::when($storeId, function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('app.categories')->with('error', 'Cannot delete category with associated products.');
        }
        
        $category->delete();
        
        return redirect()->route('app.categories')->with('success', 'Category deleted successfully!');
    }

    public function externalApiSettings()
    {
        $user = auth()->user();
        return view('customer.external-api-settings', compact('user'));
    }

    public function saveExternalApiSettings(Request $request)
    {
        $validated = $request->validate([
            'external_api_url' => 'nullable|url|max:500',
            'external_api_key' => 'nullable|string|max:2048',
            'external_api_enabled' => 'nullable|boolean',
            'clear_external_api_key' => 'nullable|boolean',
        ]);

        $user = $request->user();
        
        $user->external_api_url = $validated['external_api_url'] ?? null;
        $user->external_api_enabled = $request->boolean('external_api_enabled');

        if ($request->boolean('clear_external_api_key')) {
            $user->external_api_key_encrypted = null;
        } elseif (!empty($validated['external_api_key'])) {
            $user->external_api_key_encrypted = \Crypt::encryptString(trim($validated['external_api_key']));
        }

        $user->save();

        return redirect()
            ->route('app.external-api-settings')
            ->with('success', 'External API settings saved successfully!');
    }

    public function testExternalApiConnection(Request $request)
    {
        $user = $request->user();
        
        if (!$user->external_api_enabled || !$user->external_api_url || !$user->external_api_key_encrypted) {
            return redirect()
                ->route('app.external-api-settings')
                ->with('error', 'Please configure all API settings before testing.');
        }

        $apiService = new \App\Services\ExternalApiService($user);
        $result = $apiService->testConnection();

        if ($result['success']) {
            return redirect()
                ->route('app.external-api-settings')
                ->with('success', 'Connection successful! Your API is working correctly.');
        }

        return redirect()
            ->route('app.external-api-settings')
            ->with('error', 'Connection failed: ' . $result['message']);
    }
}
