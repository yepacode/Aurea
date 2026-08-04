<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StockNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'email'      => 'required|email|max:255',
        ]);

        // Evita duplicados pendientes del mismo correo/producto.
        StockNotification::firstOrCreate(
            [
                'product_id' => $data['product_id'],
                'email'      => $data['email'],
                'notified_at' => null,
            ]
        );

        $msg = '¡Listo! Te avisaremos por correo cuando vuelva a estar disponible.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $msg]);
        }

        return back()->with('success', $msg);
    }
}
