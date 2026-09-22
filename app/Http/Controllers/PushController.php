<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Endpoints públicos para que el navegador registre/borre su suscripción push.
 *
 * Ownership: cada suscripción pertenece a un customer_id (si hay sesión de
 * cliente) o a un session_id (anónimo). Los endpoints VERIFICAN dueño antes
 * de modificar o borrar la fila; así nadie puede desuscribir a otro con solo
 * conocer su endpoint URL.
 */
class PushController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint'   => 'required|url|max:500',
            'public_key' => 'required|string|max:255',
            'auth_token' => 'required|string|max:255',
        ]);

        $customerId = Auth::guard('customer')->id();
        $sessionId  = substr((string) $request->session()->getId(), 0, 255);

        $existing = PushSubscription::where('endpoint', $data['endpoint'])->first();

        if ($existing) {
            // Fila ya existe: solo el dueño puede actualizarla.
            //   - Si hay customer autenticado, dueño = customer_id.
            //   - Si NO hay customer, dueño = session_id (y la fila no debe
            //     estar reclamada por un customer distinto).
            $ownsByCustomer = $customerId && (int) $existing->customer_id === (int) $customerId;
            $ownsBySession  = ! $customerId
                && $existing->customer_id === null
                && $existing->session_id === $sessionId;

            if (! $ownsByCustomer && ! $ownsBySession) {
                // No confirmes existencia: devolvemos el mismo shape que un
                // subscribe exitoso pero SIN modificar la fila ajena.
                return response()->json(['ok' => true, 'id' => null]);
            }

            $existing->update([
                'public_key' => $data['public_key'],
                'auth_token' => $data['auth_token'],
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'customer_id'=> $customerId,
                'session_id' => $customerId ? null : $sessionId,
            ]);

            return response()->json(['ok' => true, 'id' => $existing->id]);
        }

        // No existe: crear nueva con owner actual.
        $sub = PushSubscription::create([
            'endpoint'   => $data['endpoint'],
            'public_key' => $data['public_key'],
            'auth_token' => $data['auth_token'],
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'customer_id'=> $customerId,
            'session_id' => $customerId ? null : $sessionId,
        ]);

        return response()->json(['ok' => true, 'id' => $sub->id]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|url|max:500',
        ]);

        $customerId = Auth::guard('customer')->id();
        $sessionId  = substr((string) $request->session()->getId(), 0, 255);

        $sub = PushSubscription::where('endpoint', $data['endpoint'])->first();

        // No existe → devolvemos ok sin confirmar/desmentir para no filtrar
        // información sobre suscripciones ajenas.
        if (! $sub) {
            return response()->json(['ok' => true]);
        }

        $ownsByCustomer = $customerId && (int) $sub->customer_id === (int) $customerId;
        $ownsBySession  = ! $customerId
            && $sub->customer_id === null
            && $sub->session_id === $sessionId;

        if (! $ownsByCustomer && ! $ownsBySession) {
            // Igual: no confirmes existencia.
            return response()->json(['ok' => true]);
        }

        $sub->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Devuelve la llave pública VAPID al navegador (para pushManager.subscribe).
     * Endpoint público y cacheable.
     */
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'key' => config('services.webpush.vapid.public_key'),
        ]);
    }
}
