<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Almacén key-value para los tokens de la API de Mercado Libre
 * (ml_access_token, ml_refresh_token, ml_expires_at, ml_user_id).
 * Mismo patrón simple que AnalyticsSetting.
 */
class MercadoLibreSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );
    }
}
