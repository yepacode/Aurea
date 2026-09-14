<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'status',
        'subtotal',
        'shipping',
        'total',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_receipt',
        'payment_transaction_id',
        'payment_response_code',
        'payment_response_reason',
        'payment_franchise',
        'payment_bank',
        'payment_authorization',
        'payment_raw_response',
        'stripe_payment_intent_id',
        'discount_code',
        'discount_amount',
        'discount_2x1',
        'discount_coupon',
        'shipping_address',
        'shipping_carrier',
        'shipping_zone_name',
        'shipping_delivery_days_min',
        'shipping_delivery_days_max',
        'tracking_number',
        'tracking_url',
        'notes',
        'tracking_token',
        'stock_decremented_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->tracking_token) {
                $order->tracking_token = Str::random(48);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'discount_2x1' => 'decimal:2',
            'discount_coupon' => 'decimal:2',
            'total' => 'decimal:2',
            'stock_decremented_at' => 'datetime',
            'payment_raw_response' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
