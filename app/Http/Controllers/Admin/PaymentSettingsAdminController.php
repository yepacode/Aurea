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
        $data = [
            'epayco_public_key'  => PaymentSetting::get('epayco_public_key', config('services.epayco.public_key', '')),
            'epayco_private_key' => PaymentSetting::get('epayco_private_key', config('services.epayco.private_key', '')),
            'epayco_p_cust_id'   => PaymentSetting::get('epayco_p_cust_id', config('services.epayco.p_cust_id', '')),
            'epayco_p_key'       => PaymentSetting::get('epayco_p_key', config('services.epayco.p_key', '')),
            'epayco_test'        => filter_var(PaymentSetting::get('epayco_test', config('services.epayco.test', true)), FILTER_VALIDATE_BOOL),
        ];

        return view('admin.payments.index', $data);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'epayco_public_key'  => 'nullable|string|max:255',
            'epayco_private_key' => 'nullable|string|max:255',
            'epayco_p_cust_id'   => 'nullable|string|max:100',
            'epayco_p_key'       => 'nullable|string|max:255',
            'epayco_test'        => 'nullable|boolean',
        ]);

        foreach (['epayco_public_key', 'epayco_private_key', 'epayco_p_cust_id', 'epayco_p_key'] as $k) {
            PaymentSetting::set($k, trim((string) ($data[$k] ?? '')));
        }

        // Si NO se envió test (checkbox off), setearlo a 0 explícitamente
        PaymentSetting::set('epayco_test', $request->has('epayco_test') ? '1' : '0');

        return redirect()->route('admin.payments.index')
            ->with('success', 'Llaves de ePayco actualizadas.');
    }
}
