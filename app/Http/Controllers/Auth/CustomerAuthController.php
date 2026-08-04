<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showRegister(): View
    {
        return view('account.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'habeas_data' => 'accepted',
        ], [
            'habeas_data.accepted' => 'Debes aceptar la política de tratamiento de datos.',
            'password.confirmed'   => 'Las contraseñas no coinciden.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $existing = Customer::where('email', $data['email'])->first();

        // Si ya existe una cuenta con contraseña → que inicie sesión.
        if ($existing && $existing->hasAccount()) {
            throw ValidationException::withMessages([
                'email' => 'Ya existe una cuenta con este correo. Inicia sesión.',
            ]);
        }

        if ($existing) {
            // Cliente que compró como invitado: se "actualiza" a cuenta y
            // conserva su historial de pedidos.
            $existing->update([
                'name'     => $data['name'],
                'phone'    => $data['phone'] ?? $existing->phone,
                'password' => $data['password'],
            ]);
            $customer = $existing;
        } else {
            $customer = Customer::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'password' => $data['password'],
            ]);
        }

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->intended(route('account.dashboard'))
            ->with('success', '¡Bienvenida a Belleza Áurea! Tu cuenta está lista.');
    }

    public function showLogin(): View
    {
        return view('account.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('account.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Correo o contraseña incorrectos.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada.');
    }
}
