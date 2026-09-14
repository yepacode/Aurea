<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Movimiento de puntos de fidelidad para un cliente.
 * points > 0 → suma al balance (earned/adjusted).
 * points < 0 → resta del balance (redeemed/expired/adjusted).
 */
class LoyaltyPoint extends Model
{
    protected $fillable = [
        'customer_id',
        'points',
        'type',
        'order_id',
        'note',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'points'     => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeEarned(Builder $query): Builder
    {
        return $query->where('type', 'earned');
    }

    public function scopeRedeemed(Builder $query): Builder
    {
        return $query->where('type', 'redeemed');
    }

    /**
     * Movimientos que aún cuentan para el balance:
     * si tienen fecha de expiración, que sea futura; sin fecha = nunca expira.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}
