<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    ];

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
