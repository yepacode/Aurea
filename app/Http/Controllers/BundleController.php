<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function index(): View
    {
        $bundles = Bundle::active()
            ->with(['items' => fn ($q) => $q->select('products.id', 'name', 'slug', 'images', 'price')])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('storefront.bundles.index', compact('bundles'));
    }

    public function show(string $slug): View
    {
        $bundle = Bundle::active()
            ->where('slug', $slug)
            ->with(['items'])
            ->firstOrFail();

        return view('storefront.bundles.show', compact('bundle'));
    }

    /**
     * Adds every product of the bundle to the cart and stores a "virtual
     * discount" in the session so the cart page shows the bundle savings
     * as its own line and subtracts them from the total.
     */
    public function add(string $slug): RedirectResponse
    {
        $bundle = Bundle::active()->where('slug', $slug)->with('items')->firstOrFail();

        if ($bundle->items->isEmpty()) {
            return redirect()->route('bundles.show', $slug)
                ->with('error', 'Este kit no tiene productos asignados.');
        }

        // 1. Add each product with its bundled quantity.
        foreach ($bundle->items as $product) {
            $qty = (int) ($product->pivot->quantity ?? 1);
            if ($qty < 1) continue;

            // Cap by stock: don't oversell.
            $available = (int) $product->stock;
            $alreadyInCart = (int) $this->cart->getItems()
                ->where('product_id', $product->id)
                ->where('variant_id', null)
                ->sum('qty');
            $can = max(0, $available - $alreadyInCart);
            if ($can <= 0) {
                return redirect()->route('bundles.show', $slug)
                    ->with('error', "Sin stock disponible para «{$product->name}».");
            }
            $qty = min($qty, $can);

            $this->cart->add($product->id, $qty);
        }

        // 2. Track the bundle discount in session (list, so several bundles can stack).
        $existing = session('bundle_discounts', []);
        $existing[] = [
            'bundle_id' => $bundle->id,
            'slug'      => $bundle->slug,
            'name'      => $bundle->name,
            'amount'    => (int) $bundle->savings,
        ];
        session(['bundle_discounts' => $existing]);

        return redirect()->route('cart.index')
            ->with('success', 'Kit «'.$bundle->name.'» agregado al carrito ✨');
    }

    /**
     * Remove a single bundle-discount line from the cart (products stay).
     */
    public function removeDiscount(int $bundleId): RedirectResponse
    {
        $existing = session('bundle_discounts', []);
        $filtered = array_values(array_filter(
            $existing,
            fn ($d) => (int) ($d['bundle_id'] ?? 0) !== $bundleId,
        ));
        session(['bundle_discounts' => $filtered]);

        return redirect()->route('cart.index')
            ->with('success', 'Descuento del kit eliminado del carrito.');
    }
}
