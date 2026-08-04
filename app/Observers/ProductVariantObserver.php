<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Support\StockNotifier;

class ProductVariantObserver
{
    public function updated(ProductVariant $variant): void
    {
        if ($variant->wasChanged('stock') && $variant->product) {
            StockNotifier::check($variant->product->fresh());
        }
    }
}
