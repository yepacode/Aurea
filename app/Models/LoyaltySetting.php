<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configuración global del programa de puntos.
 * Patrón key/value idéntico a AnalyticsSetting: leer con get() y escribir con set().
 */
class LoyaltySetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    public static function enabled(): bool
    {
        return (bool) static::get('enabled', '1');
    }

    public static function earnRate(): float
    {
        return (float) static::get('earn_rate', 0.001);
    }

    public static function expiryMonths(): int
    {
        return (int) static::get('points_expiry_months', 12);
    }

    public static function minRedemption(): int
    {
        return (int) static::get('min_redemption', 500);
    }
}
