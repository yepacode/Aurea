<?php

namespace App\Support;

use App\Mail\BackInStock;
use App\Models\Product;
use App\Models\StockNotification;
use App\Services\PushService;
use Illuminate\Support\Facades\Mail;

class StockNotifier
{
    /**
     * Si el producto tiene stock y hay solicitudes pendientes, envía los avisos
     * y las marca como notificadas. Seguro de llamar múltiples veces (idempotente
     * porque marca notified_at).
     */
    public static function check(Product $product): void
    {
        if (! $product->hasStock()) {
            return;
        }

        $pending = StockNotification::where('product_id', $product->id)
            ->pending()
            ->get();

        if ($pending->isEmpty()) {
            return;
        }

        $emails = [];

        foreach ($pending as $notification) {
            try {
                Mail::to($notification->email)->send(new BackInStock($product));
                $notification->update(['notified_at' => now()]);
                $emails[] = $notification->email;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Además del correo: si algún email tiene push activo, mandamos también
        // una notificación push (no bloquea si falla el envío).
        if (! empty($emails)) {
            try {
                app(PushService::class)->sendToEmails($emails, [
                    'title' => '✨ Volvió a estar disponible',
                    'body'  => $product->name . ' — corre antes de que se agote otra vez.',
                    'url'   => route('products.show', ['slug' => $product->slug]),
                    'image' => ! empty($product->images) ? asset('storage/' . $product->images[0]) : null,
                    'tag'   => 'back-in-stock-' . $product->id,
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
