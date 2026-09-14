<?php

namespace App\Observers;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltySetting;
use App\Models\Order;

/**
 * Otorga puntos de fidelidad cuando un pedido queda como pagado.
 * Fase 1 del programa: solo ganancia; el canje llega en el próximo sprint.
 */
class OrderObserver
{
    public function updated(Order $order): void
    {
        // Solo actuamos cuando payment_status acaba de cambiar a 'paid'.
        if (! $order->wasChanged('payment_status')) {
            return;
        }

        if ($order->payment_status !== 'paid') {
            return;
        }

        // Sin cliente asociado no hay a quién acreditarle puntos (invitado).
        if (! $order->customer_id) {
            return;
        }

        // Programa apagado desde el panel → no otorgar nada.
        if (! LoyaltySetting::enabled()) {
            return;
        }

        // Idempotencia: si ya existe un movimiento 'earned' para este pedido,
        // asumimos que ya se otorgaron los puntos (evita duplicados si el
        // observer se dispara varias veces por webhooks re-enviados, etc.).
        $already = LoyaltyPoint::where('order_id', $order->id)
            ->where('type', 'earned')
            ->exists();

        if ($already) {
            return;
        }

        $earnRate = LoyaltySetting::earnRate();

        // Se otorgan puntos solo por el gasto real en producto: total - envío.
        // El envío no debe generar loyalty (el cliente no compra envío).
        $base = max(0, ((float) $order->total) - ((float) $order->shipping));
        $points = (int) floor($base * $earnRate);

        if ($points <= 0) {
            return;
        }

        LoyaltyPoint::create([
            'customer_id' => $order->customer_id,
            'points'      => $points,
            'type'        => 'earned',
            'order_id'    => $order->id,
            'note'        => 'Compra #' . $order->id,
            'expires_at'  => now()->addMonths(LoyaltySetting::expiryMonths()),
        ]);
    }
}
