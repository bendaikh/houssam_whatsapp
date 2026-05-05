<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index($subdomain, Request $request)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);
        
        if (!$settings) {
            $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);
        }
        
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('store_id', $store->id);

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('is_active', true)
            ->where('store_id', $store->id)
            ->orderBy('order')
            ->get();
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->where('store_id', $store->id)
            ->limit(8)
            ->get();

        return view('welcome', compact('products', 'categories', 'featuredProducts', 'settings', 'store'));
    }

    public function show($subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();

        $product = Product::with(['activeVariations', 'activePromotions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->limit(4)
            ->get();

        $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);

        if ($product->theme === 'theme2') {
            return view('product-landing-theme2', compact('product', 'relatedProducts', 'store', 'settings'));
        }

        if ($product->landing_page_fr || $product->landing_page_en || $product->landing_page_ar) {
            return view('product-landing', compact('product', 'relatedProducts', 'store', 'settings'));
        }

        return view('product-detail', compact('product', 'relatedProducts', 'store', 'settings'));
    }

    public function submitLead(Request $request, $subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        // Get form fields - use product's custom fields or defaults
        $formFields = $product->form_fields ?? [
            ['id' => 'name', 'type' => 'text', 'required' => true],
            ['id' => 'phone', 'type' => 'tel', 'required' => true],
            ['id' => 'note', 'type' => 'textarea', 'required' => false],
        ];

        // Build validation rules dynamically based on form fields
        $validationRules = [
            'language' => 'required|in:fr,en,ar',
        ];

        // Track which fields are standard vs custom
        $standardFields = ['name', 'phone', 'note'];
        $customFieldsData = [];

        foreach ($formFields as $field) {
            $fieldId = $field['id'];
            
            // Build validation rule
            $rules = [];
            if ($field['required'] ?? false) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }
            
            // Add type-specific validation
            switch ($field['type'] ?? 'text') {
                case 'email':
                    $rules[] = 'email';
                    $rules[] = 'max:255';
                    break;
                case 'tel':
                    $rules[] = 'string';
                    $rules[] = 'max:20';
                    break;
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'textarea':
                    $rules[] = 'string';
                    $rules[] = 'max:1000';
                    break;
                default:
                    $rules[] = 'string';
                    $rules[] = 'max:255';
            }
            
            $validationRules[$fieldId] = implode('|', $rules);
        }

        // Ensure standard fields have fallback rules if not in form_fields
        if (!isset($validationRules['name'])) {
            $validationRules['name'] = 'nullable|string|max:255';
        }
        if (!isset($validationRules['phone'])) {
            $validationRules['phone'] = 'nullable|string|max:20';
        }
        if (!isset($validationRules['note'])) {
            $validationRules['note'] = 'nullable|string|max:1000';
        }

        // Add validation for order detail fields
        $validationRules['selected_promotion_id'] = 'nullable|integer|exists:product_promotions,id';
        $validationRules['selected_variation_id'] = 'nullable|integer|exists:product_variations,id';
        $validationRules['selected_price'] = 'nullable|numeric|min:0';

        $validated = $request->validate($validationRules);

        // Extract custom fields (fields that aren't standard)
        $orderDetailFields = ['selected_promotion_id', 'selected_variation_id', 'selected_price'];
        foreach ($validated as $key => $value) {
            if (!in_array($key, $standardFields) && !in_array($key, $orderDetailFields) && $key !== 'language' && $value !== null) {
                $customFieldsData[$key] = $value;
            }
        }

        // Determine the price if not provided
        $selectedPrice = $validated['selected_price'] ?? null;
        if (!$selectedPrice) {
            if (!empty($validated['selected_promotion_id'])) {
                $promotion = \App\Models\ProductPromotion::find($validated['selected_promotion_id']);
                $selectedPrice = $promotion ? $promotion->price : $product->price;
            } elseif (!empty($validated['selected_variation_id'])) {
                $variation = \App\Models\ProductVariation::find($validated['selected_variation_id']);
                $selectedPrice = $variation ? $variation->price : $product->price;
            } else {
                $selectedPrice = $product->price;
            }
        }

        $lead = \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'selected_promotion_id' => $validated['selected_promotion_id'] ?? null,
            'selected_variation_id' => $validated['selected_variation_id'] ?? null,
            'selected_price' => $selectedPrice,
            'user_id' => $product->user_id,
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'note' => $validated['note'] ?? null,
            'custom_fields' => !empty($customFieldsData) ? $customFieldsData : null,
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'pending',
        ]);

        \App\Jobs\PushOrderToExternalApi::dispatch($lead);

        return redirect()->route('store.product.thank-you', [$subdomain, $slug]);
    }

    public function thankYou($subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();

        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->first();

        return view('thank-you', compact('store', 'product'));
    }
}
