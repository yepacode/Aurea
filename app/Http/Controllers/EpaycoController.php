<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
     * Página que abre el widget de pago de ePayco para un pedido.
     */
    public function pay(Order $order)
    {
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
     * Redirección del navegador tras el pago. Consulta el estado real por ref_payco
     * y actualiza el pedido (clave para pruebas en localhost, donde no hay webhook).
     */
    public function response(Request $request)
    {
        $ref = $request->input('ref_payco', $request->query('ref_payco'));

        if (! $ref) {
            return redirect()->route('cart.index')->with('error', 'No recibimos la referencia del pago.');
        }

        $order = null;

        try {
            $res = Http::timeout(15)->get("https://secure.epayco.co/validation/v1/reference/{$ref}");
            if ($res->ok() && $res->json('success')) {
                $data = $res->json('data');
                $orderId = $data['x_extra1'] ?? null;
                $order = $orderId ? Order::find($orderId) : null;

                if ($order && $order->payment_status !== 'paid') {
                    $map = $this->mapStatus((int) ($data['x_cod_response'] ?? 0));
                    $order->update([
                        'payment_status'    => $map['payment_status'],
                        'status'            => $map['status'],
                        'payment_reference' => $ref,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ePayco response check failed: '.$e->getMessage());
        }

        if (! $order) {
            return redirect()->route('cart.index')
                ->with('error', 'No pudimos verificar el pago. Si te descontaron, contáctanos con la referencia '.$ref.'.');
        }

        $flash = match ($order->payment_status) {
            'paid'       => ['success' => '¡Pago aprobado! Gracias por tu compra 💛'],
            'processing' => ['success' => 'Tu pago está en proceso. Te avisaremos cuando se confirme.'],
            'failed'     => ['error'   => 'El pago fue rechazado. Puedes intentar de nuevo.'],
            default      => ['success' => 'Recibimos tu pedido. Verificaremos el estado del pago.'],
        };

        return redirect()->route('checkout.confirmation', $order->id)->with($flash);
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
            $map = $this->mapStatus($cod);
            $order->update([
                'payment_status'    => $map['payment_status'],
                'status'            => $map['status'],
                'payment_reference' => $refPayco,
            ]);
        }

        return response('ok', 200);
    }
}
