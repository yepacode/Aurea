<?php

namespace App\Support;

use App\Mail\BackInStock;
use App\Models\Product;
use App\Models\StockNotification;
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

        foreach ($pending as $notification) {
            try {
                Mail::to($notification->email)->send(new BackInStock($product));
                $notification->update(['notified_at' => now()]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
