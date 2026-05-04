<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialMediaAdsController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Fallback storage route for hosts without symlink support
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($fullPath);
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');

Route::get('/', function () {
    return view('coming-soon');
})->name('coming-soon');

// Secret login route - only accessible via /alfa/login
Route::get('/alfa/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('secret.login');

Route::post('/alfa/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::get('/store/{subdomain}', [ProductController::class, 'index'])->name('store.home');
Route::get('/store/{subdomain}/product/{slug}', [ProductController::class, 'show'])->name('store.product.show');
Route::post('/store/{subdomain}/product/{slug}/submit-lead', [ProductController::class, 'submitLead'])->name('store.product.submit-lead');
Route::get('/store/{subdomain}/product/{slug}/thank-you', [ProductController::class, 'thankYou'])->name('store.product.thank-you');

// WhatsApp Webhook (no auth required for external services)
Route::post('/webhook/whatsapp', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');

// API route for Node.js to process messages (no auth)
Route::post('/api/whatsapp/process-message', [WhatsAppController::class, 'processMessageWithAi']);

// Dashboard - redirects to workspaces management
Route::get('/dashboard', function () {
    return redirect()->route('workspaces.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Workspace Management Routes
Route::middleware(['auth'])->prefix('workspaces')->name('workspaces.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\WorkspaceController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [\App\Http\Controllers\WorkspaceController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\WorkspaceController::class, 'store'])->name('store');
    Route::get('/{workspace}/edit', [\App\Http\Controllers\WorkspaceController::class, 'edit'])->name('edit');
    Route::put('/{workspace}', [\App\Http\Controllers\WorkspaceController::class, 'update'])->name('update');
    Route::delete('/{workspace}', [\App\Http\Controllers\WorkspaceController::class, 'destroy'])->name('destroy');
    Route::post('/{workspace}/switch', [\App\Http\Controllers\WorkspaceController::class, 'switch'])->name('switch');
    
    // AI API Integration (Workspace Level)
    Route::get('/ai-settings', [CustomerDashboardController::class, 'aiSettings'])->name('ai-settings');
    Route::post('/ai-settings/openai', [CustomerDashboardController::class, 'saveOpenAiSettings'])->name('ai-settings.openai.save');
    Route::post('/ai-settings/openai/test', [CustomerDashboardController::class, 'testOpenAiConnection'])->name('ai-settings.openai.test');
    Route::post('/ai-settings/anthropic', [CustomerDashboardController::class, 'saveAnthropicSettings'])->name('ai-settings.anthropic.save');
    Route::post('/ai-settings/anthropic/test', [CustomerDashboardController::class, 'testAnthropicConnection'])->name('ai-settings.anthropic.test');
});

// Store Management Routes (must come before app routes) - requires active workspace
Route::middleware(['auth', 'require.workspace'])->prefix('stores')->name('stores.')->group(function () {
    Route::get('/', [\App\Http\Controllers\StoreManagementController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [\App\Http\Controllers\StoreManagementController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\StoreManagementController::class, 'store'])->name('store');
    Route::get('/{store}/edit', [\App\Http\Controllers\StoreManagementController::class, 'edit'])->name('edit');
    Route::put('/{store}', [\App\Http\Controllers\StoreManagementController::class, 'update'])->name('update');
    Route::delete('/{store}', [\App\Http\Controllers\StoreManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{store}/switch', [\App\Http\Controllers\StoreManagementController::class, 'switchStore'])->name('switch');
    Route::put('/{store}/domain', [\App\Http\Controllers\StoreManagementController::class, 'updateDomain'])->name('update-domain');
});

// Main App Routes (for all authenticated users) - requires active workspace and store selection
Route::middleware(['auth', 'require.workspace', 'require.store'])->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/whatsapp', [CustomerDashboardController::class, 'whatsapp'])->name('whatsapp');
    Route::get('/conversations', [CustomerDashboardController::class, 'conversations'])->name('conversations');
    Route::get('/conversations/{id}', [CustomerDashboardController::class, 'conversationDetail'])->name('conversation.detail');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
    Route::get('/products', [CustomerDashboardController::class, 'products'])->name('products');
    Route::get('/products/select-theme', [CustomerDashboardController::class, 'productsSelectTheme'])->name('products.select-theme');
    Route::get('/products/create', [CustomerDashboardController::class, 'productsCreate'])->name('products.create');
    Route::post('/products', [CustomerDashboardController::class, 'productsStore'])->name('products.store');
    Route::get('/products/{id}/edit', [CustomerDashboardController::class, 'productsEdit'])->name('products.edit');
    Route::put('/products/{id}', [CustomerDashboardController::class, 'productsUpdate'])->name('products.update');
    Route::delete('/products/{id}', [CustomerDashboardController::class, 'productsDestroy'])->name('products.destroy');
    Route::get('/products/{id}/landing-builder', [CustomerDashboardController::class, 'landingPageBuilder'])->name('products.landing-builder');
    Route::post('/products/{id}/landing-builder', [CustomerDashboardController::class, 'saveLandingPageBuilder'])->name('products.save-landing-builder');
    Route::post('/products/{id}/upload-image', [CustomerDashboardController::class, 'uploadProductImage'])->name('products.upload-image');
    Route::post('/quill-upload-image', [CustomerDashboardController::class, 'uploadQuillImage'])->name('quill.upload-image');
    Route::post('/products/{id}/set-main-image', [CustomerDashboardController::class, 'setMainImage'])->name('products.set-main-image');
    Route::post('/products/{id}/update-image-description', [CustomerDashboardController::class, 'updateImageDescription'])->name('products.update-image-description');
    Route::post('/products/{id}/generate-landing-page', [CustomerDashboardController::class, 'generateLandingPage'])->name('products.generate-landing-page');
    Route::post('/products/{id}/generate-images', [CustomerDashboardController::class, 'generateProductImages'])->name('products.generate-images');
    Route::get('/products/{id}/image-progress', [CustomerDashboardController::class, 'checkImageGenerationProgress'])->name('products.image-progress');
    Route::get('/campaigns', [CustomerDashboardController::class, 'campaigns'])->name('campaigns');
    Route::get('/leads', [CustomerDashboardController::class, 'leads'])->name('leads');
    
    // Categories
    Route::get('/categories', [CustomerDashboardController::class, 'categories'])->name('categories');
    Route::post('/categories', [CustomerDashboardController::class, 'categoriesStore'])->name('categories.store');
    Route::put('/categories/{id}', [CustomerDashboardController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [CustomerDashboardController::class, 'categoriesDestroy'])->name('categories.destroy');
    
    // Website Customization
    Route::get('/website-customization', [\App\Http\Controllers\Admin\WebsiteCustomizationController::class, 'index'])->name('website-customization');
    Route::post('/website-customization', [\App\Http\Controllers\Admin\WebsiteCustomizationController::class, 'update'])->name('website-customization.update');
    Route::get('/website-preview', [\App\Http\Controllers\Admin\WebsiteCustomizationController::class, 'preview'])->name('website-preview');
    
    // Social Media Ads Integration
    Route::get('/facebook-ads', [SocialMediaAdsController::class, 'facebookAds'])->name('facebook-ads');
    Route::post('/facebook-ads', [SocialMediaAdsController::class, 'saveFacebookSettings'])->name('facebook-ads.save');
    Route::post('/facebook-ads/test', [SocialMediaAdsController::class, 'testFacebookConnection'])->name('facebook-ads.test');
    Route::post('/facebook-ads/disconnect', [SocialMediaAdsController::class, 'disconnectFacebook'])->name('facebook-ads.disconnect');
    
    Route::get('/tiktok-ads', [SocialMediaAdsController::class, 'tiktokAds'])->name('tiktok-ads');
    Route::post('/tiktok-ads', [SocialMediaAdsController::class, 'saveTikTokSettings'])->name('tiktok-ads.save');
    Route::post('/tiktok-ads/test', [SocialMediaAdsController::class, 'testTikTokConnection'])->name('tiktok-ads.test');
    Route::post('/tiktok-ads/disconnect', [SocialMediaAdsController::class, 'disconnectTikTok'])->name('tiktok-ads.disconnect');
    
    // Ad Campaigns Dashboard
    Route::get('/ad-campaigns', [\App\Http\Controllers\AdCampaignsController::class, 'index'])->name('ad-campaigns');
    Route::post('/ad-campaigns/refresh', [\App\Http\Controllers\AdCampaignsController::class, 'refresh'])->name('ad-campaigns.refresh');
    Route::post('/ad-campaigns/analyze', [\App\Http\Controllers\AdCampaignsController::class, 'analyzeCampaigns'])->name('ad-campaigns.analyze');
    
    // AI Campaign Creator
    Route::get('/campaign-creator', [\App\Http\Controllers\CampaignCreatorController::class, 'index'])->name('campaign-creator');
    Route::post('/campaign-creator/generate', [\App\Http\Controllers\CampaignCreatorController::class, 'generateCopy'])->name('campaign-creator.generate');
    Route::post('/campaign-creator/create', [\App\Http\Controllers\CampaignCreatorController::class, 'createCampaign'])->name('campaign-creator.create');
    Route::post('/campaign-creator/facebook/pages', [\App\Http\Controllers\CampaignCreatorController::class, 'getFacebookPages'])->name('campaign-creator.facebook.pages');
    
    // External API Integration
    Route::get('/external-api-settings', [CustomerDashboardController::class, 'externalApiSettings'])->name('external-api-settings');
    Route::post('/external-api-settings', [CustomerDashboardController::class, 'saveExternalApiSettings'])->name('external-api-settings.save');
    Route::post('/external-api-settings/test', [CustomerDashboardController::class, 'testExternalApiConnection'])->name('external-api-settings.test');
    
    // Pixel Connect
    Route::get('/pixel-connect', [\App\Http\Controllers\PixelConnectController::class, 'index'])->name('pixel-connect');
    Route::post('/pixel-connect/facebook', [\App\Http\Controllers\PixelConnectController::class, 'saveFacebookPixel'])->name('pixel-connect.facebook.save');
    Route::post('/pixel-connect/facebook/disconnect', [\App\Http\Controllers\PixelConnectController::class, 'disconnectFacebookPixel'])->name('pixel-connect.facebook.disconnect');
    Route::post('/pixel-connect/tiktok', [\App\Http\Controllers\PixelConnectController::class, 'saveTikTokPixel'])->name('pixel-connect.tiktok.save');
    Route::post('/pixel-connect/tiktok/disconnect', [\App\Http\Controllers\PixelConnectController::class, 'disconnectTikTokPixel'])->name('pixel-connect.tiktok.disconnect');
    
    // WhatsApp Routes
    Route::post('/whatsapp/generate-qr', [WhatsAppController::class, 'generateQrCode'])->name('whatsapp.generate-qr');
    Route::get('/whatsapp/check-connection', [WhatsAppController::class, 'checkConnection'])->name('whatsapp.check-connection');
    Route::post('/whatsapp/save-connection', [WhatsAppController::class, 'saveConnection'])->name('whatsapp.save-connection');
    Route::post('/whatsapp/disconnect/{profile}', [WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect');
    Route::get('/whatsapp/{profile}/conversations', [WhatsAppController::class, 'getConversations'])->name('whatsapp.conversations');
    Route::get('/whatsapp/conversations/{conversation}/messages', [WhatsAppController::class, 'getMessages'])->name('whatsapp.messages');
    Route::post('/whatsapp/conversations/{conversation}/send', [WhatsAppController::class, 'sendMessage'])->name('whatsapp.send');
});

// SuperAdmin Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [SuperAdminController::class, 'customers'])->name('customers');
    Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
    
    // Workspace Management
    Route::get('/workspaces', [SuperAdminController::class, 'workspaces'])->name('workspaces');
    Route::post('/workspaces', [SuperAdminController::class, 'workspaceStore'])->name('workspaces.store');
    Route::get('/workspaces/create', [SuperAdminController::class, 'workspaceCreate'])->name('workspaces.create');
    Route::get('/workspaces/{workspace}/edit', [SuperAdminController::class, 'workspaceEdit'])->name('workspaces.edit');
    Route::put('/workspaces/{workspace}', [SuperAdminController::class, 'workspaceUpdate'])->name('workspaces.update');
    Route::delete('/workspaces/{workspace}', [SuperAdminController::class, 'workspaceDestroy'])->name('workspaces.destroy');
    Route::post('/workspaces/{workspace}/switch', [SuperAdminController::class, 'switchWorkspace'])->name('workspaces.switch');
    
    // Store Management
    Route::get('/stores', [SuperAdminController::class, 'stores'])->name('stores');
    Route::post('/stores', [SuperAdminController::class, 'storeStore'])->name('stores.store');
    Route::get('/stores/create', [SuperAdminController::class, 'storeCreate'])->name('stores.create');
    Route::get('/stores/{store}/edit', [SuperAdminController::class, 'storeEdit'])->name('stores.edit');
    Route::put('/stores/{store}', [SuperAdminController::class, 'storeUpdate'])->name('stores.update');
    Route::delete('/stores/{store}', [SuperAdminController::class, 'storeDestroy'])->name('stores.destroy');
    Route::post('/stores/{store}/switch', [SuperAdminController::class, 'switchStore'])->name('stores.switch');
});

require __DIR__.'/auth.php';
