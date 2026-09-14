<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Suscripción de un navegador a las notificaciones push (VAPID).
 * Puede estar ligada a un customer o a una sesión anónima.
 */
class PushSubscription extends Model
{
    protected $fillable = [
        'customer_id',
        'session_id',
        'endpoint',
        'public_key',
        'auth_token',
        'user_agent',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
