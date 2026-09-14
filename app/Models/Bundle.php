<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Bundle extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'price',
        'compare_price',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'price'         => 'integer',
            'compare_price' => 'integer',
            'is_active'     => 'boolean',
            'sort_order'    => 'integer',
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Bundle $bundle) {
            if (! $bundle->slug) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }

    // ── Relations ──

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_items')
            ->withPivot(['quantity', 'sort_order'])
            ->orderBy('bundle_items.sort_order')
            ->withTimestamps();
    }

    // ── Scopes ──

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    // ── Accessors ──

    public function getSavingsAttribute(): int
    {
        return max(0, (int) $this->compare_price - (int) $this->price);
    }

    public function getSavingsPercentAttribute(): int
    {
        if (! $this->compare_price || $this->compare_price <= 0) {
            return 0;
        }
        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
