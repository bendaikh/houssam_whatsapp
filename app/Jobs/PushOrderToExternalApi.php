<?php

namespace App\Jobs;

use App\Models\ProductLead;
use App\Services\ExternalApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PushOrderToExternalApi implements ShouldQueue
{
    use Queueable;

    protected $lead;

    /**
     * Create a new job instance.
     */
    public function __construct(ProductLead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lead = $this->lead->fresh(['product', 'variation']);
        
        if (!$lead) {
            Log::warning('Lead not found for pushing to external API');
            return;
        }

        $user = $lead->user;
        
        if (!$user) {
            Log::warning('User not found for lead ' . $lead->id);
            return;
        }

        $apiService = new ExternalApiService($user);
        
        if (!$apiService->isEnabled()) {
            Log::info('External API not enabled for user ' . $user->id);
            return;
        }

        $product = $lead->product;
        $sku = $lead->variation?->sku ?? $product?->sku;
        $itemPrice = (float) ($lead->selected_price ?? $product?->price ?? 0);
        $productName = $product?->name ?? 'Product from ChatEasy';

        // Format data according to external API requirements
        // Note: product_id must exist in the external system, using 1 as default
        $orderData = [
            'client_name' => $lead->name,
            'client_phone' => $lead->phone,
            'source' => 'whatsapp', // Required: manual, shopify, google_sheet, delivery_company, marketplace, whatsapp
            'items' => [
                [
                    'product_id' => 1, // Default product in external system
                    'sku' => $sku,
                    'name' => $productName,
                    'quantity' => 1,
                    'price' => $itemPrice,
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

        $result = $apiService->createOrder($orderData);
        
        if ($result['success']) {
            Log::info('Successfully pushed lead to external API', [
                'lead_id' => $lead->id,
                'user_id' => $user->id
            ]);
        } else {
            Log::error('Failed to push lead to external API', [
                'lead_id' => $lead->id,
                'user_id' => $user->id,
                'error' => $result['message']
            ]);
        }
    }
}
