<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ContactPageSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active'               => 'boolean',
        'whatsapp_widget_enabled' => 'boolean',
        'whatsapp_hours_json'     => 'array',
    ];

    /** Días en el orden Lunes→Domingo (Carbon dayOfWeekIso: 1..7). */
    public const DAY_KEYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    public const DAY_LABELS_ES = [
        'mon' => 'Lunes',
        'tue' => 'Martes',
        'wed' => 'Miércoles',
        'thu' => 'Jueves',
        'fri' => 'Viernes',
        'sat' => 'Sábado',
        'sun' => 'Domingo',
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

    /** Solo el número (dígitos), útil para armar URLs propias en el widget. */
    public function whatsappDigits(): string
    {
        $number = preg_replace('/\D/', '', $this->whatsapp_number ?? '');
        if ($number === '') {
            $legal = (string) config('legal.whatsapp');
            $number = \Illuminate\Support\Str::startsWith(trim($legal), '[') ? '' : preg_replace('/\D/', '', $legal);
        }
        return (string) $number;
    }

    /** Mensaje pre-llenado por defecto para el link de WhatsApp. */
    public function whatsappPredefinedMessage(): string
    {
        $message = trim((string) ($this->whatsapp_message ?? ''));
        return $message !== ''
            ? $message
            : '¡Hola! Me gustaría más información sobre los productos de Belleza Áurea 💛';
    }

    /**
     * Horario normalizado: array asociativo por día con enabled/open/close.
     * Fusiona lo guardado con defaults sensatos (L-V 8-18, S 9-13, Dom cerrado).
     *
     * @return array<string, array{enabled: bool, open: string, close: string}>
     */
    public function whatsappHours(): array
    {
        $defaults = [
            'mon' => ['enabled' => true,  'open' => '08:00', 'close' => '18:00'],
            'tue' => ['enabled' => true,  'open' => '08:00', 'close' => '18:00'],
            'wed' => ['enabled' => true,  'open' => '08:00', 'close' => '18:00'],
            'thu' => ['enabled' => true,  'open' => '08:00', 'close' => '18:00'],
            'fri' => ['enabled' => true,  'open' => '08:00', 'close' => '18:00'],
            'sat' => ['enabled' => true,  'open' => '09:00', 'close' => '13:00'],
            'sun' => ['enabled' => false, 'open' => '00:00', 'close' => '00:00'],
        ];

        $stored = is_array($this->whatsapp_hours_json) ? $this->whatsapp_hours_json : [];

        $out = [];
        foreach (self::DAY_KEYS as $day) {
            $slot = $stored[$day] ?? [];
            $out[$day] = [
                'enabled' => (bool) ($slot['enabled'] ?? $defaults[$day]['enabled']),
                'open'    => (string) ($slot['open']  ?? $defaults[$day]['open']),
                'close'   => (string) ($slot['close'] ?? $defaults[$day]['close']),
            ];
        }
        return $out;
    }

    /** ¿Está en línea AHORA según horario (America/Bogota)? */
    public function isWhatsappOnline(?Carbon $now = null): bool
    {
        $now = $now ? $now->copy()->setTimezone('America/Bogota')
                    : Carbon::now('America/Bogota');

        // dayOfWeekIso: 1=Lunes ... 7=Domingo
        $key = self::DAY_KEYS[$now->dayOfWeekIso - 1] ?? 'mon';
        $hours = $this->whatsappHours()[$key];

        if (! $hours['enabled']) {
            return false;
        }

        $current = $now->format('H:i');
        return $current >= $hours['open'] && $current < $hours['close'];
    }

    /** Resumen legible del horario para mostrar en el widget. */
    public function whatsappHoursSummary(): string
    {
        $hours = $this->whatsappHours();

        // Agrupa días consecutivos con el mismo horario
        $groups = [];
        $current = null;
        foreach (self::DAY_KEYS as $day) {
            $h = $hours[$day];
            $sig = $h['enabled'] ? ($h['open'] . '-' . $h['close']) : 'cerrado';
            if ($current && $current['sig'] === $sig) {
                $current['days'][] = $day;
            } else {
                if ($current) $groups[] = $current;
                $current = ['sig' => $sig, 'days' => [$day]];
            }
        }
        if ($current) $groups[] = $current;

        $parts = [];
        foreach ($groups as $g) {
            if ($g['sig'] === 'cerrado') continue;
            $first = self::DAY_LABELS_ES[$g['days'][0]];
            $last  = self::DAY_LABELS_ES[end($g['days'])];
            $range = count($g['days']) === 1 ? $first : ($first . ' a ' . $last);
            $parts[] = $range . ' ' . $g['sig'];
        }
        return $parts ? implode(' · ', $parts) : 'Cerrado';
    }

    public function whatsappWelcomeOnline(): string
    {
        $t = trim((string) $this->whatsapp_welcome_online);
        return $t !== '' ? $t : '¡Hola! Somos Belleza Áurea. Escríbenos y te respondemos en minutos.';
    }

    public function whatsappWelcomeOffline(): string
    {
        $t = trim((string) $this->whatsapp_welcome_offline);
        return $t !== '' ? $t : '¡Hola! Ahora estamos fuera de horario. Escríbenos y te respondemos apenas abramos.';
    }
}
