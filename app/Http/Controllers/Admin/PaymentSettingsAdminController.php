<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentSettingsAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.payments.index', [
            'stripeKey' => PaymentSetting::get('stripe_key', ''),
            'stripeSecret' => PaymentSetting::get('stripe_secret', ''),
            'stripeWebhookSecret' => PaymentSetting::get('stripe_webhook_secret', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'stripe_key' => 'nullable|string|max:255',
            'stripe_secret' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
        ]);

        PaymentSetting::set('stripe_key', trim((string) $request->input('stripe_key', '')));
        PaymentSetting::set('stripe_secret', trim((string) $request->input('stripe_secret', '')));
        PaymentSetting::set('stripe_webhook_secret', trim((string) $request->input('stripe_webhook_secret', '')));

        return redirect()->route('admin.payments.index')
            ->with('success', 'Llaves de la pasarela de pago actualizadas correctamente.');
    }
}
