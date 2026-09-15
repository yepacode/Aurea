<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionRenewed;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionDelivery;
use App\Services\CheckoutService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GenerateSubscriptionOrders extends Command
{
    protected $signature = 'subscriptions:generate {--dry-run : Solo muestra qué suscripciones renovaría, sin crear pedidos}';

    protected $description = 'Genera automáticamente los pedidos recurrentes de las suscripciones activas cuya próxima entrega ya se cumplió.';

    public function handle(CheckoutService $checkout): int
    {
        $dry = (bool) $this->option('dry-run');

        $due = Subscription::query()
            ->with(['customer', 'plan.products'])
            ->due(now())
            ->get();

        if ($due->isEmpty()) {
            $this->info('No hay suscripciones para generar.');
            return self::SUCCESS;
        }

        $this->info(($dry ? '[DRY-RUN] ' : '') . "Procesando {$due->count()} suscripción(es)...");

        $ok = 0;
        $fail = 0;

        foreach ($due as $subscription) {
            try {
                if ($dry) {
                    $this->line(sprintf(
                        '  · Sub #%d — cliente %s — plan %s — venció %s',
                        $subscription->id,
                        $subscription->customer->email ?? 'sin cliente',
                        $subscription->plan->name ?? '?',
                        optional($subscription->next_delivery_at)->toDateTimeString() ?? '-'
                    ));
                    continue;
                }

                $order = $this->generateOrder($subscription, $checkout);
                $ok++;

                $this->line("  ✓ Sub #{$subscription->id} → pedido #{$order->id}");
            } catch (\Throwable $e) {
                $fail++;
                report($e);
                $this->error("  ✗ Sub #{$subscription->id}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Listo. Generados: {$ok}. Fallidos: {$fail}.");
        return $fail > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function generateOrder(Subscription $subscription, CheckoutService $checkout): Order
    {
        return DB::transaction(function () use ($subscription, $checkout) {
            $plan = $subscription->plan;
            $products = $plan->products; // BelongsToMany con pivot quantity

            // Snapshot de dirección: preferimos el JSON guardado en la subscription;
            // si no hay, usamos los campos del cliente.
            $addr = $subscription->shipping_address_json ?: [
                'name'     => $subscription->customer->name,
                'phone'    => $subscription->customer->phone,
                'address'  => $subscription->customer->address,
                'city'     => $subscription->customer->city,
                'state'    => $subscription->customer->state,
                'zip_code' => $subscription->customer->zip_code,
            ];
            $shippingAddressText = implode(', ', array_filter([
                $addr['address'] ?? null,
                $addr['city'] ?? null,
                $addr['state'] ?? null,
                $addr['zip_code'] ?? null,
            ]));

            // Cálculo de totales: usamos el base_price del plan como total del ritual
            // (el descuento de suscriptora ya viene aplicado ahí). No cobramos envío
            // extra en las renovaciones — la cliente ya "compró" el ritual.
            $subtotal = 0.0;
            $itemsPayload = [];
            foreach ($products as $product) {
                $qty = (int) ($product->pivot->quantity ?? 1);
                $unit = (float) $product->price;
                $subtotal += $unit * $qty;
                $itemsPayload[] = [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'qty'        => $qty,
                    'unit_price' => $unit,
                    'total'      => $unit * $qty,
                ];
            }

            $planTotal = (float) $plan->base_price;
            // Prorrateamos el descuento de suscriptora contra la suma de precios.
            $discount = max(0, round($subtotal - $planTotal, 2));
            $total = max(0, round($subtotal - $discount, 2));

            $paymentMethod = $subscription->payment_method ?: 'epayco';
            // Prepago = ya pagado en pasarela recurrente futura; contra entrega = pending.
            $paymentStatus = $paymentMethod === 'cash_on_delivery' ? 'pending' : 'paid';

            $order = Order::create([
                'customer_id'      => $subscription->customer_id,
                'status'           => 'pending',
                'subtotal'         => $subtotal,
                'shipping'         => 0,
                'discount_amount'  => $discount,
                'discount_2x1'     => 0,
                'discount_coupon'  => $discount,
                'total'            => $total,
                'payment_method'   => $paymentMethod,
                'payment_status'   => $paymentStatus,
                'shipping_address' => $shippingAddressText,
                'notes'            => "Pedido recurrente — Plan {$plan->name} (Suscripción #{$subscription->id})",
                'is_subscription'  => true,
                'subscription_id'  => $subscription->id,
            ]);

            foreach ($itemsPayload as $it) {
                $order->items()->create($it);
            }

            // Descuento de stock idempotente (CheckoutService ya marca stock_decremented_at).
            $checkout->decrementStockForOrder($order);

            // Actualizamos el estado de la suscripción para el siguiente ciclo.
            $subscription->update([
                'last_delivery_at' => now(),
                'next_delivery_at' => now()->addDays((int) $plan->interval_days),
                'total_deliveries' => $subscription->total_deliveries + 1,
            ]);

            SubscriptionDelivery::create([
                'subscription_id' => $subscription->id,
                'order_id'        => $order->id,
                'scheduled_at'    => now(),
                'generated_at'    => now(),
                'status'          => 'generated',
                'notes'           => null,
            ]);

            // Correo al cliente (queued).
            try {
                Mail::to($subscription->customer->email)
                    ->send(new SubscriptionRenewed($subscription, $order));
            } catch (\Throwable $e) {
                report($e);
            }

            return $order;
        });
    }
}
