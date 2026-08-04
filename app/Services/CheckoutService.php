<?php

namespace App\Services;

use App\Mail\OrderAdminNotification;
use App\Mail\OrderConfirmation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutService
{
    public function __construct(
        private CartService $cart,
    ) {}

    /**
     * Process checkout: create customer, order, order items, clear cart.
     *
     * @param  array{name: string, email: string, phone?: string, address: string, city: string, state: string, zip_code: string, payment_method: string, notes?: string}  $data
     */
    public function process(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $customer = $this->findOrCreateCustomer($data);
            $order = $this->createOrder($customer, $data);
            $this->createOrderItems($order);
            $this->cart->clear();

            return $order;
        });

        $order->load('items.product', 'items.variant', 'customer');

        try {
            Mail::to($order->customer->email)->send(new OrderConfirmation($order));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to(config('mail.admin'))->send(new OrderAdminNotification($order));
        } catch (\Throwable $e) {
            report($e);
        }

        return $order;
    }

    /**
     * Find existing customer by email or create a new one.
     */
    private function findOrCreateCustomer(array $data): Customer
    {
        // Si hay un cliente con sesión iniciada, el pedido SIEMPRE se enlaza a
        // su cuenta (no cambiamos su email de cuenta, solo sus datos de envío).
        $authCustomer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
        if ($authCustomer) {
            $authCustomer->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? $authCustomer->phone,
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'],
                'zip_code' => $data['zip_code'],
            ]);

            return $authCustomer;
        }

        return Customer::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'],
                'zip_code' => $data['zip_code'],
            ],
        );
    }

    /**
     * Create the order record.
     */
    private function createOrder(Customer $customer, array $data): Order
    {
        $shippingAddress = implode(', ', array_filter([
            $data['address'],
            $data['city'] ?? null,
            $data['state'],
            $data['zip_code'],
        ]));

        $subtotal = $this->cart->getSubtotal();
        $discountAmount = (float) ($data['discount_amount'] ?? 0);
        $discount2x1 = (float) ($data['discount_2x1'] ?? 0);
        $discountCoupon = max(0, $discountAmount - $discount2x1);
        $shipping = $this->cart->getShipping($data['state'] ?? null, $discountCoupon);
        $total = max(0, $subtotal - $discountAmount + $shipping);

        return Order::create([
            'customer_id' => $customer->id,
            'status' => $data['order_status'] ?? 'pending',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount_code' => $data['discount_code'] ?? null,
            'discount_amount' => $discountAmount,
            'discount_2x1' => $discount2x1,
            'discount_coupon' => $discountCoupon,
            'total' => $total,
            'payment_method' => $data['payment_method'],
            'payment_status' => $data['payment_status'] ?? (($data['payment_method'] === 'card') ? 'processing' : 'pending'),
            'stripe_payment_intent_id' => $data['stripe_payment_intent_id'] ?? null,
            'shipping_address' => $shippingAddress,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Create order items from cart contents and decrement inventory.
     * Esta funcion corre dentro de la transaccion del checkout (ver process()),
     * asi que cualquier fallo revierte tanto la orden como los descuentos de stock.
     */
    private function createOrderItems(Order $order): void
    {
        foreach ($this->cart->getItems() as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);

            $this->decrementInventory(
                productId: (int) $item['product_id'],
                variantId: $item['variant_id'] ? (int) $item['variant_id'] : null,
                qty: (int) $item['qty'],
            );
        }
    }

    /**
     * Restar stock al cerrar la orden. Si el item tiene variante, se descuenta
     * de la variante; si no, del stock del producto. Usamos lockForUpdate para
     * evitar carreras entre dos checkouts simultaneos del mismo SKU. No bajamos
     * de 0 (en caso de oversell por race condition, se queda en 0 y queda visible
     * al admin).
     */
    private function decrementInventory(int $productId, ?int $variantId, int $qty): void
    {
        if ($qty <= 0) {
            return;
        }

        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)->lockForUpdate()->first();
            if ($variant) {
                $newStock = max(0, (int) $variant->stock - $qty);
                $variant->update(['stock' => $newStock]);
                return;
            }
        }

        $product = Product::where('id', $productId)->lockForUpdate()->first();
        if ($product) {
            $newStock = max(0, (int) $product->stock - $qty);
            $product->update(['stock' => $newStock]);
        }
    }
}
