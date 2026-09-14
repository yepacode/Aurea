<?php

namespace App\Observers;

use App\Mail\ReferralConverted;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltySetting;
use App\Models\Order;
use App\Models\Referral;
use Illuminate\Support\Facades\Mail;

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

        // Programa "Recomienda y gana": si esta es la PRIMERA compra pagada
        // del cliente y viene de una referida pendiente, cerrar el ciclo.
        $this->maybeCompleteReferral($order);
    }

    /**
     * Cierra el ciclo de referida cuando el cliente completa su primera compra
     * pagada. Idempotente: si el referral ya está completed, no hace nada.
     */
    private function maybeCompleteReferral(Order $order): void
    {
        $customer = $order->customer;
        if (! $customer || ! $customer->referred_by_customer_id) {
            return;
        }

        // ¿Es la PRIMERA orden pagada de este cliente?
        $paidCount = Order::where('customer_id', $customer->id)
            ->where('payment_status', 'paid')
            ->where('id', '<=', $order->id) // incluye la actual
            ->count();

        if ($paidCount > 1) {
            return; // ya había pagado antes; nada que hacer
        }

        // Buscar el registro de referral pendiente para este cliente.
        $referral = Referral::where('referred_customer_id', $customer->id)->first();
        if (! $referral || $referral->status === 'completed') {
            return;
        }

        $referrer = $referral->referrer;
        if (! $referrer) {
            return;
        }

        // Marcar completed + guardar la orden que gatilló la recompensa.
        $referral->update([
            'status'         => 'completed',
            'first_order_id' => $order->id,
        ]);

        $expiry = now()->addMonths(LoyaltySetting::expiryMonths());

        // Puntos a la referrer (quien invitó).
        LoyaltyPoint::create([
            'customer_id' => $referrer->id,
            'points'      => (int) $referral->reward_referrer_points,
            'type'        => 'earned',
            'order_id'    => $order->id,
            'note'        => 'Recomendaste a ' . ($customer->name ?: 'una amiga') . ' — ¡ganaste!',
            'expires_at'  => $expiry,
        ]);

        // Puntos a la referida (bienvenida).
        LoyaltyPoint::create([
            'customer_id' => $customer->id,
            'points'      => (int) $referral->reward_referred_points,
            'type'        => 'earned',
            'order_id'    => $order->id,
            'note'        => 'Bienvenida — te referió ' . ($referrer->name ?: 'una amiga'),
            'expires_at'  => $expiry,
        ]);

        // Notificar a quien invitó (best-effort; no revienta el observer si falla).
        try {
            if ($referrer->email) {
                Mail::to($referrer->email)->queue(new ReferralConverted($referral));
            }
        } catch (\Throwable $e) {
            \Log::warning('ReferralConverted mail no enviado', [
                'referral_id' => $referral->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
