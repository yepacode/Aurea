<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = ['customer_id', 'items', 'subtotal', 'reminded_at'];

    protected $casts = [
        'items'        => 'array',
        'subtotal'     => 'decimal:2',
        'reminded_at'  => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
