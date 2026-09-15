<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'customer_id',
        'subscription_plan_id',
        'status',
        'next_delivery_at',
        'last_delivery_at',
        'total_deliveries',
        'payment_method',
        'shipping_address_json',
        'cancelled_at',
        'pause_reason',
    ];

    protected function casts(): array
    {
        return [
            'next_delivery_at'       => 'datetime',
            'last_delivery_at'       => 'datetime',
            'cancelled_at'           => 'datetime',
            'total_deliveries'       => 'integer',
            'shipping_address_json'  => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(SubscriptionDelivery::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeDue(Builder $q, $when = null): Builder
    {
        return $q->where('status', 'active')
            ->where('next_delivery_at', '<=', $when ?: now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function next(): ?\Illuminate\Support\Carbon
    {
        return $this->next_delivery_at;
    }

    public function pause(?string $reason = null): void
    {
        $this->update([
            'status'       => 'paused',
            'pause_reason' => $reason,
        ]);
    }

    public function resume(): void
    {
        // Al reanudar, si la próxima entrega ya quedó en el pasado por la pausa,
        // la reprogramamos a hoy+interval del plan.
        $update = ['status' => 'active', 'pause_reason' => null];
        if ($this->next_delivery_at && $this->next_delivery_at->isPast()) {
            $days = (int) ($this->plan->interval_days ?? 30);
            $update['next_delivery_at'] = now()->addDays($days);
        }
        $this->update($update);
    }

    public function cancel(): void
    {
        $this->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
