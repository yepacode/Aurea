<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CheckoutService $checkout,
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $items = $this->cart->getItems();
        $subtotal = $this->cart->getSubtotal();
        $promo = $this->cart->calculate2x1();
        $discount2x1 = $promo['discount'];
        $freeItems = $promo['free_items'];
        $subtotalAfter2x1 = $subtotal - $discount2x1;
        $couponDiscount = $this->getSessionDiscount($subtotalAfter2x1);
        $shipping = $this->cart->getShipping(null, $couponDiscount['amount']);

        // Count eligible lens units to detect odd count (suggest picking another)
        $eligibleLensCount = $items->filter(fn ($item) =>
            $item['product']->badge_2x1
            && $item['product']->hasAnyType(['miopia', 'lectura', 'sin_graduacion'])
        )->sum('qty');

        $freeThreshold = (float) \App\Models\ShippingSetting::get('free_shipping_threshold', 999);
        $lentesPage = \App\Models\LentesPageSetting::getCurrent();

        $authCustomer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
        $prefill = null;
        if ($authCustomer) {
            $addr = $authCustomer->defaultAddress();
            $prefill = [
                'name'     => $authCustomer->name,
                'email'    => $authCustomer->email,
                'phone'    => $addr->phone ?? $authCustomer->phone,
                'address'  => $addr->address ?? $authCustomer->address,
                'city'     => $addr->city ?? $authCustomer->city,
                'state'    => $addr->state ?? $authCustomer->state,
                'zip_code' => $addr->zip_code ?? $authCustomer->zip_code,
            ];
        }

        return view('storefront.checkout', [
            'authCustomer' => $authCustomer,
            'prefill' => $prefill,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount2x1' => $discount2x1,
            'freeItems' => $freeItems,
            'eligibleLensCount' => $eligibleLensCount,
            'shipping' => $shipping,
            'freeThreshold' => $freeThreshold,
            'discount' => $couponDiscount,
            'total' => max(0, $subtotalAfter2x1 - $couponDiscount['amount'] + $shipping),
            'appliedCoupon' => $couponDiscount['code'],
            'productBenefits' => $lentesPage->product_benefits ?? [],
        ]);
    }

    public function calculateShipping(Request $request): JsonResponse
    {
        $request->validate([
            'state' => 'nullable|string|max:100',
        ]);

        $state = $request->input('state');
        $subtotal = $this->cart->getSubtotal();
        $discount2x1 = $this->cart->calculate2x1()['discount'];
        $subtotalAfter2x1 = $subtotal - $discount2x1;
        $couponDiscount = $this->getSessionDiscount($subtotalAfter2x1);
        $shipping = $this->cart->getShipping($state, $couponDiscount['amount']);
        $total = max(0, $subtotalAfter2x1 - $couponDiscount['amount'] + $shipping);

        return response()->json([
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $discountCode = DiscountCode::where('code', $code)->first();

        if (! $discountCode) {
            return response()->json(['message' => 'Código de descuento no encontrado.'], 422);
        }

        $subtotal = $this->cart->getSubtotal();
        $discount2x1 = $this->cart->calculate2x1()['discount'];
        $subtotalAfter2x1 = $subtotal - $discount2x1;

        if (! $discountCode->isValid($subtotalAfter2x1)) {
            $message = 'Código de descuento no válido.';

            if ($discountCode->expires_at?->isPast()) {
                $message = 'Este código ha expirado.';
            } elseif ($discountCode->max_uses !== null && $discountCode->times_used >= $discountCode->max_uses) {
                $message = 'Este código ha alcanzado su límite de usos.';
            } elseif ($discountCode->min_order_amount && $subtotalAfter2x1 < (float) $discountCode->min_order_amount) {
                $message = 'Compra mínima de $' . number_format($discountCode->min_order_amount, 0, ',', '.') . ' requerida.';
            } elseif (! $discountCode->is_active) {
                $message = 'Este código no está activo.';
            }

            return response()->json(['message' => $message], 422);
        }

        $discountAmount = $discountCode->calculateDiscount($subtotalAfter2x1);
        $shipping = $this->cart->getShipping(null, $discountAmount);
        $newTotal = max(0, $subtotalAfter2x1 - $discountAmount + $shipping);

        session(['discount_code_id' => $discountCode->id]);

        return response()->json([
            'success' => true,
            'code' => $discountCode->code,
            'description' => $discountCode->type === 'percentage'
                ? $discountCode->value . '% de descuento'
                : '$' . number_format($discountCode->value, 0, ',', '.') . ' de descuento',
            'discount_amount' => $discountAmount,
            'shipping' => $shipping,
            'new_total' => $newTotal,
        ]);
    }

    public function removeCoupon(): JsonResponse
    {
        session()->forget('discount_code_id');

        $subtotal = $this->cart->getSubtotal();
        $discount2x1 = $this->cart->calculate2x1()['discount'];
        $shipping = $this->cart->getShipping();

        return response()->json([
            'success' => true,
            'shipping' => $shipping,
            'new_total' => max(0, $subtotal - $discount2x1 + $shipping),
        ]);
    }

    public function createPaymentIntent(Request $request): JsonResponse
    {
        // Guard de raíz: si Stripe no está configurado (el caso actual, se usa
        // ePayco), no exponemos el endpoint. Sin este guard, un POST directo
        // pediría crear un PaymentIntent con secret vacío y reventaría con 500.
        if (empty(config('services.stripe.secret'))) {
            return response()->json(['message' => 'Método de pago no disponible.'], 404);
        }

        if ($this->cart->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío.'], 422);
        }

        $subtotal = $this->cart->getSubtotal();
        $discount2x1 = $this->cart->calculate2x1()['discount'];
        $subtotalAfter2x1 = $subtotal - $discount2x1;
        $discount = $this->getSessionDiscount($subtotalAfter2x1);
        $shipping = $this->cart->getShipping($request->input('state'), $discount['amount']);
        $total = max(0, $subtotalAfter2x1 - $discount['amount'] + $shipping);

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => (int) round($total * 100),
            'currency' => 'cop',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'cart_total' => $total,
                'discount_2x1' => $discount2x1,
                'discount_code' => $discount['code'] ?? '',
                'discount_amount' => $discount['amount'],
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function process(Request $request): RedirectResponse|JsonResponse
    {
        // Métodos válidos: solo 'card' si Stripe está configurado; siempre los otros.
        // 'transfer' solo se admite si el admin ya cargó datos bancarios — sin cuenta destino
        // el cliente no puede completar el pago, así que el checkout no lo ofrece.
        $allowedMethods = ['cash_on_delivery', 'epayco'];
        if (! empty(trim((string) \App\Models\BankTransferSetting::get('account_number', '')))) {
            $allowedMethods[] = 'transfer';
        }
        if (! empty(config('services.stripe.secret'))) {
            $allowedMethods[] = 'card';
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:10',
            'payment_method' => 'required|in:'.implode(',', $allowedMethods),
            'stripe_payment_intent_id' => 'required_if:payment_method,card|nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $subtotal = $this->cart->getSubtotal();
        $discount2x1 = $this->cart->calculate2x1()['discount'];
        $subtotalAfter2x1 = $subtotal - $discount2x1;
        $couponDiscount = $this->getSessionDiscount($subtotalAfter2x1);

        $validated['discount_code'] = $couponDiscount['code'];
        $validated['discount_amount'] = $discount2x1 + $couponDiscount['amount'];
        $validated['discount_2x1'] = $discount2x1;

        // If card payment, verify with Stripe that payment succeeded
        if ($validated['payment_method'] === 'card' && !empty($validated['stripe_payment_intent_id'])) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::retrieve($validated['stripe_payment_intent_id']);

            if ($paymentIntent->status === 'succeeded') {
                $validated['payment_status'] = 'paid';
                $validated['order_status'] = 'confirmed';
            }
        }

        $order = $this->checkout->process($validated);

        if ($couponDiscount['model']) {
            $couponDiscount['model']->increment('times_used');
        }

        session()->forget('discount_code_id');

        // Enlaza este pedido a la sesión actual para autorizar el acceso a
        // /epayco/pagar/{id} y /checkout/confirmacion/{id} sin exponer PII.
        session(['current_order_id' => $order->id]);

        // ePayco: el pedido queda pendiente y se envía al widget de pago.
        $redirect = $validated['payment_method'] === 'epayco'
            ? route('epayco.pay', $order->id)
            : route('checkout.confirmation', $order->id);

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => $redirect,
            ]);
        }

        return redirect()->to($redirect)
            ->with('success', '¡Pedido realizado con éxito!');
    }

    public function confirmation(Order $order): View
    {
        $this->authorizeOrderAccess($order);

        $order->load(['items.product', 'items.variant', 'customer']);

        return view('storefront.checkout-confirmation', [
            'order' => $order,
        ]);
    }

    /**
     * Autoriza el acceso a una orden por ID: la sesión actual la creó, o el
     * cliente autenticado es su dueño. Cualquier otro caso => 403.
     */
    private function authorizeOrderAccess(Order $order): void
    {
        if ((int) session('current_order_id') === (int) $order->id) {
            return;
        }

        $authCustomer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
        if ($authCustomer && (int) $order->customer_id === (int) $authCustomer->id) {
            return;
        }

        abort(403);
    }

    public function track(string $tracking_token): View
    {
        $order = Order::where('tracking_token', $tracking_token)
            ->with(['items.product', 'items.variant', 'customer'])
            ->firstOrFail();

        $bankDetails = [];
        if ($order->payment_method === 'transfer') {
            $bankDetails = [
                'bank_name'       => \App\Models\BankTransferSetting::get('bank_name', ''),
                'account_holder'  => \App\Models\BankTransferSetting::get('account_holder', ''),
                'account_type'    => \App\Models\BankTransferSetting::get('account_type', ''),
                'account_number'  => \App\Models\BankTransferSetting::get('account_number', ''),
                'document_type'   => \App\Models\BankTransferSetting::get('document_type', ''),
                'document_number' => \App\Models\BankTransferSetting::get('document_number', ''),
                'reference_instructions' => \App\Models\BankTransferSetting::get('reference_instructions', ''),
                'additional_notes'       => \App\Models\BankTransferSetting::get('additional_notes', ''),
            ];
        }

        return view('storefront.order-tracking', [
            'order' => $order,
            'bankDetails' => $bankDetails,
        ]);
    }

    /**
     * "Link de pago enviable": el dueño le manda al cliente el link público
     * /pedido/{tracking_token}/pagar por WhatsApp o correo. Al abrirlo, sembramos
     * `current_order_id` en la sesión y redirigimos al widget de ePayco. El guard
     * de EpaycoController@pay valida contra ese ID de sesión, así que el flujo
     * de pago funciona igual que si el cliente viniera del checkout normal.
     *
     * No exponemos aquí ningún endpoint de pago propio: sólo damos "pase" al
     * flujo existente. Transferencia y demás siguen usando el detalle público.
     */
    public function payFromToken(string $tracking_token): RedirectResponse
    {
        $order = Order::where('tracking_token', $tracking_token)->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('order.track', $tracking_token)
                ->with('success', 'Este pedido ya fue pagado.');
        }

        session(['current_order_id' => $order->id]);

        return redirect()->route('epayco.pay', $order->id);
    }

    public function uploadReceipt(Request $request, string $tracking_token): RedirectResponse
    {
        $order = Order::where('tracking_token', $tracking_token)->firstOrFail();

        if ($order->payment_method !== 'transfer' || $order->payment_status === 'paid') {
            return redirect()->route('order.track', $tracking_token)
                ->with('error', 'No es posible subir un comprobante para esta orden.');
        }

        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        // Delete old receipt if re-uploading
        if ($order->payment_receipt) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($order->payment_receipt);
        }

        $path = $request->file('receipt')->store('receipts', 'public');
        $order->update(['payment_receipt' => $path]);

        try {
            \Illuminate\Support\Facades\Mail::to(config('mail.admin'))
                ->send(new \App\Mail\ReceiptUploaded($order));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('order.track', $tracking_token)
            ->with('success', 'Comprobante subido exitosamente. Verificaremos tu pago pronto.');
    }

    private function getSessionDiscount(float $subtotal): array
    {
        $discountCodeId = session('discount_code_id');

        if (! $discountCodeId) {
            return ['code' => null, 'amount' => 0, 'model' => null];
        }

        $discountCode = DiscountCode::find($discountCodeId);

        if (! $discountCode || ! $discountCode->isValid($subtotal)) {
            session()->forget('discount_code_id');
            return ['code' => null, 'amount' => 0, 'model' => null];
        }

        return [
            'code' => $discountCode->code,
            'amount' => $discountCode->calculateDiscount($subtotal),
            'model' => $discountCode,
        ];
    }
}
