<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id', 'label', 'recipient', 'phone',
        'address', 'city', 'state', 'zip_code', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Dirección en una línea para mostrar. */
    public function getOneLineAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address, $this->city, $this->state, $this->zip_code,
        ]));
    }
}
