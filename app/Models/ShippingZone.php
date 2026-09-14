<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'carrier',
        'base_cost',
        'extra_kg_cost',
        'delivery_days_min',
        'delivery_days_max',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'base_cost'          => 'integer',
        'extra_kg_cost'      => 'integer',
        'delivery_days_min'  => 'integer',
        'delivery_days_max'  => 'integer',
        'is_active'          => 'boolean',
        'sort_order'         => 'integer',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(ShippingZoneDepartment::class);
    }

    /**
     * Devuelve el label legible de la transportadora (el que va al pedido
     * y a los correos). Cae a "Otro" si el slug no está en config.
     */
    public function carrierLabel(): string
    {
        return (string) (config('shipping.carriers.' . $this->carrier) ?? 'Otro');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Primera zona ACTIVA que contiene el departamento dado, ordenada por
     * sort_order (empate → id asc). Devuelve null si el departamento no está
     * asignado a ninguna zona activa.
     */
    public static function findForDepartment(?string $department): ?self
    {
        if (! $department) {
            return null;
        }

        $needle = trim($department);

        return static::active()
            ->whereHas('departments', function ($q) use ($needle) {
                $q->whereRaw('LOWER(department) = ?', [mb_strtolower($needle)]);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }
}
