<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLead extends Model
{
    protected $fillable = [
        'product_id',
        'selected_promotion_id',
        'selected_variation_id',
        'selected_price',
        'user_id',
        'name',
        'phone',
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
}
