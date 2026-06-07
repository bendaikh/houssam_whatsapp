<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLead extends Model
{
    public const RESERVED_CUSTOM_FIELD_KEYS = [
        'city',
        'ville',
        'address',
        'adresse',
    ];

    protected $fillable = [
        'product_id',
        'selected_promotion_id',
        'selected_variation_id',
        'selected_price',
        'user_id',
        'name',
        'phone',
        'city',
        'address',
        'note',
        'custom_fields',
        'language',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'selected_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promotion()
    {
        return $this->belongsTo(ProductPromotion::class, 'selected_promotion_id');
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'selected_variation_id');
    }

    public function getCityAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        $customFields = $this->custom_fields ?? [];

        return $customFields['city'] ?? $customFields['ville'] ?? null;
    }

    public function getAddressAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        $customFields = $this->custom_fields ?? [];

        return $customFields['address'] ?? $customFields['adresse'] ?? null;
    }

    public function getDisplayCustomFieldsAttribute(): array
    {
        $customFields = $this->custom_fields ?? [];

        return array_filter(
            $customFields,
            fn ($key) => !in_array($key, self::RESERVED_CUSTOM_FIELD_KEYS, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function getOrderQuantityAttribute(): int
    {
        if ($this->promotion) {
            return max(1, (int) $this->promotion->min_quantity);
        }

        return 1;
    }
}
