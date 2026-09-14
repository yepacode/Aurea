<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Guarda las IDs de analítica (GA4 y Meta Pixel) que el admin captura
 * desde el panel. AppServiceProvider las inyecta en config('services.analytics.*')
 * al arrancar para que las vistas las lean con config() como cualquier otro
 * servicio, permitiendo también un fallback al .env.
 */
class AnalyticsSetting extends Model
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
}
