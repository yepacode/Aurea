<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Endpoints públicos para que el navegador registre/borre su suscripción push.
 * Un mismo endpoint = una sola fila; si el usuario se loguea después,
 * se actualiza el customer_id.
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

        $sub = PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'public_key' => $data['public_key'],
                'auth_token' => $data['auth_token'],
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'customer_id'=> $customerId,
                'session_id' => $customerId ? null : substr((string) $request->session()->getId(), 0, 255),
            ]
        );

        return response()->json(['ok' => true, 'id' => $sub->id]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|url|max:500',
        ]);

        PushSubscription::where('endpoint', $data['endpoint'])->delete();

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
