<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'is_wholesaler',
        'wholesaler_status',
        'wholesaler_company_name',
        'wholesaler_nit',
        'wholesaler_monthly_volume',
        'wholesaler_requested_at',
        'wholesaler_approved_at',
        'wholesaler_notes',
        'referral_code',
        'referred_by_customer_id',
        'referral_source',
    ];

    /**
     * Al crear: asegurar un código de referida único (formato 3 letras + 4 dígitos).
     */
    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->referral_code)) {
                $customer->referral_code = static::generateUniqueReferralCode($customer->name ?? '');
            }
        });
    }

    /**
     * Genera un código con 3 letras del nombre + 4 dígitos (ej. "YEP1234").
     * Reintenta hasta encontrar uno libre en la tabla.
     */
    public static function generateUniqueReferralCode(string $seedName = ''): string
    {
        // 3 letras: primeras del nombre limpio; si no hay suficientes, se rellena aleatorio.
        $letters = Str::of($seedName)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z]/', '')
            ->substr(0, 3)
            ->toString();

        while (strlen($letters) < 3) {
            $letters .= chr(random_int(ord('A'), ord('Z')));
        }

        // Hasta 10 intentos con 4 dígitos aleatorios; luego cae a random_bytes.
        for ($i = 0; $i < 10; $i++) {
            $candidate = $letters . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            if (! static::where('referral_code', $candidate)->exists()) {
                return $candidate;
            }
        }

        // Fallback ultra-defensivo.
        return $letters . strtoupper(Str::random(6));
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'password'                  => 'hashed',
            'is_wholesaler'             => 'boolean',
            'wholesaler_requested_at'   => 'datetime',
            'wholesaler_approved_at'    => 'datetime',
        ];
    }

    /**
     * ¿Es un mayorista APROBADO y activo? (los dos flags a la vez).
     * Este es el único check que debe usarse en storefront/cart.
     */
    public function isApprovedWholesaler(): bool
    {
        return $this->is_wholesaler && $this->wholesaler_status === 'approved';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class)->orderByDesc('is_default')->latest();
    }

    public function wishlist(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')->withTimestamps();
    }

    /**
     * Todos los movimientos de puntos de fidelidad del cliente.
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Balance de puntos disponibles: suma solo los movimientos activos
     * (los que no han expirado). Fase 1 solo suma "earned", pero dejamos
     * la fórmula genérica para que redeemed/expired/adjusted resten cuando
     * el sprint de canje entre en línea.
     */
    public function pointsBalance(): int
    {
        return (int) $this->loyaltyPoints()->active()->sum('points');
    }

    /**
     * Puntos ganados en total (histórico, incluye expirados).
     */
    public function pointsEarnedTotal(): int
    {
        return (int) $this->loyaltyPoints()->earned()->sum('points');
    }

    /**
     * Puntos ya canjeados por el cliente (valor absoluto positivo).
     */
    public function pointsRedeemedTotal(): int
    {
        return (int) abs((int) $this->loyaltyPoints()->redeemed()->sum('points'));
    }

    public function hasInWishlist(int $productId): bool
    {
        return $this->wishlist()->where('products.id', $productId)->exists();
    }

    public function defaultAddress(): ?CustomerAddress
    {
        return $this->addresses()->where('is_default', true)->first()
            ?? $this->addresses()->first();
    }

    /**
     * Referidas que esta cliente HIZO (personas que se registraron con su código).
     */
    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_customer_id');
    }

    /**
     * Registro de MI propia relación como referida (si alguien me refirió).
     */
    public function referralReceived(): HasMany
    {
        return $this->hasMany(Referral::class, 'referred_customer_id');
    }

    /**
     * La cliente que me refirió (si existe).
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_by_customer_id');
    }

    /** ¿Tiene cuenta con contraseña? (los invitados no). */
    public function hasAccount(): bool
    {
        return ! empty($this->password);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomerResetPasswordNotification($token));
    }
}
