<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Integración con ePayco (Checkout Onpage).
 *
 * Flujo:
 *   1. CheckoutController@process crea el pedido (pending) y redirige a epayco.pay.
 *   2. epayco.pay abre el widget checkout.js con los datos del pedido.
 *   3. ePayco redirige al cliente a epayco.response (navegador) y notifica
 *      server-to-server a epayco.confirmation (webhook, la fuente de verdad).
 *
 * Nota sandbox: el webhook (confirmation) NO llega a localhost. En local usamos
 * epayco.response, que consulta la API pública de ePayco por ref_payco para
 * conocer el estado real. En producción manda el webhook.
 */
class EpaycoController extends Controller
{
    public function __construct(private CheckoutService $checkout) {}

    /** Códigos de respuesta de ePayco → estado interno del pedido. */
    private function mapStatus(int $cod): array
    {
        return match ($cod) {
            1       => ['payment_status' => 'paid',      'status' => 'confirmed'], // Aceptada
            3       => ['payment_status' => 'processing', 'status' => 'pending'],  // Pendiente
            2, 4    => ['payment_status' => 'failed',    'status' => 'pending'],   // Rechazada / Fallida
            default => ['payment_status' => 'pending',   'status' => 'pending'],
        };
    }

    /**
     * Toma el payload de ePayco (webhook o API validation) y arma el arreglo
     * completo de datos que guardamos en `orders`. Guardar TODO permite:
     *  - Mostrarle al cliente el motivo real de un rechazo.
     *  - Hacer reclamos con toda la info (transaction id, autorización, banco,
     *    franquicia, referencia).
     * Los campos del webhook usan prefijo x_; los de la API validation llegan
     * con el MISMO nombre (x_...). Así el helper funciona para ambos.
     */
    private function mapEpaycoPayload(array $data, int $cod): array
    {
        $map = $this->mapStatus($cod);

        return [
            'payment_status'         => $map['payment_status'],
            'status'                 => $map['status'],
            'payment_reference'      => $data['x_ref_payco'] ?? null,
            'payment_transaction_id' => $data['x_transaction_id'] ?? null,
            'payment_response_code'  => (string) $cod,
            'payment_response_reason'=> $data['x_response_reason_text'] ?? ($data['x_response'] ?? null),
            'payment_franchise'      => $data['x_franchise'] ?? null,
            'payment_bank'           => $data['x_bank_name'] ?? null,
            'payment_authorization'  => $data['x_approval_code'] ?? ($data['x_transaction_id'] ?? null),
            // Payload COMPLETO: guardamos TODO lo que devuelve la pasarela
            // (aunque no lo mostremos hoy). Nunca perdemos información.
            'payment_raw_response'   => $data,
        ];
    }

    /**
     * Página que abre el widget de pago de ePayco para un pedido.
     */
    public function pay(Order $order)
    {
        // Solo autoriza a quien creó el pedido en esta sesión, o al cliente
        // dueño autenticado. Otro caso => 403 (evita exponer datos ni permitir
        // que un tercero pague/abra el widget con nombre y correo de la víctima).
        if ((int) session('current_order_id') !== (int) $order->id) {
            $authCustomer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
            if (! $authCustomer || (int) $order->customer_id !== (int) $authCustomer->id) {
                abort(403);
            }
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Este pedido ya fue pagado.');
        }

        $order->load('customer');

        return view('storefront.epayco.pay', [
            'order'    => $order,
            'publicKey' => config('services.epayco.public_key'),
            'test'     => filter_var(config('services.epayco.test'), FILTER_VALIDATE_BOOL),
            'currency' => config('services.epayco.currency', 'cop'),
        ]);
    }

    /**
     * Redirección del navegador tras el pago.
     *
     * SEGURIDAD: este endpoint NO es fuente de verdad. Los datos llegan por GET
     * desde el navegador del cliente y son manipulables. La ÚNICA vía autorizada
     * para actualizar el estado del pago es el webhook `confirmation`, que valida
     * x_signature contra p_cust_id + p_key.
     *
     * Aquí solo:
     *   1) Leemos `x_extra1` (id del pedido) y `ref_payco` (opcional, log).
     *   2) Validamos ownership del pedido (sesión o customer autenticado).
     *   3) Redirigimos a la pantalla de confirmación.
     */
    public function response(Request $request)
    {
        // ref_payco lo aceptamos solo para logging; validamos formato para no
        // dejar que un valor arbitrario acabe en logs / URLs.
        $ref = (string) $request->input('ref_payco', '');
        if ($ref !== '' && ! preg_match('/^[A-Za-z0-9]+$/', $ref)) {
            $ref = '';
        }

        $orderId = $request->input('x_extra1');
        if (! is_numeric($orderId)) {
            return redirect('/')->with('error', 'Referencia de pago inválida.');
        }

        $order = Order::find((int) $orderId);
        if (! $order) {
            return redirect('/')->with('error', 'Pedido no encontrado.');
        }

        // Ownership: mismo criterio que CheckoutController@authorizeOrderAccess.
        if (! $this->userOwnsOrder($order)) {
            abort(403);
        }

        if ($ref !== '') {
            Log::info('ePayco response redirect', ['order' => $order->id, 'ref' => $ref]);
        }

        // El estado real del pago llega por webhook (confirmation). Aquí solo
        // redirigimos: la pantalla de confirmación mostrará lo que ya esté en BD.
        return redirect()->route('checkout.confirmation', $order->id);
    }

    /**
     * Autoriza a ver un pedido: la sesión que lo creó, o el cliente autenticado
     * que es su dueño. Espejo de CheckoutController::authorizeOrderAccess().
     */
    private function userOwnsOrder(Order $order): bool
    {
        if ((int) session('current_order_id') === (int) $order->id) {
            return true;
        }

        $authCustomer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
        if ($authCustomer && (int) $order->customer_id === (int) $authCustomer->id) {
            return true;
        }

        return false;
    }

    /**
     * Webhook server-to-server de ePayco. Fuente de verdad del pago en producción.
     * Valida la firma antes de actualizar el pedido.
     */
    public function confirmation(Request $request)
    {
        $refPayco    = $request->input('x_ref_payco');
        $transaction = $request->input('x_transaction_id');
        $amount      = $request->input('x_amount');
        $currency    = $request->input('x_currency_code');
        $signature   = $request->input('x_signature');
        $orderId     = $request->input('x_extra1');
        $cod         = (int) $request->input('x_cod_response');

        $custId = config('services.epayco.p_cust_id');
        $pKey   = config('services.epayco.p_key');

        $expected = hash('sha256', implode('^', [
            $custId, $pKey, $refPayco, $transaction, $amount, $currency,
        ]));

        if (! $signature || ! hash_equals($expected, $signature)) {
            Log::warning('ePayco webhook: firma inválida', ['ref' => $refPayco, 'order' => $orderId]);
            return response('invalid signature', 401);
        }

        $order = Order::find($orderId);
        if (! $order) {
            return response('order not found', 404);
        }

        // No degradar un pago ya aprobado.
        if ($order->payment_status !== 'paid') {
            $update = $this->mapEpaycoPayload($request->all(), $cod);
            $order->update($update);

            if ($update['payment_status'] === 'paid') {
                $this->checkout->decrementStockForOrder($order->refresh());
            }
        }

        return response('ok', 200);
    }
}
