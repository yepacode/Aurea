<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ShippingSetting;
use Illuminate\Support\Collection;

/**
 * Sugerencias para el drawer del carrito (up-sell / cross-sell).
 *
 * Lógica:
 *   - Si aún falta $X para envío gratis: priorizar productos "baratos" que
 *     el cliente pueda añadir para llegar (rango $10.000 – $30.000).
 *   - Si ya pasó el umbral (o no hay umbral): priorizar productos de la misma
 *     categoría que los que ya están en el carrito (cross-sell complementario).
 *   - En cualquier caso se excluye lo que ya está en el carrito, se exige
 *     stock e imagen, y se devuelve un máximo de N productos.
 */
class UpsellService
{
    public function __construct(private CartService $cart) {}

    /**
     * Devuelve hasta $max productos sugeridos para el carrito actual.
     * Si el carrito está vacío, devuelve colección vacía.
     */
    public function suggestForCart(int $max = 3): Collection
    {
        $items = $this->cart->getItems();

        if ($items->isEmpty()) {
            return collect();
        }

        $cartProductIds = $items->pluck('product_id')->unique()->all();
        $cartCategoryIds = $items
            ->map(fn ($i) => $i['product']->category_id ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $threshold = (float) ShippingSetting::get('free_shipping_threshold', 0);
        $subtotal = $this->cart->getSubtotal();
        $missingForFree = $threshold > 0 ? max(0, $threshold - $subtotal) : 0;

        $base = fn () => Product::active()
            ->where(function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock', '>', 0));
            })
            ->whereNotIn('id', $cartProductIds)
            ->whereNotNull('images')
            ->whereRaw('JSON_LENGTH(images) > 0');

        $suggestions = collect();

        // 1) Aún falta para envío gratis → productos baratos que sumen (idealmente $10k-$30k).
        if ($missingForFree > 0) {
            $suggestions = $base()
                ->whereBetween('price', [10000, 30000])
                ->orderBy('price')
                ->take($max)
                ->get();
        }

        // 2) Complementarios: misma categoría que lo que ya hay en el carrito.
        if ($suggestions->count() < $max && ! empty($cartCategoryIds)) {
            $need = $max - $suggestions->count();
            $exclude = $suggestions->pluck('id')->all();
            $catFill = $base()
                ->whereIn('category_id', $cartCategoryIds)
                ->when($exclude, fn ($q) => $q->whereNotIn('id', $exclude))
                ->orderBy('price')
                ->take($need)
                ->get();
            $suggestions = $suggestions->concat($catFill);
        }

        // 3) Cualquier producto activo — precio ascendente para que sean opciones "add-on".
        if ($suggestions->count() < $max) {
            $need = $max - $suggestions->count();
            $exclude = $suggestions->pluck('id')->all();
            $fill = $base()
                ->when($exclude, fn ($q) => $q->whereNotIn('id', $exclude))
                ->orderBy('price')
                ->take($need)
                ->get();
            $suggestions = $suggestions->concat($fill);
        }

        return $suggestions->take($max)->values();
    }

    /**
     * Serializa las sugerencias en el formato que espera el drawer (JSON).
     */
    public function suggestForCartJson(int $max = 3): array
    {
        return $this->suggestForCart($max)
            ->map(fn ($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'slug'       => $p->slug,
                'image'      => $p->images[0] ?? null,
                'price'      => (float) $p->price,
                'price_fmt'  => '$' . number_format($p->price, 0, ',', '.'),
            ])
            ->all();
    }
}
