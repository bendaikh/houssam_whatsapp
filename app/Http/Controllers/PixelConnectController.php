<?php

namespace App\Http\Controllers;

use App\Models\FacebookPixel;
use App\Models\Store;
use Illuminate\Http\Request;

class PixelConnectController extends Controller
{
    public function index()
    {
        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return redirect()->route('stores.dashboard')->with('error', 'Please select a store first.');
        }

        $activeStore = Store::with('facebookPixels')->findOrFail($activeStoreId);

        return view('customer.pixel-connect', compact('activeStore'));
    }

    public function storeFacebookPixel(Request $request)
    {
        $request->validate([
            'pixel_id' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $exists = FacebookPixel::where('store_id', $activeStoreId)
            ->where('pixel_id', $request->pixel_id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'This Pixel ID is already configured for this store.'], 422);
        }

        FacebookPixel::create([
            'store_id' => $activeStoreId,
            'name' => $request->name ?: 'Facebook Pixel',
            'pixel_id' => $request->pixel_id,
            'is_enabled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel added successfully!',
        ]);
    }

    public function updateFacebookPixel(Request $request, FacebookPixel $facebookPixel)
    {
        $this->authorizePixel($facebookPixel);

        $request->validate([
            'pixel_id' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'is_enabled' => 'nullable|boolean',
        ]);

        $duplicate = FacebookPixel::where('store_id', $facebookPixel->store_id)
            ->where('pixel_id', $request->pixel_id)
            ->where('id', '!=', $facebookPixel->id)
            ->exists();

        if ($duplicate) {
            return response()->json(['error' => 'This Pixel ID is already configured for this store.'], 422);
        }

        $facebookPixel->update([
            'pixel_id' => $request->pixel_id,
            'name' => $request->name ?: $facebookPixel->name,
            'is_enabled' => $request->boolean('is_enabled', $facebookPixel->is_enabled),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel updated successfully!',
        ]);
    }

    public function toggleFacebookPixel(FacebookPixel $facebookPixel)
    {
        $this->authorizePixel($facebookPixel);

        $facebookPixel->update([
            'is_enabled' => !$facebookPixel->is_enabled,
        ]);

        return response()->json([
            'success' => true,
            'message' => $facebookPixel->is_enabled ? 'Facebook Pixel enabled.' : 'Facebook Pixel disabled.',
            'is_enabled' => $facebookPixel->is_enabled,
        ]);
    }

    public function deleteFacebookPixel(FacebookPixel $facebookPixel)
    {
        $this->authorizePixel($facebookPixel);

        $facebookPixel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel deleted successfully!',
        ]);
    }

    public function saveTikTokPixel(Request $request)
    {
        $request->validate([
            'tiktok_pixel_id' => 'required|string|max:255',
        ]);

        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $store->update([
            'tiktok_pixel_id' => $request->tiktok_pixel_id,
            'tiktok_pixel_enabled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TikTok Pixel connected successfully!',
        ]);
    }

    public function disconnectTikTokPixel()
    {
        $activeStoreId = session('active_store_id');

        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $store->update([
            'tiktok_pixel_id' => null,
            'tiktok_pixel_enabled' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TikTok Pixel disconnected successfully!',
        ]);
    }

    protected function authorizePixel(FacebookPixel $facebookPixel): void
    {
        $activeStoreId = session('active_store_id');

        if (!$activeStoreId || $facebookPixel->store_id != $activeStoreId) {
            abort(403);
        }
    }
}
