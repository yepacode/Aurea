<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function dashboard(): View
    {
        $customer = $this->customer();
        $recentOrders = $customer->orders()->latest()->take(5)->get();
        $ordersCount = $customer->orders()->count();

        return view('account.dashboard', compact('customer', 'recentOrders', 'ordersCount'));
    }

    public function orders(): View
    {
        $customer = $this->customer();
        $orders = $customer->orders()->latest()->paginate(10);

        return view('account.orders', compact('customer', 'orders'));
    }

    public function order(Order $order): View
    {
        // Seguridad: el pedido debe ser del cliente autenticado.
        abort_unless($order->customer_id === $this->customer()->id, 403);

        $order->load(['items.product', 'items.variant']);

        return view('account.order', compact('order'));
    }

    public function profile(): View
    {
        $customer = $this->customer();

        return view('account.profile', compact('customer'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $customer = $this->customer();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
            'city'     => 'nullable|string|max:100',
            'state'    => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $update = collect($data)->except('password')->toArray();
        if (! empty($data['password'])) {
            $update['password'] = $data['password'];
        }

        $customer->update($update);

        return redirect()->route('account.profile')->with('success', 'Datos actualizados.');
    }
}
