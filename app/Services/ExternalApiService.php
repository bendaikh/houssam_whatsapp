<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    protected ?User $user = null;
    protected ?Store $store = null;
    protected ?string $apiUrl = null;
    protected ?string $apiKey = null;
    protected bool $enabled = false;
    protected string $contextLabel = 'unknown';

    public function __construct(?User $user = null)
    {
        if ($user) {
            $this->configureFromUser($user);
        }
    }

    /**
     * Account-level Alfa-COD Connect — used to load sellers.
     */
    public static function forUser(User $user): self
    {
        $service = new self();
        $service->configureFromUser($user);

        return $service;
    }

    /**
     * Per-store System Connect — used to push orders.
     */
    public static function forStore(Store $store): self
    {
        $service = new self();
        $service->configureFromStore($store);

        return $service;
    }

    protected function configureFromUser(User $user): void
    {
        $this->user = $user;
        $this->store = null;
        $this->contextLabel = 'user:' . $user->id;
        $this->configure(
            (bool) $user->external_api_enabled,
            $user->external_api_url,
            $user->external_api_key_encrypted
        );
    }

    protected function configureFromStore(Store $store): void
    {
        $this->store = $store;
        $this->user = $store->user;
        $this->contextLabel = 'store:' . $store->id;
        $this->configure(
            (bool) $store->system_connect_enabled,
            $store->system_connect_url,
            $store->system_connect_key_encrypted
        );
    }

    protected function configure(bool $enabled, ?string $url, ?string $encryptedKey): void
    {
        $this->enabled = false;
        $this->apiUrl = null;
        $this->apiKey = null;

        if (!$enabled || !$url || !$encryptedKey) {
            return;
        }

        $this->apiUrl = rtrim($url, '/');
        $this->apiUrl = preg_replace('#/api/?$#i', '', $this->apiUrl);

        try {
            $this->apiKey = Crypt::decryptString($encryptedKey);
            $this->enabled = !empty($this->apiUrl) && !empty($this->apiKey);
        } catch (\Throwable $e) {
            Log::error('Failed to decrypt external API key for ' . $this->contextLabel, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiUrl) && !empty($this->apiKey);
    }

    public function createOrder(array $orderData): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'System Connect is not enabled or configured properly for this store',
            ];
        }

        $url = $this->apiUrl . '/api/orders';

        try {
            Log::info('Attempting to create order in external API', [
                'url' => $url,
                'context' => $this->contextLabel,
                'order_data' => $orderData,
            ]);

            $jsonBody = json_encode($orderData);

            Log::info('Raw JSON body being sent', [
                'json_body' => $jsonBody,
                'json_valid' => json_last_error() === JSON_ERROR_NONE,
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
                    'context' => $this->contextLabel,
                    'url' => $url,
                    'response' => $response->json(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Order created successfully',
                    'data' => $response->json(),
                ];
            }

            Log::warning('Failed to push order to external API', [
                'context' => $this->contextLabel,
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create order: ' . $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('Exception while pushing order to external API', [
                'context' => $this->contextLabel,
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    public function testConnection(): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'API is not enabled or configured properly',
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
                    'message' => 'Connection successful',
                ];
            }

            return [
                'success' => false,
                'message' => 'Connection failed with status: ' . $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch sellers (vendors) from Alfa-COD Connect (account-level credentials).
     *
     * @return array{success: bool, message?: string, sellers?: array<int, array<string, mixed>>}
     */
    public function getSellers(): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Alfa-COD Connect is not enabled or configured properly',
                'sellers' => [],
            ];
        }

        $endpoints = [
            $this->apiUrl . '/api/sellers',
            $this->apiUrl . '/api/external/sellers',
        ];

        $lastError = 'Unable to fetch sellers from Alfa-COD';

        foreach ($endpoints as $url) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->timeout(20)
                ->get($url, ['active_only' => 1]);

                $contentType = (string) $response->header('Content-Type');
                $body = $response->body();
                $looksLikeHtml = str_contains($contentType, 'text/html')
                    || str_starts_with(ltrim($body), '<!DOCTYPE')
                    || str_starts_with(ltrim($body), '<html');

                if ($looksLikeHtml) {
                    $lastError = 'Alfa-COD /api/sellers is not deployed on the live server yet. Pull latest master on alfa-cod.com and clear route cache.';
                    Log::warning('Alfa-COD sellers endpoint returned HTML', [
                        'context' => $this->contextLabel,
                        'url' => $url,
                        'status' => $response->status(),
                    ]);
                    continue;
                }

                if (!$response->successful()) {
                    $lastError = 'Failed to fetch sellers (HTTP ' . $response->status() . ')';
                    Log::warning('Failed to fetch Alfa-COD sellers', [
                        'context' => $this->contextLabel,
                        'url' => $url,
                        'status' => $response->status(),
                        'body' => $body,
                    ]);
                    continue;
                }

                $payload = $response->json();
                if (!is_array($payload)) {
                    $lastError = 'Alfa-COD sellers response was not valid JSON';
                    continue;
                }

                $rawSellers = [];
                if (isset($payload['data']) && is_array($payload['data'])) {
                    $rawSellers = $payload['data'];
                } elseif (array_is_list($payload)) {
                    $rawSellers = $payload;
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
                    'context' => $this->contextLabel,
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
                    'context' => $this->contextLabel,
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $sellers = $this->fallbackSellersFromAuth($lastError);

        return [
            'success' => false,
            'message' => $lastError,
            'sellers' => $sellers,
        ];
    }

    /**
     * When /api/sellers is not deployed yet, at least return the seller linked to the API key.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fallbackSellersFromAuth(string &$message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout(15)
            ->get($this->apiUrl . '/api/test-auth');

            if (!$response->successful()) {
                return [];
            }

            $payload = $response->json();
            if (!is_array($payload)) {
                return [];
            }

            $vendorId = $payload['integration']['vendor_id'] ?? null;
            if (!$vendorId) {
                return [];
            }

            $user = is_array($payload['authenticated_user'] ?? null) ? $payload['authenticated_user'] : [];
            $name = $user['name'] ?? ('Seller #' . $vendorId);
            $email = $user['email'] ?? null;

            $message = 'Full sellers list is unavailable until Alfa-COD deploys /api/sellers. Showing the seller linked to your API key only.';

            return [[
                'id' => (int) $vendorId,
                'name' => $name,
                'company_name' => $name,
                'email' => $email,
                'phone' => null,
                'label' => trim($name . ($email ? " ({$email})" : '')),
                'is_active' => true,
            ]];
        } catch (\Throwable $e) {
            Log::warning('Failed Alfa-COD sellers fallback from test-auth', [
                'context' => $this->contextLabel,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
