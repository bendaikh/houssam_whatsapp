<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $fillable = [
        'store_id',
        'ip_address',
        'reason',
        'blocked_by',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function blockedByUser()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }
}
