<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Services\PushService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Panel admin de notificaciones push:
 *   - stats de suscritos
 *   - broadcast manual a todos los suscritos
 */
class PushAdminController extends Controller
{
    public function index(): View
    {
        $total    = PushSubscription::count();
        $last30d  = PushSubscription::where('created_at', '>=', now()->subDays(30))->count();
        $identified = PushSubscription::whereNotNull('customer_id')->count();
        $anon     = $total - $identified;

        return view('admin.push.index', compact('total', 'last30d', 'identified', 'anon'));
    }

    public function send(Request $request, PushService $push): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:80',
            'body'  => 'required|string|max:200',
            'image' => 'nullable|url|max:500',
            'url'   => 'nullable|url|max:500',
        ]);

        $payload = [
            'title' => $data['title'],
            'body'  => $data['body'],
            'image' => $data['image'] ?? null,
            'url'   => $data['url'] ?? url('/'),
        ];

        [$sent, $failed, $expired] = $push->broadcastToAll($payload);

        $msg = "Broadcast enviado. Entregados: {$sent}. Fallidos: {$failed}. Expirados y limpiados: {$expired}.";

        return redirect()->route('admin.push.index')->with('success', $msg);
    }
}
