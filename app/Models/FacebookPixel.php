<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookPixel extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'pixel_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
