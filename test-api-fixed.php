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
    
    $lead->load(['product', 'variation']);
    $sku = $lead->variation?->sku ?? $lead->product?->sku;
    $productName = $lead->product->name ?? 'Product from ChatEasy';

    // Create order data matching the external API format
    $orderData = [
        'client_name' => $lead->name,
        'client_phone' => $lead->phone,
        'source' => 'whatsapp',
        'items' => [
            [
                'product_id' => 1, // Default product in external system
                'sku' => $sku,
                'name' => $productName,
                'quantity' => 1,
                'price' => (float) ($lead->selected_price ?? $lead->product->price ?? 0),
            ]
        ],
        'notes' => ($lead->note ?? '') . "\n[ChatEasy Product: " . $productName . ($sku ? " | SKU: {$sku}" : '') . "]",
        'metadata' => [
            'chateasy_lead_id' => $lead->id,
            'chateasy_product_id' => $lead->product_id,
            'chateasy_variation_id' => $lead->selected_variation_id,
            'sku' => $sku,
            'language' => $lead->language,
            'created_at' => $lead->created_at->toIso8601String(),
        ]
    ];
    
    echo "Order Data to Send:\n";
    echo json_encode($orderData, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "Sending to external API...\n";
    $result = $apiService->createOrder($orderData);
    
    echo "\nAPI Response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
}
