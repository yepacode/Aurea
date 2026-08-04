<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\LentesPageSetting;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingSetting;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function index(): View
    {
        $data = $this->cartData();

        // Trust badges / beneficios — misma fuente que /checkout
        $lentesPage = LentesPageSetting::getCurrent();
        $data['productBenefits'] = $lentesPage->product_benefits ?? [];
        $data['freeThreshold'] = (float) ShippingSetting::get('free_shipping_threshold', 999);

        return view('storefront.cart', $data);
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'qty' => 'integer|min:1|max:10000',
        ]);

        $productId = (int) $validated['product_id'];
        $variantId = isset($validated['variant_id']) ? (int) $validated['variant_id'] : null;
        $qty = (int) ($validated['qty'] ?? 1);

        // Stock = stock disponible − qty ya en el carrito para el mismo SKU.
        $availableStock = $this->stockFor($productId, $variantId);
        $alreadyInCart = $this->qtyInCart($productId, $variantId);
        $remaining = max(0, $availableStock - $alreadyInCart);

        if ($qty > $remaining) {
            $msg = $remaining > 0
                ? "Solo quedan {$remaining} unidad(es) disponibles."
                : 'Este producto no tiene stock disponible.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $this->cart->add($productId, $qty, $variantId);

        if ($request->expectsJson()) {
            return response()->json(array_merge(
                ['message' => 'Producto agregado al carrito.'],
                $this->cartData(),
            ));
        }

        return redirect()->route('cart.index')
            ->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, string $itemId): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:0|max:10000',
        ]);

        $newQty = (int) $validated['qty'];

        // Validar contra stock real (la actualizacion REEMPLAZA la cantidad).
        $items = $this->cart->getItems();
        $item = $items->firstWhere('key', $itemId);

        if ($item && $newQty > 0) {
            $availableStock = $this->stockFor((int) $item['product_id'], $item['variant_id'] ? (int) $item['variant_id'] : null);
            if ($newQty > $availableStock) {
                $msg = $availableStock > 0
                    ? "Solo quedan {$availableStock} unidad(es) disponibles."
                    : 'Sin stock disponible.';
                if ($request->expectsJson()) {
                    return response()->json(array_merge(['message' => $msg], $this->cartData()), 422);
                }
                return redirect()->route('cart.index')->with('error', $msg);
            }
        }

        $this->cart->update($itemId, $newQty);

        if ($request->expectsJson()) {
            return response()->json($this->cartData());
        }

        return redirect()->route('cart.index');
    }

    /**
     * Stock disponible para un producto/variante.
     */
    private function stockFor(int $productId, ?int $variantId): int
    {
        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)->where('is_active', true)->first();
            return $variant ? (int) $variant->stock : 0;
        }
        $product = Product::where('id', $productId)->first();
        return $product ? (int) $product->stock : 0;
    }

    /**
     * Cantidad ya presente en el carrito para el mismo SKU (producto + variante).
     */
    private function qtyInCart(int $productId, ?int $variantId): int
    {
        return (int) $this->cart->getItems()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->sum('qty');
    }

    public function remove(string $itemId): RedirectResponse|JsonResponse
    {
        $this->cart->remove($itemId);

        if (request()->expectsJson()) {
            return response()->json($this->cartData());
        }

        return redirect()->route('cart.index')
            ->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Build the full cart data array (used by JSON responses and the cart page view).
     */
    private function cartData(): array
    {
        $items = $this->cart->getItems();
        $promo = $this->cart->calculate2x1();
        $subtotal = $this->cart->getSubtotal();
        $discount = $promo['discount'];
        $subtotalConDescuento = $subtotal - $discount;

        // Coupon discount from session (calculated against subtotal-after-2x1)
        $couponCode = null;
        $couponDescription = null;
        $couponDiscount = 0;
        $discountCodeId = session('discount_code_id');

        if ($discountCodeId) {
            $discountCode = DiscountCode::find($discountCodeId);
            if ($discountCode && $discountCode->isValid($subtotalConDescuento)) {
                $couponCode = $discountCode->code;
                $couponDiscount = $discountCode->calculateDiscount($subtotalConDescuento);
                $couponDescription = $discountCode->type === 'percentage'
                    ? $discountCode->value . '% de descuento'
                    : '$' . number_format($discountCode->value, 0, ',', '.') . ' de descuento';
            } else {
                session()->forget('discount_code_id');
            }
        }

        // Free-shipping threshold evaluated AFTER 2x1 AND coupon discount.
        $threshold = (float) ShippingSetting::get('free_shipping_threshold', 999);
        $shipping = $this->cart->getShipping(null, $couponDiscount);

        $total = $subtotalConDescuento - $couponDiscount + $shipping;

        return [
            'cart_count' => $this->cart->count(),
            'items' => $items->map(fn ($item) => [
                'key' => $item['key'],
                'name' => $item['product']->name,
                'slug' => $item['product']->slug,
                'image' => $item['product']->images[0] ?? null,
                'variant' => $item['variant']
                    ? trim(($item['variant']->color ?? $item['variant']->value) . ' ' . ($item['variant']->graduation ?? ''))
                    : null,
                'type' => $item['product']->type,
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ])->values(),
            'subtotal' => $subtotal,
            'discount_2x1' => $discount,
            'free_items' => $promo['free_items'],
            'coupon_code' => $couponCode,
            'coupon_description' => $couponDescription,
            'coupon_discount' => $couponDiscount,
            'shipping' => $shipping,
            'free_threshold' => $threshold,
            'total' => max(0, $total),
        ];
    }
}
