<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    protected $user;
    protected $apiUrl;
    protected $apiKey;

    public function __construct(User $user)
    {
        $this->user = $user;
        
        if ($user->external_api_enabled && $user->external_api_url && $user->external_api_key_encrypted) {
            // Remove /api or /api/ from the end if present
            $this->apiUrl = rtrim($user->external_api_url, '/');
            $this->apiUrl = preg_replace('#/api/?$#i', '', $this->apiUrl);
            
            try {
                $this->apiKey = Crypt::decryptString($user->external_api_key_encrypted);
            } catch (\Throwable $e) {
                Log::error('Failed to decrypt external API key for user ' . $user->id, ['error' => $e->getMessage()]);
            }
        }
    }

    public function isEnabled(): bool
    {
        return $this->user->external_api_enabled && !empty($this->apiUrl) && !empty($this->apiKey);
    }

    public function createOrder(array $orderData): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'External API is not enabled or configured properly'
            ];
        }

        $url = $this->apiUrl . '/api/orders';

        try {
            Log::info('Attempting to create order in external API', [
                'url' => $url,
                'user_id' => $this->user->id,
                'order_data' => $orderData
            ]);

            $jsonBody = json_encode($orderData);
            
            Log::info('Raw JSON body being sent', [
                'json_body' => $jsonBody,
                'json_valid' => json_last_error() === JSON_ERROR_NONE
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->withBody($jsonBody, 'application/json')
            ->post($url);

            if ($response->successful()) {
                Log::info('Order pushed to external API successfully', [
                    'user_id' => $this->user->id,
                    'url' => $url,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'message' => 'Order created successfully',
                    'data' => $response->json()
                ];
            }

            Log::warning('Failed to push order to external API', [
                'user_id' => $this->user->id,
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create order: ' . $response->body(),
                'status' => $response->status()
            ];

        } catch (\Throwable $e) {
            Log::error('Exception while pushing order to external API', [
                'user_id' => $this->user->id,
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    public function testConnection(): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'External API is not enabled or configured properly'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/api/orders');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection successful'
                ];
            }

            return [
                'success' => false,
                'message' => 'Connection failed with status: ' . $response->status()
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch sellers (vendors) from Alfa-COD / System Connect.
     *
     * @return array{success: bool, message?: string, sellers?: array<int, array<string, mixed>>}
     */
    public function getSellers(): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'External API is not enabled or configured properly',
                'sellers' => [],
            ];
        }

        $endpoints = [
            $this->apiUrl . '/api/sellers',
            $this->apiUrl . '/api/external/sellers',
        ];

        $lastError = 'Unable to fetch sellers';

        foreach ($endpoints as $url) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->timeout(20)
                ->get($url, ['active_only' => 1]);

                if (!$response->successful()) {
                    $lastError = 'Failed to fetch sellers (HTTP ' . $response->status() . ') from ' . $url;
                    Log::warning('Failed to fetch Alfa-COD sellers', [
                        'user_id' => $this->user->id,
                        'url' => $url,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    continue;
                }

                $payload = $response->json();
                $rawSellers = [];

                if (is_array($payload)) {
                    if (isset($payload['data']) && is_array($payload['data'])) {
                        $rawSellers = $payload['data'];
                    } elseif (array_is_list($payload)) {
                        $rawSellers = $payload;
                    }
                }

                $sellers = collect($rawSellers)
                    ->filter(fn ($seller) => is_array($seller) && isset($seller['id']))
                    ->map(function (array $seller) {
                        $name = $seller['company_name']
                            ?? $seller['name']
                            ?? $seller['label']
                            ?? ('Seller #' . $seller['id']);

                        $label = $seller['label'] ?? trim(
                            $name . (!empty($seller['email']) ? ' (' . $seller['email'] . ')' : '')
                        );

                        return [
                            'id' => (int) $seller['id'],
                            'name' => $seller['name'] ?? $name,
                            'company_name' => $seller['company_name'] ?? null,
                            'email' => $seller['email'] ?? null,
                            'phone' => $seller['phone'] ?? null,
                            'label' => $label,
                            'is_active' => (bool) ($seller['is_active'] ?? true),
                        ];
                    })
                    ->values()
                    ->all();

                Log::info('Fetched Alfa-COD sellers', [
                    'user_id' => $this->user->id,
                    'url' => $url,
                    'count' => count($sellers),
                ]);

                return [
                    'success' => true,
                    'message' => 'Sellers loaded successfully',
                    'sellers' => $sellers,
                ];
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::error('Exception while fetching Alfa-COD sellers', [
                    'user_id' => $this->user->id,
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => false,
            'message' => $lastError,
            'sellers' => [],
        ];
    }
}
