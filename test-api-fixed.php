<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
$apiService = new \App\Services\ExternalApiService($user);

echo "=== Testing Updated API Integration ===\n\n";
echo "API Enabled: " . ($apiService->isEnabled() ? 'Yes' : 'No') . "\n";
echo "API URL: " . $user->external_api_url . "\n\n";

$lead = \App\Models\ProductLead::latest()->first();
if ($lead) {
    echo "Testing with lead:\n";
    echo "- Name: {$lead->name}\n";
    echo "- Phone: {$lead->phone}\n";
    echo "- Product ID: {$lead->product_id}\n";
    echo "- Product Price: " . ($lead->product->price ?? 0) . "\n\n";
    
    $lead->load(['product.store.websiteSettings', 'variation']);
    echo "- Store: " . ($lead->product?->store?->name ?? 'N/A') . "\n\n";

    echo "Dispatching PushOrderToExternalApi job...\n";
    \App\Jobs\PushOrderToExternalApi::dispatchSync($lead);
    echo "Job completed. Check logs for API response.\n";
}
