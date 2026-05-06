<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreManagementController extends Controller
{
    use AuthorizesRequests;
    public function dashboard(Request $request)
    {
        $activeWorkspaceId = session('active_workspace_id');
        $activeWorkspace = $activeWorkspaceId ? \App\Models\Workspace::find($activeWorkspaceId) : null;
        $view = $request->get('view', 'overview');
        
        $stores = auth()->user()->stores()
            ->when($activeWorkspaceId, function ($query, $workspaceId) {
                return $query->where('workspace_id', $workspaceId);
            })
            ->withCount('products', 'categories')
            ->latest()
            ->get();
        
        // Workspace-specific statistics
        $storeIds = $stores->pluck('id');
        $totalProducts = \App\Models\Product::whereIn('store_id', $storeIds)->count();
        $totalOrders = \App\Models\Order::whereIn('store_id', $storeIds)->count();
        $newOrders = \App\Models\Order::whereIn('store_id', $storeIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $totalRevenue = \App\Models\Order::whereIn('store_id', $storeIds)
            ->where('status', 'completed')
            ->sum('total');
        $pendingOrders = \App\Models\Order::whereIn('store_id', $storeIds)
            ->where('status', 'pending')
            ->count();
            
        $stats = [
            'total_stores' => $stores->count(),
            'active_stores' => $stores->where('is_active', true)->count(),
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'new_orders' => $newOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
        ];
        
        $currentStoreId = session('active_store_id');
        
        if ($view === 'list') {
            return view('stores.list', compact('stores', 'stats', 'currentStoreId', 'activeWorkspace'));
        }
        
        return view('stores.overview', compact('stores', 'stats', 'currentStoreId', 'activeWorkspace'));
    }
    
    public function index()
    {
        $activeWorkspaceId = session('active_workspace_id');
        
        $stores = auth()->user()->stores()
            ->when($activeWorkspaceId, function ($query, $workspaceId) {
                return $query->where('workspace_id', $workspaceId);
            })
            ->withCount('products', 'categories')
            ->latest()
            ->paginate(20);
            
        $currentStoreId = session('active_store_id');
        
        return view('stores.index', compact('stores', 'currentStoreId'));
    }
    
    public function create()
    {
        return view('stores.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:stores,subdomain|alpha_dash',
            'domain' => 'nullable|string|max:255|unique:stores,domain',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['user_id'] = auth()->id();
        $validated['workspace_id'] = session('active_workspace_id');
        
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }
        
        $store = Store::create($validated);
        
        session(['active_store_id' => $store->id]);
        
        return redirect()->route('stores.dashboard')->with('success', 'Store created successfully! You can now manage it.');
    }
    
    public function edit(Store $store)
    {
        $this->authorize('update', $store);
        
        return view('stores.edit', compact('store'));
    }
    
    public function update(Request $request, Store $store)
    {
        $this->authorize('update', $store);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|alpha_dash|unique:stores,subdomain,' . $store->id,
            'domain' => 'nullable|string|max:255|unique:stores,domain,' . $store->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $store->update($validated);
        
        return redirect()->route('stores.dashboard')->with('success', 'Store updated successfully!');
    }
    
    public function destroy(Store $store)
    {
        $this->authorize('delete', $store);
        
        if (session('active_store_id') == $store->id) {
            session()->forget('active_store_id');
        }
        
        $store->delete();
        
        return redirect()->route('stores.dashboard')->with('success', 'Store deleted successfully!');
    }
    
    public function switchStore(Store $store)
    {
        $this->authorize('view', $store);
        
        session(['active_store_id' => $store->id]);
        
        return redirect()->route('app.dashboard')->with('success', 'Now managing: ' . $store->name);
    }
    
    public function updateDomain(Request $request, Store $store)
    {
        $this->authorize('update', $store);
        
        $validated = $request->validate([
            'domain' => 'nullable|string|max:255|unique:stores,domain,' . $store->id,
        ]);
        
        // Clean up the domain (remove http://, https://, www. if user included them)
        $domain = $validated['domain'] ?? null;
        if ($domain) {
            $domain = preg_replace('#^https?://#', '', $domain);
            $domain = trim($domain);
        }
        
        $store->update(['domain' => $domain]);
        
        if ($domain) {
            return redirect()->route('stores.dashboard')->with('success', 'Custom domain added successfully! Please configure your DNS records.');
        } else {
            return redirect()->route('stores.dashboard')->with('success', 'Custom domain removed. Store is accessible via subdomain.');
        }
    }
    
    public function duplicate(Store $store)
    {
        $this->authorize('view', $store);
        
        // Create a copy of the store with a new name and subdomain
        $newStore = $store->replicate();
        $newStore->name = $store->name . ' (Copy)';
        $newStore->subdomain = $store->subdomain . '-copy-' . time();
        $newStore->domain = null; // Reset domain for the copy
        $newStore->save();
        
        // Duplicate all products from the original store
        $products = \App\Models\Product::where('store_id', $store->id)->get();
        
        foreach ($products as $product) {
            $newProduct = $product->replicate();
            $newProduct->store_id = $newStore->id;
            $newProduct->slug = $product->slug . '-copy-' . time();
            $newProduct->save();
            
            // Duplicate product variations if any
            if ($product->has_variations) {
                foreach ($product->variations as $variation) {
                    $newVariation = $variation->replicate();
                    $newVariation->product_id = $newProduct->id;
                    $newVariation->save();
                }
            }
            
            // Duplicate product promotions if any
            if ($product->has_promotions) {
                foreach ($product->promotions as $promotion) {
                    $newPromotion = $promotion->replicate();
                    $newPromotion->product_id = $newProduct->id;
                    $newPromotion->save();
                }
            }
        }
        
        // Duplicate categories
        $categories = \App\Models\Category::where('store_id', $store->id)->get();
        $categoryMapping = []; // Map old category IDs to new ones
        
        foreach ($categories as $category) {
            $newCategory = $category->replicate();
            $newCategory->store_id = $newStore->id;
            $newCategory->slug = $category->slug . '-copy-' . time();
            $newCategory->save();
            
            $categoryMapping[$category->id] = $newCategory->id;
        }
        
        // Update category references in duplicated products
        foreach (\App\Models\Product::where('store_id', $newStore->id)->get() as $product) {
            if ($product->category_id && isset($categoryMapping[$product->category_id])) {
                $product->update(['category_id' => $categoryMapping[$product->category_id]]);
            }
        }
        
        return redirect()->route('stores.dashboard')->with('success', 'Store "' . $store->name . '" duplicated successfully with all ' . $products->count() . ' products!');
    }
}
