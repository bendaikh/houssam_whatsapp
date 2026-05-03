<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLead extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'name',
        'phone',
        'note',
        'custom_fields',
        'language',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
