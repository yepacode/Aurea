<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPageSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getCurrent(): static
    {
        return static::where('is_active', true)->latest()->first()
            ?? static::create([]);
    }

    /**
     * Cache de URL WhatsApp por request para evitar reconsultar el modelo
     * cada vez que el blade lo invoca.
     */
    private static ?string $cachedWhatsappUrl = null;

    /**
     * URL completa de WhatsApp para botones flotantes/CTAs.
     * Construida a partir de whatsapp_number y whatsapp_message del admin,
     * con fallback a los valores históricos si están vacíos.
     */
    public static function whatsappUrl(): string
    {
        if (self::$cachedWhatsappUrl !== null) {
            return self::$cachedWhatsappUrl;
        }

        $page = static::getCurrent();

        $number = preg_replace('/\D/', '', $page->whatsapp_number ?? '');
        if ($number === '') {
            // Fallback a config/legal.php (ignora el placeholder entre corchetes)
            $legal = (string) config('legal.whatsapp');
            $number = \Illuminate\Support\Str::startsWith(trim($legal), '[') ? '' : preg_replace('/\D/', '', $legal);
        }

        $message = trim((string) ($page->whatsapp_message ?? ''));
        if ($message === '') {
            $message = '¡Hola! Me gustaría más información sobre los productos de Belleza Áurea 💛';
        }

        $base = $number !== '' ? 'https://wa.me/' . $number : 'https://wa.me/';

        return self::$cachedWhatsappUrl = $base . '?text=' . rawurlencode($message);
    }
}
