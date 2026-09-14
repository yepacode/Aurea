<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una referida del programa "Recomienda y gana".
 * Se crea en pending al momento del registro y pasa a completed cuando la
 * referida completa su primera compra pagada.
 */
class Referral extends Model
{
    protected $fillable = [
        'referrer_customer_id',
        'referred_customer_id',
        'first_order_id',
        'status',
        'reward_referrer_points',
        'reward_referred_points',
    ];

    protected function casts(): array
    {
        return [
            'reward_referrer_points' => 'integer',
            'reward_referred_points' => 'integer',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referrer_customer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }

    public function firstOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'first_order_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
