<?php

namespace App\Observers;

use App\Models\Product;
use App\Support\StockNotifier;

class ProductObserver
{
    public function updated(Product $product): void
    {
        // Solo si cambió el stock y ahora hay disponibilidad.
        if ($product->wasChanged('stock') && $product->hasStock()) {
            StockNotifier::check($product);
        }
    }
}
