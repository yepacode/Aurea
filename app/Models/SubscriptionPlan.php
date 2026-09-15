<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'base_price',
        'regular_price',
        'discount_percent',
        'interval_days',
        'delivery_days_message',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price'       => 'decimal:2',
            'regular_price'    => 'decimal:2',
            'discount_percent' => 'integer',
            'interval_days'    => 'integer',
            'is_active'        => 'boolean',
            'sort_order'       => 'integer',
        ];
    }

    /** Productos incluidos en el plan (con cantidad). */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'subscription_plan_items')
            ->withPivot(['quantity', 'sort_order'])
            ->withTimestamps()
            ->orderBy('subscription_plan_items.sort_order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionPlanItem::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Calcula descuento % automáticamente a partir de regular_price vs base_price.
     * Si el admin lo puso a mano, respeta el valor guardado.
     */
    public function computedDiscountPercent(): int
    {
        if ((int) $this->discount_percent > 0) {
            return (int) $this->discount_percent;
        }
        $reg = (float) $this->regular_price;
        $base = (float) $this->base_price;
        if ($reg <= 0 || $base <= 0 || $base >= $reg) {
            return 0;
        }
        return (int) round((($reg - $base) / $reg) * 100);
    }
}
