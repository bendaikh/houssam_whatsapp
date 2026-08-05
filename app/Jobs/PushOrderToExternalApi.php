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
        $lead = $this->lead->fresh(['product.store.websiteSettings', 'variation', 'promotion']);
        
        if (!$lead) {
            Log::warning('Lead not found for pushing to external API');
            return;
        }

        $user = $lead->user;
        
        if (!$user) {
            Log::warning('User not found for lead ' . $lead->id);
            return;
        }

        $product = $lead->product;
        $store = $product?->store;

        if (!$store) {
            Log::warning('Store not found for lead ' . $lead->id . ' — cannot push via System Connect');
            return;
        }

        $apiService = ExternalApiService::forStore($store);
        
        if (!$apiService->isEnabled()) {
            Log::info('System Connect not enabled for store ' . $store->id . ' (lead ' . $lead->id . ')');
            return;
        }

        $websiteSettings = $store->websiteSettings;
        $sku = $lead->variation?->sku ?? $product?->sku;
        $itemPrice = (float) ($lead->selected_price ?? $product?->price ?? 0);
        $productName = $product?->name ?? 'Product from ChatEasy';
        $quantity = $lead->order_quantity;
        $website = $this->buildWebsitePayload($store, $websiteSettings, $product, $user);

        // Format data according to external API requirements
        // Note: product_id must exist in the external system, using 1 as default
        $orderData = [
            'client_name' => $lead->name,
            'client_phone' => $lead->phone,
            'source' => 'whatsapp', // Required: manual, shopify, google_sheet, delivery_company, marketplace, whatsapp
            'website' => $website,
            'items' => [
                [
                    'product_id' => 1, // Default product in external system
                    'sku' => $sku,
                    'name' => $productName,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                ]
            ],
            'notes' => ($lead->note ?? '') . "\n[ChatEasy Product: " . $productName . ($sku ? " | SKU: {$sku}" : '') . ($website['name'] ? " | Website: {$website['name']}" : '') . "]",
            'metadata' => [
                'chateasy_lead_id' => $lead->id,
                'chateasy_product_id' => $lead->product_id,
                'chateasy_variation_id' => $lead->selected_variation_id,
                'chateasy_promotion_id' => $lead->selected_promotion_id,
                'quantity' => $quantity,
                'chateasy_store_id' => $store->id,
                'sku' => $sku,
                'language' => $lead->language,
                'created_at' => $lead->created_at->toIso8601String(),
                'website' => $website,
                'alfa_cod_seller_id' => $store->alfa_cod_seller_id,
                'alfa_cod_seller_name' => $store->alfa_cod_seller_name,
            ]
        ];

        // Assign the Alfa-COD seller linked to this store so the order appears in their account
        if ($store->alfa_cod_seller_id) {
            $orderData['vendor_id'] = (int) $store->alfa_cod_seller_id;
        }

        $result = $apiService->createOrder($orderData);
        
        if ($result['success']) {
            Log::info('Successfully pushed lead to System Connect', [
                'lead_id' => $lead->id,
                'store_id' => $store->id,
                'user_id' => $user->id,
            ]);
        } else {
            Log::error('Failed to push lead to System Connect', [
                'lead_id' => $lead->id,
                'store_id' => $store->id,
                'user_id' => $user->id,
                'error' => $result['message'],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildWebsitePayload($store, $websiteSettings, $product, $user): array
    {
        if (!$store) {
            return [
                'id' => null,
                'name' => null,
                'store_name' => null,
                'subdomain' => null,
                'domain' => null,
                'url' => null,
                'product_url' => null,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'company_name' => $user->company_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'contact' => [
                    'phone' => null,
                    'email' => null,
                ],
            ];
        }

        $websiteUrl = $this->buildStoreWebsiteUrl($store);
        $productUrl = $product
            ? route('store.product.show', [$store->subdomain, $product->slug])
            : null;

        return [
            'id' => $store->id,
            'name' => $websiteSettings?->site_name ?? $store->name,
            'store_name' => $store->name,
            'subdomain' => $store->subdomain,
            'domain' => $store->domain,
            'url' => $websiteUrl,
            'product_url' => $productUrl,
            'owner' => [
                'id' => $user->id,
                'name' => $user->name,
                'company_name' => $user->company_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'contact' => [
                'phone' => $websiteSettings?->contact_phone,
                'email' => $websiteSettings?->contact_email,
            ],
        ];
    }

    protected function buildStoreWebsiteUrl($store): string
    {
        if ($store->domain) {
            $domain = trim($store->domain);

            if (!preg_match('#^https?://#i', $domain)) {
                $domain = 'https://' . $domain;
            }

            return rtrim($domain, '/');
        }

        return url('/store/' . $store->subdomain);
    }
}
