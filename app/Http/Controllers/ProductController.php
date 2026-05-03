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

        if ($product->theme === 'theme2') {
            return view('product-landing-theme2', compact('product', 'relatedProducts', 'store'));
        }

        if ($product->landing_page_fr || $product->landing_page_en || $product->landing_page_ar) {
            return view('product-landing', compact('product', 'relatedProducts', 'store'));
        }

        return view('product-detail', compact('product', 'relatedProducts', 'store'));
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

        // Base validation rules
        $validationRules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'language' => 'required|in:fr,en,ar',
        ];

        // Add validation for custom form fields
        $customFieldsData = [];
        if (!empty($product->form_fields)) {
            foreach ($product->form_fields as $field) {
                $fieldName = 'custom_' . $field['id'];
                
                // Build validation rule
                $rules = [];
                if ($field['required'] ?? false) {
                    $rules[] = 'required';
                } else {
                    $rules[] = 'nullable';
                }
                
                // Add type-specific validation
                switch ($field['type']) {
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
                
                $validationRules[$fieldName] = implode('|', $rules);
            }
        }

        $validated = $request->validate($validationRules);

        // Extract custom fields from validated data
        foreach ($validated as $key => $value) {
            if (str_starts_with($key, 'custom_')) {
                $fieldId = substr($key, 7); // Remove 'custom_' prefix
                $customFieldsData[$fieldId] = $value;
            }
        }

        $lead = \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'note' => $validated['note'],
            'custom_fields' => !empty($customFieldsData) ? $customFieldsData : null,
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        \App\Jobs\PushOrderToExternalApi::dispatch($lead);

        $successMessages = [
            'fr' => 'Merci ! Nous vous contactons bientôt.',
            'en' => 'Thank you! We will contact you soon.',
            'ar' => 'شكرا لك! سنتصل بك قريبا.',
        ];

        return back()->with('success', $successMessages[$validated['language']] ?? $successMessages['fr']);
    }
}
