<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = ['city', 'state', 'price', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function findForCity(?string $city): ?self
    {
        if (! $city) {
            return null;
        }

        return static::active()
            ->whereRaw('LOWER(city) = ?', [mb_strtolower(trim($city))])
            ->first();
    }

    public static function findForState(?string $state): ?self
    {
        if (! $state) {
            return null;
        }

        return static::active()
            ->whereRaw('LOWER(state) = ?', [mb_strtolower(trim($state))])
            ->first();
    }
}
